<?php
/**
 * WordPress Admin registration, sanitization, and admin-post handlers.
 *
 * Visual rendering lives in `admin-view.php` and schema/mapping helpers live
 * in `admin-schema.php` to keep responsibilities cleanly separated.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Build the monochrome Argon double-hexagon SVG data URI for the top-level WP Admin menu icon.
 *
 * @return string
 */
function argon_astro_menu_icon_uri() {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">'
        . '<path fill="black" fill-opacity="0.42" d="M14.6 6.2c-.3-.5-1-.9-1.6-.9H8.8c-.6 0-1.3.4-1.6.9L5.1 9.8c-.3.5-.3 1.3 0 1.8l2.1 3.6c.3.5 1 .9 1.6.9H13c.6 0 1.3-.4 1.6-.9l2.1-3.6c.3-.5.3-1.3 0-1.8l-2.1-3.6z"/>'
        . '<path fill="black" d="M12.6 3.9c-.3-.5-1-.9-1.6-.9H6.6c-.6 0-1.3.4-1.6.9L2.8 7.7c-.3.5-.3 1.3 0 1.8L5 13.3c.3.5 1 .9 1.6.9H11c.6 0 1.3-.4 1.6-.9l2.2-3.8c.3-.5.3-1.3 0-1.8l-2.2-3.8z"/>'
        . '</svg>';
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

/**
 * Build the admin URL for the standalone Argon settings page.
 *
 * @param array<string, string|int> $extra_args
 * @return string
 */
function argon_astro_settings_page_url(array $extra_args = array()) {
    return add_query_arg(
        array_merge(array('page' => 'argon-bridge'), $extra_args),
        admin_url('admin.php')
    );
}

add_action('admin_menu', function () {
    add_menu_page(
        'Argon 主题设置',
        'Argon',
        'manage_options',
        'argon-bridge',
        'argon_astro_render_settings',
        argon_astro_menu_icon_uri(),
        59
    );
});

add_action('admin_init', function () {
    register_setting('argon_astro', 'argon_astro_config', array(
        'type' => 'array',
        'sanitize_callback' => 'argon_astro_sanitize_config',
    ));
    register_setting('argon_astro', 'argon_astro_hooks', array(
        'type' => 'array',
        'sanitize_callback' => 'argon_astro_sanitize_hooks',
    ));
    register_setting('argon_astro', 'argon_astro_hook_secret', array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    register_setting('argon_astro', 'argon_astro_auto_approve', array(
        'type' => 'boolean',
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));
});

add_action('admin_post_argon_astro_manual_deploy', 'argon_astro_handle_manual_deploy');
add_action('admin_post_argon_astro_import_json', 'argon_astro_handle_import_json');
add_action('admin_post_argon_astro_import_legacy', 'argon_astro_handle_import_legacy');

/**
 * Sanitize `argon_astro_config` from any supported input format:
 * - Visual form nested array (`argon_astro_config[...]`)
 * - Flat upstream `argon_*` array
 * - JSON string (either flat upstream Argon JSON or nested Astro JSON)
 *
 * Preserves previous config and records a settings error on invalid JSON.
 * Always strips `comments` and `source`.
 *
 * @param mixed $value
 * @return array<string, mixed>
 */
function argon_astro_sanitize_config($value) {
    $previous = function_exists('get_option') ? get_option('argon_astro_config', array()) : array();
    if (!is_array($previous)) {
        $previous = array();
    }

    if (!is_array($value)) {
        $raw = trim((string) $value);
        if ($raw === '') {
            $value = array();
        } else {
            $decoded = json_decode($raw, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                if (function_exists('add_settings_error')) {
                    add_settings_error(
                        'argon_astro_config',
                        'argon_astro_invalid_json',
                        'Invalid JSON in theme configuration: ' . json_last_error_msg(),
                        'error'
                    );
                }
                return $previous;
            }
            $value = $decoded;
        }
    }

    if (isset($_POST['option_page']) && $_POST['option_page'] === 'argon_astro' && function_exists('update_option')) {
        update_option('argon_astro_last_saved_at', time(), false);
    }

    if (argon_astro_is_flat_argon_array($value)) {
        $mapped = argon_astro_flat_to_nested($value);
        $merged = argon_astro_merge_config($previous, $mapped);
        return argon_astro_sanitize_nested_config($merged, $previous);
    }

    return argon_astro_sanitize_nested_config($value, $previous);
}

/**
 * Sanitize CI/deploy webhook provider selection, URLs, and GitHub dispatch coordinates.
 *
 * @param mixed $value
 * @return array<string, string>
 */
function argon_astro_sanitize_hooks($value) {
    $clean = array();
    if (!is_array($value)) {
        return $clean;
    }
    $allowed_providers = array('none', 'github', 'cloudflare', 'vercel', 'netlify', 'generic');
    if (isset($value['provider']) && in_array((string) $value['provider'], $allowed_providers, true)) {
        $clean['provider'] = (string) $value['provider'];
    }
    foreach (array('github_repo', 'github_token', 'cloudflare', 'vercel', 'netlify', 'generic') as $key) {
        if (!empty($value[$key])) {
            $clean[$key] = sanitize_text_field($value[$key]);
        }
    }
    return $clean;
}

/**
 * Handle manual deploy trigger button from the settings page.
 *
 * @return void
 */
function argon_astro_handle_manual_deploy() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized', 403);
    }
    check_admin_referer('argon_astro_manual_deploy');
    argon_astro_schedule_deploy('manual');
    $redirect = argon_astro_settings_page_url(array('argon_deploy_triggered' => '1'));
    wp_safe_redirect($redirect);
    exit;
}

