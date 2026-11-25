<?php
if (!defined('ABSPATH')) {
    exit;
}

function argon_astro_can_build() {
    return current_user_can('edit_posts');
}

function argon_astro_rest_permission() {
    return argon_astro_can_build();
}

function argon_astro_verify_request_signature(WP_REST_Request $request) {
    $secret = (string) get_option('argon_astro_hook_secret', '');
    if ($secret === '') {
        return new WP_Error('argon_astro_no_secret', 'Webhook secret is not configured.', array('status' => 503));
    }
    $timestamp = (string) $request->get_header('x-argon-timestamp');
    $nonce = (string) $request->get_header('x-argon-nonce');
    $signature = (string) $request->get_header('x-argon-signature');
    if ($nonce === '' || !argon_astro_timestamp_fresh($timestamp, time())) {
        return new WP_Error('argon_astro_stale', 'Timestamp is missing or outside the allowed window.', array('status' => 401));
    }
    $key = 'argon_astro_nonce_' . md5($nonce);
    if (get_transient($key)) {
        return new WP_Error('argon_astro_replay', 'Nonce was already used.', array('status' => 409));
    }
    $expected = argon_astro_sign($secret, $timestamp, $nonce, $request->get_body());
    if (!hash_equals($expected, $signature)) {
        return new WP_Error('argon_astro_bad_signature', 'Signature does not match.', array('status' => 401));
    }
    set_transient($key, 1, 600);
    return true;
}
