<?php
if (!defined('ABSPATH')) {
    exit;
}

define('ARGON_ASTRO_DEBOUNCE_SECONDS', 15);
define('ARGON_ASTRO_RATE_LIMIT_SECONDS', 60);
define('ARGON_ASTRO_WEBHOOK_LOG_LIMIT', 50);

function argon_astro_tracked_post_types() {
    return array('post', 'page', 'shuoshuo');
}

function argon_astro_append_webhook_log($entries) {
    $existing = get_option('argon_astro_webhook_log', array());
    if (!is_array($existing)) {
        $existing = array();
    }
    foreach ($entries as $entry) {
        $existing[] = $entry;
    }
    if (count($existing) > ARGON_ASTRO_WEBHOOK_LOG_LIMIT) {
        $existing = array_slice($existing, -ARGON_ASTRO_WEBHOOK_LOG_LIMIT);
    }
    update_option('argon_astro_webhook_log', $existing, false);
    return $existing;
}

function argon_astro_register_deploy_route() {
    register_rest_route('argon-astro/v1', '/deploy', array(
        'methods' => 'POST',
        'permission_callback' => function (WP_REST_Request $request) {
            if (!argon_astro_can_build()) {
                return false;
            }
            return argon_astro_verify_request_signature($request);
        },
        'callback' => function (WP_REST_Request $request) {
            $reason = sanitize_text_field((string) ($request->get_param('reason') ?: 'rest-deploy'));
            argon_astro_schedule_deploy($reason);
            return array('scheduled' => true, 'reason' => $reason);
        },
    ));
}

/**
 * Schedule a trailing-edge debounced deploy event.
 */
function argon_astro_schedule_deploy($reason = 'theme-config') {
    $reason = is_string($reason) && $reason !== '' ? $reason : 'theme-config';
    if (function_exists('wp_clear_scheduled_hook')) {
        wp_clear_scheduled_hook('argon_astro_deploy_event');
    }
    wp_schedule_single_event(time() + ARGON_ASTRO_DEBOUNCE_SECONDS, 'argon_astro_deploy_event');
    update_option('argon_astro_pending_deploy', time(), false);
    update_option('argon_astro_pending_reason', $reason, false);
}

function argon_astro_run_scheduled_deploy() {
    $last = (int) get_option('argon_astro_last_deploy', 0);
    $delay = argon_astro_retry_delay(time(), $last, ARGON_ASTRO_RATE_LIMIT_SECONDS);
    if ($delay > 0) {
        if (function_exists('wp_clear_scheduled_hook')) {
            wp_clear_scheduled_hook('argon_astro_deploy_event');
        }
        wp_schedule_single_event(time() + $delay, 'argon_astro_deploy_event');
        return;
    }
    $reason = (string) get_option('argon_astro_pending_reason', 'theme-config');
    if ($reason === '') {
        $reason = 'theme-config';
    }
    delete_option('argon_astro_pending_deploy');
    delete_option('argon_astro_pending_reason');
    update_option('argon_astro_last_deploy', time(), false);
    argon_astro_dispatch_hooks($reason);
}

