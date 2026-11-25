<?php
/**
 * Schema defaults, sanitization, and bidirectional Argon option mapping.
 *
 * Pure helper layer safe to include without WordPress loaded. Maps flat
 * upstream `argon_*` WordPress theme options to and from the nested
 * `argon_astro_config` tree consumed by Argon.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Return the 12 classic Argon preset theme colors.
 *
 * @return array<int, array{hex: string, label: string}>
 */
function argon_astro_theme_color_presets() {
    return array(
        array('hex' => '#5e72e4', 'label' => 'Argon (默认)'),
        array('hex' => '#fa7298', 'label' => '粉'),
        array('hex' => '#009688', 'label' => '水鸭青'),
        array('hex' => '#607d8b', 'label' => '蓝灰'),
        array('hex' => '#2196f3', 'label' => '天蓝'),
        array('hex' => '#3f51b5', 'label' => '靛蓝'),
        array('hex' => '#ff9700', 'label' => '橙'),
        array('hex' => '#109d58', 'label' => '绿'),
        array('hex' => '#dc4437', 'label' => '红'),
        array('hex' => '#673bb7', 'label' => '紫'),
        array('hex' => '#212121', 'label' => '黑'),
        array('hex' => '#795547', 'label' => '棕'),
    );
}

/**
 * Return supported Highlight.js code themes from upstream Argon.
 *
 * @return array<int, string>
 */
function argon_astro_code_themes() {
    return array(
        'a11y-dark', 'a11y-light', 'agate', 'an-old-hope', 'androidstudio',
        'arduino-light', 'arta', 'ascetic', 'atelier-cave-dark', 'atelier-cave-light',
        'atelier-dune-dark', 'atelier-dune-light', 'atelier-estuary-dark', 'atelier-estuary-light',
        'atelier-forest-dark', 'atelier-forest-light', 'atelier-heath-dark', 'atelier-heath-light',
        'atelier-lakeside-dark', 'atelier-lakeside-light', 'atelier-plateau-dark', 'atelier-plateau-light',
        'atelier-savanna-dark', 'atelier-savanna-light', 'atelier-seaside-dark', 'atelier-seaside-light',
        'atelier-sulphurpool-dark', 'atelier-sulphurpool-light', 'atom-one-dark-reasonable',
        'atom-one-dark', 'atom-one-light', 'brown-paper', 'codepen-embed', 'color-brewer',
        'darcula', 'dark', 'darkula', 'default', 'devibeans', 'docco', 'dracula', 'far',
        'felipec', 'foundation', 'github-dark-dimmed', 'github-dark', 'github-gist', 'github',
        'gml', 'googlecode', 'gradient-dark', 'gradient-light', 'grayscale', 'gruvbox-dark',
        'gruvbox-light', 'hopscotch', 'hybrid', 'idea', 'intellij-light', 'ir-black',
        'isbl-editor-dark', 'isbl-editor-light', 'kimbie-dark', 'kimbie-light', 'lightfair',
        'lioshi', 'magula', 'mono-blue', 'monokai-sublime', 'monokai', 'night-owl',
        'nnfx-dark', 'nnfx-light', 'nnfx', 'nord', 'obsidian', 'ocean', 'onedark',
        'paraiso-dark', 'paraiso-light', 'pojoaque', 'purebasic', 'qtcreator-dark',
        'qtcreator-light', 'railscasts', 'rainbow', 'routeros', 'school-book',
        'shades-of-purple', 'solarized-dark', 'solarized-light', 'srcery',
        'stackoverflow-dark', 'stackoverflow-light', 'sunburst', 'tokyo-night-dark',
        'tomorrow-night-blue', 'tomorrow-night-bright', 'tomorrow-night-eighties',
        'tomorrow-night', 'tomorrow', 'vs', 'vs2015', 'xcode', 'xt256', 'zenburn',
    );
}

/**
 * Return all upstream `argon_*` WordPress option keys supported by the bridge.
 * WordPress-only options and comment-provider keys are intentionally omitted.
 *
 * @return array<int, string>
 */
function argon_astro_legacy_option_keys() {
    return array(
        'argon_theme_color',
        'argon_show_customize_theme_color_picker',
        'argon_enable_immersion_color',
        'argon_darkmode_autoswitch',
        'argon_enable_amoled_dark',
        'argon_card_radius',
        'argon_card_shadow',
        'argon_page_layout',
        'argon_article_list_waterflow',
        'argon_article_list_layout',
        'argon_font',
        'argon_dateformat',
        'argon_enable_headroom',
        'argon_toolbar_title',
        'argon_toolbar_icon',
        'argon_toolbar_icon_link',
        'argon_toolbar_blur',
        'argon_banner_title',
        'argon_banner_subtitle',
        'argon_banner_size',
        'argon_page_background_banner_style',
        'argon_show_toolbar_mask',
        'argon_banner_background_url',
        'argon_banner_background_color_type',
        'argon_banner_background_hide_shapes',
        'argon_enable_banner_title_typing_effect',
        'argon_banner_typing_effect_interval',
        'argon_page_background_url',
        'argon_page_background_dark_url',
        'argon_page_background_opacity',
        'argon_sidebar_banner_title',
        'argon_sidebar_banner_subtitle',
        'argon_sidebar_auther_name',
        'argon_sidebar_auther_image',
        'argon_sidebar_author_description',
        'argon_sidebar_announcement',
        'argon_fab_show_settings_button',
        'argon_fab_show_darkmode_button',
        'argon_fab_show_gotocomment_button',
        'argon_seo_description',
        'argon_seo_keywords',
        'argon_article_meta',
        'argon_show_readingtime',
        'argon_reading_speed',
        'argon_reading_speed_en',
        'argon_reading_speed_code',
        'argon_show_thumbnail_in_banner_in_content_page',
        'argon_first_image_as_thumbnail_by_default',
        'argon_reference_list_title',
        'argon_show_sharebtn',
        'argon_show_headindex_number',
        'argon_donate_qrcode_url',
        'argon_additional_content_after_post',
        'argon_related_post',
        'argon_related_post_sort_orderby',
        'argon_related_post_sort_order',
        'argon_related_post_limit',
        'argon_article_header_style',
        'argon_outdated_info_time_type',
        'argon_outdated_info_days',
        'argon_outdated_info_tip_type',
        'argon_outdated_info_tip_content',
        'argon_archives_timeline_show_month',
        'argon_archives_timeline_url',
        'argon_footer_html',
        'argon_hide_footer_author',
        'argon_enable_code_highlight',
        'argon_code_theme',
        'argon_code_highlight_hide_linenumber',
        'argon_code_highlight_break_line',
        'argon_code_highlight_transparent_linenumber',
        'argon_disable_codeblock_style',
        'argon_math_render',
        'argon_mathjax_cdn_url',
        'argon_mathjax_v2_cdn_url',
        'argon_katex_cdn_url',
        'argon_enable_lazyload',
        'argon_lazyload_threshold',
        'argon_lazyload_effect',
        'argon_lazyload_loading_style',
        'argon_enable_fancybox',
        'argon_enable_zoomify',
        'argon_zoomify_duration',
        'argon_zoomify_easing',
        'argon_zoomify_scale',
        'argon_enable_pangu',
        'argon_custom_html_head',
        'argon_custom_html_foot',
        'argon_enable_smoothscroll_type',
        'argon_enable_into_article_animation',
        'argon_disable_pjax_animation',
        'argon_pjax_disabled',
        'argon_hide_categories',
        'argon_home_show_shuoshuo',
        'argon_fold_long_shuoshuo',
        'argon_fold_long_comments',
        'argon_hide_shortcode_in_preview',
        'argon_trim_words_count',
        'argon_enable_mobile_scale',
        'argon_disable_googlefont',
        'argon_enable_search_filters',
        'argon_search_filters_type',
    );
}

/**
 * Return the full nested default configuration used for the visual editor
 * and Argon-compatible JSON export (never includes `comments` or `source`).
 *
 * @return array<string, mixed>
 */
function argon_astro_schema_defaults() {
    return array(
        'version' => 1,
        'site' => array(
            'title' => '',
            'description' => '',
            'url' => '',
            'language' => '',
            'keywords' => '',
            'dateFormat' => 'YMD',
        ),
        'appearance' => array(
            'color' => '#5e72e4',
            'allowColorPicker' => true,
            'cardRadius' => 4,
            'cardShadow' => 'small',
            'font' => 'sans',
            'layout' => 'double',
            'immersionColor' => false,
            'amoledDark' => false,
            'darkmode' => 'system',
            'articleHeaderStyle' => 'article-header-style-default',
            'mobileScale' => false,
            'disableGoogleFont' => true,
            'customHeadHtml' => '',
            'customFootHtml' => '',
        ),
        'banner' => array(
            'size' => 'full',
            'title' => '',
            'subtitle' => '',
            'typing' => false,
            'typingInterval' => 100,
            'backgroundUrl' => '',
            'hideShapes' => false,
            'colorType' => 'shape-primary',
            'toolbarMask' => false,
        ),
        'background' => array(
            'url' => '',
            'darkUrl' => '',
            'opacity' => 1,
            'bannerStyle' => 'normal',
        ),
        'toolbar' => array(
            'title' => '',
            'icon' => '',
            'iconLink' => '',
            'blur' => false,
            'headroom' => 'false',
        ),
        'sidebar' => array(
            'announcement' => '',
            'bannerTitle' => '',
            'bannerSubtitle' => '',
            'authorName' => '',
            'authorDescription' => '',
            'authorImage' => '',
        ),
        'article' => array(
            'listLayout' => '1',
            'waterflow' => '1',
            'meta' => array('time', 'categories'),
            'hideCategories' => false,
            'showReadingTime' => true,
            'readingSpeed' => 300,
            'readingSpeedEn' => 160,
            'readingSpeedCode' => 80,
            'trimWords' => 175,
            'hideShortcodeInPreview' => true,
            'intoArticleAnimation' => true,
            'thumbnailInBanner' => false,
            'firstImageAsThumbnail' => true,
            'showShare' => true,
            'related' => 'tag',
            'relatedLimit' => 10,
            'relatedOrderBy' => 'date',
            'relatedOrder' => 'desc',
            'outdatedDays' => 0,
            'outdatedTip' => '本文最后更新于 %date_delta% 天前，其中的信息可能已经有所发展或是发生改变。',
            'outdatedTipType' => 'warning',
            'outdatedTimeType' => 'absolute',
            'archivesUrl' => '/archives/',
            'donateQr' => '',
            'afterPostHtml' => '',
            'showCatalogNumbers' => false,
            'timelineShowMonth' => true,
            'enableSearchFilters' => true,
            'searchFilters' => array('post', 'page', 'shuoshuo'),
            'referenceTitle' => '参考',
        ),
        'footer' => array(
            'html' => '',
            'hideAuthor' => false,
        ),
        'motion' => array(
            'lazyload' => true,
            'lazyloadThreshold' => 800,
            'lazyloadEffect' => 'fadeIn',
            'lazyloadLoadingStyle' => '1',
            'zoomify' => false,
            'zoomifyDuration' => 200,
            'zoomifyEasing' => 'cubic-bezier(0.4,0,0,1)',
            'readingProgress' => true,
            'zoomifyScale' => 0.9,
            'fancybox' => true,
            'smoothScroll' => '1',
            'pangu' => '',
            'foldLongComments' => false,
            'foldLongShuoshuo' => false,
            'pjax' => true,
            'pjaxAnimation' => true,
            'codeHighlight' => false,
            'codeTheme' => 'vs2015',
            'codeHideLineNumber' => false,
            'codeTransparentLineNumber' => false,
            'codeBreakLine' => false,
            'disableCodeblockStyle' => false,
            'math' => 'none',
            'katexCdn' => '',
            'mathjaxCdn' => '',
            'mathjaxV2Cdn' => '',
        ),
        'fab' => array(
            'darkmode' => false,
            'comment' => false,
            'settings' => true,
        ),
        'home' => array(
            'showShuoshuo' => true,
            'pageSize' => 10,
        ),
        'friendLinks' => array(),
        'agent' => array(
            'enabled' => true,
            'robotsTxt' => true,
            'llmsTxt' => true,
            'markdownMirrors' => true,
            'jsonIndex' => true,
            'jsonLd' => true,
            'webMcp' => true,
            'allowTrainingCrawlers' => true,
            'customRobotsTxt' => '',
            'customLlmsTxt' => '',
        ),
    );
}

