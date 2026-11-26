<?php
if (!defined('ABSPATH')) {
    exit;
}

function argon_astro_is_qq_number($value) {
    return (bool) preg_match('/^[1-9][0-9]{4,10}$/', trim((string) $value));
}

function argon_astro_is_valid_contact($contact) {
    $contact = trim((string) $contact);
    if ($contact === '') {
        return false;
    }
    if (argon_astro_is_qq_number($contact)) {
        return true;
    }
    return function_exists('is_email')
        ? (bool) is_email($contact)
        : (bool) filter_var($contact, FILTER_VALIDATE_EMAIL);
}

function argon_astro_normalize_contact_email($contact) {
    $contact = trim((string) $contact);
    if (argon_astro_is_qq_number($contact)) {
        return $contact . '@qq.com';
    }
    return $contact;
}

function argon_astro_format_comment($comment, $parent_authors = array()) {
    $id = (int) $comment->comment_ID;
    $parent_id = (int) $comment->comment_parent;
    $private = (bool) get_comment_meta($id, 'argon_private', true);
    $pinned = (bool) get_comment_meta($id, 'argon_pinned', true);
    $markdown = (bool) get_comment_meta($id, 'argon_markdown', true);
    $upvotes = (int) get_comment_meta($id, 'argon_upvote', true);
    $qq = (string) get_comment_meta($id, 'qq_number', true);
    $history = json_decode((string) get_comment_meta($id, 'argon_edit_history', true), true);
    if (!is_array($history)) {
        $history = array();
    }
    $parent_author = '';
    if ($parent_id > 0) {
        if (isset($parent_authors[$parent_id])) {
            $parent_author = (string) $parent_authors[$parent_id];
        } elseif (function_exists('get_comment')) {
            $parent_comment = get_comment($parent_id);
            if ($parent_comment) {
                $parent_author = (string) $parent_comment->comment_author;
            }
        }
    }
    $raw_gmt = trim((string) $comment->comment_date_gmt);
    if (preg_match('/^\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}:\d{2}$/', $raw_gmt)) {
        $date = preg_replace('/\s+/', 'T', $raw_gmt) . 'Z';
    } elseif ($raw_gmt !== '' && $raw_gmt !== '0000-00-00 00:00:00') {
        $ts = strtotime($raw_gmt);
        $date = $ts !== false ? gmdate('Y-m-d\TH:i:s\Z', $ts) : $raw_gmt;
    } else {
        $date = gmdate('Y-m-d\TH:i:s\Z');
    }

    return array(
        'id' => $id,
        'parent' => $parent_id,
        'author' => (string) $comment->comment_author,
        'date' => $date,
        'content' => $private ? '' : (string) $comment->comment_content,
        'url' => (string) $comment->comment_author_url,
        'parentAuthor' => $parent_author,
        'pinned' => $pinned,
        'private' => $private,
        'markdown' => $markdown,
        'upvotes' => $upvotes,
        'qq' => $qq,
        'gravatar' => md5(strtolower(trim((string) $comment->comment_author_email))),
        'userAgent' => isset($comment->comment_agent) ? (string) $comment->comment_agent : '',
        'editHistory' => $history,
    );
}

function argon_astro_export_comments($post_id) {
    $comments = get_comments(array(
        'post_id' => (int) $post_id,
        'status' => 'approve',
        'order' => 'ASC',
    ));
    $authors = array();
    foreach ($comments as $comment) {
        $authors[(int) $comment->comment_ID] = (string) $comment->comment_author;
    }
    return array_map(function ($comment) use ($authors) {
        return argon_astro_format_comment($comment, $authors);
    }, $comments);
}

function argon_astro_register_comment_routes() {
    register_rest_route('argon-astro/v1', '/comments', array(
        array(
            'methods' => 'GET',
            'permission_callback' => 'argon_astro_rest_permission',
            'callback' => 'argon_astro_list_comments',
        ),
        array(
            'methods' => 'POST',
            'permission_callback' => 'argon_astro_rest_permission',
            'callback' => 'argon_astro_create_comment',
        ),
    ));
    register_rest_route('argon-astro/v1', '/comments/edit', array(
        'methods' => 'POST',
        'permission_callback' => 'argon_astro_rest_permission',
        'callback' => 'argon_astro_edit_comment',
    ));
    register_rest_route('argon-astro/v1', '/comments/pin', array(
        'methods' => 'POST',
        'permission_callback' => 'argon_astro_rest_permission',
        'callback' => 'argon_astro_pin_comment',
    ));
    register_rest_route('argon-astro/v1', '/comments/history', array(
        'methods' => 'GET',
        'permission_callback' => 'argon_astro_rest_permission',
        'callback' => 'argon_astro_comment_history',
    ));
}

