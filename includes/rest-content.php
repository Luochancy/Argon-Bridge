<?php
if (!defined('ABSPATH')) {
    exit;
}

function argon_astro_relative_permalink($post) {
    if (!function_exists('get_permalink')) {
        return '';
    }
    $permalink = (string) get_permalink($post);
    if ($permalink === '') {
        return '';
    }
    $parts = parse_url($permalink);
    $path = isset($parts['path']) ? (string) $parts['path'] : '';
    // Ignore plain ?p=123 query permalinks where path is just "/".
    if ($path === '' || $path === '/' || !empty($parts['query'])) {
        return '';
    }
    // Strip WordPress subdirectory base path if installed in a subfolder.
    if (function_exists('home_url')) {
        $home_path = (string) parse_url(home_url('/'), PHP_URL_PATH);
        $home_prefix = rtrim($home_path, '/');
        if ($home_prefix !== '' && strpos($path, $home_prefix . '/') === 0) {
            $path = substr($path, strlen($home_prefix));
        }
    }
    return $path;
}

function argon_astro_resolve_page_template($post) {
    if ($post->post_type !== 'page') {
        return '';
    }
    $slug = function_exists('get_page_template_slug') ? (string) get_page_template_slug($post->ID) : '';
    $base = strtolower(basename($slug));
    if ($base === 'timeline.php') {
        return 'timeline';
    }
    if ($base === 'msgboard.php') {
        return 'message';
    }
    if ($base === 'shuoshuo.php') {
        return 'shuoshuo';
    }
    return 'page';
}

function argon_astro_map_post($post) {
    $categories = wp_get_post_terms($post->ID, 'category', array('fields' => 'names'));
    $tags = wp_get_post_terms($post->ID, 'post_tag', array('fields' => 'names'));
    $thumb_id = (int) get_post_thumbnail_id($post);
    $thumb_url = $thumb_id > 0 ? (string) wp_get_attachment_url($thumb_id) : '';
    $modified = get_post_modified_time('c', true, $post);
    $protected = $post->post_password !== '';
    $sticky = $post->post_type === 'shuoshuo'
        ? (bool) get_post_meta($post->ID, 'argon_sticky', true)
        : is_sticky($post->ID);
    $comments_open = function_exists('comments_open')
        ? (bool) comments_open($post->ID)
        : (isset($post->comment_status) && $post->comment_status === 'open');

    $item = array(
        'id' => (int) $post->ID,
        'slug' => $post->post_name,
        'permalink' => argon_astro_relative_permalink($post),
        'template' => argon_astro_resolve_page_template($post),
        'commentsOpen' => $comments_open,
        'title' => get_the_title($post),
        'date' => get_post_time('c', true, $post),
        'modified' => $modified,
        'modifiedDate' => $modified,
        'excerpt' => wp_strip_all_tags(get_the_excerpt($post)),
        'status' => $post->post_status,
        'sticky' => $sticky,
        'categories' => is_wp_error($categories) ? array() : $categories,
        'tags' => is_wp_error($tags) ? array() : $tags,
        'commentCount' => (int) $post->comment_count,
        'upvotes' => (int) get_post_meta($post->ID, 'argon_upvote', true),
        'featuredMediaId' => $thumb_id,
        'featuredMediaUrl' => $thumb_url,
        'protected' => $protected,
        'raw' => $protected ? '' : $post->post_content,
        'rendered' => $protected ? '' : apply_filters('the_content', $post->post_content),
        'media' => $protected ? array() : argon_astro_collect_media($post->ID, $post->post_content),
        'comments' => $protected ? array() : argon_astro_export_comments($post->ID),
    );
    if ($post->post_type === 'shuoshuo') {
        $item['mood'] = (string) get_post_meta($post->ID, 'argon_mood', true);
        $meta_images = get_post_meta($post->ID, 'argon_images', true);
        $images = is_array($meta_images) ? array_values(array_filter(array_map('strval', $meta_images))) : array();
        foreach ($item['media'] as $media) {
            if (!empty($media['sourceUrl'])) {
                $images[] = $media['sourceUrl'];
            }
        }
        $item['images'] = array_values(array_unique($images));
    }
    return $item;
}

function argon_astro_rest_collection(WP_REST_Request $request, $post_type) {
    $page = max(1, (int) $request->get_param('page'));
    $per_page = min(100, max(1, (int) ($request->get_param('per_page') ?: 50)));
    $query = new WP_Query(array(
        'post_type' => $post_type,
        'post_status' => 'publish',
        'posts_per_page' => $per_page,
        'paged' => $page,
        'orderby' => 'date',
        'order' => 'DESC',
    ));
    $items = array();
    foreach ($query->posts as $post) {
        $items[] = argon_astro_map_post($post);
    }
    return array(
        'page' => $page,
        'totalPages' => max(1, (int) $query->max_num_pages),
        'items' => $items,
    );
}

function argon_astro_register_content_routes() {
    $types = array('posts' => 'post', 'pages' => 'page', 'shuoshuo' => 'shuoshuo');
    foreach ($types as $route => $type) {
        register_rest_route('argon-astro/v1', '/' . $route, array(
            'methods' => 'GET',
            'permission_callback' => 'argon_astro_rest_permission',
            'callback' => function (WP_REST_Request $request) use ($type) {
                return argon_astro_rest_collection($request, $type);
            },
        ));
    }
}
