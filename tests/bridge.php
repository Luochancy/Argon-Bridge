<?php
/**
 * Unit tests for Argon Bridge helpers and WordPress-stubbed endpoints.
 */

define('ABSPATH', __DIR__ . '/');

// Minimal WordPress function stubs for CLI testing.
$GLOBALS['__wp_options'] = array();
$GLOBALS['__wp_actions'] = array();
$GLOBALS['__wp_scheduled'] = array();
$GLOBALS['__wp_post_meta'] = array();
$GLOBALS['__wp_comment_meta'] = array();
$GLOBALS['__wp_comments'] = array();
$GLOBALS['__wp_settings_errors'] = array();
$GLOBALS['__wp_remote_posts'] = array();

if (!class_exists('WP_Error')) {
    class WP_Error {
        public $code;
        public $message;
        public $data;
        public function __construct($code, $message = '', $data = array()) {
            $this->code = $code;
            $this->message = $message;
            $this->data = $data;
        }
        public function get_error_code() { return $this->code; }
        public function get_error_message() { return $this->message; }
        public function get_error_data() { return $this->data; }
    }
}
if (!class_exists('WP_REST_Request')) {
    class WP_REST_Request {
        private $params;
        public function __construct($params = array()) {
            $this->params = $params;
        }
        public function get_param($key) {
            return array_key_exists($key, $this->params) ? $this->params[$key] : null;
        }
    }
}

function is_wp_error($thing) { return $thing instanceof WP_Error; }
function add_action($hook, $cb, $priority = 10, $args = 1) { $GLOBALS['__wp_actions'][$hook][] = $cb; }
function get_option($key, $default = false) { return array_key_exists($key, $GLOBALS['__wp_options']) ? $GLOBALS['__wp_options'][$key] : $default; }
function update_option($key, $val, $autoload = null) { $GLOBALS['__wp_options'][$key] = $val; return true; }
function delete_option($key) { unset($GLOBALS['__wp_options'][$key]); return true; }
function get_post_meta($id, $key, $single = false) { return isset($GLOBALS['__wp_post_meta'][$id][$key]) ? $GLOBALS['__wp_post_meta'][$id][$key] : ''; }
function update_post_meta($id, $key, $val) { $GLOBALS['__wp_post_meta'][$id][$key] = $val; return true; }
function get_post($id) { return $GLOBALS['__wp_posts'][$id] ?? null; }
function wp_mail($to, $subject, $message) { $GLOBALS['__wp_mails'][] = compact('to', 'subject', 'message'); return true; }
function is_active_sidebar($id) { return !empty($GLOBALS['__wp_sidebars'][$id]); }
function dynamic_sidebar($id) { echo $GLOBALS['__wp_sidebars'][$id] ?? ''; }
function get_comment_meta($id, $key, $single = false) { return isset($GLOBALS['__wp_comment_meta'][$id][$key]) ? $GLOBALS['__wp_comment_meta'][$id][$key] : ''; }
function update_comment_meta($id, $key, $val) { $GLOBALS['__wp_comment_meta'][$id][$key] = $val; return true; }
function get_comment($id) { return isset($GLOBALS['__wp_comments'][$id]) ? $GLOBALS['__wp_comments'][$id] : null; }
function get_comments($args = array()) {
    $post_id = isset($args['post_id']) ? (int) $args['post_id'] : 0;
    return array_values(array_filter($GLOBALS['__wp_comments'], function ($c) use ($post_id) {
        return (int) $c->comment_post_ID === $post_id;
    }));
}
function wp_new_comment($data, $wp_error = false) {
    if (!empty($GLOBALS['__wp_force_comment_error'])) {
        return $GLOBALS['__wp_force_comment_error'];
    }
    static $next_id = 100;
    $id = $next_id++;
    $GLOBALS['__wp_comments'][$id] = (object) array_merge(array(
        'comment_ID' => $id,
        'comment_date_gmt' => '2026-11-11 00:00:00',
        'comment_agent' => 'TestBrowser/1.0',
    ), $data);
    return $id;
}
function get_nav_menu_locations() { return $GLOBALS['__wp_nav_locations'] ?? array(); }
function wp_get_nav_menu_items($menu_id) { return $GLOBALS['__wp_nav_items'][$menu_id] ?? array(); }
function get_bookmarks($args = array()) { return $GLOBALS['__wp_bookmarks'] ?? array(); }
function get_permalink($post) { return is_object($post) && isset($post->permalink) ? $post->permalink : ''; }
function get_page_template_slug($id) { return $GLOBALS['__wp_page_templates'][$id] ?? ''; }
function wp_update_comment($data) {
    $id = (int) $data['comment_ID'];
    if (isset($GLOBALS['__wp_comments'][$id])) {
        $GLOBALS['__wp_comments'][$id]->comment_content = $data['comment_content'];
    }
    return 1;
}
function wp_json_encode($data, $flags = 0) { return json_encode($data, $flags); }
function sanitize_text_field($str) { return trim(strip_tags((string) $str)); }
function esc_url_raw($url) { return trim((string) $url); }
function rest_sanitize_boolean($val) { return filter_var($val, FILTER_VALIDATE_BOOLEAN); }
function is_email($email) { return (bool) filter_var($email, FILTER_VALIDATE_EMAIL); }
function mysql_to_rfc3339($dt) { return str_replace(' ', 'T', (string) $dt) . 'Z'; }
function home_url($path = '') { return 'https://cms.example' . $path; }
function wp_get_attachment_url($id) { return $id === 7 ? 'https://cms.example/wp-content/uploads/hero.jpg' : ''; }
function wp_get_attachment_metadata($id) { return array('width' => 1200, 'height' => 630); }
function get_post_mime_type($id) { return 'image/jpeg'; }
function get_post_thumbnail_id($post) { return is_object($post) ? (int) ($post->thumb_id ?? 0) : 0; }
function attachment_url_to_postid($url) { return $url === 'https://cms.example/wp-content/uploads/hero.jpg' ? 7 : 0; }
function wp_clear_scheduled_hook($hook) { unset($GLOBALS['__wp_scheduled'][$hook]); }
function wp_schedule_single_event($ts, $hook) { $GLOBALS['__wp_scheduled'][$hook] = $ts; return true; }
function wp_generate_password($len = 16, $special = false) { return str_repeat('a', $len); }
function wp_remote_post($url, $args = array()) {
    $GLOBALS['__wp_remote_posts'][] = array('url' => $url, 'args' => $args);
    return array('response' => array('code' => 202));
}
function wp_remote_retrieve_response_code($res) { return $res['response']['code'] ?? 0; }
function add_settings_error($setting, $code, $message, $type = 'error') {
    $GLOBALS['__wp_settings_errors'][] = compact('setting', 'code', 'message', 'type');
}
function wp_is_post_revision($id) { return false; }