function argon_astro_comment_history(WP_REST_Request $request) {
    $id = (int) $request->get_param('id');
    if ($id < 1) {
        return new WP_Error('argon_astro_bad_id', 'Invalid comment id.', array('status' => 400));
    }
    $comment = function_exists('get_comment') ? get_comment($id) : null;
    if (!$comment) {
        return new WP_Error('argon_astro_missing_comment', 'Comment was not found.', array('status' => 404));
    }
    $private = (bool) get_comment_meta($id, 'argon_private', true);
    if ($private) {
        return array('id' => $id, 'history' => array());
    }
    $raw = (string) get_comment_meta($id, 'argon_edit_history', true);
    $history = json_decode($raw, true);
    if (!is_array($history)) {
        $legacy = json_decode((string) get_comment_meta($id, 'comment_edit_history', true), true);
        $history = is_array($legacy) ? $legacy : array();
    }
    return array('id' => $id, 'history' => array_values($history));
}

function argon_astro_list_comments(WP_REST_Request $request) {
    $post_id = (int) $request->get_param('post');
    return argon_astro_export_comments($post_id);
}

function argon_astro_create_comment(WP_REST_Request $request) {
    if ($request->get_param('action') === 'pin') {
        return argon_astro_pin_comment($request);
    }
    $post_id = (int) $request->get_param('postId');
    $content = trim((string) $request->get_param('content'));
    $author = trim((string) $request->get_param('author'));
    $contact = trim((string) $request->get_param('email'));
    if ($post_id < 1 || $content === '' || strlen($content) > 2000 || $author === '' || !argon_astro_is_valid_contact($contact)) {
        return new WP_Error('argon_astro_bad_comment', 'Comment payload is invalid.', array('status' => 400));
    }

    $is_qq = argon_astro_is_qq_number($contact);
    $normalized_email = argon_astro_normalize_contact_email($contact);
    $auto_approve = (bool) get_option('argon_astro_auto_approve', false);
    $approved = $auto_approve ? 1 : (get_option('comment_moderation') ? 0 : 1);

    $agent = '';
    if (method_exists($request, 'get_header')) {
        $agent = sanitize_text_field((string) $request->get_header('user-agent'));
    }

    $id = wp_new_comment(array(
        'comment_post_ID' => $post_id,
        'comment_content' => $content,
        'comment_author' => $author,
        'comment_author_email' => $normalized_email,
        'comment_author_url' => esc_url_raw((string) $request->get_param('url')),
        'comment_parent' => (int) $request->get_param('parent'),
        'comment_approved' => $approved,
        'comment_type' => 'comment',
        'comment_agent' => $agent,
        'comment_author_IP' => '',
    ), true);
    if (is_wp_error($id)) {
        $code = method_exists($id, 'get_error_code') ? (string) $id->get_error_code() : 'comment_error';
        if ($code === 'comment_duplicate') {
            return new WP_Error('comment_duplicate', '检测到重复评论，请勿重复提交。', array('status' => 409));
        }
        if ($code === 'comment_flood') {
            return new WP_Error('comment_flood', '评论提交过于频繁，请稍后再试。', array('status' => 429));
        }
        $raw_data = method_exists($id, 'get_error_data') ? $id->get_error_data() : null;
        $status = 400;
        if (is_array($raw_data) && isset($raw_data['status']) && is_numeric($raw_data['status'])) {
            $status = (int) $raw_data['status'];
        } elseif (is_numeric($raw_data) && (int) $raw_data >= 400 && (int) $raw_data < 600) {
            $status = (int) $raw_data;
        }
        $msg = method_exists($id, 'get_error_message') ? (string) $id->get_error_message() : '评论提交失败。';
        return new WP_Error($code, $msg !== '' ? $msg : '评论提交失败。', array('status' => $status));
    }

    $id = (int) $id;
    if ($auto_approve && function_exists('wp_set_comment_status')) {
        // wp_new_comment runs WordPress moderation filters and ignores comment_approved;
        // explicitly approve when the site administrator enabled argon_astro_auto_approve.
        wp_set_comment_status($id, 'approve');
    }
    $actual_approved = $approved === 1;
    if (function_exists('get_comment')) {
        $saved = get_comment($id);
        if ($saved) {
            $actual_approved = (string) $saved->comment_approved === '1';
        }
    }
    if (rest_sanitize_boolean($request->get_param('mailNotice'))) {
        update_comment_meta($id, 'argon_mail_notice', '1');
    }
    if (rest_sanitize_boolean($request->get_param('markdown'))) {
        update_comment_meta($id, 'argon_markdown', '1');
    }
    if (rest_sanitize_boolean($request->get_param('private'))) {
        update_comment_meta($id, 'argon_private', '1');
    }
    if ($is_qq) {
        update_comment_meta($id, 'qq_number', $contact);
    }

    // Send reply email notification when replying to a comment whose author opted into mailNotice.
    $parent_id = (int) $request->get_param('parent');
    if ($parent_id > 0 && $actual_approved && function_exists('get_comment') && function_exists('wp_mail')) {
        $parent_comment = get_comment($parent_id);
        $wants_mail = (string) get_comment_meta($parent_id, 'argon_mail_notice', true) === '1'
            || (string) get_comment_meta($parent_id, 'enable_mailnotice', true) === 'true';
        if ($parent_comment && $wants_mail) {
            $parent_email = trim((string) ($parent_comment->comment_author_email ?? ''));
            if ($parent_email !== '' && strtolower($parent_email) !== strtolower($normalized_email) && argon_astro_is_valid_contact($parent_email)) {
                $site_name = function_exists('get_bloginfo') ? (string) get_bloginfo('name') : 'Blog';
                $post_title = function_exists('get_the_title') ? (string) get_the_title($post_id) : '';
                $subject = '[' . $site_name . '] 您的评论收到了新回复';
                $message = "您好，" . (string) $parent_comment->comment_author . "：\n\n"
                    . $author . " 回复了您在" . ($post_title !== '' ? "《" . $post_title . "》" : "文章") . "下的评论：\n\n"
                    . $content . "\n";
                @wp_mail($parent_email, $subject, $message);
            }
        }
    }

    return array(
        'id' => $id,
        'approved' => $actual_approved,
        'markdown' => rest_sanitize_boolean($request->get_param('markdown')),
        'private' => rest_sanitize_boolean($request->get_param('private')),
    );
}