/**
 * PHP 7.4-compatible check for sequential list arrays.
 *
 * @param array<mixed> $arr
 * @return bool
 */
function argon_astro_is_list(array $arr) {
    $expected = 0;
    foreach ($arr as $key => $_) {
        if ($key !== $expected) {
            return false;
        }
        $expected++;
    }
    return true;
}

/**
 * Recursively merge associative config maps while replacing list arrays whole.
 *
 * @param array<string, mixed> $base
 * @param array<string, mixed> $override
 * @return array<string, mixed>
 */
function argon_astro_merge_config(array $base, array $override) {
    $result = $base;
    foreach ($override as $key => $value) {
        if (
            isset($result[$key])
            && is_array($result[$key])
            && is_array($value)
            && !argon_astro_is_list($result[$key])
            && !argon_astro_is_list($value)
        ) {
            $result[$key] = argon_astro_merge_config($result[$key], $value);
        } else {
            $result[$key] = $value;
        }
    }
    return $result;
}

/**
 * Detect whether an input array uses flat upstream `argon_*` keys.
 *
 * @param array<mixed> $input
 * @return bool
 */
function argon_astro_is_flat_argon_array(array $input) {
    foreach ($input as $key => $_) {
        if (is_string($key) && strpos($key, 'argon_') === 0) {
            return true;
        }
    }
    return false;
}

/**
 * Coerce a boolean-like value (`true`, `"true"`, `"1"`, `"false"`, `"0"`).
 *
 * @param mixed $value
 * @param bool  $default
 * @return bool
 */
function argon_astro_to_bool($value, $default = false) {
    if (is_bool($value)) {
        return $value;
    }
    if (is_int($value) || is_float($value)) {
        return ((int) $value) !== 0;
    }
    if (is_string($value)) {
        $lower = strtolower(trim($value));
        if (in_array($lower, array('true', '1', 'yes', 'on'), true)) {
            return true;
        }
        if (in_array($lower, array('false', '0', 'no', 'off', ''), true)) {
            return false;
        }
    }
    return (bool) $default;
}

/**
 * Coerce an integer within inclusive bounds.
 *
 * @param mixed $value
 * @param int   $min
 * @param int   $max
 * @param int   $default
 * @return int
 */
function argon_astro_to_int($value, $min, $max, $default) {
    if ($value === '' || $value === null || !is_numeric($value)) {
        return $default;
    }
    $num = (int) round((float) $value);
    if ($num < $min) {
        return $min;
    }
    if ($num > $max) {
        return $max;
    }
    return $num;
}

/**
 * Coerce a float within inclusive bounds, returning an int when whole.
 *
 * @param mixed $value
 * @param float $min
 * @param float $max
 * @param float $default
 * @return float|int
 */
function argon_astro_to_number($value, $min, $max, $default) {
    if ($value === '' || $value === null || !is_numeric($value)) {
        $num = (float) $default;
    } else {
        $num = (float) $value;
    }
    if ($num < $min) {
        $num = (float) $min;
    }
    if ($num > $max) {
        $num = (float) $max;
    }
    if (floor($num) === $num) {
        return (int) $num;
    }
    return round($num, 4);
}

/**
 * Sanitize a 3- or 6-digit hex color string.
 *
 * @param mixed  $value
 * @param string $default
 * @return string
 */
function argon_astro_sanitize_hex_color($value, $default = '#5e72e4') {
    $raw = trim((string) $value);
    if (preg_match('/^#[0-9a-fA-F]{6}$/', $raw)) {
        return strtolower($raw);
    }
    if (preg_match('/^#[0-9a-fA-F]{3}$/', $raw)) {
        $r = $raw[1];
        $g = $raw[2];
        $b = $raw[3];
        return strtolower('#' . $r . $r . $g . $g . $b . $b);
    }
    return $default;
}

/**
 * Sanitize a plain single-line text value safely with or without WordPress.
 *
 * @param mixed $value
 * @return string
 */
function argon_astro_clean_text($value) {
    $str = (string) $value;
    if (function_exists('sanitize_text_field')) {
        return sanitize_text_field($str);
    }
    return trim(strip_tags($str));
}

/**
 * Sanitize a URL, relative path, or Argon sentinel token (`--bing--`, `--hitokoto--`).
 *
 * @param mixed $value
 * @return string
 */
function argon_astro_sanitize_url_or_token($value) {
    $raw = trim(argon_astro_clean_text($value));
    if ($raw === '') {
        return '';
    }
    if (preg_match('/^--[a-z0-9_-]+--$/i', $raw)) {
        return $raw;
    }
    if (preg_match('/^\s*(javascript|vbscript|data)\s*:/i', $raw)) {
        return '';
    }
    return $raw;
}

/**
 * Normalize a math CDN URL so protocol-relative upstream URLs become HTTPS.
 *
 * @param mixed $value
 * @return string
 */
function argon_astro_normalize_math_cdn($value) {
    $raw = trim(argon_astro_clean_text($value));
    if ($raw === '') {
        return '';
    }
    if (strpos($raw, '//') === 0) {
        $raw = 'https:' . $raw;
    }
    if (strpos($raw, 'https://') !== 0) {
        return '';
    }
    return $raw;
}

/**
 * Pick an allowed enum value or return a fallback.
 *
 * @param mixed              $value
 * @param array<int, string> $allowed
 * @param string             $default
 * @return string
 */
function argon_astro_enum($value, array $allowed, $default) {
    $str = trim((string) $value);
    return in_array($str, $allowed, true) ? $str : $default;
}

/**
 * Sanitize article meta item ordering (`time`, `edittime`, `views`, `comments`, `categories`, `author`).
 *
 * @param mixed $raw
 * @return array<int, string>
 */
function argon_astro_parse_article_meta($raw) {
    $allowed = array('time', 'edittime', 'views', 'comments', 'categories', 'author');
    if (is_string($raw)) {
        $parts = explode('|', $raw);
    } elseif (is_array($raw)) {
        $parts = $raw;
    } else {
        return array('time', 'categories');
    }

    $clean = array();
    foreach ($parts as $item) {
        $token = strtolower(trim((string) $item));
        if (in_array($token, $allowed, true) && !in_array($token, $clean, true)) {
            $clean[] = $token;
        }
    }
    return $clean;
}

/**
 * Sanitize search filter content types (`post`, `page`, `shuoshuo`).
 * Strips upstream `*` default-checked prefixes cleanly.
 *
 * @param mixed $raw
 * @return array<int, string>
 */
function argon_astro_parse_search_filters($raw) {
    $allowed = array('post', 'page', 'shuoshuo');
    if (is_string($raw)) {
        $parts = explode(',', $raw);
    } elseif (is_array($raw)) {
        $parts = $raw;
    } else {
        return $allowed;
    }

    $clean = array();
    foreach ($parts as $item) {
        $token = strtolower(ltrim(trim((string) $item), '*'));
        if (in_array($token, $allowed, true) && !in_array($token, $clean, true)) {
            $clean[] = $token;
        }
    }
    return $clean;
}

/**
 * Parse friend links from either structured arrays or pipe-delimited lines
 * (`Name | URL | Description | Avatar`).
 *
 * @param mixed $raw
 * @return array<int, array{name: string, url: string, description: string, avatar: string}>
 */
function argon_astro_parse_friend_links($raw) {
    $links = array();

    if (is_string($raw)) {
        $lines = preg_split('/\r\n|\r|\n/', $raw);
        if (!is_array($lines)) {
            return $links;
        }
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            $cols = array_map('trim', explode('|', $line));
            $name = isset($cols[0]) ? argon_astro_clean_text($cols[0]) : '';
            $url = isset($cols[1]) ? argon_astro_sanitize_url_or_token($cols[1]) : '';
            $desc = isset($cols[2]) ? argon_astro_clean_text($cols[2]) : '';
            $avatar = isset($cols[3]) ? argon_astro_sanitize_url_or_token($cols[3]) : '';
            if ($name === '' && $url === '') {
                continue;
            }
            $links[] = array(
                'name' => $name,
                'url' => $url,
                'description' => $desc,
                'avatar' => $avatar,
            );
        }
        return $links;
    }

    if (!is_array($raw)) {
        return $links;
    }

    foreach ($raw as $row) {
        if (!is_array($row)) {
            continue;
        }
        $name = isset($row['name']) ? argon_astro_clean_text($row['name']) : '';
        $url = isset($row['url']) ? argon_astro_sanitize_url_or_token($row['url']) : '';
        $desc = isset($row['description']) ? argon_astro_clean_text($row['description']) : '';
        $avatar = isset($row['avatar']) ? argon_astro_sanitize_url_or_token($row['avatar']) : '';
        if ($name === '' && $url === '') {
            continue;
        }
        $links[] = array(
            'name' => $name,
            'url' => $url,
            'description' => $desc,
            'avatar' => $avatar,
        );
    }

    return $links;
}

/**
 * Format friend links into pipe-delimited text lines for readable display or import.
 *
 * @param array<int, array<string, mixed>> $links
 * @return string
 */
function argon_astro_format_friend_links_text(array $links) {
    $lines = array();
    foreach ($links as $row) {
        if (!is_array($row)) {
            continue;
        }
        $name = isset($row['name']) ? trim((string) $row['name']) : '';
        $url = isset($row['url']) ? trim((string) $row['url']) : '';
        $desc = isset($row['description']) ? trim((string) $row['description']) : '';
        $avatar = isset($row['avatar']) ? trim((string) $row['avatar']) : '';
        if ($name === '' && $url === '') {
            continue;
        }
        $lines[] = $name . ' | ' . $url . ' | ' . $desc . ' | ' . $avatar;
    }
    return implode("\n", $lines);
}

/**
 * Convert flat upstream `argon_*` WordPress options into a nested `argon_astro_config` tree.
 *
 * Always strips `comments` and `source` and skips WordPress-only options such as
 * `argon_update_source`, `argon_enable_login_css`, `argon_wp_path`, and `argon_assets_path`.
 *
 * @param array<string, mixed> $flat
 * @return array<string, mixed>
 */