require dirname(__DIR__) . '/includes/signature.php';
require dirname(__DIR__) . '/includes/media.php';
require dirname(__DIR__) . '/includes/admin-schema.php';
require dirname(__DIR__) . '/includes/rest-config.php';
require dirname(__DIR__) . '/includes/rest-content.php';
require dirname(__DIR__) . '/includes/rest-comments.php';
require dirname(__DIR__) . '/includes/rest-post-unlock.php';
require dirname(__DIR__) . '/includes/rest-views.php';
require dirname(__DIR__) . '/includes/webhooks.php';
require dirname(__DIR__) . '/includes/admin-view.php';
require dirname(__DIR__) . '/includes/admin.php';

function assert_true($cond, $msg) {
    if (!$cond) {
        fwrite(STDERR, "FAIL: $msg\n");
        exit(1);
    }
}

// 1. Media extraction: plain <img>, srcset, same-origin filtering, config URLs
$html = '<p><img src="https://cms.example/wp-content/uploads/a.jpg" srcset="https://cms.example/wp-content/uploads/a-300x200.jpg 300w, https://other.example/ext.jpg 600w"></p>';
$urls = argon_astro_extract_content_image_urls($html, 'https://cms.example/');
assert_true(in_array('https://cms.example/wp-content/uploads/a.jpg', $urls, true), 'plain img src extracted');
assert_true(in_array('https://cms.example/wp-content/uploads/a-300x200.jpg', $urls, true), 'same-origin srcset extracted');
assert_true(!in_array('https://other.example/ext.jpg', $urls, true), 'cross-origin srcset excluded');

$cfg_urls = argon_astro_extract_config_image_urls(array(
    'banner' => array('backgroundUrl' => 'https://cms.example/wp-content/uploads/banner.png'),
    'background' => array('url' => 'https://cms.example/wp-content/uploads/bg.png', 'darkUrl' => 'https://cdn.other/dark.png'),
    'sidebar' => array('authorImage' => 'https://cms.example/wp-content/uploads/avatar.png'),
    'friendLinks' => array(array('name' => 'F', 'avatar' => 'https://cms.example/wp-content/uploads/friend.png')),
), 'https://cms.example');
assert_true(count($cfg_urls) === 4, 'config same-origin images extracted');

// 2. Comments: QQ number accepted, private masked, markdown/pin/moderation respected
assert_true(argon_astro_is_valid_contact('123456789'), 'QQ number accepted');
assert_true(argon_astro_is_valid_contact('user@example.com'), 'email accepted');
assert_true(!argon_astro_is_valid_contact('123'), 'short numeric rejected');

update_option('comment_moderation', 1);
update_option('argon_astro_auto_approve', false);
$created = argon_astro_create_comment(new WP_REST_Request(array(
    'postId' => 10,
    'author' => 'QQUser',
    'email' => '123456789',
    'content' => '**secret** note',
    'markdown' => true,
    'private' => true,
)));
assert_true(!is_wp_error($created) && $created['approved'] === false, 'moderation queue respected');
$cid = $created['id'];
assert_true(get_comment_meta($cid, 'qq_number', true) === '123456789', 'QQ stored in meta');

$pinned = argon_astro_pin_comment(new WP_REST_Request(array('id' => $cid, 'pinned' => true)));
assert_true($pinned['pinned'] === true, 'comment pinned');

$exported = argon_astro_export_comments(10);
assert_true(count($exported) === 1, 'comment exported');
assert_true($exported[0]['private'] === true && $exported[0]['content'] === '', 'private comment content masked');
assert_true($exported[0]['markdown'] === true && $exported[0]['pinned'] === true, 'markdown and pinned exported');

// 3. Webhooks: hooks registered, reason passed, log capped at 50
assert_true(!empty($GLOBALS['__wp_actions']['add_option_argon_astro_config']), 'add_option hook registered');
assert_true(!empty($GLOBALS['__wp_actions']['save_post']), 'save_post hook registered');
assert_true(!empty($GLOBALS['__wp_actions']['transition_post_status']), 'transition_post_status hook registered');
assert_true(!empty($GLOBALS['__wp_actions']['deleted_post']), 'deleted_post hook registered');

