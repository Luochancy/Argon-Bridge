<?php
if (!defined('ABSPATH')) {
    exit;
}

function argon_astro_register_post_unlock_route() {
    register_rest_route('argon-astro/v1', '/post-unlock', array(
        'methods' => 'POST',
        'permission_callback' => 'argon_astro_rest_permission',
        'callback' => 'argon_astro_unlock_post',
    ));
}

function argon_astro_unlock_post(WP_REST_Request $request) {
    $id = (int) $request->get_param('id');
    $password = (string) $request->get_param('password');
    if ($id < 1 || $password === '') {
        return new WP_Error('argon_astro_bad_unlock', '参数无效。', array('status' => 400));
    }

    $post = function_exists('get_post') ? get_post($id) : null;
    if (!$post || $post->post_status !== 'publish') {
        return new WP_Error('argon_astro_missing_post', '文章不存在。', array('status' => 404));
    }
    if ((string) $post->post_password === '') {
        return new WP_Error('argon_astro_not_locked', '该文章未加密。', array('status' => 400));
    }
    if (!hash_equals((string) $post->post_password, $password)) {
        return new WP_Error('argon_astro_wrong_password', '密码错误。', array('status' => 403));
    }

    $raw = (string) $post->post_content;
    $html = function_exists('apply_filters') ? (string) apply_filters('the_content', $raw) : $raw;
    if (function_exists('home_url')) {
        $site_origin = argon_astro_url_origin(home_url('/'));
        if ($site_origin !== '') {
            $html = preg_replace_callback(
                '/(\bhref=["\'])' . preg_quote($site_origin, '/') . '(\/[^"\'<>\s]*|(?=["\']))/i',
                function ($m) {
                    return $m[1] . ($m[2] !== '' ? $m[2] : '/');
                },
                $html
            );
        }
    }

    return array(
        'id' => (int) $post->ID,
        'title' => function_exists('get_the_title') ? (string) get_the_title($post) : (string) ($post->post_title ?? ''),
        'html' => $html,
    );
}
