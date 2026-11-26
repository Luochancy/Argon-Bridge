<?php
if (!defined('ABSPATH')) {
    exit;
}

function argon_astro_register_views_route() {
    register_rest_route('argon-astro/v1', '/views', array(
        array(
            'methods' => 'GET',
            'permission_callback' => 'argon_astro_rest_permission',
            'callback' => 'argon_astro_get_views',
        ),
        array(
            'methods' => 'POST',
            'permission_callback' => 'argon_astro_rest_permission',
            'callback' => 'argon_astro_increment_views',
        ),
    ));
}

function argon_astro_get_views(WP_REST_Request $request) {
    $id = (int) $request->get_param('id');
    if ($id < 1) {
        return new WP_Error('argon_astro_bad_id', 'Invalid post id.', array('status' => 400));
    }
    $views = (int) get_post_meta($id, 'views', true);
    return array('id' => $id, 'views' => max(0, $views));
}

function argon_astro_increment_views(WP_REST_Request $request) {
    $id = (int) $request->get_param('id');
    if ($id < 1) {
        return new WP_Error('argon_astro_bad_id', 'Invalid post id.', array('status' => 400));
    }
    if (function_exists('get_post')) {
        $post = get_post($id);
        if (!$post || $post->post_status !== 'publish') {
            return new WP_Error('argon_astro_missing_post', 'Post not found.', array('status' => 404));
        }
    }
    $views = max(0, (int) get_post_meta($id, 'views', true)) + 1;
    update_post_meta($id, 'views', $views);
    return array('id' => $id, 'views' => $views);
}