argon_astro_schedule_deploy('post-save:post');
assert_true(get_option('argon_astro_pending_reason') === 'post-save:post', 'pending reason stored');

update_option('argon_astro_hooks', array('generic' => 'https://deploy.example/hook'));
update_option('argon_astro_hook_secret', 's3cr3t');
$seed = array();
for ($i = 0; $i < 55; $i++) {
    $seed[] = array('timestamp' => '2026-01-01T00:00:00Z', 'reason' => 'r' . $i, 'target' => 'generic', 'status' => 202, 'ok' => true);
}
argon_astro_append_webhook_log($seed);
assert_true(count(get_option('argon_astro_webhook_log')) === 50, 'webhook log capped at 50');

argon_astro_run_scheduled_deploy();
$log = get_option('argon_astro_webhook_log');
$last = end($log);
assert_true($last['reason'] === 'post-save:post' && $last['ok'] === true, 'scheduled deploy passed actual reason');

// 4. Admin JSON validation preserves previous config and records error
update_option('argon_astro_config', array('version' => 1, 'site' => array('title' => 'Kept')));
$sanitized = argon_astro_sanitize_config('{invalid json');
assert_true($sanitized['site']['title'] === 'Kept', 'invalid JSON preserves previous config');
assert_true(count($GLOBALS['__wp_settings_errors']) === 1, 'invalid JSON records settings error');

// 5. Flat original Argon JSON -> nested config mapping & WP-only/comment/source stripping
$real_argon_flat = array(
    'argon_theme_color' => '#94cced',
    'argon_show_customize_theme_color_picker' => true,
    'argon_enable_immersion_color' => 'true',
    'argon_darkmode_autoswitch' => 'alwayson',
    'argon_enable_amoled_dark' => 'true',
    'argon_card_radius' => '8.5',
    'argon_card_shadow' => 'default',
    'argon_page_layout' => 'double-reverse',
    'argon_article_list_waterflow' => '2and3',
    'argon_article_list_layout' => '2',
    'argon_font' => 'sans-serif',
    'argon_dateformat' => 'DMY',
    'argon_enable_headroom' => 'true',
    'argon_toolbar_title' => 'My Blog',
    'argon_toolbar_icon' => 'https://cms.example/icon.png',
    'argon_toolbar_icon_link' => '/',
    'argon_toolbar_blur' => 'true',
    'argon_banner_title' => 'Hello Argon',
    'argon_banner_subtitle' => 'Subtitle',
    'argon_banner_size' => 'fullscreen',
    'argon_page_background_banner_style' => 'transparent',
    'argon_show_toolbar_mask' => 'true',
    'argon_banner_background_url' => '--bing--',
    'argon_banner_background_color_type' => 'shape-default',
    'argon_banner_background_hide_shapes' => 'true',
    'argon_enable_banner_title_typing_effect' => 'true',
    'argon_banner_typing_effect_interval' => '120',
    'argon_page_background_url' => 'https://cms.example/bg.jpg',
    'argon_page_background_dark_url' => 'https://cms.example/bg-dark.jpg',
    'argon_page_background_opacity' => '0.85',
    'argon_sidebar_banner_title' => 'Side Title',
    'argon_sidebar_banner_subtitle' => '--hitokoto--',
    'argon_sidebar_auther_name' => 'Luochancy',
    'argon_sidebar_auther_image' => 'https://cms.example/avatar.png',
    'argon_sidebar_author_description' => 'Builder',
    'argon_sidebar_announcement' => '<b>Notice</b>',
    'argon_fab_show_settings_button' => 'true',
    'argon_fab_show_darkmode_button' => 'true',
    'argon_fab_show_gotocomment_button' => 'false',
    'argon_seo_description' => 'SEO desc',
    'argon_seo_keywords' => 'argon,astro',
    'argon_article_meta' => 'categories|time|views|comments',
    'argon_show_readingtime' => 'true',
    'argon_reading_speed' => '320',
    'argon_reading_speed_en' => '180',
    'argon_reading_speed_code' => '40',
    'argon_show_thumbnail_in_banner_in_content_page' => 'true',
    'argon_first_image_as_thumbnail_by_default' => 'false',
    'argon_reference_list_title' => 'References',
    'argon_show_sharebtn' => 'domestic',
    'argon_show_headindex_number' => 'true',
    'argon_donate_qrcode_url' => 'https://cms.example/qr.png',
    'argon_additional_content_after_post' => '<p>Thanks</p>',
    'argon_related_post' => 'category,tag',
    'argon_related_post_sort_orderby' => 'title',
    'argon_related_post_sort_order' => 'ASC',
    'argon_related_post_limit' => '6',
    'argon_article_header_style' => 'article-header-style-2',
    'argon_outdated_info_time_type' => 'modifiedtime',
    'argon_outdated_info_days' => '180',
    'argon_outdated_info_tip_type' => 'toast',
    'argon_outdated_info_tip_content' => 'Updated %date_delta% days ago.',
    'argon_archives_timeline_show_month' => 'false',
    'argon_archives_timeline_url' => '/archives/',
    'argon_footer_html' => '<p>Footer</p>',
    'argon_hide_footer_author' => 'true',
    'argon_enable_code_highlight' => 'true',
    'argon_code_theme' => 'github-dark',
    'argon_code_highlight_hide_linenumber' => 'true',
    'argon_code_highlight_break_line' => 'true',
    'argon_code_highlight_transparent_linenumber' => 'true',
    'argon_disable_codeblock_style' => 'false',
    'argon_math_render' => 'mathjax3',
    'argon_mathjax_cdn_url' => '//cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml-full.js',
    'argon_mathjax_v2_cdn_url' => '',
    'argon_katex_cdn_url' => '//cdn.jsdelivr.net/npm/katex@0.16.11/dist/',
    'argon_enable_lazyload' => 'true',
    'argon_lazyload_threshold' => '600',
    'argon_lazyload_effect' => 'slideDown',
    'argon_lazyload_loading_style' => '5',
    'argon_enable_fancybox' => 'false',
    'argon_enable_zoomify' => 'true',
    'argon_zoomify_duration' => '250',
    'argon_zoomify_easing' => 'ease-out',
    'argon_zoomify_scale' => '0.85',
    'argon_enable_pangu' => 'article|shuoshuo',
    'argon_custom_html_head' => '<meta name="x" content="1">',
    'argon_custom_html_foot' => '<script>console.log(1)</script>',
    'argon_enable_smoothscroll_type' => '1_pulse',
    'argon_enable_into_article_animation' => 'false',
    'argon_disable_pjax_animation' => 'true',
    'argon_pjax_disabled' => 'true',
    'argon_hide_categories' => 'true',
    'argon_home_show_shuoshuo' => 'false',
    'argon_fold_long_shuoshuo' => 'true',
    'argon_fold_long_comments' => 'true',
    'argon_hide_shortcode_in_preview' => 'false',
    'argon_trim_words_count' => '140',
    'argon_enable_mobile_scale' => 'true',
    'argon_disable_googlefont' => 'false',
    'argon_enable_search_filters' => 'true',
    'argon_search_filters_type' => '*post,*page,shuoshuo',
    // WP-only and disallowed keys that must be ignored/stripped.
    'argon_update_source' => 'github',
    'argon_enable_login_css' => 'true',
    'argon_wp_path' => '/blog/',
    'argon_assets_path' => 'jsdelivr',
    'comments' => array('provider' => 'giscus'),
    'source' => 'local',
);

