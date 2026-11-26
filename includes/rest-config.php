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

function argon_astro_relativize_same_origin_url($url, $site_url = '') {
    $url = trim((string) $url);
    if ($url === '') {
        return '';
    }
    if ($site_url === '' && function_exists('home_url')) {
        $site_url = home_url('/');
    }
    if ($site_url !== '' && function_exists('argon_astro_is_same_origin') && argon_astro_is_same_origin($url, $site_url)) {
        $parts = parse_url($url);
        $path = isset($parts['path']) && $parts['path'] !== '' ? $parts['path'] : '/';
        if (!empty($parts['query'])) {
            $path .= '?' . $parts['query'];
        }
        if (!empty($parts['fragment'])) {
            $path .= '#' . $parts['fragment'];
        }
        return $path;
    }
    return $url;
}

function argon_astro_extract_menu_icon_and_label($raw_title, $classes = array()) {
    $title = (string) $raw_title;
    $icon_tokens = array();

    if (preg_match('/<i\b[^>]*class=["\']([^"\']+)["\'][^>]*>\s*<\/i>/i', $title, $m)) {
        foreach (preg_split('/\s+/', trim($m[1])) as $tok) {
            if (preg_match('/^(?:fa|ni)(?:-[a-z0-9-]+)?$/i', $tok)) {
                $icon_tokens[] = $tok;
            }
        }
        $title = preg_replace('/<i\b[^>]*>\s*<\/i>/i', '', $title, 1);
    }

    if (empty($icon_tokens) && is_array($classes)) {
        foreach ($classes as $cls) {
            $cls = trim((string) $cls);
            if (preg_match('/^(?:fa|ni)(?:-[a-z0-9-]+)?$/i', $cls)) {
                $icon_tokens[] = $cls;
            }
        }
    }

    $icon = '';
    if (!empty($icon_tokens)) {
        $icon_tokens = array_values(array_unique($icon_tokens));
        $has_prefix = false;
        foreach ($icon_tokens as $tok) {
            if (strtolower($tok) === 'fa' || strtolower($tok) === 'ni') {
                $has_prefix = true;
                break;
            }
        }
        if (!$has_prefix && stripos($icon_tokens[0], 'fa-') === 0) {
            array_unshift($icon_tokens, 'fa');
        }
        $icon = implode(' ', $icon_tokens);
    }

    $label = trim(html_entity_decode(strip_tags($title), ENT_QUOTES, 'UTF-8'));
    return array('label' => $label, 'icon' => $icon);
}

function argon_astro_export_nav_menu($location, $site_url = '') {
    if (!function_exists('get_nav_menu_locations') || !function_exists('wp_get_nav_menu_items')) {
        return null;
    }
    $locations = get_nav_menu_locations();
    if (!is_array($locations) || empty($locations[$location])) {
        return null;
    }
    $raw_items = wp_get_nav_menu_items((int) $locations[$location]);
    if (!is_array($raw_items)) {
        return null;
    }

    $nodes = array();
    $children_by_parent = array();
    foreach ($raw_items as $item) {
        if (!is_object($item)) {
            continue;
        }
        $id = isset($item->ID) ? (int) $item->ID : 0;
        $parent = isset($item->menu_item_parent) ? (int) $item->menu_item_parent : 0;
        $classes = isset($item->classes) && is_array($item->classes) ? $item->classes : array();
        $parsed = argon_astro_extract_menu_icon_and_label(isset($item->title) ? $item->title : '', $classes);
        $href = argon_astro_relativize_same_origin_url(isset($item->url) ? $item->url : '#', $site_url);
        $target = isset($item->target) ? trim((string) $item->target) : '';
        $node = array(
            'label' => $parsed['label'],
            'href' => $href !== '' ? $href : '#',
            'children' => array(),
        );
        if ($parsed['icon'] !== '') {
            $node['icon'] = $parsed['icon'];
        }
        if ($target !== '') {
            $node['target'] = $target;
        }
        $nodes[$id] = $node;
        $children_by_parent[$parent][] = $id;
    }

    $build_tree = function ($parent_id, $depth = 0) use (&$build_tree, &$nodes, &$children_by_parent) {
        if ($depth > 4 || empty($children_by_parent[$parent_id])) {
            return array();
        }
        $out = array();
        foreach ($children_by_parent[$parent_id] as $cid) {
            if (!isset($nodes[$cid])) {
                continue;
            }
            $node = $nodes[$cid];
            $node['children'] = $build_tree($cid, $depth + 1);
            $out[] = $node;
        }
        return $out;
    };

    return $build_tree(0, 0);
}