function argon_astro_dispatch_hooks($reason) {
    $hooks = get_option('argon_astro_hooks', array());
    if (!is_array($hooks)) {
        $hooks = array();
    }
    $reason = is_string($reason) && $reason !== '' ? $reason : 'theme-config';
    $now = time();
    $body = wp_json_encode(array('reason' => $reason, 'sentAt' => $now));
    $timestamp = (string) $now;
    $nonce = wp_generate_password(16, false);
    $secret = (string) get_option('argon_astro_hook_secret', '');
    $signature = $secret !== '' ? argon_astro_sign($secret, $timestamp, $nonce, $body) : '';
    $headers = array(
        'Content-Type' => 'application/json',
        'X-Argon-Timestamp' => $timestamp,
        'X-Argon-Nonce' => $nonce,
        'X-Argon-Signature' => $signature,
    );
    $entries = array();
    $provider = isset($hooks['provider']) ? (string) $hooks['provider'] : '';
    if ($provider === 'none') {
        return $entries;
    }
    if ($provider !== '') {
        if ($provider === 'github' && !empty($hooks['github_repo']) && !empty($hooks['github_token'])) {
            $entries[] = argon_astro_post_github($hooks['github_repo'], $hooks['github_token'], $reason);
        } elseif (in_array($provider, array('cloudflare', 'vercel', 'netlify', 'generic'), true) && !empty($hooks[$provider])) {
            $entries[] = argon_astro_post_hook($provider, $hooks[$provider], $headers, $body, $reason);
        }
    } else {
        // Legacy fallback when no explicit provider is set.
        if (!empty($hooks['github_repo']) && !empty($hooks['github_token'])) {
            $entries[] = argon_astro_post_github($hooks['github_repo'], $hooks['github_token'], $reason);
        }
        foreach (array('cloudflare', 'vercel', 'netlify', 'generic') as $name) {
            if (!empty($hooks[$name])) {
                $entries[] = argon_astro_post_hook($name, $hooks[$name], $headers, $body, $reason);
            }
        }
    }
    if (!empty($entries)) {
        argon_astro_append_webhook_log($entries);
    }
    return $entries;
}

function argon_astro_post_github($repo, $token, $reason) {
    $response = wp_remote_post('https://api.github.com/repos/' . trim($repo, '/') . '/dispatches', array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/vnd.github+json',
            'Content-Type' => 'application/json',
            'User-Agent' => 'argon-bridge',
        ),
        'body' => wp_json_encode(array(
            'event_type' => 'argon-config',
            'client_payload' => array('reason' => $reason),
        )),
        'timeout' => 15,
    ));
    $code = is_wp_error($response) ? 0 : (int) wp_remote_retrieve_response_code($response);
    return array(
        'timestamp' => gmdate('c'),
        'reason' => $reason,
        'target' => 'github',
        'status' => $code,
        'ok' => !is_wp_error($response) && $code >= 200 && $code < 300,
    );
}

function argon_astro_post_hook($name, $url, $headers, $body, $reason = 'theme-config') {
    $response = wp_remote_post($url, array('headers' => $headers, 'body' => $body, 'timeout' => 15));
    $code = is_wp_error($response) ? 0 : (int) wp_remote_retrieve_response_code($response);
    return array(
        'timestamp' => gmdate('c'),
        'reason' => $reason,
        'target' => $name,
        'status' => $code,
        'ok' => !is_wp_error($response) && $code >= 200 && $code < 300,
    );
}

function argon_astro_on_config_change() {
    argon_astro_schedule_deploy('theme-config');
}

function argon_astro_on_save_post($post_id, $post, $update) {
    if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || wp_is_post_revision($post_id)) {
        return;
    }
    if (!$post || !in_array($post->post_type, argon_astro_tracked_post_types(), true)) {
        return;
    }
    if ($post->post_status !== 'publish') {
        return;
    }
    argon_astro_schedule_deploy('post-save:' . $post->post_type);
}

function argon_astro_on_transition_post_status($new_status, $old_status, $post) {
    if (!$post || !in_array($post->post_type, argon_astro_tracked_post_types(), true)) {
        return;
    }
    if ($new_status === $old_status) {
        return;
    }
    if ($new_status === 'publish' || $old_status === 'publish') {
        argon_astro_schedule_deploy('post-transition:' . $post->post_type);
    }
}

function argon_astro_on_deleted_post($post_id) {
    $post = function_exists('get_post') ? get_post($post_id) : null;
    if ($post && !in_array($post->post_type, argon_astro_tracked_post_types(), true)) {
        return;
    }
    argon_astro_schedule_deploy('post-delete');
}

add_action('add_option_argon_astro_config', 'argon_astro_on_config_change', 10, 0);
add_action('update_option_argon_astro_config', 'argon_astro_on_config_change', 10, 0);
add_action('save_post', 'argon_astro_on_save_post', 10, 3);
add_action('transition_post_status', 'argon_astro_on_transition_post_status', 10, 3);
add_action('deleted_post', 'argon_astro_on_deleted_post', 10, 1);