$nested = argon_astro_flat_to_nested($real_argon_flat);
assert_true($nested['appearance']['color'] === '#94cced', 'theme color mapped');
assert_true($nested['appearance']['immersionColor'] === true, 'immersion color mapped');
assert_true($nested['appearance']['darkmode'] === 'always', 'darkmode alwayson mapped to always');
assert_true($nested['appearance']['cardRadius'] === 8.5, 'float card radius preserved');
assert_true($nested['appearance']['cardShadow'] === 'small', 'card shadow default mapped to small');
assert_true($nested['appearance']['font'] === 'sans', 'sans-serif mapped to sans');
assert_true($nested['appearance']['layout'] === 'double-reverse', 'page layout mapped');
assert_true($nested['article']['waterflow'] === '2and3', 'waterflow 2and3 mapped');
assert_true($nested['banner']['size'] === 'fullscreen', 'banner fullscreen mapped');
assert_true($nested['banner']['backgroundUrl'] === '--bing--', 'banner --bing-- token preserved');
assert_true($nested['background']['bannerStyle'] === 'transparent', 'transparent banner style mapped');
assert_true($nested['sidebar']['authorName'] === 'Luochancy', 'upstream auther typo mapped to authorName');
assert_true($nested['sidebar']['bannerSubtitle'] === '--hitokoto--', 'hitokoto token preserved');
assert_true($nested['article']['meta'] === array('categories', 'time', 'views', 'comments'), 'article meta order preserved');
assert_true($nested['article']['outdatedTipType'] === 'info', 'outdated toast mapped to info');
assert_true($nested['article']['outdatedTimeType'] === 'relative', 'outdated modifiedtime mapped to relative');
assert_true($nested['article']['outdatedDays'] === 180, 'outdated days mapped');
assert_true($nested['article']['relatedOrderBy'] === 'title' && $nested['article']['relatedOrder'] === 'asc', 'related sort mapped');
assert_true($nested['article']['searchFilters'] === array('post', 'page', 'shuoshuo'), 'search filters parsed without asterisks');
assert_true($nested['article']['showShare'] === true, 'domestic sharebtn mapped to true');
assert_true($nested['motion']['math'] === 'mathjax3', 'math renderer mapped');
assert_true($nested['motion']['mathjaxCdn'] === 'https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml-full.js', 'protocol-relative mathjax CDN upgraded to https');
assert_true($nested['motion']['pjax'] === false && $nested['motion']['pjaxAnimation'] === false, 'inverted pjax flags mapped');
assert_true($nested['motion']['pangu'] === 'article|shuoshuo', 'pangu combination preserved');
assert_true(!isset($nested['comments']) && !isset($nested['source']), 'comments and source stripped');

