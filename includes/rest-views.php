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

function argon_astro_read_post_views($id) {
    $id = (int) $id;
    if ($id < 1) {
        return 0;
    }
    $meta_raw = get_post_meta($id, 'views', true);
    $meta_views = is_numeric(str_replace(',', '', (string) $meta_raw))
        ? max(0, (int) str_replace(',', '', (string) $meta_raw))
        : 0;
    if ($meta_views === 0) {
        $alt_raw = get_post_meta($id, 'post_views_count', true);
        if (is_numeric(str_replace(',', '', (string) $alt_raw))) {
            $meta_views = max(0, (int) str_replace(',', '', (string) $alt_raw));
        }
    }
    $pvc_views = 0;
    if (function_exists('pvc_get_post_views')) {
        $pvc_raw = pvc_get_post_views($id);
        $pvc_views = max(0, (int) str_replace(',', '', (string) $pvc_raw));
    }
    return max($meta_views, $pvc_views);
}

function argon_astro_get_views(WP_REST_Request $request) {
    $ids_param = trim((string) $request->get_param('ids'));
    if ($ids_param !== '') {
        $parts = array_slice(preg_split('/[\s,]+/', $ids_param), 0, 50);
        $map = array();
        foreach ($parts as $part) {
            if (!preg_match('/^[0-9]+$/', $part)) {
                continue;
            }
            $pid = (int) $part;
            if ($pid > 0) {
                $map[(string) $pid] = argon_astro_read_post_views($pid);
            }
        }
        return array('views' => $map);
    }
    $id = (int) $request->get_param('id');
    if ($id < 1) {
        return new WP_Error('argon_astro_bad_id', 'Invalid post id.', array('status' => 400));
    }
    return array('id' => $id, 'views' => argon_astro_read_post_views($id));
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
    $views = argon_astro_read_post_views($id) + 1;
    update_post_meta($id, 'views', $views);
    return array('id' => $id, 'views' => $views);
}
