<?php
if (!defined('ABSPATH')) {
    exit;
}

function argon_astro_register_shuoshuo() {
    if (post_type_exists('shuoshuo')) {
        return;
    }
    register_post_type('shuoshuo', array(
        'labels' => array(
            'name' => '说说',
            'singular_name' => '说说',
            'add_new_item' => '发表说说',
            'edit_item' => '编辑说说',
        ),
        'public' => true,
        'show_ui' => true,
        'show_in_rest' => true,
        'exclude_from_search' => true,
        'menu_icon' => 'dashicons-format-quote',
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'comments', 'custom-fields'),
        'rewrite' => array('slug' => 'shuoshuo', 'with_front' => false),
    ));
}