/**
 * Handle one-click JSON import (supports both original flat Argon JSON and nested Astro JSON).
 *
 * @return void
 */
function argon_astro_handle_import_json() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized', 403);
    }
    check_admin_referer('argon_astro_import_json');

    $raw = isset($_POST['argon_astro_import_payload']) ? (string) $_POST['argon_astro_import_payload'] : '';
    if (function_exists('wp_unslash')) {
        $raw = (string) wp_unslash($raw);
    }
    $raw = trim($raw);
    $decoded = $raw !== '' ? json_decode($raw, true) : null;

    if ($raw === '' || json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
        $redirect = argon_astro_settings_page_url(array('argon_notice' => 'import_error'));
        wp_safe_redirect($redirect);
        exit;
    }

    $previous = get_option('argon_astro_config', array());
    if (!is_array($previous)) {
        $previous = array();
    }

    $incoming = argon_astro_is_flat_argon_array($decoded)
        ? argon_astro_flat_to_nested($decoded)
        : $decoded;
    $merged = argon_astro_merge_config($previous, $incoming);
    $sanitized = argon_astro_sanitize_nested_config($merged, $previous);

    update_option('argon_astro_config', $sanitized);

    $redirect = argon_astro_settings_page_url(array('argon_notice' => 'import_ok'));
    wp_safe_redirect($redirect);
    exit;
}

/**
 * Handle one-click import from existing legacy `argon_*` WordPress theme options.
 *
 * @return void
 */
function argon_astro_handle_import_legacy() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized', 403);
    }
    check_admin_referer('argon_astro_import_legacy');

    $count = argon_astro_count_legacy_options();
    $imported = argon_astro_import_legacy_options();

    if ($count === 0 || empty($imported)) {
        $redirect = argon_astro_settings_page_url(array('argon_notice' => 'legacy_empty'));
        wp_safe_redirect($redirect);
        exit;
    }

    $previous = get_option('argon_astro_config', array());
    if (!is_array($previous)) {
        $previous = array();
    }
    $merged = argon_astro_merge_config($previous, $imported);
    $sanitized = argon_astro_sanitize_nested_config($merged, $previous);

    update_option('argon_astro_config', $sanitized);

    $redirect = argon_astro_settings_page_url(array(
        'argon_notice' => 'legacy_ok',
        'argon_imported' => $count,
    ));
    wp_safe_redirect($redirect);
    exit;
}
