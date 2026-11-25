<?php
if (!defined('ABSPATH')) {
    exit;
}

function argon_astro_register_comment_upvote_route() {
    register_rest_route('argon-astro/v1', '/comment-upvote', array(
        'methods' => 'POST',
        'permission_callback' => 'argon_astro_rest_permission',
        'callback' => 'argon_astro_upvote_comment',
    ));
}

function argon_astro_upvote_comment(WP_REST_Request $request) {
    $id = (int) $request->get_param('id');
    $comment = get_comment($id);
    if (!$comment) {
        return new WP_Error('argon_astro_missing_comment', 'Comment was not found.', array('status' => 404));
    }
    $total = (int) get_comment_meta($id, 'argon_upvote', true) + 1;
    update_comment_meta($id, 'argon_upvote', $total);
    return array('status' => 'success', 'total' => $total);
}