function argon_astro_edit_comment(WP_REST_Request $request) {
    $id = (int) $request->get_param('id');
    $contact = trim((string) $request->get_param('email'));
    $content = trim((string) $request->get_param('content'));
    $comment = get_comment($id);
    if (!$comment) {
        return new WP_Error('argon_astro_missing_comment', 'Comment was not found.', array('status' => 404));
    }
    $normalized = strtolower(argon_astro_normalize_contact_email($contact));
    $stored_qq = (string) get_comment_meta($id, 'qq_number', true);
    $matches_owner = strtolower((string) $comment->comment_author_email) === $normalized
        || ($stored_qq !== '' && $stored_qq === $contact);
    if ($content === '' || strlen($content) > 2000 || !argon_astro_is_valid_contact($contact) || !$matches_owner) {
        return new WP_Error('argon_astro_forbidden_edit', 'Comment edit was refused.', array('status' => 403));
    }
    $history = json_decode((string) get_comment_meta($id, 'argon_edit_history', true), true);
    if (!is_array($history)) {
        $history = array();
    }
    $history[] = array('content' => $comment->comment_content, 'date' => gmdate('c'));
    update_comment_meta($id, 'argon_edit_history', wp_json_encode($history));
    wp_update_comment(array(
        'comment_ID' => $id,
        'comment_content' => $content,
    ));
    return array('id' => $id, 'content' => $content, 'editHistory' => $history);
}

function argon_astro_pin_comment(WP_REST_Request $request) {
    $id = (int) $request->get_param('id');
    $comment = get_comment($id);
    if (!$comment) {
        return new WP_Error('argon_astro_missing_comment', 'Comment was not found.', array('status' => 404));
    }
    $raw_pinned = $request->get_param('pinned');
    if ($raw_pinned === null) {
        $next = !(bool) get_comment_meta($id, 'argon_pinned', true);
    } else {
        $next = rest_sanitize_boolean($raw_pinned);
    }
    update_comment_meta($id, 'argon_pinned', $next ? '1' : '0');
    return array('id' => $id, 'pinned' => $next);
}
