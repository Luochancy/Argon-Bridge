<?php
if (!defined('ABSPATH')) {
    exit;
}

function argon_astro_register_upvote_route() {
    register_rest_route('argon-astro/v1', '/shuoshuo-upvote', array(
        'methods' => 'POST',
        'permission_callback' => 'argon_astro_rest_permission',
        'callback' => 'argon_astro_upvote_shuoshuo',
    ));
}

function argon_astro_upvote_shuoshuo(WP_REST_Request $request) {
    $id = (int) $request->get_param('id');
    $post = get_post($id);
    if (!$post || $post->post_type !== 'shuoshuo') {
        return new WP_Error('argon_astro_missing', 'Shuoshuo was not found.', array('status' => 404));
    }
    $total = (int) get_post_meta($id, 'argon_upvote', true) + 1;
    update_post_meta($id, 'argon_upvote', $total);
    return array('status' => 'success', 'total' => $total);
}