function argon_astro_export_wp_bookmarks() {
    if (!function_exists('get_bookmarks')) {
        return array();
    }
    $bookmarks = get_bookmarks(array(
        'orderby' => 'name',
        'order' => 'ASC',
    ));
    if (!is_array($bookmarks)) {
        return array();
    }
    $links = array();
    foreach ($bookmarks as $bm) {
        if (!is_object($bm)) {
            continue;
        }
        $name = trim((string) ($bm->link_name ?? ''));
        $url = trim((string) ($bm->link_url ?? ''));
        if ($name === '' && $url === '') {
            continue;
        }
        $links[] = array(
            'name' => $name,
            'url' => $url,
            'description' => trim((string) ($bm->link_description ?? '')),
            'avatar' => trim((string) ($bm->link_image ?? '')),
        );
    }
    return $links;
}

function argon_astro_capture_sidebar_html($sidebar_id, $site_url = '') {
    if (!function_exists('is_active_sidebar') || !function_exists('dynamic_sidebar')) {
        return '';
    }
    if (!is_active_sidebar($sidebar_id)) {
        return '';
    }
    ob_start();
    dynamic_sidebar($sidebar_id);
    $html = (string) ob_get_clean();
    if (trim($html) === '') {
        return '';
    }
    // Strip inline scripts and CMS admin/ajax endpoints, and relativize same-origin links.
    $html = preg_replace('/<script\b[^>]*>[\s\S]*?<\/script>/i', '', $html);
    if ($site_url !== '' && function_exists('argon_astro_url_origin')) {
        $origin = argon_astro_url_origin($site_url);
        if ($origin !== '') {
            $html = preg_replace_callback(
                '/(\b(?:href|action)=["\'])' . preg_quote($origin, '/') . '(\/[^"\'<>\s]*|(?=["\']))/i',
                function ($m) {
                    return $m[1] . ($m[2] !== '' ? $m[2] : '/');
                },
                $html
            );
        }
    }
    $html = str_ireplace(array('wp-json', 'wp-admin', 'admin-ajax.php'), '', $html);
    return trim($html);
}

function argon_astro_export_wp_taxonomies($site_url = '') {
    if (!function_exists('get_terms')) {
        return array('tags' => array(), 'categories' => array());
    }
    $export = function ($taxonomy) use ($site_url) {
        $terms = get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => false));
        if (!is_array($terms) || is_wp_error($terms)) {
            return array();
        }
        $out = array();
        foreach ($terms as $term) {
            if (!is_object($term) || empty($term->name)) {
                continue;
            }
            $permalink = '';
            if (function_exists('get_term_link')) {
                $link = get_term_link($term);
                if (!is_wp_error($link) && is_string($link)) {
                    $permalink = argon_astro_relativize_same_origin_url($link, $site_url);
                }
            }
            $out[] = array(
                'name' => (string) $term->name,
                'slug' => isset($term->slug) ? (string) $term->slug : '',
                'permalink' => $permalink,
            );
        }
        return $out;
    };
    return array(
        'tags' => $export('post_tag'),
        'categories' => $export('category'),
    );
}

