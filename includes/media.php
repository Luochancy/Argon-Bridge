<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Pure media URL helpers (safe to include without WordPress).
 */

function argon_astro_url_origin($url) {
    $parts = parse_url((string) $url);
    if (empty($parts['scheme']) || empty($parts['host'])) {
        return '';
    }
    $origin = strtolower($parts['scheme']) . '://' . strtolower($parts['host']);
    if (!empty($parts['port'])) {
        $origin .= ':' . (int) $parts['port'];
    }
    return $origin;
}

function argon_astro_is_same_origin($url, $site_url) {
    $target = argon_astro_url_origin($url);
    $site = argon_astro_url_origin($site_url);
    return $target !== '' && $site !== '' && $target === $site;
}

function argon_astro_extract_content_image_urls($content, $site_url) {
    $urls = array();
    $content = (string) $content;
    if (preg_match_all('/<img\b[^>]*\bsrc=["\']([^"\']+)["\']/i', $content, $matches)) {
        foreach ($matches[1] as $src) {
            $src = trim(html_entity_decode($src, ENT_QUOTES, 'UTF-8'));
            if (argon_astro_is_same_origin($src, $site_url)) {
                $urls[] = $src;
            }
        }
    }
    if (preg_match_all('/\bsrcset=["\']([^"\']+)["\']/i', $content, $matches)) {
        foreach ($matches[1] as $srcset) {
            $entries = explode(',', $srcset);
            foreach ($entries as $entry) {
                $parts = preg_split('/\s+/', trim($entry));
                if (!empty($parts[0])) {
                    $candidate = html_entity_decode($parts[0], ENT_QUOTES, 'UTF-8');
                    if (argon_astro_is_same_origin($candidate, $site_url)) {
                        $urls[] = $candidate;
                    }
                }
            }
        }
    }
    return array_values(array_unique($urls));
}

function argon_astro_extract_config_image_urls($config, $site_url) {
    if (!is_array($config)) {
        return array();
    }
    $candidates = array();
    if (!empty($config['banner']['backgroundUrl'])) {
        $candidates[] = $config['banner']['backgroundUrl'];
    }
    if (!empty($config['background']['url'])) {
        $candidates[] = $config['background']['url'];
    }
    if (!empty($config['background']['darkUrl'])) {
        $candidates[] = $config['background']['darkUrl'];
    }
    if (!empty($config['sidebar']['authorImage'])) {
        $candidates[] = $config['sidebar']['authorImage'];
    }
    if (!empty($config['friendLinks']) && is_array($config['friendLinks'])) {
        foreach ($config['friendLinks'] as $link) {
            if (is_array($link) && !empty($link['avatar'])) {
                $candidates[] = $link['avatar'];
            }
        }
    }
    $urls = array();
    foreach ($candidates as $url) {
        $url = trim((string) $url);
        if (argon_astro_is_same_origin($url, $site_url)) {
            $urls[] = $url;
        }
    }
    return array_values(array_unique($urls));
}

function argon_astro_media_item($id) {
    $id = (int) $id;
    $url = wp_get_attachment_url($id);
    if (!$url) {
        return null;
    }
    $meta = wp_get_attachment_metadata($id);
    return array(
        'id' => $id,
        'sourceUrl' => $url,
        'alt' => (string) get_post_meta($id, '_wp_attachment_image_alt', true),
        'mime' => (string) get_post_mime_type($id),
        'width' => isset($meta['width']) ? (int) $meta['width'] : null,
        'height' => isset($meta['height']) ? (int) $meta['height'] : null,
    );
}

function argon_astro_collect_media($post_id, $content) {
    $ids = array();
    $thumb = (int) get_post_thumbnail_id($post_id);
    if ($thumb) {
        $ids[] = $thumb;
    }
    if (preg_match_all('/wp-image-(\d+)/', $content, $matches)) {
        foreach ($matches[1] as $id) {
            $ids[] = (int) $id;
        }
    }
    if (preg_match_all('/"id"\s*:\s*(\d+)/', $content, $matches)) {
        foreach ($matches[1] as $id) {
            $ids[] = (int) $id;
        }
    }
    $items = array();
    $seen_urls = array();
    foreach (array_unique($ids) as $id) {
        $item = argon_astro_media_item($id);
        if ($item && empty($seen_urls[$item['sourceUrl']])) {
            $seen_urls[$item['sourceUrl']] = true;
            $items[] = $item;
        }
    }

    $site_url = function_exists('home_url') ? home_url('/') : '';
    $extra_urls = argon_astro_extract_content_image_urls($content, $site_url);

    $shuoshuo_images = get_post_meta($post_id, 'argon_images', true);
    if (is_array($shuoshuo_images)) {
        foreach ($shuoshuo_images as $img_url) {
            $img_url = trim((string) $img_url);
            if ($img_url !== '' && argon_astro_is_same_origin($img_url, $site_url)) {
                $extra_urls[] = $img_url;
            }
        }
    }

    foreach (array_unique($extra_urls) as $url) {
        if (!empty($seen_urls[$url])) {
            continue;
        }
        $attached_id = function_exists('attachment_url_to_postid') ? (int) attachment_url_to_postid($url) : 0;
        if ($attached_id > 0) {
            $item = argon_astro_media_item($attached_id);
            if ($item) {
                $seen_urls[$item['sourceUrl']] = true;
                $seen_urls[$url] = true;
                $items[] = $item;
                continue;
            }
        }
        $seen_urls[$url] = true;
        $items[] = array(
            'id' => 0,
            'sourceUrl' => $url,
            'alt' => '',
            'mime' => '',
            'width' => null,
            'height' => null,
        );
    }

    return $items;
}

function argon_astro_collect_config_media($config) {
    $site_url = function_exists('home_url') ? home_url('/') : '';
    $urls = argon_astro_extract_config_image_urls($config, $site_url);
    $items = array();
    foreach ($urls as $url) {
        $attached_id = function_exists('attachment_url_to_postid') ? (int) attachment_url_to_postid($url) : 0;
        if ($attached_id > 0) {
            $item = argon_astro_media_item($attached_id);
            if ($item) {
                $items[] = $item;
                continue;
            }
        }
        $items[] = array(
            'id' => 0,
            'sourceUrl' => $url,
            'alt' => '',
            'mime' => '',
            'width' => null,
            'height' => null,
        );
    }
    return $items;
}
