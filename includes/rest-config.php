<?php
if (!defined('ABSPATH')) {
    exit;
}

function argon_astro_default_config() {
    return array(
        'version' => 1,
        'site' => array(
            'title' => get_bloginfo('name'),
            'description' => get_bloginfo('description'),
            'language' => get_bloginfo('language'),
            'keywords' => '',
        ),
        'appearance' => array('color' => '#5e72e4', 'layout' => 'double', 'darkmode' => 'system'),
        'friendLinks' => array(),
    );
}

function argon_astro_public_config() {
    $stored = get_option('argon_astro_config', array());
    if (!is_array($stored)) {
        $stored = array();
    }
    unset($stored['comments'], $stored['source']);
    $config = function_exists('argon_astro_merge_config')
        ? argon_astro_merge_config(argon_astro_default_config(), $stored)
        : array_replace_recursive(argon_astro_default_config(), $stored);

    // Fall back field-by-field to WordPress settings whenever an individual site field is left blank.
    if (!isset($config['site']) || !is_array($config['site'])) {
        $config['site'] = array();
    }
    $wp_site_fallbacks = array(
        'title' => (string) get_bloginfo('name'),
        'description' => (string) get_bloginfo('description'),
        'language' => (string) get_bloginfo('language'),
    );
    foreach ($wp_site_fallbacks as $field => $wp_value) {
        $current = isset($config['site'][$field]) ? trim((string) $config['site'][$field]) : '';
        if ($current === '') {
            $config['site'][$field] = $wp_value;
        }
    }
    if (isset($config['site']['url']) && trim((string) $config['site']['url']) === '') {
        unset($config['site']['url']);
    }

    if (function_exists('argon_astro_collect_config_media')) {
        $config['media'] = argon_astro_collect_config_media($config);
    }
    return $config;
}

function argon_astro_register_config_route() {
    register_rest_route('argon-astro/v1', '/config', array(
        'methods' => 'GET',
        'permission_callback' => 'argon_astro_rest_permission',
        'callback' => 'argon_astro_public_config',
    ));
}