function argon_astro_wp_timezone_string() {
    if (function_exists('wp_timezone_string')) {
        $tz = trim((string) wp_timezone_string());
        if ($tz !== '') {
            return $tz;
        }
    }
    if (function_exists('get_option')) {
        $tz_string = trim((string) get_option('timezone_string', ''));
        if ($tz_string !== '') {
            return $tz_string;
        }
        $offset = get_option('gmt_offset', '');
        if ($offset !== '' && is_numeric($offset)) {
            $minutes = (int) round(((float) $offset) * 60);
            $sign = $minutes >= 0 ? '+' : '-';
            $abs = abs($minutes);
            return sprintf('%s%02d:%02d', $sign, (int) floor($abs / 60), $abs % 60);
        }
    }
    return '';
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
    if (empty($config['site']['timezone'])) {
        $wp_tz = argon_astro_wp_timezone_string();
        if ($wp_tz !== '') {
            $config['site']['timezone'] = $wp_tz;
        }
    }
    if (empty($config['article']['meta']) && function_exists('get_option')) {
        $legacy_meta = get_option('argon_article_meta', '');
        if ($legacy_meta !== '' && function_exists('argon_astro_parse_article_meta')) {
            $parsed_meta = argon_astro_parse_article_meta($legacy_meta);
            if (!empty($parsed_meta)) {
                if (!isset($config['article']) || !is_array($config['article'])) {
                    $config['article'] = array();
                }
                $config['article']['meta'] = $parsed_meta;
            }
        }
    }
    if (empty($config['site']['icon']) && function_exists('get_site_icon_url')) {
        $site_icon = (string) get_site_icon_url(192);
        if ($site_icon !== '') {
            $config['site']['icon'] = $site_icon;
        }
    }

    $site_url = function_exists('home_url') ? home_url('/') : '';

    // Relativize same-origin links in config so static site never redirects back to the headless CMS host.
    if (!empty($config['toolbar']['iconLink'])) {
        $config['toolbar']['iconLink'] = argon_astro_relativize_same_origin_url($config['toolbar']['iconLink'], $site_url);
    }
    if (!empty($config['article']['archivesUrl'])) {
        $config['article']['archivesUrl'] = argon_astro_relativize_same_origin_url($config['article']['archivesUrl'], $site_url);
    }

    // Export WordPress nav menus (Appearance -> Menus) when assigned to Argon locations.
    $toolbar_menu = argon_astro_export_nav_menu('toolbar_menu', $site_url);
    if (is_array($toolbar_menu) && !empty($toolbar_menu)) {
        if (!isset($config['toolbar']) || !is_array($config['toolbar'])) {
            $config['toolbar'] = array();
        }
        $config['toolbar']['menu'] = $toolbar_menu;
    }

    $leftbar_menu = argon_astro_export_nav_menu('leftbar_menu', $site_url);
    if (is_array($leftbar_menu) && !empty($leftbar_menu)) {
        if (!isset($config['sidebar']) || !is_array($config['sidebar'])) {
            $config['sidebar'] = array();
        }
        $config['sidebar']['menu'] = $leftbar_menu;
    }

    $author_links = argon_astro_export_nav_menu('leftbar_author_links', $site_url);
    if (is_array($author_links) && !empty($author_links)) {
        if (!isset($config['sidebar']) || !is_array($config['sidebar'])) {
            $config['sidebar'] = array();
        }
        $config['sidebar']['authorLinks'] = array_map(function ($item) {
            $out = array(
                'label' => $item['label'],
                'href' => $item['href'],
            );
            if (!empty($item['icon'])) {
                $out['icon'] = $item['icon'];
            }
            return $out;
        }, $author_links);
    }

    $sidebar_friend_links = argon_astro_export_nav_menu('leftbar_friend_links', $site_url);
    if (is_array($sidebar_friend_links) && !empty($sidebar_friend_links)) {
        if (!isset($config['sidebar']) || !is_array($config['sidebar'])) {
            $config['sidebar'] = array();
        }
        $config['sidebar']['friendLinks'] = array_map(function ($item) {
            return array(
                'label' => $item['label'],
                'href' => $item['href'],
            );
        }, $sidebar_friend_links);
    }

    // Fall back to WordPress native Link Manager (wp_links / get_bookmarks) when friendLinks config is empty.
    if (empty($config['friendLinks']) || !is_array($config['friendLinks'])) {
        $bookmarks = argon_astro_export_wp_bookmarks();
        if (!empty($bookmarks)) {
            $config['friendLinks'] = $bookmarks;
        }
    }

    $taxonomies = argon_astro_export_wp_taxonomies($site_url);
    if (!empty($taxonomies['tags']) || !empty($taxonomies['categories'])) {
        $config['taxonomies'] = $taxonomies;
    }

    // Export WordPress widget areas (leftbar-tools, leftbar-siteinfo-extra-tools, rightbar-tools).
    if (!isset($config['sidebar']) || !is_array($config['sidebar'])) {
        $config['sidebar'] = array();
    }
    $left_tools = argon_astro_capture_sidebar_html('leftbar-tools', $site_url);
    $extra_tools = argon_astro_capture_sidebar_html('leftbar-siteinfo-extra-tools', $site_url);
    $right_tools = argon_astro_capture_sidebar_html('rightbar-tools', $site_url);
    if (function_exists('is_active_sidebar')) {
        if ($left_tools !== '' || empty($config['sidebar']['widgets'])) {
            $config['sidebar']['widgets'] = array();
        }
        $config['sidebar']['widgetsHtml'] = $left_tools;
        $config['sidebar']['extraHtml'] = $extra_tools;
        $config['sidebar']['rightWidgetsHtml'] = $right_tools;
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