// 6. Nested -> flat round-trip export compatible with original Argon JSON
$roundtrip = argon_astro_nested_to_flat($nested);
assert_true($roundtrip['argon_theme_color'] === '#94cced', 'round-trip theme color');
assert_true($roundtrip['argon_enable_immersion_color'] === 'true', 'round-trip immersion color');
assert_true($roundtrip['argon_darkmode_autoswitch'] === 'alwayson', 'round-trip darkmode alwayson');
assert_true($roundtrip['argon_card_shadow'] === 'default', 'round-trip card shadow');
assert_true($roundtrip['argon_font'] === 'sans-serif', 'round-trip font');
assert_true($roundtrip['argon_article_list_waterflow'] === '2and3', 'round-trip waterflow 2and3');
assert_true($roundtrip['argon_banner_size'] === 'fullscreen', 'round-trip banner fullscreen');
assert_true($roundtrip['argon_outdated_info_tip_type'] === 'toast', 'round-trip outdated tip toast');
assert_true($roundtrip['argon_outdated_info_time_type'] === 'modifiedtime', 'round-trip outdated time modifiedtime');
assert_true($roundtrip['argon_article_meta'] === 'categories|time|views|comments', 'round-trip article meta string');
assert_true($roundtrip['argon_sidebar_auther_name'] === 'Luochancy', 'round-trip auther key');
assert_true($roundtrip['argon_pjax_disabled'] === 'true' && $roundtrip['argon_disable_pjax_animation'] === 'true', 'round-trip inverted pjax flags');
assert_true($roundtrip['argon_related_post_sort_order'] === 'ASC', 'round-trip uppercase ASC');

// Outdated days <= 0 maps to -1 in upstream Argon
$disabled_outdated = argon_astro_flat_to_nested(array('argon_outdated_info_days' => '-1'));
assert_true($disabled_outdated['article']['outdatedDays'] === 0, '-1 outdated days becomes 0');
$disabled_flat = argon_astro_nested_to_flat($disabled_outdated);
assert_true($disabled_flat['argon_outdated_info_days'] === '-1', '0 outdated days exports as -1');

// 7. Legacy WP option import reads argon_* options from DB
update_option('argon_theme_color', '#fa7298');
update_option('argon_banner_size', 'mini');
update_option('argon_update_source', 'github');
$legacy = argon_astro_import_legacy_options();
assert_true($legacy['appearance']['color'] === '#fa7298', 'legacy option theme color imported');
assert_true($legacy['banner']['size'] === 'mini', 'legacy option banner size imported');
assert_true(argon_astro_count_legacy_options() >= 2, 'legacy option counter detects existing options');

// 8. Visual form sanitization & backward-compatible flat JSON string sanitization
update_option('argon_astro_config', array(
    'version' => 1,
    'toolbar' => array('menu' => array(array('label' => 'Home', 'href' => '/', 'children' => array()))),
));
$form_sanitized = argon_astro_sanitize_config(array(
    'appearance' => array(
        'color' => '#94CCED',
        'allowColorPicker' => '0',
        'cardRadius' => '999',
        'cardShadow' => 'big',
        'font' => 'serif',
        'layout' => 'invalid-layout',
        'darkmode' => 'time',
        'immersionColor' => '1',
    ),
    'banner' => array(
        'size' => 'fullscreen',
        'backgroundUrl' => 'javascript:alert(1)',
    ),
    'article' => array(
        '_meta_present' => '1',
        'meta' => array('categories', 'time', 'bogus'),
        'waterflow' => '2and3',
        'trimWords' => '2000',
    ),
    'friendLinks' => array(
        array('name' => 'Argon', 'url' => 'https://example.com', 'description' => 'Theme', 'avatar' => 'https://cms.example/a.png'),
        array('name' => '', 'url' => '', 'description' => '', 'avatar' => ''),
    ),
    'comments' => array('provider' => 'waline'),
    'source' => 'local',
));
assert_true($form_sanitized['appearance']['color'] === '#94cced', 'visual form hex color lowercased');
assert_true($form_sanitized['appearance']['allowColorPicker'] === false, 'visual form checkbox 0 becomes false');
assert_true($form_sanitized['appearance']['cardRadius'] === 30, 'visual form cardRadius clamped to 30');
assert_true($form_sanitized['appearance']['layout'] === 'double', 'invalid layout falls back to double');
assert_true($form_sanitized['banner']['backgroundUrl'] === '', 'javascript URL rejected');
assert_true($form_sanitized['article']['meta'] === array('categories', 'time'), 'bogus meta token filtered');
assert_true($form_sanitized['article']['trimWords'] === 1000, 'trimWords clamped to 1000');
assert_true(count($form_sanitized['friendLinks']) === 1 && $form_sanitized['friendLinks'][0]['name'] === 'Argon', 'empty friend link rows skipped');
assert_true(count($form_sanitized['toolbar']['menu']) === 1, 'existing toolbar.menu preserved across visual form saves');
assert_true(!isset($form_sanitized['comments']) && !isset($form_sanitized['source']), 'comments and source stripped in form sanitization');

// Backward compatibility: pasting flat Argon JSON string directly into sanitize callback
$flat_json_sanitized = argon_astro_sanitize_config(json_encode(array(
    'argon_theme_color' => '#009688',
    'argon_banner_size' => 'fullscreen',
)));
assert_true($flat_json_sanitized['appearance']['color'] === '#009688', 'flat Argon JSON string accepted by sanitize callback');
assert_true($flat_json_sanitized['banner']['size'] === 'fullscreen', 'flat Argon JSON banner size preserved');

// Merge helper replaces lists instead of merging numeric indices
$merged_lists = argon_astro_merge_config(
    array('article' => array('meta' => array('time', 'categories', 'views'))),
    array('article' => array('meta' => array('time')))
);
assert_true($merged_lists['article']['meta'] === array('time'), 'merge_config replaces list arrays whole');