function argon_astro_flat_to_nested(array $flat) {
    $nested = array('version' => 1);

    $has = function ($key) use ($flat) {
        return array_key_exists($key, $flat) && $flat[$key] !== null;
    };

    // Site & SEO.
    if ($has('argon_dateformat')) {
        $nested['site']['dateFormat'] = argon_astro_enum(strtoupper((string) $flat['argon_dateformat']), array('YMD', 'DMY', 'MDY'), 'YMD');
    }
    if ($has('argon_seo_description')) {
        $nested['site']['description'] = trim((string) $flat['argon_seo_description']);
    }
    if ($has('argon_seo_keywords')) {
        $nested['site']['keywords'] = argon_astro_clean_text($flat['argon_seo_keywords']);
    }

    // Appearance & Theme Color.
    if ($has('argon_theme_color')) {
        $nested['appearance']['color'] = argon_astro_sanitize_hex_color($flat['argon_theme_color'], '#5e72e4');
    }
    if ($has('argon_show_customize_theme_color_picker')) {
        $nested['appearance']['allowColorPicker'] = argon_astro_to_bool($flat['argon_show_customize_theme_color_picker'], true);
    }
    if ($has('argon_enable_immersion_color')) {
        $nested['appearance']['immersionColor'] = argon_astro_to_bool($flat['argon_enable_immersion_color'], false);
    }
    if ($has('argon_darkmode_autoswitch')) {
        $dm = strtolower(trim((string) $flat['argon_darkmode_autoswitch']));
        if ($flat['argon_darkmode_autoswitch'] === false || $dm === 'false' || $dm === 'off' || $dm === '') {
            $nested['appearance']['darkmode'] = 'off';
        } elseif ($dm === 'alwayson' || $dm === 'always' || $dm === 'true' || $flat['argon_darkmode_autoswitch'] === true) {
            $nested['appearance']['darkmode'] = 'always';
        } elseif ($dm === 'time') {
            $nested['appearance']['darkmode'] = 'time';
        } else {
            $nested['appearance']['darkmode'] = 'system';
        }
    }
    if ($has('argon_enable_amoled_dark')) {
        $nested['appearance']['amoledDark'] = argon_astro_to_bool($flat['argon_enable_amoled_dark'], false);
    }
    if ($has('argon_card_radius')) {
        $nested['appearance']['cardRadius'] = argon_astro_to_number($flat['argon_card_radius'], 0, 30, 4);
    }
    if ($has('argon_card_shadow')) {
        $shadow = strtolower(trim((string) $flat['argon_card_shadow']));
        $nested['appearance']['cardShadow'] = ($shadow === 'big') ? 'big' : 'small';
    }
    if ($has('argon_page_layout')) {
        $nested['appearance']['layout'] = argon_astro_enum($flat['argon_page_layout'], array('single', 'double', 'double-reverse', 'triple'), 'double');
    }
    if ($has('argon_font')) {
        $font = strtolower(trim((string) $flat['argon_font']));
        $nested['appearance']['font'] = ($font === 'serif') ? 'serif' : 'sans';
    }
    if ($has('argon_article_header_style')) {
        $nested['appearance']['articleHeaderStyle'] = argon_astro_enum(
            $flat['argon_article_header_style'],
            array('article-header-style-default', 'article-header-style-1', 'article-header-style-2'),
            'article-header-style-default'
        );
    }
    if ($has('argon_enable_mobile_scale')) {
        $nested['appearance']['mobileScale'] = argon_astro_to_bool($flat['argon_enable_mobile_scale'], false);
    }
    if ($has('argon_disable_googlefont')) {
        $nested['appearance']['disableGoogleFont'] = argon_astro_to_bool($flat['argon_disable_googlefont'], true);
    }
    if ($has('argon_custom_html_head')) {
        $nested['appearance']['customHeadHtml'] = (string) $flat['argon_custom_html_head'];
    }
    if ($has('argon_custom_html_foot')) {
        $nested['appearance']['customFootHtml'] = (string) $flat['argon_custom_html_foot'];
    }

    // Toolbar.
    if ($has('argon_enable_headroom')) {
        $hr = $flat['argon_enable_headroom'];
        if (is_bool($hr)) {
            $hr = $hr ? 'true' : 'false';
        }
        $nested['toolbar']['headroom'] = argon_astro_enum(strtolower(trim((string) $hr)), array('false', 'true', 'absolute'), 'false');
    }
    if ($has('argon_toolbar_title')) {
        $nested['toolbar']['title'] = argon_astro_clean_text($flat['argon_toolbar_title']);
    }
    if ($has('argon_toolbar_icon')) {
        $nested['toolbar']['icon'] = argon_astro_sanitize_url_or_token($flat['argon_toolbar_icon']);
    }
    if ($has('argon_toolbar_icon_link')) {
        $nested['toolbar']['iconLink'] = argon_astro_sanitize_url_or_token($flat['argon_toolbar_icon_link']);
    }
    if ($has('argon_toolbar_blur')) {
        $nested['toolbar']['blur'] = argon_astro_to_bool($flat['argon_toolbar_blur'], false);
    }

    // Banner.
    if ($has('argon_banner_title')) {
        $nested['banner']['title'] = argon_astro_clean_text($flat['argon_banner_title']);
    }
    if ($has('argon_banner_subtitle')) {
        $nested['banner']['subtitle'] = argon_astro_clean_text($flat['argon_banner_subtitle']);
    }
    if ($has('argon_banner_size')) {
        $nested['banner']['size'] = argon_astro_enum($flat['argon_banner_size'], array('full', 'mini', 'hide', 'fullscreen'), 'full');
    }
    if ($has('argon_show_toolbar_mask')) {
        $nested['banner']['toolbarMask'] = argon_astro_to_bool($flat['argon_show_toolbar_mask'], false);
    }
    if ($has('argon_banner_background_url')) {
        $nested['banner']['backgroundUrl'] = argon_astro_sanitize_url_or_token($flat['argon_banner_background_url']);
    }
    if ($has('argon_banner_background_color_type')) {
        $nested['banner']['colorType'] = argon_astro_enum(
            $flat['argon_banner_background_color_type'],
            array('shape-primary', 'shape-default', 'shape-dark', 'bg-gradient-success', 'bg-gradient-info', 'bg-gradient-warning', 'bg-gradient-danger'),
            'shape-primary'
        );
    }
    if ($has('argon_banner_background_hide_shapes')) {
        $nested['banner']['hideShapes'] = argon_astro_to_bool($flat['argon_banner_background_hide_shapes'], false);
    }
    if ($has('argon_enable_banner_title_typing_effect')) {
        $nested['banner']['typing'] = argon_astro_to_bool($flat['argon_enable_banner_title_typing_effect'], false);
    }
    if ($has('argon_banner_typing_effect_interval')) {
        $nested['banner']['typingInterval'] = argon_astro_to_int($flat['argon_banner_typing_effect_interval'], 1, 10000, 100);
    }

    // Page Background.
    if ($has('argon_page_background_url')) {
        $nested['background']['url'] = argon_astro_sanitize_url_or_token($flat['argon_page_background_url']);
    }
    if ($has('argon_page_background_dark_url')) {
        $nested['background']['darkUrl'] = argon_astro_sanitize_url_or_token($flat['argon_page_background_dark_url']);
    }
    if ($has('argon_page_background_opacity')) {
        $nested['background']['opacity'] = argon_astro_to_number($flat['argon_page_background_opacity'], 0, 1, 1);
    }
    if ($has('argon_page_background_banner_style')) {
        $style = strtolower(trim((string) $flat['argon_page_background_banner_style']));
        $nested['background']['bannerStyle'] = ($style === 'transparent') ? 'transparent' : 'normal';
    }

    // Sidebar (preserve upstream `auther` spelling and also accept `author` alias).
    if ($has('argon_sidebar_banner_title')) {
        $nested['sidebar']['bannerTitle'] = argon_astro_clean_text($flat['argon_sidebar_banner_title']);
    }
    if ($has('argon_sidebar_banner_subtitle')) {
        $nested['sidebar']['bannerSubtitle'] = argon_astro_clean_text($flat['argon_sidebar_banner_subtitle']);
    }
    if ($has('argon_sidebar_auther_name')) {
        $nested['sidebar']['authorName'] = argon_astro_clean_text($flat['argon_sidebar_auther_name']);
    } elseif ($has('argon_sidebar_author_name')) {
        $nested['sidebar']['authorName'] = argon_astro_clean_text($flat['argon_sidebar_author_name']);
    }
    if ($has('argon_sidebar_auther_image')) {
        $nested['sidebar']['authorImage'] = argon_astro_sanitize_url_or_token($flat['argon_sidebar_auther_image']);
    } elseif ($has('argon_sidebar_author_image')) {
        $nested['sidebar']['authorImage'] = argon_astro_sanitize_url_or_token($flat['argon_sidebar_author_image']);
    }
    if ($has('argon_sidebar_author_description')) {
        $nested['sidebar']['authorDescription'] = argon_astro_clean_text($flat['argon_sidebar_author_description']);
    }
    if ($has('argon_sidebar_announcement')) {
        $nested['sidebar']['announcement'] = (string) $flat['argon_sidebar_announcement'];
    }

    // Floating Action Buttons.
    if ($has('argon_fab_show_settings_button')) {
        $nested['fab']['settings'] = argon_astro_to_bool($flat['argon_fab_show_settings_button'], true);
    }
    if ($has('argon_fab_show_darkmode_button')) {
        $nested['fab']['darkmode'] = argon_astro_to_bool($flat['argon_fab_show_darkmode_button'], false);
    }
    if ($has('argon_fab_show_gotocomment_button')) {
        $nested['fab']['comment'] = argon_astro_to_bool($flat['argon_fab_show_gotocomment_button'], false);
    }

    // Article, Archives & Search.
    if ($has('argon_article_list_waterflow')) {
        $wf = trim((string) $flat['argon_article_list_waterflow']);
        if ($wf === '2,3') {
            $wf = '2and3';
        }
        $nested['article']['waterflow'] = argon_astro_enum($wf, array('1', '2', '3', '2and3'), '1');
    }
    if ($has('argon_article_list_layout')) {
        $nested['article']['listLayout'] = argon_astro_enum($flat['argon_article_list_layout'], array('1', '2', '3'), '1');
    }
    if ($has('argon_article_meta')) {
        $nested['article']['meta'] = argon_astro_parse_article_meta($flat['argon_article_meta']);
    }
    if ($has('argon_hide_categories')) {
        $nested['article']['hideCategories'] = argon_astro_to_bool($flat['argon_hide_categories'], false);
    }
    if ($has('argon_show_readingtime')) {
        $nested['article']['showReadingTime'] = argon_astro_to_bool($flat['argon_show_readingtime'], true);
    }
    if ($has('argon_reading_speed')) {
        $nested['article']['readingSpeed'] = argon_astro_to_int($flat['argon_reading_speed'], 1, 5000, 300);
    }
    if ($has('argon_reading_speed_en')) {
        $nested['article']['readingSpeedEn'] = argon_astro_to_int($flat['argon_reading_speed_en'], 1, 5000, 160);
    }
    if ($has('argon_reading_speed_code')) {
        $nested['article']['readingSpeedCode'] = argon_astro_to_int($flat['argon_reading_speed_code'], 1, 5000, 80);
    }
    if ($has('argon_show_thumbnail_in_banner_in_content_page')) {
        $nested['article']['thumbnailInBanner'] = argon_astro_to_bool($flat['argon_show_thumbnail_in_banner_in_content_page'], false);
    }
    if ($has('argon_first_image_as_thumbnail_by_default')) {
        $nested['article']['firstImageAsThumbnail'] = argon_astro_to_bool($flat['argon_first_image_as_thumbnail_by_default'], true);
    }
    if ($has('argon_reference_list_title')) {
        $nested['article']['referenceTitle'] = argon_astro_clean_text($flat['argon_reference_list_title']);
    }
    if ($has('argon_show_sharebtn')) {
        $share = strtolower(trim((string) $flat['argon_show_sharebtn']));
        if ($flat['argon_show_sharebtn'] === false || $share === 'false' || $share === '0' || $share === '') {
            $nested['article']['showShare'] = false;
        } else {
            // Upstream supports "true", "domestic", and "abroad".
            $nested['article']['showShare'] = true;
        }
    }
    if ($has('argon_show_headindex_number')) {
        $nested['article']['showCatalogNumbers'] = argon_astro_to_bool($flat['argon_show_headindex_number'], false);
    }
    if ($has('argon_donate_qrcode_url')) {
        $nested['article']['donateQr'] = argon_astro_sanitize_url_or_token($flat['argon_donate_qrcode_url']);
    }
    if ($has('argon_additional_content_after_post')) {
        $nested['article']['afterPostHtml'] = (string) $flat['argon_additional_content_after_post'];
    }
    if ($has('argon_related_post')) {
        $rel = trim((string) $flat['argon_related_post']);
        if ($rel === 'tag,category') {
            $rel = 'category,tag';
        }
        $nested['article']['related'] = argon_astro_enum($rel, array('disabled', 'category', 'tag', 'category,tag'), 'tag');
    }
    if ($has('argon_related_post_sort_orderby')) {
        $orderby = strtolower(trim((string) $flat['argon_related_post_sort_orderby']));
        $nested['article']['relatedOrderBy'] = ($orderby === 'title') ? 'title' : 'date';
    }
    if ($has('argon_related_post_sort_order')) {
        $order = strtolower(trim((string) $flat['argon_related_post_sort_order']));
        $nested['article']['relatedOrder'] = ($order === 'asc') ? 'asc' : 'desc';
    }
    if ($has('argon_related_post_limit')) {
        $nested['article']['relatedLimit'] = argon_astro_to_int($flat['argon_related_post_limit'], 1, 100, 10);
    }
    if ($has('argon_outdated_info_time_type')) {
        $tt = strtolower(trim((string) $flat['argon_outdated_info_time_type']));
        $nested['article']['outdatedTimeType'] = ($tt === 'modifiedtime' || $tt === 'relative') ? 'relative' : 'absolute';
    }
    if ($has('argon_outdated_info_days')) {
        $days = is_numeric($flat['argon_outdated_info_days']) ? (int) $flat['argon_outdated_info_days'] : 0;
        // Upstream uses -1 to disable; normalized schema disables when <= 0.
        $nested['article']['outdatedDays'] = $days > 0 ? min(99999, $days) : 0;
    }
    if ($has('argon_outdated_info_tip_type')) {
        $tip_type = strtolower(trim((string) $flat['argon_outdated_info_tip_type']));
        $nested['article']['outdatedTipType'] = ($tip_type === 'toast' || $tip_type === 'info') ? 'info' : 'warning';
    }
    if ($has('argon_outdated_info_tip_content')) {
        $nested['article']['outdatedTip'] = trim((string) $flat['argon_outdated_info_tip_content']);
    }
    if ($has('argon_archives_timeline_show_month')) {
        $nested['article']['timelineShowMonth'] = argon_astro_to_bool($flat['argon_archives_timeline_show_month'], true);
    }
    if ($has('argon_archives_timeline_url')) {
        $nested['article']['archivesUrl'] = argon_astro_sanitize_url_or_token($flat['argon_archives_timeline_url']);
    }
    if ($has('argon_enable_into_article_animation')) {
        $nested['article']['intoArticleAnimation'] = argon_astro_to_bool($flat['argon_enable_into_article_animation'], true);
    }
    if ($has('argon_hide_shortcode_in_preview')) {
        $nested['article']['hideShortcodeInPreview'] = argon_astro_to_bool($flat['argon_hide_shortcode_in_preview'], true);
    }
    if ($has('argon_trim_words_count')) {
        $nested['article']['trimWords'] = argon_astro_to_int($flat['argon_trim_words_count'], 0, 1000, 175);
    }
    if ($has('argon_enable_search_filters')) {
        $nested['article']['enableSearchFilters'] = argon_astro_to_bool($flat['argon_enable_search_filters'], true);
    }
    if ($has('argon_search_filters_type')) {
        $nested['article']['searchFilters'] = argon_astro_parse_search_filters($flat['argon_search_filters_type']);
    }

    // Footer.
    if ($has('argon_footer_html')) {
        $nested['footer']['html'] = (string) $flat['argon_footer_html'];
    }
    if ($has('argon_hide_footer_author')) {
        $nested['footer']['hideAuthor'] = argon_astro_to_bool($flat['argon_hide_footer_author'], false);
    }

    // Motion, Code Highlight & Math.
    if ($has('argon_enable_code_highlight')) {
        $nested['motion']['codeHighlight'] = argon_astro_to_bool($flat['argon_enable_code_highlight'], false);
    }
    if ($has('argon_code_theme')) {
        $theme = argon_astro_clean_text($flat['argon_code_theme']);
        if ($theme !== '') {
            $nested['motion']['codeTheme'] = $theme;
        }
    }
    if ($has('argon_code_highlight_hide_linenumber')) {
        $nested['motion']['codeHideLineNumber'] = argon_astro_to_bool($flat['argon_code_highlight_hide_linenumber'], false);
    }
    if ($has('argon_code_highlight_break_line')) {
        $nested['motion']['codeBreakLine'] = argon_astro_to_bool($flat['argon_code_highlight_break_line'], false);
    }
    if ($has('argon_code_highlight_transparent_linenumber')) {
        $nested['motion']['codeTransparentLineNumber'] = argon_astro_to_bool($flat['argon_code_highlight_transparent_linenumber'], false);
    }
    if ($has('argon_disable_codeblock_style')) {
        $nested['motion']['disableCodeblockStyle'] = argon_astro_to_bool($flat['argon_disable_codeblock_style'], false);
    }
    if ($has('argon_math_render')) {
        $nested['motion']['math'] = argon_astro_enum($flat['argon_math_render'], array('none', 'katex', 'mathjax2', 'mathjax3'), 'none');
    }
    if ($has('argon_mathjax_cdn_url')) {
        $nested['motion']['mathjaxCdn'] = argon_astro_normalize_math_cdn($flat['argon_mathjax_cdn_url']);
    }
    if ($has('argon_mathjax_v2_cdn_url')) {
        $nested['motion']['mathjaxV2Cdn'] = argon_astro_normalize_math_cdn($flat['argon_mathjax_v2_cdn_url']);
    }
    if ($has('argon_katex_cdn_url')) {
        $nested['motion']['katexCdn'] = argon_astro_normalize_math_cdn($flat['argon_katex_cdn_url']);
    }
    if ($has('argon_enable_lazyload')) {
        $nested['motion']['lazyload'] = argon_astro_to_bool($flat['argon_enable_lazyload'], true);
    }
    if ($has('argon_lazyload_threshold')) {
        $nested['motion']['lazyloadThreshold'] = argon_astro_to_int($flat['argon_lazyload_threshold'], 0, 2500, 800);
    }
    if ($has('argon_lazyload_effect')) {
        $nested['motion']['lazyloadEffect'] = argon_astro_enum($flat['argon_lazyload_effect'], array('fadeIn', 'slideDown', 'none'), 'fadeIn');
    }
    if ($has('argon_lazyload_loading_style')) {
        $nested['motion']['lazyloadLoadingStyle'] = argon_astro_enum(
            $flat['argon_lazyload_loading_style'],
            array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', 'none'),
            '1'
        );
    }
    if ($has('argon_enable_fancybox')) {
        $nested['motion']['fancybox'] = argon_astro_to_bool($flat['argon_enable_fancybox'], true);
    }
    if ($has('argon_enable_zoomify')) {
        $nested['motion']['zoomify'] = argon_astro_to_bool($flat['argon_enable_zoomify'], false);
    }
    if ($has('argon_zoomify_duration')) {
        $nested['motion']['zoomifyDuration'] = argon_astro_to_int($flat['argon_zoomify_duration'], 0, 10000, 200);
    }
    if ($has('argon_zoomify_easing')) {
        $easing = argon_astro_clean_text($flat['argon_zoomify_easing']);
        if ($easing !== '') {
            $nested['motion']['zoomifyEasing'] = $easing;
        }
    }
    if ($has('argon_zoomify_scale')) {
        $nested['motion']['zoomifyScale'] = argon_astro_to_number($flat['argon_zoomify_scale'], 0.01, 1, 0.9);
    }
    if ($has('argon_enable_pangu')) {
        $pangu = trim((string) $flat['argon_enable_pangu']);
        if ($flat['argon_enable_pangu'] === false || strtolower($pangu) === 'false' || $pangu === '') {
            $nested['motion']['pangu'] = '';
        } else {
            $nested['motion']['pangu'] = argon_astro_enum(
                $pangu,
                array('article', 'shuoshuo', 'comment', 'article|comment', 'article|shuoshuo', 'shuoshuo|comment', 'article|shuoshuo|comment', 'all'),
                ''
            );
        }
    }
    if ($has('argon_enable_smoothscroll_type')) {
        $nested['motion']['smoothScroll'] = argon_astro_enum(
            $flat['argon_enable_smoothscroll_type'],
            array('1', '1_pulse', '2', '3', 'disabled'),
            '1'
        );
    }
    if ($has('argon_disable_pjax_animation')) {
        // Inverted polarity: upstream "disable" -> Astro "enable".
        $nested['motion']['pjaxAnimation'] = !argon_astro_to_bool($flat['argon_disable_pjax_animation'], false);
    }
    if ($has('argon_pjax_disabled')) {
        // Inverted polarity: upstream "disabled" -> Astro "enabled".
        $nested['motion']['pjax'] = !argon_astro_to_bool($flat['argon_pjax_disabled'], false);
    }
    if ($has('argon_fold_long_shuoshuo')) {
        $nested['motion']['foldLongShuoshuo'] = argon_astro_to_bool($flat['argon_fold_long_shuoshuo'], false);
    }
    if ($has('argon_fold_long_comments')) {
        $nested['motion']['foldLongComments'] = argon_astro_to_bool($flat['argon_fold_long_comments'], false);
    }

    // Home.
    if ($has('argon_home_show_shuoshuo')) {
        $nested['home']['showShuoshuo'] = argon_astro_to_bool($flat['argon_home_show_shuoshuo'], true);
    }

    // Never allow headless config to override comment provider or source mode.
    unset($nested['comments'], $nested['source']);

    return $nested;
}