// 9. Smoke-test visual admin view rendering with zero warnings and balanced forms
function current_user_can($cap) { return true; }
function get_bloginfo($key = '') {
    if ($key === 'language') return 'zh-CN';
    if ($key === 'description') return 'WP Tagline';
    return 'Demo Blog';
}
function esc_attr($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }
function esc_html($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }
function esc_textarea($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }
function esc_url($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }
function selected($a, $b = true, $echo = true) { $r = ((string) $a === (string) $b) ? ' selected="selected"' : ''; if ($echo) echo $r; return $r; }
function checked($a, $b = true, $echo = true) { $r = ((string) $a === (string) $b) ? ' checked="checked"' : ''; if ($echo) echo $r; return $r; }
function settings_fields($g) { echo '<input type="hidden" name="option_page" value="' . esc_attr($g) . '" />'; }
function settings_errors($s = '') {}
function submit_button($t = 'Save', $type = 'primary', $name = 'submit', $wrap = true) { echo '<button type="submit">' . esc_html($t) . '</button>'; }
function admin_url($p = '') { return '/wp-admin/' . $p; }
function wp_nonce_field($a) { echo '<input type="hidden" name="_wpnonce" value="test" />'; }

$prev_err = error_reporting(E_ALL);
ob_start();
argon_astro_render_settings();
$admin_html = ob_get_clean();
error_reporting($prev_err);
assert_true(strpos($admin_html, 'id="tab-appearance"') !== false, 'appearance tab rendered');
assert_true(strpos($admin_html, 'id="tab-header"') !== false, 'header & cover tab rendered');
assert_true(strpos($admin_html, 'id="tab-content"') !== false, 'content & sidebar tab rendered');
assert_true(strpos($admin_html, 'id="tab-effects"') !== false, 'rendering & interactions tab rendered');
assert_true(strpos($admin_html, 'id="tab-headless"') !== false, 'headless & deploy tab rendered');
assert_true(strpos($admin_html, 'id="tab-backup"') !== false, 'import & export tab rendered');
assert_true(strpos($admin_html, 'id="sec-code-highlight"') !== false, 'dedicated code highlight card rendered');
assert_true(strpos($admin_html, 'id="sec-math"') !== false, 'dedicated math card rendered');
assert_true(strpos($admin_html, 'id="argon_math_selector"') !== false, 'single-select math engine selector rendered');
assert_true(strpos($admin_html, 'id="sec-fab"') !== false, 'dedicated FAB card rendered');
assert_true(strpos($admin_html, 'id="sec-friend-links"') !== false, 'dedicated friend links card rendered');
assert_true(strpos($admin_html, 'id="sec-agent"') !== false, 'dedicated AI agent card rendered');
assert_true(strpos($admin_html, 'id="sec-ci-hooks"') !== false, 'dedicated CI hooks card rendered');
assert_true(strpos($admin_html, 'id="argon_ci_provider_selector"') !== false, 'single-select CI provider selector rendered');
assert_true(strpos($admin_html, 'id="argon_save_submit"') !== false, 'save cooldown submit button rendered');
assert_true(strpos($admin_html, 'data-cooldown-seconds="60"') !== false, '60-second save cooldown metadata rendered');

// 11. Single-select CI provider dispatch only fires the chosen provider
$GLOBALS['__wp_remote_posts'] = array();
update_option('argon_astro_hooks', array(
    'provider' => 'cloudflare',
    'cloudflare' => 'https://cf.example/hook',
    'vercel' => 'https://vercel.example/hook',
    'github_repo' => 'org/repo',
    'github_token' => 'tok',
));
$cf_only = argon_astro_dispatch_hooks('provider-test');
assert_true(count($cf_only) === 1 && $cf_only[0]['target'] === 'cloudflare', 'only selected CI provider dispatched');
update_option('argon_astro_hooks', array('provider' => 'none', 'cloudflare' => 'https://cf.example/hook'));
$none_dispatched = argon_astro_dispatch_hooks('provider-none');
assert_true(count($none_dispatched) === 0, 'provider=none dispatches no hooks');
assert_true(strpos($admin_html, 'id="argon_admin_nav"') !== false, 'left vertical workspace nav rendered');
assert_true(strpos($admin_html, 'id="argon_admin_search"') !== false, 'cross-tab quick search rendered');
assert_true(substr_count($admin_html, 'class="argon-preset-swatch"') === 12, '12 Argon preset color swatches rendered');
assert_true(substr_count($admin_html, '<form ') === substr_count($admin_html, '</form>'), 'all HTML forms balanced (no nested forms)');
assert_true(strpos($admin_html, 'argon_theme_color') !== false, 'Argon JSON export embedded cleanly');
assert_true(strpos($admin_html, 'argon_astro_config[agent][llmsTxt]') !== false, 'agent browsing toggles rendered');
assert_true(strpos($admin_html, 'argon_astro_config[agent][allowTrainingCrawlers]') !== false, 'agent crawler toggle rendered');

// 10. Agent browsing config sanitization
$agent_sanitized = argon_astro_sanitize_nested_config(array(
    'agent' => array(
        'enabled' => '1',
        'robotsTxt' => '0',
        'llmsTxt' => '1',
        'markdownMirrors' => '0',
        'jsonIndex' => '1',
        'jsonLd' => '0',
        'webMcp' => '1',
        'allowTrainingCrawlers' => '0',
        'customRobotsTxt' => "User-agent: *\r\nDisallow: /private/\r\n",
        'customLlmsTxt' => "# Custom LLMs\n\n- [Home](/)",
    ),
));
assert_true($agent_sanitized['agent']['enabled'] === true, 'agent.enabled preserved');
assert_true($agent_sanitized['agent']['robotsTxt'] === false, 'agent.robotsTxt sanitized to false');
assert_true($agent_sanitized['agent']['markdownMirrors'] === false, 'agent.markdownMirrors sanitized to false');
assert_true($agent_sanitized['agent']['allowTrainingCrawlers'] === false, 'agent.allowTrainingCrawlers sanitized to false');
assert_true($agent_sanitized['agent']['customRobotsTxt'] === "User-agent: *\nDisallow: /private/", 'customRobotsTxt preserves multiline text');
assert_true($agent_sanitized['agent']['customLlmsTxt'] === "# Custom LLMs\n\n- [Home](/)", 'customLlmsTxt preserves multiline markdown');

// 12. Site fields default empty in schema and fall back per-field to WordPress settings on REST export
$defaults = argon_astro_schema_defaults();
assert_true($defaults['site']['title'] === '' && $defaults['site']['description'] === '' && $defaults['site']['language'] === '', 'site fields default empty');
update_option('argon_astro_config', array(
    'version' => 1,
    'site' => array('title' => '', 'description' => 'Custom SEO Desc', 'language' => '', 'url' => ''),
));
$pub = argon_astro_public_config();
assert_true($pub['site']['title'] === 'Demo Blog', 'empty site.title falls back to WP blogname');
assert_true($pub['site']['language'] === 'zh-CN', 'empty site.language falls back to WP language');
assert_true($pub['site']['description'] === 'Custom SEO Desc', 'non-empty site.description overrides WP tagline');
assert_true(!array_key_exists('url', $pub['site']), 'empty site.url omitted so local site.url stays intact');

// 13. Nav menus, icon extraction, same-origin URL relativization, bookmarks fallback, and permalinks
$GLOBALS['__wp_nav_locations'] = array(
    'toolbar_menu' => 1,
    'leftbar_menu' => 2,
    'leftbar_author_links' => 3,
);
$GLOBALS['__wp_nav_items'] = array(
    1 => array(
        (object) array('ID' => 11, 'menu_item_parent' => 0, 'title' => '<i class="fa fa-home"></i> 首页', 'url' => 'https://cms.example/', 'target' => '', 'classes' => array()),
        (object) array('ID' => 12, 'menu_item_parent' => 0, 'title' => '<i class="fa fa-link"></i> 友人帐', 'url' => 'https://cms.example/links.html', 'target' => '', 'classes' => array()),
        (object) array('ID' => 13, 'menu_item_parent' => 0, 'title' => '<i class="fa fa-subway"></i> 开往', 'url' => 'https://www.travellings.cn/go.html', 'target' => '_blank', 'classes' => array()),
    ),
    2 => array(
        (object) array('ID' => 21, 'menu_item_parent' => 0, 'title' => '关于我', 'url' => 'https://cms.example/aboutme.html', 'target' => '', 'classes' => array('fa', 'fa-user-o')),
    ),
    3 => array(
        (object) array('ID' => 31, 'menu_item_parent' => 0, 'title' => '<i class="fa fa-github"></i> GitHub', 'url' => 'https://github.com/Luochancy', 'target' => '_blank', 'classes' => array()),
    ),
);
$GLOBALS['__wp_bookmarks'] = array(
    (object) array('link_name' => 'Friend A', 'link_url' => 'https://friend-a.example', 'link_description' => 'Hello', 'link_image' => 'https://cdn.example/a.png'),
);
update_option('argon_astro_config', array(
    'version' => 1,
    'toolbar' => array('iconLink' => 'https://cms.example/'),
    'article' => array('archivesUrl' => 'https://cms.example/archive.html'),
));
$pub_menus = argon_astro_public_config();
assert_true($pub_menus['toolbar']['iconLink'] === '/', 'same-origin toolbar.iconLink relativized');
assert_true($pub_menus['article']['archivesUrl'] === '/archive.html', 'same-origin archivesUrl relativized');
assert_true(count($pub_menus['toolbar']['menu']) === 3, 'toolbar_menu exported');
assert_true($pub_menus['toolbar']['menu'][0]['label'] === '首页' && $pub_menus['toolbar']['menu'][0]['icon'] === 'fa fa-home' && $pub_menus['toolbar']['menu'][0]['href'] === '/', 'menu icon & same-origin href parsed');
assert_true($pub_menus['toolbar']['menu'][1]['href'] === '/links.html', 'menu .html permalink relativized');
assert_true($pub_menus['toolbar']['menu'][2]['href'] === 'https://www.travellings.cn/go.html' && $pub_menus['toolbar']['menu'][2]['target'] === '_blank', 'external menu href & target preserved');
assert_true($pub_menus['sidebar']['menu'][0]['icon'] === 'fa fa-user-o' && $pub_menus['sidebar']['menu'][0]['href'] === '/aboutme.html', 'leftbar_menu class icon parsed');
assert_true($pub_menus['sidebar']['authorLinks'][0]['label'] === 'GitHub' && $pub_menus['sidebar']['authorLinks'][0]['icon'] === 'fa fa-github', 'leftbar_author_links exported');
assert_true(count($pub_menus['friendLinks']) === 1 && $pub_menus['friendLinks'][0]['name'] === 'Friend A', 'WP bookmarks exported to friendLinks');