/**
 * Convert nested `argon_astro_config` back into flat `argon_*` keys for
 * original Argon-compatible JSON export.
 *
 * @param array<string, mixed> $config
 * @return array<string, string>
 */
function argon_astro_nested_to_flat(array $config) {
    $flat = array();
    $bool_str = function ($val) {
        return argon_astro_to_bool($val, false) ? 'true' : 'false';
    };

    $site = isset($config['site']) && is_array($config['site']) ? $config['site'] : array();
    $app = isset($config['appearance']) && is_array($config['appearance']) ? $config['appearance'] : array();
    $banner = isset($config['banner']) && is_array($config['banner']) ? $config['banner'] : array();
    $bg = isset($config['background']) && is_array($config['background']) ? $config['background'] : array();
    $toolbar = isset($config['toolbar']) && is_array($config['toolbar']) ? $config['toolbar'] : array();
    $sidebar = isset($config['sidebar']) && is_array($config['sidebar']) ? $config['sidebar'] : array();
    $article = isset($config['article']) && is_array($config['article']) ? $config['article'] : array();
    $footer = isset($config['footer']) && is_array($config['footer']) ? $config['footer'] : array();
    $motion = isset($config['motion']) && is_array($config['motion']) ? $config['motion'] : array();
    $fab = isset($config['fab']) && is_array($config['fab']) ? $config['fab'] : array();
    $home = isset($config['home']) && is_array($config['home']) ? $config['home'] : array();

    // Appearance.
    if (array_key_exists('color', $app)) {
        $flat['argon_theme_color'] = argon_astro_sanitize_hex_color($app['color'], '#5e72e4');
    }
    if (array_key_exists('allowColorPicker', $app)) {
        $flat['argon_show_customize_theme_color_picker'] = $bool_str($app['allowColorPicker']);
    }
    if (array_key_exists('immersionColor', $app)) {
        $flat['argon_enable_immersion_color'] = $bool_str($app['immersionColor']);
    }
    if (array_key_exists('darkmode', $app)) {
        $dm = strtolower(trim((string) $app['darkmode']));
        if ($dm === 'off' || $dm === 'false') {
            $flat['argon_darkmode_autoswitch'] = 'false';
        } elseif ($dm === 'always' || $dm === 'alwayson') {
            $flat['argon_darkmode_autoswitch'] = 'alwayson';
        } elseif ($dm === 'time') {
            $flat['argon_darkmode_autoswitch'] = 'time';
        } else {
            $flat['argon_darkmode_autoswitch'] = 'system';
        }
    }
    if (array_key_exists('amoledDark', $app)) {
        $flat['argon_enable_amoled_dark'] = $bool_str($app['amoledDark']);
    }
    if (array_key_exists('cardRadius', $app)) {
        $flat['argon_card_radius'] = (string) argon_astro_to_number($app['cardRadius'], 0, 30, 4);
    }
    if (array_key_exists('cardShadow', $app)) {
        $flat['argon_card_shadow'] = ($app['cardShadow'] === 'big') ? 'big' : 'default';
    }
    if (array_key_exists('layout', $app)) {
        $flat['argon_page_layout'] = argon_astro_enum($app['layout'], array('single', 'double', 'double-reverse', 'triple'), 'double');
    }
    if (array_key_exists('font', $app)) {
        $flat['argon_font'] = ($app['font'] === 'serif') ? 'serif' : 'sans-serif';
    }
    if (array_key_exists('articleHeaderStyle', $app)) {
        $flat['argon_article_header_style'] = (string) $app['articleHeaderStyle'];
    }
    if (array_key_exists('mobileScale', $app)) {
        $flat['argon_enable_mobile_scale'] = $bool_str($app['mobileScale']);
    }
    if (array_key_exists('disableGoogleFont', $app)) {
        $flat['argon_disable_googlefont'] = $bool_str($app['disableGoogleFont']);
    }
    if (array_key_exists('customHeadHtml', $app)) {
        $flat['argon_custom_html_head'] = (string) $app['customHeadHtml'];
    }
    if (array_key_exists('customFootHtml', $app)) {
        $flat['argon_custom_html_foot'] = (string) $app['customFootHtml'];
    }

    // Site & SEO.
    if (array_key_exists('dateFormat', $site)) {
        $flat['argon_dateformat'] = argon_astro_enum($site['dateFormat'], array('YMD', 'DMY', 'MDY'), 'YMD');
    }
    if (array_key_exists('description', $site)) {
        $flat['argon_seo_description'] = (string) $site['description'];
    }
    if (array_key_exists('keywords', $site)) {
        $flat['argon_seo_keywords'] = (string) $site['keywords'];
    }

    // Toolbar.
    if (array_key_exists('headroom', $toolbar)) {
        $flat['argon_enable_headroom'] = argon_astro_enum($toolbar['headroom'], array('false', 'true', 'absolute'), 'false');
    }
    if (array_key_exists('title', $toolbar)) {
        $flat['argon_toolbar_title'] = (string) $toolbar['title'];
    }
    if (array_key_exists('icon', $toolbar)) {
        $flat['argon_toolbar_icon'] = (string) $toolbar['icon'];
    }
    if (array_key_exists('iconLink', $toolbar)) {
        $flat['argon_toolbar_icon_link'] = (string) $toolbar['iconLink'];
    }
    if (array_key_exists('blur', $toolbar)) {
        $flat['argon_toolbar_blur'] = $bool_str($toolbar['blur']);
    }

    // Banner.
    if (array_key_exists('title', $banner)) {
        $flat['argon_banner_title'] = (string) $banner['title'];
    }
    if (array_key_exists('subtitle', $banner)) {
        $flat['argon_banner_subtitle'] = (string) $banner['subtitle'];
    }
    if (array_key_exists('size', $banner)) {
        $flat['argon_banner_size'] = argon_astro_enum($banner['size'], array('full', 'mini', 'hide', 'fullscreen'), 'full');
    }
    if (array_key_exists('toolbarMask', $banner)) {
        $flat['argon_show_toolbar_mask'] = $bool_str($banner['toolbarMask']);
    }
    if (array_key_exists('backgroundUrl', $banner)) {
        $flat['argon_banner_background_url'] = (string) $banner['backgroundUrl'];
    }
    if (array_key_exists('colorType', $banner)) {
        $flat['argon_banner_background_color_type'] = (string) $banner['colorType'];
    }
    if (array_key_exists('hideShapes', $banner)) {
        $flat['argon_banner_background_hide_shapes'] = $bool_str($banner['hideShapes']);
    }
    if (array_key_exists('typing', $banner)) {
        $flat['argon_enable_banner_title_typing_effect'] = $bool_str($banner['typing']);
    }
    if (array_key_exists('typingInterval', $banner)) {
        $flat['argon_banner_typing_effect_interval'] = (string) argon_astro_to_int($banner['typingInterval'], 1, 10000, 100);
    }

    // Background.
    if (array_key_exists('url', $bg)) {
        $flat['argon_page_background_url'] = (string) $bg['url'];
    }
    if (array_key_exists('darkUrl', $bg)) {
        $flat['argon_page_background_dark_url'] = (string) $bg['darkUrl'];
    }
    if (array_key_exists('opacity', $bg)) {
        $flat['argon_page_background_opacity'] = (string) argon_astro_to_number($bg['opacity'], 0, 1, 1);
    }
    if (array_key_exists('bannerStyle', $bg)) {
        $flat['argon_page_background_banner_style'] = ($bg['bannerStyle'] === 'transparent') ? 'transparent' : 'false';
    }

    // Sidebar.
    if (array_key_exists('bannerTitle', $sidebar)) {
        $flat['argon_sidebar_banner_title'] = (string) $sidebar['bannerTitle'];
    }
    if (array_key_exists('bannerSubtitle', $sidebar)) {
        $flat['argon_sidebar_banner_subtitle'] = (string) $sidebar['bannerSubtitle'];
    }
    if (array_key_exists('authorName', $sidebar)) {
        $flat['argon_sidebar_auther_name'] = (string) $sidebar['authorName'];
    }
    if (array_key_exists('authorImage', $sidebar)) {
        $flat['argon_sidebar_auther_image'] = (string) $sidebar['authorImage'];
    }
    if (array_key_exists('authorDescription', $sidebar)) {
        $flat['argon_sidebar_author_description'] = (string) $sidebar['authorDescription'];
    }
    if (array_key_exists('announcement', $sidebar)) {
        $flat['argon_sidebar_announcement'] = (string) $sidebar['announcement'];
    }

    // FAB.
    if (array_key_exists('settings', $fab)) {
        $flat['argon_fab_show_settings_button'] = $bool_str($fab['settings']);
    }
    if (array_key_exists('darkmode', $fab)) {
        $flat['argon_fab_show_darkmode_button'] = $bool_str($fab['darkmode']);
    }
    if (array_key_exists('comment', $fab)) {
        $flat['argon_fab_show_gotocomment_button'] = $bool_str($fab['comment']);
    }

    // Article, Archives & Search.
    if (array_key_exists('waterflow', $article)) {
        $wf = trim((string) $article['waterflow']);
        $flat['argon_article_list_waterflow'] = ($wf === '2,3') ? '2and3' : argon_astro_enum($wf, array('1', '2', '3', '2and3'), '1');
    }
    if (array_key_exists('listLayout', $article)) {
        $flat['argon_article_list_layout'] = argon_astro_enum($article['listLayout'], array('1', '2', '3'), '1');
    }
    if (array_key_exists('meta', $article)) {
        $flat['argon_article_meta'] = implode('|', argon_astro_parse_article_meta($article['meta']));
    }
    if (array_key_exists('hideCategories', $article)) {
        $flat['argon_hide_categories'] = $bool_str($article['hideCategories']);
    }
    if (array_key_exists('showReadingTime', $article)) {
        $flat['argon_show_readingtime'] = $bool_str($article['showReadingTime']);
    }
    if (array_key_exists('readingSpeed', $article)) {
        $flat['argon_reading_speed'] = (string) argon_astro_to_int($article['readingSpeed'], 1, 5000, 300);
    }
    if (array_key_exists('readingSpeedEn', $article)) {
        $flat['argon_reading_speed_en'] = (string) argon_astro_to_int($article['readingSpeedEn'], 1, 5000, 160);
    }
    if (array_key_exists('readingSpeedCode', $article)) {
        $flat['argon_reading_speed_code'] = (string) argon_astro_to_int($article['readingSpeedCode'], 1, 5000, 80);
    }
    if (array_key_exists('thumbnailInBanner', $article)) {
        $flat['argon_show_thumbnail_in_banner_in_content_page'] = $bool_str($article['thumbnailInBanner']);
    }
    if (array_key_exists('firstImageAsThumbnail', $article)) {
        $flat['argon_first_image_as_thumbnail_by_default'] = $bool_str($article['firstImageAsThumbnail']);
    }
    if (array_key_exists('referenceTitle', $article)) {
        $flat['argon_reference_list_title'] = (string) $article['referenceTitle'];
    }
    if (array_key_exists('showShare', $article)) {
        $flat['argon_show_sharebtn'] = $bool_str($article['showShare']);
    }
    if (array_key_exists('showCatalogNumbers', $article)) {
        $flat['argon_show_headindex_number'] = $bool_str($article['showCatalogNumbers']);
    }
    if (array_key_exists('donateQr', $article)) {
        $flat['argon_donate_qrcode_url'] = (string) $article['donateQr'];
    }
    if (array_key_exists('afterPostHtml', $article)) {
        $flat['argon_additional_content_after_post'] = (string) $article['afterPostHtml'];
    }
    if (array_key_exists('related', $article)) {
        $rel = trim((string) $article['related']);
        $flat['argon_related_post'] = ($rel === 'tag,category') ? 'category,tag' : argon_astro_enum($rel, array('disabled', 'category', 'tag', 'category,tag'), 'tag');
    }
    if (array_key_exists('relatedOrderBy', $article)) {
        $flat['argon_related_post_sort_orderby'] = ($article['relatedOrderBy'] === 'title') ? 'title' : 'date';
    }
    if (array_key_exists('relatedOrder', $article)) {
        $flat['argon_related_post_sort_order'] = (strtolower((string) $article['relatedOrder']) === 'asc') ? 'ASC' : 'DESC';
    }
    if (array_key_exists('relatedLimit', $article)) {
        $flat['argon_related_post_limit'] = (string) argon_astro_to_int($article['relatedLimit'], 1, 100, 10);
    }
    if (array_key_exists('outdatedTimeType', $article)) {
        $flat['argon_outdated_info_time_type'] = ($article['outdatedTimeType'] === 'relative') ? 'modifiedtime' : 'createdtime';
    }
    if (array_key_exists('outdatedDays', $article)) {
        $days = is_numeric($article['outdatedDays']) ? (int) $article['outdatedDays'] : 0;
        $flat['argon_outdated_info_days'] = $days > 0 ? (string) $days : '-1';
    }
    if (array_key_exists('outdatedTipType', $article)) {
        $flat['argon_outdated_info_tip_type'] = ($article['outdatedTipType'] === 'info') ? 'toast' : 'inpost';
    }
    if (array_key_exists('outdatedTip', $article)) {
        $flat['argon_outdated_info_tip_content'] = (string) $article['outdatedTip'];
    }
    if (array_key_exists('timelineShowMonth', $article)) {
        $flat['argon_archives_timeline_show_month'] = $bool_str($article['timelineShowMonth']);
    }
    if (array_key_exists('archivesUrl', $article)) {
        $flat['argon_archives_timeline_url'] = (string) $article['archivesUrl'];
    }
    if (array_key_exists('intoArticleAnimation', $article)) {
        $flat['argon_enable_into_article_animation'] = $bool_str($article['intoArticleAnimation']);
    }
    if (array_key_exists('hideShortcodeInPreview', $article)) {
        $flat['argon_hide_shortcode_in_preview'] = $bool_str($article['hideShortcodeInPreview']);
    }
    if (array_key_exists('trimWords', $article)) {
        $flat['argon_trim_words_count'] = (string) argon_astro_to_int($article['trimWords'], 0, 1000, 175);
    }
    if (array_key_exists('enableSearchFilters', $article)) {
        $flat['argon_enable_search_filters'] = $bool_str($article['enableSearchFilters']);
    }
    if (array_key_exists('searchFilters', $article)) {
        $filters = argon_astro_parse_search_filters($article['searchFilters']);
        $tagged = array();
        foreach ($filters as $filter) {
            $tagged[] = '*' . $filter;
        }
        $flat['argon_search_filters_type'] = implode(',', $tagged);
    }

    // Footer.
    if (array_key_exists('html', $footer)) {
        $flat['argon_footer_html'] = (string) $footer['html'];
    }
    if (array_key_exists('hideAuthor', $footer)) {
        $flat['argon_hide_footer_author'] = $bool_str($footer['hideAuthor']);
    }

    // Motion, Code Highlight & Math.
    if (array_key_exists('codeHighlight', $motion)) {
        $flat['argon_enable_code_highlight'] = $bool_str($motion['codeHighlight']);
    }
    if (array_key_exists('codeTheme', $motion)) {
        $flat['argon_code_theme'] = (string) $motion['codeTheme'];
    }
    if (array_key_exists('codeHideLineNumber', $motion)) {
        $flat['argon_code_highlight_hide_linenumber'] = $bool_str($motion['codeHideLineNumber']);
    }
    if (array_key_exists('codeBreakLine', $motion)) {
        $flat['argon_code_highlight_break_line'] = $bool_str($motion['codeBreakLine']);
    }
    if (array_key_exists('codeTransparentLineNumber', $motion)) {
        $flat['argon_code_highlight_transparent_linenumber'] = $bool_str($motion['codeTransparentLineNumber']);
    }
    if (array_key_exists('disableCodeblockStyle', $motion)) {
        $flat['argon_disable_codeblock_style'] = $bool_str($motion['disableCodeblockStyle']);
    }
    if (array_key_exists('math', $motion)) {
        $flat['argon_math_render'] = argon_astro_enum($motion['math'], array('none', 'katex', 'mathjax2', 'mathjax3'), 'none');
    }
    if (array_key_exists('mathjaxCdn', $motion)) {
        $flat['argon_mathjax_cdn_url'] = (string) $motion['mathjaxCdn'];
    }
    if (array_key_exists('mathjaxV2Cdn', $motion)) {
        $flat['argon_mathjax_v2_cdn_url'] = (string) $motion['mathjaxV2Cdn'];
    }
    if (array_key_exists('katexCdn', $motion)) {
        $flat['argon_katex_cdn_url'] = (string) $motion['katexCdn'];
    }
    if (array_key_exists('lazyload', $motion)) {
        $flat['argon_enable_lazyload'] = $bool_str($motion['lazyload']);
    }
    if (array_key_exists('lazyloadThreshold', $motion)) {
        $flat['argon_lazyload_threshold'] = (string) argon_astro_to_int($motion['lazyloadThreshold'], 0, 2500, 800);
    }
    if (array_key_exists('lazyloadEffect', $motion)) {
        $flat['argon_lazyload_effect'] = argon_astro_enum($motion['lazyloadEffect'], array('fadeIn', 'slideDown', 'none'), 'fadeIn');
    }
    if (array_key_exists('lazyloadLoadingStyle', $motion)) {
        $flat['argon_lazyload_loading_style'] = (string) $motion['lazyloadLoadingStyle'];
    }
    if (array_key_exists('fancybox', $motion)) {
        $flat['argon_enable_fancybox'] = $bool_str($motion['fancybox']);
    }
    if (array_key_exists('zoomify', $motion)) {
        $flat['argon_enable_zoomify'] = $bool_str($motion['zoomify']);
    }
    if (array_key_exists('zoomifyDuration', $motion)) {
        $flat['argon_zoomify_duration'] = (string) argon_astro_to_int($motion['zoomifyDuration'], 0, 10000, 200);
    }
    if (array_key_exists('zoomifyEasing', $motion)) {
        $flat['argon_zoomify_easing'] = (string) $motion['zoomifyEasing'];
    }
    if (array_key_exists('zoomifyScale', $motion)) {
        $flat['argon_zoomify_scale'] = (string) argon_astro_to_number($motion['zoomifyScale'], 0.01, 1, 0.9);
    }
    if (array_key_exists('pangu', $motion)) {
        $pangu = trim((string) $motion['pangu']);
        if ($pangu === '' || strtolower($pangu) === 'false') {
            $flat['argon_enable_pangu'] = 'false';
        } elseif ($pangu === 'all') {
            $flat['argon_enable_pangu'] = 'article|shuoshuo|comment';
        } else {
            $flat['argon_enable_pangu'] = $pangu;
        }
    }
    if (array_key_exists('smoothScroll', $motion)) {
        $flat['argon_enable_smoothscroll_type'] = argon_astro_enum($motion['smoothScroll'], array('1', '1_pulse', '2', '3', 'disabled'), '1');
    }
    if (array_key_exists('pjaxAnimation', $motion)) {
        // Inverted polarity back to upstream "disable".
        $flat['argon_disable_pjax_animation'] = argon_astro_to_bool($motion['pjaxAnimation'], true) ? 'false' : 'true';
    }
    if (array_key_exists('pjax', $motion)) {
        // Inverted polarity back to upstream "disabled".
        $flat['argon_pjax_disabled'] = argon_astro_to_bool($motion['pjax'], true) ? 'false' : 'true';
    }
    if (array_key_exists('foldLongShuoshuo', $motion)) {
        $flat['argon_fold_long_shuoshuo'] = $bool_str($motion['foldLongShuoshuo']);
    }
    if (array_key_exists('foldLongComments', $motion)) {
        $flat['argon_fold_long_comments'] = $bool_str($motion['foldLongComments']);
    }

    // Home.
    if (array_key_exists('showShuoshuo', $home)) {
        $flat['argon_home_show_shuoshuo'] = $bool_str($home['showShuoshuo']);
    }

    return $flat;
}