assert_true(argon_astro_relative_permalink((object) array('permalink' => 'https://cms.example/archives/707.html')) === '/archives/707.html', 'numeric .html permalink preserved');
assert_true(argon_astro_relative_permalink((object) array('permalink' => 'https://cms.example/aboutme.html')) === '/aboutme.html', 'page .html permalink preserved');
assert_true(argon_astro_relative_permalink((object) array('permalink' => 'https://cms.example/?p=707')) === '', 'plain query permalink ignored');
$GLOBALS['__wp_page_templates'] = array(50 => 'timeline.php', 51 => 'msgboard.php', 52 => 'shuoshuo.php');
assert_true(argon_astro_resolve_page_template((object) array('ID' => 50, 'post_type' => 'page')) === 'timeline', 'timeline.php mapped');
assert_true(argon_astro_resolve_page_template((object) array('ID' => 51, 'post_type' => 'page')) === 'message', 'msgboard.php mapped');
assert_true(argon_astro_resolve_page_template((object) array('ID' => 52, 'post_type' => 'page')) === 'shuoshuo', 'shuoshuo.php mapped');

// 14. Comment duplicate & flood errors return clean 409/429 REST status instead of 500
$GLOBALS['__wp_force_comment_error'] = new WP_Error('comment_duplicate', 'Duplicate comment detected', 409);
$dup_res = argon_astro_create_comment(new WP_REST_Request(array(
    'postId' => 10,
    'author' => 'Tester',
    'email' => 'test@example.com',
    'content' => 'dup',
)));
assert_true(is_wp_error($dup_res) && is_array($dup_res->get_error_data()) && $dup_res->get_error_data()['status'] === 409, 'duplicate comment normalized to REST 409');
$GLOBALS['__wp_force_comment_error'] = new WP_Error('comment_flood', 'Too fast', 429);
$flood_res = argon_astro_create_comment(new WP_REST_Request(array(
    'postId' => 10,
    'author' => 'Tester',
    'email' => 'test@example.com',
    'content' => 'fast',
)));
assert_true(is_wp_error($flood_res) && is_array($flood_res->get_error_data()) && $flood_res->get_error_data()['status'] === 429, 'comment flood normalized to REST 429');
$GLOBALS['__wp_force_comment_error'] = null;

// 15. Post unlock (timing-safe password check) & views increment & comment history & reply wp_mail & sidebar widget capture
$GLOBALS['__wp_posts'] = array(
    77 => (object) array('ID' => 77, 'post_status' => 'publish', 'post_password' => 's3cret!', 'post_title' => 'Locked', 'post_content' => '<p>Top secret <a href="https://cms.example/aboutme.html">about</a></p>'),
    88 => (object) array('ID' => 88, 'post_status' => 'publish', 'post_password' => '', 'post_title' => 'Open', 'post_content' => '<p>Open</p>'),
);
$bad_unlock = argon_astro_unlock_post(new WP_REST_Request(array('id' => 77, 'password' => 'wrong')));
assert_true(is_wp_error($bad_unlock) && $bad_unlock->get_error_data()['status'] === 403, 'wrong password returns 403');
$good_unlock = argon_astro_unlock_post(new WP_REST_Request(array('id' => 77, 'password' => 's3cret!')));
assert_true(!is_wp_error($good_unlock) && strpos($good_unlock['html'], 'Top secret') !== false && strpos($good_unlock['html'], 'href="/aboutme.html"') !== false, 'correct password unlocks post & relativizes links');

$GLOBALS['__wp_post_meta'][88]['views'] = 41;
$v_get = argon_astro_get_views(new WP_REST_Request(array('id' => 88)));
assert_true($v_get['views'] === 41, 'views GET returns existing count');
$v_inc = argon_astro_increment_views(new WP_REST_Request(array('id' => 88)));
assert_true($v_inc['views'] === 42, 'views POST increments count');

$GLOBALS['__wp_mails'] = array();
update_option('comment_moderation', 0);
$p_comment = argon_astro_create_comment(new WP_REST_Request(array(
    'postId' => 88,
    'author' => 'ParentUser',
    'email' => 'parent@example.com',
    'content' => 'First',
    'mailNotice' => true,
)));
$p_id = $p_comment['id'];
argon_astro_edit_comment(new WP_REST_Request(array('id' => $p_id, 'email' => 'parent@example.com', 'content' => 'First edited')));
$hist = argon_astro_comment_history(new WP_REST_Request(array('id' => $p_id)));
assert_true(!is_wp_error($hist) && count($hist['history']) === 1 && $hist['history'][0]['content'] === 'First', 'comment edit history returned');

argon_astro_create_comment(new WP_REST_Request(array(
    'postId' => 88,
    'parent' => $p_id,
    'author' => 'Replier',
    'email' => 'replier@example.com',
    'content' => 'Nice!',
)));
assert_true(count($GLOBALS['__wp_mails']) === 1 && $GLOBALS['__wp_mails'][0]['to'] === 'parent@example.com', 'reply email notification sent via wp_mail');

$GLOBALS['__wp_sidebars'] = array(
    'leftbar-tools' => '<div class="widget"><a href="https://cms.example/archive.html">归档</a><script>alert(1)</script></div>',
    'rightbar-tools' => '<div class="widget">Right Widget</div>',
);
$pub_widgets = argon_astro_public_config();
assert_true(strpos($pub_widgets['sidebar']['widgetsHtml'], 'href="/archive.html"') !== false && strpos($pub_widgets['sidebar']['widgetsHtml'], '<script>') === false, 'leftbar-tools captured, relativized, scripts stripped');
assert_true(strpos($pub_widgets['sidebar']['rightWidgetsHtml'], 'Right Widget') !== false, 'rightbar-tools captured');

echo "bridge php tests ok\n";