/**
 * Read existing legacy `argon_*` WordPress options from the database and map
 * them into the nested `argon_astro_config` tree.
 *
 * @return array<string, mixed>
 */
function argon_astro_import_legacy_options() {
    if (!function_exists('get_option')) {
        return array();
    }
    $sentinel = '__ARGON_ASTRO_OPTION_MISSING__';
    $found = array();
    foreach (argon_astro_legacy_option_keys() as $key) {
        $val = get_option($key, $sentinel);
        if ($val !== $sentinel) {
            $found[$key] = $val;
        }
    }
    if (empty($found)) {
        return array();
    }
    return argon_astro_flat_to_nested($found);
}

/**
 * Count how many legacy `argon_*` options currently exist in the WordPress database.
 *
 * @return int
 */
function argon_astro_count_legacy_options() {
    if (!function_exists('get_option')) {
        return 0;
    }
    $sentinel = '__ARGON_ASTRO_OPTION_MISSING__';
    $count = 0;
    foreach (argon_astro_legacy_option_keys() as $key) {
        $val = get_option($key, $sentinel);
        if ($val !== $sentinel) {
            $count++;
        }
    }
    return $count;
}

/**
 * Sanitize a nested `argon_astro_config` array coming from the visual form or JSON import.
 * Preserves non-visual nested fields (menus, authorLinks, widgets) from `$previous` when absent.
 *
 * @param array<string, mixed> $input
 * @param array<string, mixed> $previous
 * @return array<string, mixed>
 */
function argon_astro_sanitize_nested_config(array $input, array $previous = array()) {
    $clean = array('version' => 1);

    // Site.
    if (isset($input['site']) && is_array($input['site'])) {
        $s = $input['site'];
        $clean['site'] = array();
        if (array_key_exists('title', $s)) {
            $clean['site']['title'] = argon_astro_clean_text($s['title']);
        }
        if (array_key_exists('description', $s)) {
            $clean['site']['description'] = trim((string) $s['description']);
        }
        if (array_key_exists('url', $s)) {
            $clean['site']['url'] = argon_astro_sanitize_url_or_token($s['url']);
        }
        if (array_key_exists('language', $s)) {
            $clean['site']['language'] = argon_astro_clean_text($s['language']);
        }
        if (array_key_exists('keywords', $s)) {
            $clean['site']['keywords'] = argon_astro_clean_text($s['keywords']);
        }
        if (array_key_exists('dateFormat', $s)) {
            $clean['site']['dateFormat'] = argon_astro_enum(strtoupper((string) $s['dateFormat']), array('YMD', 'DMY', 'MDY'), 'YMD');
        }
    }

    // Appearance.
    if (isset($input['appearance']) && is_array($input['appearance'])) {
        $a = $input['appearance'];
        $clean['appearance'] = array();
        if (array_key_exists('color', $a)) {
            $clean['appearance']['color'] = argon_astro_sanitize_hex_color($a['color'], '#5e72e4');
        }
        if (array_key_exists('allowColorPicker', $a)) {
            $clean['appearance']['allowColorPicker'] = argon_astro_to_bool($a['allowColorPicker'], true);
        }
        if (array_key_exists('cardRadius', $a)) {
            $clean['appearance']['cardRadius'] = argon_astro_to_number($a['cardRadius'], 0, 30, 4);
        }
        if (array_key_exists('cardShadow', $a)) {
            $clean['appearance']['cardShadow'] = ($a['cardShadow'] === 'big') ? 'big' : 'small';
        }
        if (array_key_exists('font', $a)) {
            $clean['appearance']['font'] = ($a['font'] === 'serif') ? 'serif' : 'sans';
        }
        if (array_key_exists('layout', $a)) {
            $clean['appearance']['layout'] = argon_astro_enum($a['layout'], array('single', 'double', 'double-reverse', 'triple'), 'double');
        }
        if (array_key_exists('immersionColor', $a)) {
            $clean['appearance']['immersionColor'] = argon_astro_to_bool($a['immersionColor'], false);
        }
        if (array_key_exists('amoledDark', $a)) {
            $clean['appearance']['amoledDark'] = argon_astro_to_bool($a['amoledDark'], false);
        }
        if (array_key_exists('darkmode', $a)) {
            $dm = strtolower(trim((string) $a['darkmode']));
            if ($dm === 'false') {
                $dm = 'off';
            } elseif ($dm === 'alwayson') {
                $dm = 'always';
            }
            $clean['appearance']['darkmode'] = argon_astro_enum($dm, array('off', 'system', 'time', 'always'), 'system');
        }
        if (array_key_exists('articleHeaderStyle', $a)) {
            $clean['appearance']['articleHeaderStyle'] = argon_astro_enum(
                $a['articleHeaderStyle'],
                array('article-header-style-default', 'article-header-style-1', 'article-header-style-2'),
                'article-header-style-default'
            );
        }
        if (array_key_exists('mobileScale', $a)) {
            $clean['appearance']['mobileScale'] = argon_astro_to_bool($a['mobileScale'], false);
        }
        if (array_key_exists('disableGoogleFont', $a)) {
            $clean['appearance']['disableGoogleFont'] = argon_astro_to_bool($a['disableGoogleFont'], true);
        }
        if (array_key_exists('customHeadHtml', $a)) {
            $clean['appearance']['customHeadHtml'] = (string) $a['customHeadHtml'];
        }
        if (array_key_exists('customFootHtml', $a)) {
            $clean['appearance']['customFootHtml'] = (string) $a['customFootHtml'];
        }
    }

    // Banner.
    if (isset($input['banner']) && is_array($input['banner'])) {
        $b = $input['banner'];
        $clean['banner'] = array();
        if (array_key_exists('size', $b)) {
            $clean['banner']['size'] = argon_astro_enum($b['size'], array('full', 'mini', 'hide', 'fullscreen'), 'full');
        }
        if (array_key_exists('title', $b)) {
            $clean['banner']['title'] = argon_astro_clean_text($b['title']);
        }
        if (array_key_exists('subtitle', $b)) {
            $clean['banner']['subtitle'] = argon_astro_clean_text($b['subtitle']);
        }
        if (array_key_exists('typing', $b)) {
            $clean['banner']['typing'] = argon_astro_to_bool($b['typing'], false);
        }
        if (array_key_exists('typingInterval', $b)) {
            $clean['banner']['typingInterval'] = argon_astro_to_int($b['typingInterval'], 1, 10000, 100);
        }
        if (array_key_exists('backgroundUrl', $b)) {
            $clean['banner']['backgroundUrl'] = argon_astro_sanitize_url_or_token($b['backgroundUrl']);
        }
        if (array_key_exists('hideShapes', $b)) {
            $clean['banner']['hideShapes'] = argon_astro_to_bool($b['hideShapes'], false);
        }
        if (array_key_exists('colorType', $b)) {
            $clean['banner']['colorType'] = argon_astro_enum(
                $b['colorType'],
                array('shape-primary', 'shape-default', 'shape-dark', 'bg-gradient-success', 'bg-gradient-info', 'bg-gradient-warning', 'bg-gradient-danger'),
                'shape-primary'
            );
        }
        if (array_key_exists('toolbarMask', $b)) {
            $clean['banner']['toolbarMask'] = argon_astro_to_bool($b['toolbarMask'], false);
        }
    }

    // Background.
    if (isset($input['background']) && is_array($input['background'])) {
        $bg = $input['background'];
        $clean['background'] = array();
        if (array_key_exists('url', $bg)) {
            $clean['background']['url'] = argon_astro_sanitize_url_or_token($bg['url']);
        }
        if (array_key_exists('darkUrl', $bg)) {
            $clean['background']['darkUrl'] = argon_astro_sanitize_url_or_token($bg['darkUrl']);
        }
        if (array_key_exists('opacity', $bg)) {
            $clean['background']['opacity'] = argon_astro_to_number($bg['opacity'], 0, 1, 1);
        }
        if (array_key_exists('bannerStyle', $bg)) {
            $clean['background']['bannerStyle'] = ($bg['bannerStyle'] === 'transparent') ? 'transparent' : 'normal';
        }
    }

    // Toolbar.
    if (isset($input['toolbar']) && is_array($input['toolbar'])) {
        $t = $input['toolbar'];
        $clean['toolbar'] = array();
        if (array_key_exists('title', $t)) {
            $clean['toolbar']['title'] = argon_astro_clean_text($t['title']);
        }
        if (array_key_exists('icon', $t)) {
            $clean['toolbar']['icon'] = argon_astro_sanitize_url_or_token($t['icon']);
        }
        if (array_key_exists('iconLink', $t)) {
            $clean['toolbar']['iconLink'] = argon_astro_sanitize_url_or_token($t['iconLink']);
        }
        if (array_key_exists('blur', $t)) {
            $clean['toolbar']['blur'] = argon_astro_to_bool($t['blur'], false);
        }
        if (array_key_exists('headroom', $t)) {
            $hr = is_bool($t['headroom']) ? ($t['headroom'] ? 'true' : 'false') : (string) $t['headroom'];
            $clean['toolbar']['headroom'] = argon_astro_enum(strtolower(trim($hr)), array('false', 'true', 'absolute'), 'false');
        }
        if (isset($t['menu']) && is_array($t['menu'])) {
            $clean['toolbar']['menu'] = $t['menu'];
        }
    }
    if (!isset($clean['toolbar']['menu']) && isset($previous['toolbar']['menu']) && is_array($previous['toolbar']['menu'])) {
        $clean['toolbar']['menu'] = $previous['toolbar']['menu'];
    }

    // Sidebar.
    if (isset($input['sidebar']) && is_array($input['sidebar'])) {
        $sb = $input['sidebar'];
        $clean['sidebar'] = array();
        if (array_key_exists('announcement', $sb)) {
            $clean['sidebar']['announcement'] = (string) $sb['announcement'];
        }
        if (array_key_exists('bannerTitle', $sb)) {
            $clean['sidebar']['bannerTitle'] = argon_astro_clean_text($sb['bannerTitle']);
        }
        if (array_key_exists('bannerSubtitle', $sb)) {
            $clean['sidebar']['bannerSubtitle'] = argon_astro_clean_text($sb['bannerSubtitle']);
        }
        if (array_key_exists('authorName', $sb)) {
            $clean['sidebar']['authorName'] = argon_astro_clean_text($sb['authorName']);
        }
        if (array_key_exists('authorDescription', $sb)) {
            $clean['sidebar']['authorDescription'] = argon_astro_clean_text($sb['authorDescription']);
        }
        if (array_key_exists('authorImage', $sb)) {
            $clean['sidebar']['authorImage'] = argon_astro_sanitize_url_or_token($sb['authorImage']);
        }
        foreach (array('menu', 'authorLinks', 'widgets') as $preserve_key) {
            if (isset($sb[$preserve_key]) && is_array($sb[$preserve_key])) {
                $clean['sidebar'][$preserve_key] = $sb[$preserve_key];
            }
        }
    }
    foreach (array('menu', 'authorLinks', 'widgets') as $preserve_key) {
        if (!isset($clean['sidebar'][$preserve_key]) && isset($previous['sidebar'][$preserve_key]) && is_array($previous['sidebar'][$preserve_key])) {
            $clean['sidebar'][$preserve_key] = $previous['sidebar'][$preserve_key];
        }
    }

    // Article.
    if (isset($input['article']) && is_array($input['article'])) {
        $ar = $input['article'];
        $clean['article'] = array();
        if (array_key_exists('listLayout', $ar)) {
            $clean['article']['listLayout'] = argon_astro_enum($ar['listLayout'], array('1', '2', '3'), '1');
        }
        if (array_key_exists('waterflow', $ar)) {
            $wf = trim((string) $ar['waterflow']);
            if ($wf === '2,3') {
                $wf = '2and3';
            }
            $clean['article']['waterflow'] = argon_astro_enum($wf, array('1', '2', '3', '2and3'), '1');
        }
        if (array_key_exists('meta', $ar)) {
            $clean['article']['meta'] = argon_astro_parse_article_meta($ar['meta']);
        } elseif (!empty($ar['_meta_present'])) {
            $clean['article']['meta'] = array();
        }
        if (array_key_exists('hideCategories', $ar)) {
            $clean['article']['hideCategories'] = argon_astro_to_bool($ar['hideCategories'], false);
        }
        if (array_key_exists('showReadingTime', $ar)) {
            $clean['article']['showReadingTime'] = argon_astro_to_bool($ar['showReadingTime'], true);
        }
        if (array_key_exists('readingSpeed', $ar)) {
            $clean['article']['readingSpeed'] = argon_astro_to_int($ar['readingSpeed'], 1, 5000, 300);
        }
        if (array_key_exists('readingSpeedEn', $ar)) {
            $clean['article']['readingSpeedEn'] = argon_astro_to_int($ar['readingSpeedEn'], 1, 5000, 160);
        }
        if (array_key_exists('readingSpeedCode', $ar)) {
            $clean['article']['readingSpeedCode'] = argon_astro_to_int($ar['readingSpeedCode'], 1, 5000, 80);
        }
        if (array_key_exists('trimWords', $ar)) {
            $clean['article']['trimWords'] = argon_astro_to_int($ar['trimWords'], 0, 1000, 175);
        }
        if (array_key_exists('hideShortcodeInPreview', $ar)) {
            $clean['article']['hideShortcodeInPreview'] = argon_astro_to_bool($ar['hideShortcodeInPreview'], true);
        }
        if (array_key_exists('intoArticleAnimation', $ar)) {
            $clean['article']['intoArticleAnimation'] = argon_astro_to_bool($ar['intoArticleAnimation'], true);
        }
        if (array_key_exists('thumbnailInBanner', $ar)) {
            $clean['article']['thumbnailInBanner'] = argon_astro_to_bool($ar['thumbnailInBanner'], false);
        }
        if (array_key_exists('firstImageAsThumbnail', $ar)) {
            $clean['article']['firstImageAsThumbnail'] = argon_astro_to_bool($ar['firstImageAsThumbnail'], true);
        }
        if (array_key_exists('showShare', $ar)) {
            $clean['article']['showShare'] = argon_astro_to_bool($ar['showShare'], true);
        }
        if (array_key_exists('related', $ar)) {
            $rel = trim((string) $ar['related']);
            if ($rel === 'tag,category') {
                $rel = 'category,tag';
            }
            $clean['article']['related'] = argon_astro_enum($rel, array('disabled', 'category', 'tag', 'category,tag'), 'tag');
        }
        if (array_key_exists('relatedLimit', $ar)) {
            $clean['article']['relatedLimit'] = argon_astro_to_int($ar['relatedLimit'], 1, 100, 10);
        }
        if (array_key_exists('relatedOrderBy', $ar)) {
            $clean['article']['relatedOrderBy'] = ($ar['relatedOrderBy'] === 'title') ? 'title' : 'date';
        }
        if (array_key_exists('relatedOrder', $ar)) {
            $clean['article']['relatedOrder'] = (strtolower((string) $ar['relatedOrder']) === 'asc') ? 'asc' : 'desc';
        }
        if (array_key_exists('outdatedDays', $ar)) {
            $days = is_numeric($ar['outdatedDays']) ? (int) $ar['outdatedDays'] : 0;
            $clean['article']['outdatedDays'] = $days > 0 ? min(99999, $days) : 0;
        }
        if (array_key_exists('outdatedTip', $ar)) {
            $clean['article']['outdatedTip'] = trim((string) $ar['outdatedTip']);
        }
        if (array_key_exists('outdatedTipType', $ar)) {
            $tt = strtolower(trim((string) $ar['outdatedTipType']));
            $clean['article']['outdatedTipType'] = ($tt === 'info' || $tt === 'toast') ? 'info' : 'warning';
        }
        if (array_key_exists('outdatedTimeType', $ar)) {
            $tm = strtolower(trim((string) $ar['outdatedTimeType']));
            $clean['article']['outdatedTimeType'] = ($tm === 'relative' || $tm === 'modifiedtime') ? 'relative' : 'absolute';
        }
        if (array_key_exists('archivesUrl', $ar)) {
            $clean['article']['archivesUrl'] = argon_astro_sanitize_url_or_token($ar['archivesUrl']);
        }
        if (array_key_exists('donateQr', $ar)) {
            $clean['article']['donateQr'] = argon_astro_sanitize_url_or_token($ar['donateQr']);
        }
        if (array_key_exists('afterPostHtml', $ar)) {
            $clean['article']['afterPostHtml'] = (string) $ar['afterPostHtml'];
        }
        if (array_key_exists('showCatalogNumbers', $ar)) {
            $clean['article']['showCatalogNumbers'] = argon_astro_to_bool($ar['showCatalogNumbers'], false);
        }
        if (array_key_exists('timelineShowMonth', $ar)) {
            $clean['article']['timelineShowMonth'] = argon_astro_to_bool($ar['timelineShowMonth'], true);
        }
        if (array_key_exists('enableSearchFilters', $ar)) {
            $clean['article']['enableSearchFilters'] = argon_astro_to_bool($ar['enableSearchFilters'], true);
        }
        if (array_key_exists('searchFilters', $ar)) {
            $clean['article']['searchFilters'] = argon_astro_parse_search_filters($ar['searchFilters']);
        } elseif (!empty($ar['_search_filters_present'])) {
            $clean['article']['searchFilters'] = array();
        }
        if (array_key_exists('referenceTitle', $ar)) {
            $clean['article']['referenceTitle'] = argon_astro_clean_text($ar['referenceTitle']);
        }
    }

    // Footer.
    if (isset($input['footer']) && is_array($input['footer'])) {
        $f = $input['footer'];
        $clean['footer'] = array();
        if (array_key_exists('html', $f)) {
            $clean['footer']['html'] = (string) $f['html'];
        }
        if (array_key_exists('hideAuthor', $f)) {
            $clean['footer']['hideAuthor'] = argon_astro_to_bool($f['hideAuthor'], false);
        }
    }

    // Motion.
    if (isset($input['motion']) && is_array($input['motion'])) {
        $m = $input['motion'];
        $clean['motion'] = array();
        if (array_key_exists('lazyload', $m)) {
            $clean['motion']['lazyload'] = argon_astro_to_bool($m['lazyload'], true);
        }
        if (array_key_exists('lazyloadThreshold', $m)) {
            $clean['motion']['lazyloadThreshold'] = argon_astro_to_int($m['lazyloadThreshold'], 0, 2500, 800);
        }
        if (array_key_exists('lazyloadEffect', $m)) {
            $clean['motion']['lazyloadEffect'] = argon_astro_enum($m['lazyloadEffect'], array('fadeIn', 'slideDown', 'none'), 'fadeIn');
        }
        if (array_key_exists('lazyloadLoadingStyle', $m)) {
            $clean['motion']['lazyloadLoadingStyle'] = argon_astro_enum(
                $m['lazyloadLoadingStyle'],
                array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', 'none'),
                '1'
            );
        }
        if (array_key_exists('zoomify', $m)) {
            $clean['motion']['zoomify'] = argon_astro_to_bool($m['zoomify'], false);
        }
        if (array_key_exists('zoomifyDuration', $m)) {
            $clean['motion']['zoomifyDuration'] = argon_astro_to_int($m['zoomifyDuration'], 0, 10000, 200);
        }
        if (array_key_exists('zoomifyEasing', $m)) {
            $clean['motion']['zoomifyEasing'] = argon_astro_clean_text($m['zoomifyEasing']);
        }
        if (array_key_exists('readingProgress', $m)) {
            $clean['motion']['readingProgress'] = argon_astro_to_bool($m['readingProgress'], true);
        }
        if (array_key_exists('zoomifyScale', $m)) {
            $clean['motion']['zoomifyScale'] = argon_astro_to_number($m['zoomifyScale'], 0.01, 1, 0.9);
        }
        if (array_key_exists('fancybox', $m)) {
            $clean['motion']['fancybox'] = argon_astro_to_bool($m['fancybox'], true);
        }
        if (array_key_exists('smoothScroll', $m)) {
            $clean['motion']['smoothScroll'] = argon_astro_enum($m['smoothScroll'], array('1', '1_pulse', '2', '3', 'disabled'), '1');
        }
        if (array_key_exists('pangu', $m)) {
            $pangu = trim((string) $m['pangu']);
            $clean['motion']['pangu'] = ($pangu === 'false' || $pangu === '')
                ? ''
                : argon_astro_enum(
                    $pangu,
                    array('article', 'shuoshuo', 'comment', 'article|comment', 'article|shuoshuo', 'shuoshuo|comment', 'article|shuoshuo|comment', 'all'),
                    ''
                );
        }
        if (array_key_exists('foldLongComments', $m)) {
            $clean['motion']['foldLongComments'] = argon_astro_to_bool($m['foldLongComments'], false);
        }
        if (array_key_exists('foldLongShuoshuo', $m)) {
            $clean['motion']['foldLongShuoshuo'] = argon_astro_to_bool($m['foldLongShuoshuo'], false);
        }
        if (array_key_exists('pjax', $m)) {
            $clean['motion']['pjax'] = argon_astro_to_bool($m['pjax'], true);
        }
        if (array_key_exists('pjaxAnimation', $m)) {
            $clean['motion']['pjaxAnimation'] = argon_astro_to_bool($m['pjaxAnimation'], true);
        }
        if (array_key_exists('codeHighlight', $m)) {
            $clean['motion']['codeHighlight'] = argon_astro_to_bool($m['codeHighlight'], false);
        }
        if (array_key_exists('codeTheme', $m)) {
            $clean['motion']['codeTheme'] = argon_astro_clean_text($m['codeTheme']);
        }
        if (array_key_exists('codeHideLineNumber', $m)) {
            $clean['motion']['codeHideLineNumber'] = argon_astro_to_bool($m['codeHideLineNumber'], false);
        }
        if (array_key_exists('codeTransparentLineNumber', $m)) {
            $clean['motion']['codeTransparentLineNumber'] = argon_astro_to_bool($m['codeTransparentLineNumber'], false);
        }
        if (array_key_exists('codeBreakLine', $m)) {
            $clean['motion']['codeBreakLine'] = argon_astro_to_bool($m['codeBreakLine'], false);
        }
        if (array_key_exists('disableCodeblockStyle', $m)) {
            $clean['motion']['disableCodeblockStyle'] = argon_astro_to_bool($m['disableCodeblockStyle'], false);
        }
        if (array_key_exists('math', $m)) {
            $clean['motion']['math'] = argon_astro_enum($m['math'], array('none', 'katex', 'mathjax2', 'mathjax3'), 'none');
        }
        if (array_key_exists('katexCdn', $m)) {
            $clean['motion']['katexCdn'] = argon_astro_normalize_math_cdn($m['katexCdn']);
        }
        if (array_key_exists('mathjaxCdn', $m)) {
            $clean['motion']['mathjaxCdn'] = argon_astro_normalize_math_cdn($m['mathjaxCdn']);
        }
        if (array_key_exists('mathjaxV2Cdn', $m)) {
            $clean['motion']['mathjaxV2Cdn'] = argon_astro_normalize_math_cdn($m['mathjaxV2Cdn']);
        }
    }

    // FAB.
    if (isset($input['fab']) && is_array($input['fab'])) {
        $fb = $input['fab'];
        $clean['fab'] = array();
        if (array_key_exists('darkmode', $fb)) {
            $clean['fab']['darkmode'] = argon_astro_to_bool($fb['darkmode'], false);
        }
        if (array_key_exists('comment', $fb)) {
            $clean['fab']['comment'] = argon_astro_to_bool($fb['comment'], false);
        }
        if (array_key_exists('settings', $fb)) {
            $clean['fab']['settings'] = argon_astro_to_bool($fb['settings'], true);
        }
    }

    // Home.
    if (isset($input['home']) && is_array($input['home'])) {
        $h = $input['home'];
        $clean['home'] = array();
        if (array_key_exists('showShuoshuo', $h)) {
            $clean['home']['showShuoshuo'] = argon_astro_to_bool($h['showShuoshuo'], true);
        }
        if (array_key_exists('pageSize', $h)) {
            $clean['home']['pageSize'] = argon_astro_to_int($h['pageSize'], 1, 100, 10);
        }
    }

    // Friend links: accept structured array or pipe-delimited text field.
    if (array_key_exists('friendLinks', $input)) {
        $clean['friendLinks'] = argon_astro_parse_friend_links($input['friendLinks']);
    } elseif (array_key_exists('friendLinksText', $input)) {
        $clean['friendLinks'] = argon_astro_parse_friend_links((string) $input['friendLinksText']);
    } elseif (!empty($input['_friend_links_present'])) {
        $clean['friendLinks'] = array();
    } elseif (isset($previous['friendLinks']) && is_array($previous['friendLinks'])) {
        $clean['friendLinks'] = $previous['friendLinks'];
    }

    if (isset($input['agent']) && is_array($input['agent'])) {
        $agent_in = $input['agent'];
        $clean_multiline = function ($val) {
            return trim(str_replace(array("\r\n", "\r"), "\n", (string) $val));
        };
        $clean['agent'] = array(
            'enabled' => array_key_exists('enabled', $agent_in) ? argon_astro_to_bool($agent_in['enabled']) : true,
            'robotsTxt' => array_key_exists('robotsTxt', $agent_in) ? argon_astro_to_bool($agent_in['robotsTxt']) : true,
            'llmsTxt' => array_key_exists('llmsTxt', $agent_in) ? argon_astro_to_bool($agent_in['llmsTxt']) : true,
            'markdownMirrors' => array_key_exists('markdownMirrors', $agent_in) ? argon_astro_to_bool($agent_in['markdownMirrors']) : true,
            'jsonIndex' => array_key_exists('jsonIndex', $agent_in) ? argon_astro_to_bool($agent_in['jsonIndex']) : true,
            'jsonLd' => array_key_exists('jsonLd', $agent_in) ? argon_astro_to_bool($agent_in['jsonLd']) : true,
            'webMcp' => array_key_exists('webMcp', $agent_in) ? argon_astro_to_bool($agent_in['webMcp']) : true,
            'allowTrainingCrawlers' => array_key_exists('allowTrainingCrawlers', $agent_in) ? argon_astro_to_bool($agent_in['allowTrainingCrawlers']) : true,
            'customRobotsTxt' => array_key_exists('customRobotsTxt', $agent_in) ? $clean_multiline($agent_in['customRobotsTxt']) : '',
            'customLlmsTxt' => array_key_exists('customLlmsTxt', $agent_in) ? $clean_multiline($agent_in['customLlmsTxt']) : '',
        );
    } elseif (isset($previous['agent']) && is_array($previous['agent'])) {
        $clean['agent'] = $previous['agent'];
    }

    unset($clean['comments'], $clean['source']);
    return $clean;
}

/**
 * Return the full effective nested config for rendering the admin UI and JSON export.
 *
 * @return array<string, mixed>
 */
function argon_astro_effective_config() {
    $stored = function_exists('get_option') ? get_option('argon_astro_config', array()) : array();
    if (!is_array($stored)) {
        $stored = array();
    }
    unset($stored['comments'], $stored['source']);
    return argon_astro_merge_config(argon_astro_schema_defaults(), $stored);
}
