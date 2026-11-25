<?php
/**
 * Modular Argon-styled WordPress Admin Settings Workspace for Argon Bridge.
 *
 * Splits settings into 6 focused workspace tabs with single-responsibility cards
 * (includes/views/tab-*.php), a sticky left navigation rail, live cross-tab search,
 * animated upstream Argon SMIL logo, and zero nested HTML forms.
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/views/tab-appearance.php';
require_once __DIR__ . '/views/tab-header.php';
require_once __DIR__ . '/views/tab-content.php';
require_once __DIR__ . '/views/tab-effects.php';
require_once __DIR__ . '/views/tab-headless.php';
require_once __DIR__ . '/views/tab-backup.php';

/**
 * Render a standard Yes/No select dropdown backed by boolean 1/0 values.
 *
 * @param string $name
 * @param bool   $current
 * @param string $true_label
 * @param string $false_label
 * @param string $extra_attrs
 * @return void
 */
function argon_astro_render_bool_select($name, $current, $true_label = '开启', $false_label = '关闭', $extra_attrs = '') {
    echo '<select name="' . esc_attr($name) . '"' . ($extra_attrs !== '' ? ' ' . $extra_attrs : '') . '>';
    echo '<option value="1"' . selected((bool) $current, true, false) . '>' . esc_html($true_label) . '</option>';
    echo '<option value="0"' . selected((bool) $current, false, false) . '>' . esc_html($false_label) . '</option>';
    echo '</select>';
}

/**
 * Render a select dropdown from an associative array of value => label pairs.
 *
 * @param string                $name
 * @param string                $current
 * @param array<string, string> $options
 * @param string                $extra_attrs
 * @return void
 */
function argon_astro_render_select($name, $current, array $options, $extra_attrs = '') {
    echo '<select name="' . esc_attr($name) . '"' . ($extra_attrs !== '' ? ' ' . $extra_attrs : '') . '>';
    foreach ($options as $val => $label) {
        echo '<option value="' . esc_attr((string) $val) . '"' . selected((string) $current, (string) $val, false) . '>' . esc_html($label) . '</option>';
    }
    echo '</select>';
}

/**
 * Render the complete Argon WordPress settings workspace.
 *
 * @return void
 */
function argon_astro_render_settings() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $cfg = argon_astro_effective_config();
    $hooks = get_option('argon_astro_hooks', array());
    if (!is_array($hooks)) {
        $hooks = array();
    }
    $log = get_option('argon_astro_webhook_log', array());
    if (!is_array($log)) {
        $log = array();
    }

    $ctx = array(
        'cfg' => $cfg,
        'site' => $cfg['site'],
        'app' => $cfg['appearance'],
        'banner' => $cfg['banner'],
        'bg' => $cfg['background'],
        'toolbar' => $cfg['toolbar'],
        'sidebar' => $cfg['sidebar'],
        'article' => $cfg['article'],
        'footer' => $cfg['footer'],
        'motion' => $cfg['motion'],
        'fab' => $cfg['fab'],
        'home' => $cfg['home'],
        'agent' => isset($cfg['agent']) && is_array($cfg['agent']) ? $cfg['agent'] : array(),
        'friend_links' => is_array($cfg['friendLinks']) ? $cfg['friendLinks'] : array(),
        'hooks' => $hooks,
        'secret' => (string) get_option('argon_astro_hook_secret', ''),
        'auto_approve' => (bool) get_option('argon_astro_auto_approve', false),
        'log' => $log,
        'legacy_count' => argon_astro_count_legacy_options(),
        'exported_argon_json' => (string) wp_json_encode(argon_astro_nested_to_flat($cfg), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'exported_astro_json' => (string) wp_json_encode($cfg, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'presets' => argon_astro_theme_color_presets(),
        'code_themes' => argon_astro_code_themes(),
        'meta_labels' => array(
            'time' => '发布时间',
            'edittime' => '修改时间',
            'views' => '浏览量',
            'comments' => '评论数',
            'categories' => '所属分类',
            'author' => '作者',
        ),
        'search_filter_labels' => array(
            'post' => '文章 (post)',
            'page' => '页面 (page)',
            'shuoshuo' => '说说 (shuoshuo)',
        ),
        'banner_gradients' => array(
            'shape-primary' => array('label' => '样式 1', 'css' => 'linear-gradient(150deg,#281483 15%,#8f6ed5 70%,#d782d9 94%)'),
            'shape-default' => array('label' => '样式 2', 'css' => 'linear-gradient(150deg,#7795f8 15%,#6772e5 70%,#555abf 94%)'),
            'shape-dark' => array('label' => '样式 3', 'css' => 'linear-gradient(150deg,#32325d 15%,#32325d 70%,#32325d 94%)'),
            'bg-gradient-success' => array('label' => '样式 4', 'css' => 'linear-gradient(87deg,#2dce89 0,#2dcecc 100%)'),
            'bg-gradient-info' => array('label' => '样式 5', 'css' => 'linear-gradient(87deg,#11cdef 0,#1171ef 100%)'),
            'bg-gradient-warning' => array('label' => '样式 6', 'css' => 'linear-gradient(87deg,#fb6340 0,#fbb140 100%)'),
            'bg-gradient-danger' => array('label' => '样式 7', 'css' => 'linear-gradient(87deg,#f5365c 0,#f56036 100%)'),
        ),
    );

    $nav_tabs = array(
        'appearance' => array('label' => '外观与布局', 'icon' => 'dashicons-art', 'cards' => array('sec-appearance-color' => '主题色彩与夜间模式', 'sec-appearance-layout' => '卡片、字体与页面布局')),
        'header' => array('label' => '导航与封面', 'icon' => 'dashicons-cover-image', 'cards' => array('sec-toolbar' => '顶部导航栏', 'sec-banner' => '顶部 Banner (封面)', 'sec-background' => '页面全局背景', 'sec-fab' => '浮动操作按钮 (FAB)')),
        'content' => array('label' => '内容与侧栏', 'icon' => 'dashicons-text-page', 'cards' => array('sec-site-seo' => '站点信息与 SEO', 'sec-sidebar' => '左侧栏与公告', 'sec-articles' => '文章与阅读体验', 'sec-home-archive' => '首页、说说、归档与搜索', 'sec-friend-links' => '友情链接', 'sec-footer-html' => '页脚与自定义代码')),
        'effects' => array('label' => '渲染与交互', 'icon' => 'dashicons-editor-code', 'cards' => array('sec-code-highlight' => '代码高亮', 'sec-math' => '数学公式渲染', 'sec-images' => '图片懒加载与灯箱', 'sec-motion' => '滚动、PJAX 与阅读增强')),
        'headless' => array('label' => '无头桥接与部署', 'icon' => 'dashicons-cloud-upload', 'cards' => array('sec-agent' => 'AI 智能体浏览支持', 'sec-headless-comments' => '无头评论桥接', 'sec-ci-hooks' => 'CI / 构建部署钩子', 'sec-deploy-log' => '手动部署与投递日志')),
        'backup' => array('label' => '导入与导出', 'icon' => 'dashicons-database-import', 'cards' => array('sec-import' => '导入配置', 'sec-export' => '导出备份 JSON')),
    );

    $notice = isset($_GET['argon_notice']) ? sanitize_text_field((string) $_GET['argon_notice']) : '';
    $imported_count = isset($_GET['argon_imported']) ? (int) $_GET['argon_imported'] : 0;
    $active_color = (string) $ctx['app']['color'];
    $last_saved_at = (int) get_option('argon_astro_last_saved_at', 0);
    $cooldown_seconds = defined('ARGON_ASTRO_RATE_LIMIT_SECONDS') ? (int) ARGON_ASTRO_RATE_LIMIT_SECONDS : 60;
    $cooldown_until = $last_saved_at > 0 ? ($last_saved_at + $cooldown_seconds) : 0;
    $initial_remaining = max(0, $cooldown_until - time());
    ?>
<div class="wrap argon-astro-admin">
    <style>
        .argon-astro-admin {
            --argon-accent: <?php echo esc_attr($active_color); ?>;
            max-width: none;
            margin: 14px 18px 24px 0;
            box-sizing: border-box;
        }
        .argon-hero {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px 22px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
            box-shadow: 0 1px 3px rgba(15, 23, 42, .04);
        }
        .argon-hero-brand {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }
        .argon-hero-meta h1 {
            margin: 0;
            padding: 0;
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.3;
        }
        .argon-hero-meta p {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 13px;
        }
        .argon-hero-tools {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .argon-search-box {
            position: relative;
            display: flex;
            align-items: center;
        }
        .argon-search-box .dashicons {
            position: absolute;
            left: 10px;
            color: #94a3b8;
            font-size: 16px;
            width: 16px;
            height: 16px;
            pointer-events: none;
        }
        .argon-search-box input {
            padding: 6px 28px 6px 32px;
            width: 280px;
            border: 1px solid #cbd5e1;
            border-radius: 999px;
            font-size: 13px;
            background: #f8fafc;
            transition: border-color .15s ease, background .15s ease, box-shadow .15s ease;
        }
        .argon-search-box input:focus {
            background: #fff;
            border-color: #5e72e4;
            box-shadow: 0 0 0 2px rgba(94, 114, 228, .15);
            outline: none;
        }
        .argon-search-clear {
            position: absolute;
            right: 8px;
            border: 0;
            background: transparent;
            color: #94a3b8;
            cursor: pointer;
            font-size: 15px;
            line-height: 1;
            display: none;
        }
        .argon-search-clear.is-visible { display: inline-block; }
        .argon-color-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 5px 11px;
            border-radius: 999px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            font-size: 12px;
            color: #334155;
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        }
        .argon-color-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--argon-accent, #5e72e4);
            box-shadow: 0 0 0 2px rgba(0, 0, 0, .06);
        }
        .argon-workspace {
            display: grid;
            grid-template-columns: 232px minmax(0, 1fr);
            gap: 18px;
            align-items: start;
        }
        .argon-nav-rail {
            position: sticky;
            top: 46px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, .04);
        }
        .argon-nav-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .argon-nav-item { margin-bottom: 4px; }
        .argon-nav-btn {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 10px 12px;
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: #334155;
            font-size: 13.5px;
            font-weight: 500;
            cursor: pointer;
            text-align: left;
            transition: background .15s ease, color .15s ease;
        }
        .argon-nav-btn:hover {
            background: #f1f5f9;
            color: #0f172a;
        }
        .argon-nav-btn.is-active {
            background: #eef2ff;
            color: #4f46e5;
            font-weight: 600;
        }
        .argon-nav-btn:focus {
            outline: none;
            box-shadow: none;
        }
        .argon-nav-btn:focus-visible {
            outline: 2px solid #5e72e4;
            outline-offset: 1px;
        }
        .argon-nav-btn .dashicons {
            font-size: 17px;
            width: 17px;
            height: 17px;
            color: inherit;
        }
        .argon-nav-sublist {
            list-style: none;
            margin: 4px 0 8px 26px;
            padding: 0 0 0 10px;
            border-left: 2px solid #e2e8f0;
            display: none;
        }
        .argon-nav-item.is-active .argon-nav-sublist { display: block; }
        .argon-nav-sublist li { margin: 2px 0; }
        .argon-nav-sublist a {
            display: block;
            padding: 4px 8px;
            border-radius: 5px;
            color: #64748b;
            font-size: 12.5px;
            text-decoration: none;
        }
        .argon-nav-sublist a:hover {
            background: #f8fafc;
            color: #4f46e5;
        }
        .argon-search-banner {
            display: none;
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            color: #3730a3;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 14px;
            font-size: 13px;
            align-items: center;
            justify-content: space-between;
        }
        .argon-search-banner.is-visible { display: flex; }
        .argon-tab-panel,
        .argon-tab-extra {
            display: none;
        }
        .argon-tab-panel.is-active,
        .argon-tab-extra.is-active,
        .argon-astro-admin.is-searching .argon-tab-panel,
        .argon-astro-admin.is-searching .argon-tab-extra {
            display: block;
        }
        .argon-astro-section {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px 24px;
            margin-bottom: 16px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, .04);
        }
        .argon-astro-section.is-search-hidden,
        .argon-astro-section tr.is-search-hidden {
            display: none;
        }
        .argon-card-head {
            padding-bottom: 12px;
            margin-bottom: 14px;
            border-bottom: 1px solid #f1f5f9;
        }
        .argon-card-head h2 {
            font-size: 17px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
        }
        .argon-card-head h2:before {
            content: '';
            background: #5e72e4;
            height: 16px;
            width: 5px;
            display: inline-block;
            border-radius: 10px;
            margin-right: 10px;
            flex-shrink: 0;
        }
        .argon-card-desc {
            margin: 5px 0 0 15px;
            color: #64748b;
            font-size: 12.5px;
        }
        .argon-astro-section .form-table {
            margin-top: 0;
        }
        .argon-astro-section .form-table th {
            width: 210px;
            padding: 13px 14px 13px 0;
            color: #1e293b;
            font-weight: 600;
        }
        .argon-astro-section .form-table td {
            padding: 11px 8px;
        }
        .argon-inline-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px 16px;
        }
        .argon-two-col {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 12px 18px;
        }
        .argon-field-block {
            margin: 0;
        }
        .argon-ci-selector {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 14px;
        }
        .argon-ci-choice {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 14px;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            background: #fff;
            color: #334155;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: border-color .15s ease, background .15s ease, color .15s ease;
        }
        .argon-ci-choice:hover {
            border-color: #94a3b8;
            background: #f8fafc;
        }
        .argon-ci-choice.is-selected {
            border-color: #5e72e4;
            background: #eef2ff;
            color: #4338ca;
            font-weight: 600;
        }
        .argon-ci-choice input[type="radio"] {
            margin: 0;
        }
        .argon-ci-panel {
            display: none;
            border: 1px solid #e2e8f0;
            border-radius: 9px;
            background: #f8fafc;
            padding: 16px 18px;
        }
        .argon-ci-panel.is-active {
            display: block;
        }
        .argon-ci-empty {
            color: #64748b;
            font-size: 13px;
        }
        .argon-ci-panel-sub {
            font-size: 12.5px;
            color: #64748b;
        }
        .argon-ci-pair {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px 18px;
        }
        .argon-ci-field label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }
        .argon-ci-field input {
            width: 100%;
            min-height: 36px;
            padding: 5px 10px;
            background: #fff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            box-sizing: border-box;
        }
        .argon-ci-field input:focus {
            border-color: #5e72e4;
            box-shadow: 0 0 0 2px rgba(94, 114, 228, .14);
            outline: none;
        }
        .argon-secret-input {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .argon-secret-input input {
            flex: 1;
            min-width: 0;
        }
        .argon-secret-input .button {
            flex-shrink: 0;
            height: 36px;
            line-height: 34px;
            padding: 0 10px;
        }
        .argon-save-status {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #475569;
        }
        .argon-save-status.is-cooldown {
            color: #b45309;
            font-weight: 600;
        }
        #argon_save_submit:disabled {
            opacity: .65;
            cursor: not-allowed;
        }
        @media screen and (max-width: 780px) {
            .argon-ci-pair {
                grid-template-columns: 1fr;
            }
        }
        .argon-toggle-stack {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .argon-toggle-row {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            padding: 8px 12px;
            border: 1px solid #f1f5f9;
            border-radius: 7px;
            background: #f8fafc;
            cursor: pointer;
        }
        .argon-toggle-row:hover {
            border-color: #cbd5e1;
        }
        .argon-color-picker-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .argon-color-input {
            height: 38px;
            width: 68px;
            padding: 2px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            cursor: pointer;
            background: #fff;
        }
        .argon-color-hex {
            height: 38px;
            width: 108px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 14px;
        }
        .argon-preset-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 14px;
        }
        .argon-preset-box {
            width: 60px;
            text-align: center;
            cursor: pointer;
            user-select: none;
        }
        .argon-preset-swatch {
            width: 42px;
            height: 42px;
            margin: 0 auto 5px;
            border-radius: 50%;
            transition: transform .18s ease, box-shadow .18s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .14);
            border: 3px solid transparent;
            box-sizing: border-box;
        }
        .argon-preset-box:hover .argon-preset-swatch { transform: scale(1.1); }
        .argon-preset-box.is-active .argon-preset-swatch { border-color: #0f172a; transform: scale(1.08); }
        .argon-preset-name { font-size: 12px; color: #475569; }
        .argon-immersion-demo { display: flex; align-items: center; flex-wrap: wrap; gap: 14px; margin-top: 12px; }
        .argon-immersion-box { width: 196px; height: 108px; border-radius: 6px; position: relative; box-shadow: 0 2px 6px rgba(0, 0, 0, .08); }
        .argon-immersion-card { position: absolute; left: 28px; right: 28px; top: 24px; bottom: 24px; border-radius: 4px; box-shadow: 0 2px 6px rgba(0, 0, 0, .08); }
        .argon-radio-cards { display: flex; flex-wrap: wrap; gap: 12px; }
        .argon-radio-card {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 10px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            background: #fafafa;
            transition: border-color .18s ease, background .18s ease;
        }
        .argon-radio-card:hover { border-color: #94a3b8; }
        .argon-radio-card.is-selected { border-color: #5e72e4; background: #f5f7ff; }
        .argon-radio-card svg { display: block; border-radius: 4px; }
        .argon-radio-inline label { display: inline-block; margin-right: 18px; }
        .argon-gradient-grid { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
        .argon-gradient-chip {
            width: 92px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            color: #fff;
            font-size: 12.5px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            transition: transform .18s ease;
            border: 2px solid transparent;
            box-sizing: border-box;
        }
        .argon-gradient-chip:hover { transform: scale(1.05); }
        .argon-gradient-chip.is-active { border-color: #0f172a; box-shadow: 0 3px 10px rgba(0, 0, 0, .2); }
        .argon-header-preview { font-size: 18px; font-weight: 600; position: relative; margin: 6px 24px 6px 0; display: inline-block; }
        .argon-header-preview.style-1:after { content: ''; display: block; position: absolute; background: #5e72e4; opacity: .25; border-radius: 15px; left: -2px; bottom: 0; width: 42px; height: 10px; }
        .argon-header-preview.style-2:before { content: ''; display: inline-block; background: #5e72e4; border-radius: 15px; width: 5px; height: 16px; margin-right: 8px; vertical-align: middle; }
        .argon-check-group { display: flex; flex-wrap: wrap; gap: 8px 10px; }
        .argon-check-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 999px;
            cursor: pointer;
            font-size: 13px;
        }
        .argon-math-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 10px;
        }
        .argon-math-option {
            padding: 12px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #fafbfc;
        }
        .argon-math-cdn { margin-top: 8px; padding-left: 22px; }
        .argon-math-cdn input { width: 100%; margin-top: 4px; }
        .argon-links-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .argon-links-table th, .argon-links-table td { padding: 8px 6px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        .argon-links-table input[type="text"] { width: 100%; }
        .argon-save-bar {
            position: sticky;
            bottom: 16px;
            z-index: 20;
            background: rgba(255, 255, 255, .96);
            backdrop-filter: blur(8px);
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 12px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            box-shadow: 0 6px 20px rgba(15, 23, 42, .10);
            margin-bottom: 18px;
        }
        .argon-save-bar.is-hidden { display: none; }
        @media screen and (max-width: 960px) {
            .argon-astro-admin { margin-right: 10px; }
            .argon-workspace { grid-template-columns: 1fr; }
            .argon-nav-rail { position: static; padding: 8px; }
            .argon-nav-list { display: flex; flex-wrap: wrap; gap: 4px; }
            .argon-nav-item { margin-bottom: 0; }
            .argon-nav-btn { width: auto; padding: 8px 12px; }
            .argon-nav-item.is-active .argon-nav-sublist { display: none; }
            .argon-search-box input { width: 200px; }
        }
    </style>

    <header class="argon-hero">
        <div class="argon-hero-brand">
            <!-- Upstream Argon animated SMIL SVG logo (click to replay animation). -->
            <svg id="argon_admin_logo" width="184" style="cursor: pointer; display: block;" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="673.92 415.2 510.83 151.8" aria-label="Argon" title="点击重播 Logo 动画">
                <g>
                    <g>
                        <path fill="rgba(94, 114, 228, 0)" stroke="#5E72E4" stroke-width="3" stroke-dasharray="402" stroke-dashoffset="402" d="M811.38,450.13c-2.2-3.81-7.6-6.93-12-6.93h-52.59c-4.4,0-9.8,3.12-12,6.93l-26.29,45.54c-2.2,3.81-2.2,10.05,0,13.86l26.29,45.54c2.2,3.81,7.6,6.93,12,6.93h52.59c4.4,0,9.8-3.12,12-6.93l26.29-45.54c2.2-3.81,2.2-10.05,0-13.86L811.38,450.13z">
                            <animate attributeName="stroke-width" begin="0.8s" values="3; 0" dur="0.9s" fill="freeze" calcMode="spline" keySplines="0.8 0 0.2 1"/>
                            <animate attributeName="stroke-dashoffset" begin="0.3s" values="402; 0" dur="0.9s" fill="freeze" calcMode="spline" keySplines="0.8 0 0.2 1"/>
                            <animate attributeName="fill" begin="0.8s" values="rgba(94, 114, 228, 0); rgba(94, 114, 228, 0.3)" dur="0.9s" fill="freeze" calcMode="spline" keySplines="0.8 0 0.2 1"/>
                        </path>
                    </g>
                    <g>
                        <path fill="rgba(94, 114, 228, 0)" d="M783.65,422.13c-2.2-3.81-7.6-6.93-12-6.93H715.6c-4.4,0-9.8,3.12-12,6.93l-28.03,48.54c-2.2,3.81-2.2,10.05,0,13.86l28.03,48.54c2.2,3.81,7.6,6.93,12,6.93h56.05c4.4,0,9.8-3.12,12-6.93l28.03-48.54c2.2-3.81,2.2-10.05,0-13.86L783.65,422.13z">
                            <animateTransform attributeName="transform" type="translate" begin="1.2s" values="27.73,28; 0,0" dur="1.0s" fill="freeze" calcMode="spline" keySplines="0.8 0 0.2 1"/>
                            <animate attributeName="fill" begin="1.2s" values="rgba(94, 114, 228, 0); rgba(94, 114, 228, 0.8)" dur="1.0s" fill="freeze" calcMode="spline" keySplines="0.8 0 0.2 1"/>
                        </path>
                    </g>
                </g>
                <g>
                    <g>
                        <clipPath id="argon_admin_clip_1"><rect x="887.47" y="441.31" width="68.76" height="83.07"/></clipPath>
                        <path clip-path="url(#argon_admin_clip_1)" fill="none" stroke="#5E72E4" stroke-width="0" stroke-linecap="square" stroke-linejoin="bevel" stroke-dasharray="190" d="M893.52,533.63l28.71-90.3l31.52,90.31">
                            <animate attributeName="stroke-width" begin="0.8s" values="3; 10" dur="0.9s" fill="freeze" calcMode="spline" keySplines="0.8 0 0.2 1"/>
                            <animate attributeName="stroke-dashoffset" begin="0.3s" values="190; 0" dur="0.9s" fill="freeze" calcMode="spline" keySplines="0.8 0 0.2 1"/>
                            <set attributeName="stroke-width" to="3" begin="0.3s"/>
                        </path>
                        <line clip-path="url(#argon_admin_clip_1)" fill="none" stroke="#5E72E4" stroke-width="0" stroke-miterlimit="10" stroke-dasharray="45" x1="940.44" y1="495.5" x2="905" y2="495.5">
                            <animate attributeName="stroke-width" begin="0.8s" values="3; 10" dur="0.9s" fill="freeze" calcMode="spline" keySplines="0.8 0 0.2 1"/>
                            <animate attributeName="stroke-dashoffset" begin="0.3s" values="-37; 0" dur="0.9s" fill="freeze" calcMode="spline" keySplines="0.8 0 0.2 1"/>
                            <set attributeName="stroke-width" to="3" begin="0.3s"/>
                        </line>
                    </g>
                    <g>
                        <path fill="none" stroke="#5E72E4" stroke-width="0" stroke-miterlimit="10" stroke-dasharray="56" d="M976.86,469.29v55.09">
                            <animate attributeName="stroke-width" begin="0.95s" values="3; 10" dur="0.9s" fill="freeze" calcMode="spline" keySplines="0.8 0 0.2 1"/>
                            <animate attributeName="stroke-dashoffset" begin="0.45s" values="56; 0" dur="0.9s" fill="freeze" calcMode="spline" keySplines="0.8 0 0.2 1"/>
                            <set attributeName="stroke-width" to="3" begin="0.45s"/>
                        </path>
                        <path fill="none" stroke="#5E72E4" stroke-width="0" stroke-miterlimit="10" stroke-dasharray="38" d="M976.86,489.77c0-9.68,7.85-17.52,17.52-17.52c3.5,0,6.76,1.03,9.5,2.8">
                            <animate attributeName="stroke-width" begin="0.95s" values="3; 10" dur="0.9s" fill="freeze" calcMode="spline" keySplines="0.8 0 0.2 1"/>
                            <animate attributeName="stroke-dashoffset" begin="0.45s" values="38; 0" dur="0.9s" fill="freeze" calcMode="spline" keySplines="0.8 0 0.2 1"/>
                            <set attributeName="stroke-width" to="3" begin="0.45s"/>
                        </path>
                    </g>
                    <g>
                        <path fill="none" stroke="#5E72E4" stroke-width="0" stroke-miterlimit="10" stroke-dasharray="124" d="M1057.86,492.08c0,10.94-8.87,19.81-19.81,19.81c-10.94,0-19.81-8.87-19.81-19.81s8.87-19.81,19.81-19.81C1048.99,472.27,1057.86,481.14,1057.86,492.08z">
                            <animate attributeName="stroke-width" begin="1.1s" values="3; 10" dur="0.9s" fill="freeze" calcMode="spline" keySplines="0.8 0 0.2 1"/>
                            <animate attributeName="stroke-dashoffset" begin="0.6s" values="-124; 0" dur="0.9s" fill="freeze" calcMode="spline" keySplines="0.8 0 0.2 1"/>
                            <set attributeName="stroke-width" to="3" begin="0.6s"/>
                        </path>
                        <path fill="none" stroke="#5E72E4" stroke-width="0" stroke-miterlimit="10" stroke-dasharray="110" d="M1057.84,467.27v54.05c0,10.94-8.87,19.81-19.81,19.81c-8.36,0-15.51-5.18-18.42-12.5">
                            <animate attributeName="stroke-width" begin="1.1s" values="3; 10" dur="0.9s" fill="freeze" calcMode="spline" keySplines="0.8 0 0.2 1"/>
                            <animate attributeName="stroke-dashoffset" begin="0.6s" values="110; 0" dur="0.9s" fill="freeze" calcMode="spline" keySplines="0.8 0 0.2 1"/>
                            <set attributeName="stroke-width" to="3" begin="0.6s"/>
                        </path>
                    </g>
                    <g>
                        <path fill="none" stroke="#5E72E4" stroke-width="0" stroke-miterlimit="10" stroke-dasharray="140" d="M1121.83,495.46c0,12.81-9.45,23.19-21.11,23.19s-21.11-10.38-21.11-23.19c0-12.81,9.45-23.19,21.11-23.19S1121.83,482.65,1121.83,495.46z">
                            <animate attributeName="stroke-width" begin="1.25s" values="3; 10" dur="0.9s" fill="freeze" calcMode="spline" keySplines="0.8 0 0.2 1"/>
                            <animate attributeName="stroke-dashoffset" begin="0.75s" values="-140; 0" dur="0.9s" fill="freeze" calcMode="spline" keySplines="0.8 0 0.2 1"/>
                            <set attributeName="stroke-width" to="3" begin="0.75s"/>
                        </path>
                    </g>
                    <g>
                        <path fill="none" stroke="#5E72E4" stroke-width="0" stroke-miterlimit="10" stroke-dasharray="57" d="M1143.78,524.38v-55.71">
                            <animate attributeName="stroke-width" begin="1.4s" values="3; 10" dur="0.9s" fill="freeze" calcMode="spline" keySplines="0.8 0 0.2 1"/>
                            <animate attributeName="stroke-dashoffset" begin="0.9s" values="-57; 0" dur="0.9s" fill="freeze" calcMode="spline" keySplines="0.8 0 0.2 1"/>
                            <set attributeName="stroke-width" to="3" begin="0.9s"/>
                        </path>
                        <path fill="none" stroke="#5E72E4" stroke-width="0" stroke-miterlimit="10" stroke-dasharray="90" d="M1143.95,490.15c0-9.88,8.01-17.9,17.9-17.9c9.88,0,17.9,8.01,17.9,17.9v34.23">
                            <animate attributeName="stroke-width" begin="1.4s" values="3; 10" dur="0.9s" fill="freeze" calcMode="spline" keySplines="0.8 0 0.2 1"/>
                            <animate attributeName="stroke-dashoffset" begin="0.9s" values="90; 0" dur="0.9s" fill="freeze" calcMode="spline" keySplines="0.8 0 0.2 1"/>
                            <set attributeName="stroke-width" to="3" begin="0.9s"/>
                        </path>
                    </g>
                </g>
            </svg>
            <div class="argon-hero-meta">
                <h1>主题设置</h1>
            </div>
        </div>
        <div class="argon-hero-tools">
            <span class="argon-color-pill" id="argon_live_color_pill" title="当前主题色">
                <span class="argon-color-dot" id="argon_live_color_dot"></span>
                <span id="argon_live_color_text"><?php echo esc_html($active_color); ?></span>
            </span>
            <div class="argon-search-box">
                <span class="dashicons dashicons-search" aria-hidden="true"></span>
                <input type="search" id="argon_admin_search" placeholder="搜索设置（高亮 / Webhook / 圆角）" autocomplete="off" />
                <button type="button" class="argon-search-clear" id="argon_admin_search_clear" aria-label="清除搜索">&times;</button>
            </div>
        </div>
    </header>

    <?php
    if (!empty($_GET['argon_deploy_triggered'])) {
        echo '<div class="notice notice-success is-dismissible"><p>已排队触发手动构建。</p></div>';
    }
    if ($notice === 'import_ok') {
        echo '<div class="notice notice-success is-dismissible"><p>配置已导入并保存。</p></div>';
    } elseif ($notice === 'import_error') {
        echo '<div class="notice notice-error is-dismissible"><p>导入失败：JSON 格式无效。</p></div>';
    } elseif ($notice === 'legacy_ok') {
        echo '<div class="notice notice-success is-dismissible"><p>已从原版 Argon 导入 ' . esc_html((string) $imported_count) . ' 项设置。</p></div>';
    } elseif ($notice === 'legacy_empty') {
        echo '<div class="notice notice-warning is-dismissible"><p>未检测到可导入的原版 Argon 设置。</p></div>';
    }
    settings_errors();
    ?>

    <div class="argon-workspace">
        <nav class="argon-nav-rail" id="argon_admin_nav" aria-label="设置分区导航">
            <ul class="argon-nav-list">
                <?php $first_tab = true; ?>
                <?php foreach ($nav_tabs as $tab_key => $tab_meta) : ?>
                    <li class="argon-nav-item<?php echo $first_tab ? ' is-active' : ''; ?>" data-nav-tab="<?php echo esc_attr($tab_key); ?>">
                        <button type="button" class="argon-nav-btn<?php echo $first_tab ? ' is-active' : ''; ?>" data-tab-target="<?php echo esc_attr($tab_key); ?>" aria-selected="<?php echo $first_tab ? 'true' : 'false'; ?>">
                            <span class="dashicons <?php echo esc_attr($tab_meta['icon']); ?>" aria-hidden="true"></span>
                            <span><?php echo esc_html($tab_meta['label']); ?></span>
                        </button>
                        <ul class="argon-nav-sublist">
                            <?php foreach ($tab_meta['cards'] as $card_id => $card_label) : ?>
                                <li><a href="#<?php echo esc_attr($card_id); ?>" data-jump-card="<?php echo esc_attr($card_id); ?>"><?php echo esc_html($card_label); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <?php $first_tab = false; ?>
                <?php endforeach; ?>
            </ul>
        </nav>

        <div class="argon-workspace-main">
            <div class="argon-search-banner" id="argon_search_banner">
                <span id="argon_search_summary"></span>
                <button type="button" class="button button-small" id="argon_search_reset">返回当前分页</button>
            </div>

            <form method="post" action="options.php" id="argon_astro_main_form">
                <?php settings_fields('argon_astro'); ?>

                <?php argon_astro_render_tab_appearance($ctx); ?>
                <?php argon_astro_render_tab_header($ctx); ?>
                <?php argon_astro_render_tab_content($ctx); ?>
                <?php argon_astro_render_tab_effects($ctx); ?>
                <?php argon_astro_render_tab_headless_form($ctx); ?>

                <div class="argon-save-bar" id="argon_save_bar" data-cooldown-until="<?php echo esc_attr((string) $cooldown_until); ?>" data-cooldown-seconds="<?php echo esc_attr((string) $cooldown_seconds); ?>">
                    <span class="argon-save-status<?php echo $initial_remaining > 0 ? ' is-cooldown' : ''; ?>" id="argon_save_status">
                        <?php
                        if ($initial_remaining > 0) {
                            echo esc_html('保存冷却中，' . $initial_remaining . ' 秒后可再次提交。');
                        } else {
                            echo esc_html('保存后 ' . $cooldown_seconds . ' 秒内不可重复提交。');
                        }
                        ?>
                    </span>
                    <button type="submit" name="submit" id="argon_save_submit" class="button button-primary button-large"<?php echo $initial_remaining > 0 ? ' disabled="disabled"' : ''; ?>>
                        <?php echo $initial_remaining > 0 ? esc_html('保存冷却中 (' . $initial_remaining . 's)') : '保存更改'; ?>
                    </button>
                </div>
            </form>

            <?php argon_astro_render_tab_headless_extra($ctx); ?>
            <?php argon_astro_render_tab_backup($ctx); ?>
        </div>
    </div>

    <script>
    (function () {
        var adminRoot = document.querySelector('.argon-astro-admin');
        var navItems = Array.prototype.slice.call(document.querySelectorAll('#argon_admin_nav .argon-nav-item'));
        var tabPanels = Array.prototype.slice.call(document.querySelectorAll('.argon-tab-panel'));
        var tabExtras = Array.prototype.slice.call(document.querySelectorAll('.argon-tab-extra'));
        var saveBar = document.getElementById('argon_save_bar');
        var activeTab = 'appearance';

        function activateTab(tabKey, pushHash) {
            var valid = navItems.some(function (li) { return li.getAttribute('data-nav-tab') === tabKey; });
            if (!valid) tabKey = 'appearance';
            activeTab = tabKey;

            navItems.forEach(function (li) {
                var isMatch = li.getAttribute('data-nav-tab') === tabKey;
                li.classList.toggle('is-active', isMatch);
                var btn = li.querySelector('.argon-nav-btn');
                if (btn) {
                    btn.classList.toggle('is-active', isMatch);
                    btn.setAttribute('aria-selected', isMatch ? 'true' : 'false');
                }
            });
            tabPanels.forEach(function (panel) {
                panel.classList.toggle('is-active', panel.getAttribute('data-tab') === tabKey);
            });
            tabExtras.forEach(function (extra) {
                extra.classList.toggle('is-active', extra.getAttribute('data-tab-extra') === tabKey);
            });
            if (saveBar) {
                saveBar.classList.toggle('is-hidden', tabKey === 'backup');
            }
            try { sessionStorage.setItem('argon_astro_admin_tab', tabKey); } catch (e) {}
            if (pushHash && window.history && window.history.replaceState) {
                window.history.replaceState(null, '', '#tab-' + tabKey);
            }
        }

        // Restore active tab from URL hash or sessionStorage.
        var initialHash = (window.location.hash || '').replace(/^#tab-/, '');
        var savedTab = '';
        try { savedTab = sessionStorage.getItem('argon_astro_admin_tab') || ''; } catch (e) {}
        activateTab(initialHash || savedTab || 'appearance', false);

        navItems.forEach(function (li) {
            var btn = li.querySelector('.argon-nav-btn');
            if (btn) {
                btn.addEventListener('click', function () {
                    clearSearch();
                    activateTab(btn.getAttribute('data-tab-target') || 'appearance', true);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }
            li.querySelectorAll('.argon-nav-sublist a').forEach(function (link) {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    clearSearch();
                    var cardId = link.getAttribute('data-jump-card') || '';
                    var target = document.getElementById(cardId);
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });
        });

        // Live cross-tab filter search across all cards and rows.
        var searchInput = document.getElementById('argon_admin_search');
        var searchClear = document.getElementById('argon_admin_search_clear');
        var searchBanner = document.getElementById('argon_search_banner');
        var searchSummary = document.getElementById('argon_search_summary');
        var searchReset = document.getElementById('argon_search_reset');
        var allCards = Array.prototype.slice.call(document.querySelectorAll('.argon-astro-section'));

        function clearSearch() {
            if (searchInput) searchInput.value = '';
            if (searchClear) searchClear.classList.remove('is-visible');
            if (adminRoot) adminRoot.classList.remove('is-searching');
            if (searchBanner) searchBanner.classList.remove('is-visible');
            allCards.forEach(function (card) {
                card.classList.remove('is-search-hidden');
                card.querySelectorAll('tr').forEach(function (tr) { tr.classList.remove('is-search-hidden'); });
            });
            activateTab(activeTab, false);
        }

        function runSearch(rawQuery) {
            var q = (rawQuery || '').trim().toLowerCase();
            if (!q) {
                clearSearch();
                return;
            }
            if (searchClear) searchClear.classList.add('is-visible');
            if (adminRoot) adminRoot.classList.add('is-searching');
            if (saveBar) saveBar.classList.remove('is-hidden');

            var matchedCards = 0;
            allCards.forEach(function (card) {
                var headText = (card.getAttribute('data-card-title') || '').toLowerCase() + ' ' + ((card.querySelector('.argon-card-desc') || {}).textContent || '').toLowerCase();
                var rows = Array.prototype.slice.call(card.querySelectorAll(':scope > .form-table > tbody > tr'));
                var cardHit = headText.indexOf(q) !== -1;

                if (rows.length > 0) {
                    var rowHits = 0;
                    rows.forEach(function (tr) {
                        var hit = cardHit || (tr.textContent || '').toLowerCase().indexOf(q) !== -1;
                        tr.classList.toggle('is-search-hidden', !hit);
                        if (hit) rowHits++;
                    });
                    var show = rowHits > 0;
                    card.classList.toggle('is-search-hidden', !show);
                    if (show) matchedCards++;
                } else {
                    var matchWhole = cardHit || (card.textContent || '').toLowerCase().indexOf(q) !== -1;
                    card.classList.toggle('is-search-hidden', !matchWhole);
                    if (matchWhole) matchedCards++;
                }
            });

            if (searchSummary) {
                searchSummary.textContent = matchedCards > 0
                    ? '跨分组搜索 “' + rawQuery.trim() + '”：共匹配 ' + matchedCards + ' 个设置卡片'
                    : '未找到包含 “' + rawQuery.trim() + '” 的设置项';
            }
            if (searchBanner) searchBanner.classList.add('is-visible');
        }

        if (searchInput) {
            searchInput.addEventListener('input', function () { runSearch(searchInput.value); });
        }
        if (searchClear) searchClear.addEventListener('click', clearSearch);
        if (searchReset) searchReset.addEventListener('click', clearSearch);

        // Replay Argon animated SMIL SVG logo on click.
        var adminLogo = document.getElementById('argon_admin_logo');
        if (adminLogo) {
            adminLogo.addEventListener('click', function () {
                if (typeof adminLogo.setCurrentTime === 'function') {
                    adminLogo.setCurrentTime(0);
                }
            });
        }

        // Sync color picker, hex field, live header badge, and 12 Argon preset swatches.
        var picker = document.getElementById('argon_color_picker');
        var hex = document.getElementById('argon_color_hex');
        var liveDot = document.getElementById('argon_live_color_dot');
        var liveText = document.getElementById('argon_live_color_text');
        var presetBoxes = document.querySelectorAll('#argon_color_presets .argon-preset-box');
        function syncColor(val) {
            if (!/^#[0-9a-fA-F]{6}$/.test(val)) return;
            var lower = val.toLowerCase();
            if (picker) picker.value = lower;
            if (hex) hex.value = lower;
            if (liveDot) liveDot.style.background = lower;
            if (liveText) liveText.textContent = lower;
            presetBoxes.forEach(function (box) {
                box.classList.toggle('is-active', (box.getAttribute('data-color') || '').toLowerCase() === lower);
            });
        }
        if (picker) picker.addEventListener('input', function () { syncColor(picker.value); });
        if (hex) hex.addEventListener('change', function () { syncColor(hex.value.trim()); });
        presetBoxes.forEach(function (box) {
            box.addEventListener('click', function () { syncColor(box.getAttribute('data-color') || '#5e72e4'); });
        });

        // Highlight selected SVG radio cards on change.
        document.querySelectorAll('.argon-radio-card input[type="radio"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                var group = radio.closest('.argon-radio-cards');
                if (!group) return;
                group.querySelectorAll('.argon-radio-card').forEach(function (card) {
                    card.classList.toggle('is-selected', !!card.querySelector('input[type="radio"]:checked'));
                });
            });
        });

        // Sync Banner gradient chips with select dropdown.
        var gradSelect = document.getElementById('argon_banner_color_type');
        var gradChips = document.querySelectorAll('#argon_banner_gradients .argon-gradient-chip');
        function syncGradient(val) {
            if (gradSelect) gradSelect.value = val;
            gradChips.forEach(function (chip) {
                chip.classList.toggle('is-active', chip.getAttribute('data-gradient') === val);
            });
        }
        if (gradSelect) gradSelect.addEventListener('change', function () { syncGradient(gradSelect.value); });
        gradChips.forEach(function (chip) {
            chip.addEventListener('click', function () { syncGradient(chip.getAttribute('data-gradient') || 'shape-primary'); });
        });

        // Keep Fancybox and Zoomify mutually exclusive like upstream Argon.
        var fancyboxSel = document.getElementById('argon_fancybox_select');
        var zoomifySel = document.getElementById('argon_zoomify_select');
        if (fancyboxSel && zoomifySel) {
            fancyboxSel.addEventListener('change', function () { if (fancyboxSel.value === '1') zoomifySel.value = '0'; });
            zoomifySel.addEventListener('change', function () { if (zoomifySel.value === '1') fancyboxSel.value = '0'; });
        }

        // Shared helper for single-select pill groups with conditional panels (CI hooks, Math engine).
        function bindRadioPanelGroup(selectorId, stageId, panelAttr) {
            var choices = Array.prototype.slice.call(document.querySelectorAll('#' + selectorId + ' .argon-ci-choice'));
            var panels = Array.prototype.slice.call(document.querySelectorAll('#' + stageId + ' .argon-ci-panel'));
            function sync(val) {
                choices.forEach(function (label) {
                    var radio = label.querySelector('input[type="radio"]');
                    label.classList.toggle('is-selected', !!(radio && radio.value === val));
                });
                panels.forEach(function (panel) {
                    panel.classList.toggle('is-active', panel.getAttribute(panelAttr) === val);
                });
            }
            choices.forEach(function (label) {
                var radio = label.querySelector('input[type="radio"]');
                if (radio) {
                    radio.addEventListener('change', function () {
                        if (radio.checked) sync(radio.value);
                    });
                }
            });
        }
        bindRadioPanelGroup('argon_ci_provider_selector', 'argon_ci_stage', 'data-ci-panel');
        bindRadioPanelGroup('argon_math_selector', 'argon_math_stage', 'data-math-panel');

        // Toggle visibility of secret inputs (PAT / HMAC key) and generate random HMAC secret.
        document.querySelectorAll('.argon-toggle-secret').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var target = document.getElementById(btn.getAttribute('data-target') || '');
                if (!target) return;
                var show = target.type === 'password';
                target.type = show ? 'text' : 'password';
                btn.textContent = show ? '隐藏' : '显示';
            });
        });
        var genSecretBtn = document.getElementById('argon_gen_hook_secret');
        var secretInput = document.getElementById('argon_hook_secret');
        if (genSecretBtn && secretInput) {
            genSecretBtn.addEventListener('click', function () {
                var bytes = new Uint8Array(24);
                if (window.crypto && window.crypto.getRandomValues) {
                    window.crypto.getRandomValues(bytes);
                } else {
                    for (var i = 0; i < bytes.length; i++) bytes[i] = Math.floor(Math.random() * 256);
                }
                var hexStr = Array.prototype.map.call(bytes, function (b) {
                    return ('0' + b.toString(16)).slice(-2);
                }).join('');
                secretInput.value = hexStr;
                secretInput.type = 'text';
                var toggle = document.querySelector('.argon-toggle-secret[data-target="argon_hook_secret"]');
                if (toggle) toggle.textContent = '隐藏';
            });
        }

        // 60-second submit cooldown persisted across reloads until countdown reaches 0.
        var mainForm = document.getElementById('argon_astro_main_form');
        var saveSubmitBtn = document.getElementById('argon_save_submit');
        var saveStatusEl = document.getElementById('argon_save_status');
        var COOLDOWN_KEY = 'argon_astro_save_cooldown_until';
        var cooldownSec = parseInt((saveBar && saveBar.getAttribute('data-cooldown-seconds')) || '60', 10) || 60;
        var serverUntilMs = (parseInt((saveBar && saveBar.getAttribute('data-cooldown-until')) || '0', 10) || 0) * 1000;
        var storedUntilMs = 0;
        try { storedUntilMs = parseInt(localStorage.getItem(COOLDOWN_KEY) || '0', 10) || 0; } catch (e) {}
        var cooldownUntilMs = Math.max(serverUntilMs, storedUntilMs);
        var cooldownTimer = null;

        function renderCooldownState() {
            if (!saveSubmitBtn) return;
            var remaining = Math.ceil((cooldownUntilMs - Date.now()) / 1000);
            if (remaining > 0) {
                saveSubmitBtn.disabled = true;
                saveSubmitBtn.textContent = '保存冷却中 (' + remaining + 's)';
                if (saveStatusEl) {
                    saveStatusEl.classList.add('is-cooldown');
                    saveStatusEl.textContent = '保存冷却中，' + remaining + ' 秒后可再次提交。';
                }
                if (!cooldownTimer) {
                    cooldownTimer = window.setInterval(renderCooldownState, 1000);
                }
                return;
            }
            if (cooldownTimer) {
                window.clearInterval(cooldownTimer);
                cooldownTimer = null;
            }
            cooldownUntilMs = 0;
            try { localStorage.removeItem(COOLDOWN_KEY); } catch (e) {}
            saveSubmitBtn.disabled = false;
            saveSubmitBtn.textContent = '保存更改';
            if (saveStatusEl) {
                saveStatusEl.classList.remove('is-cooldown');
                saveStatusEl.textContent = '保存后 ' + cooldownSec + ' 秒内不可重复提交。';
            }
        }

        if (mainForm && saveSubmitBtn) {
            mainForm.addEventListener('submit', function (e) {
                if (cooldownUntilMs > Date.now()) {
                    e.preventDefault();
                    renderCooldownState();
                    return;
                }
                cooldownUntilMs = Date.now() + cooldownSec * 1000;
                try { localStorage.setItem(COOLDOWN_KEY, String(cooldownUntilMs)); } catch (err) {}
                saveSubmitBtn.disabled = true;
                saveSubmitBtn.textContent = '保存中 (' + cooldownSec + 's)...';
            });
            renderCooldownState();
        }

        // Dynamic Friend Links table row add / remove.
        var linkTbody = document.querySelector('#argon_friend_links_table tbody');
        var addLinkBtn = document.getElementById('argon_add_friend_link');
        var nextLinkIdx = linkTbody ? linkTbody.querySelectorAll('tr').length : 0;
        if (addLinkBtn && linkTbody) {
            addLinkBtn.addEventListener('click', function () {
                var idx = nextLinkIdx++;
                var tr = document.createElement('tr');
                tr.innerHTML =
                    '<td><input type="text" name="argon_astro_config[friendLinks][' + idx + '][name]" placeholder="站点名称" /></td>' +
                    '<td><input type="text" name="argon_astro_config[friendLinks][' + idx + '][url]" placeholder="https://..." /></td>' +
                    '<td><input type="text" name="argon_astro_config[friendLinks][' + idx + '][description]" placeholder="一句话介绍" /></td>' +
                    '<td><input type="text" name="argon_astro_config[friendLinks][' + idx + '][avatar]" placeholder="https://..." /></td>' +
                    '<td><button type="button" class="button button-link-delete argon-remove-link">删除</button></td>';
                linkTbody.appendChild(tr);
            });
            linkTbody.addEventListener('click', function (e) {
                if (e.target && e.target.classList.contains('argon-remove-link')) {
                    var row = e.target.closest('tr');
                    if (row) row.remove();
                }
            });
        }

        // Export JSON reveal, copy, and download helpers.
        var exportWrap = document.getElementById('argon_export_wrap');
        var exportBox = document.getElementById('argon_export_json_box');
        var copyStatus = document.getElementById('argon_copy_status');
        var toggleExportBtn = document.getElementById('argon_toggle_export_btn');
        var copyExportBtn = document.getElementById('argon_copy_export_btn');
        var dlExportBtn = document.getElementById('argon_download_export_btn');
        if (toggleExportBtn && exportWrap) {
            toggleExportBtn.addEventListener('click', function () {
                exportWrap.style.display = exportWrap.style.display === 'none' ? '' : 'none';
            });
        }
        if (copyExportBtn && exportBox) {
            copyExportBtn.addEventListener('click', function () {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(exportBox.value).then(function () {
                        if (copyStatus) copyStatus.textContent = '已复制到剪贴板';
                    });
                } else {
                    if (exportWrap) exportWrap.style.display = '';
                    exportBox.select();
                    document.execCommand('copy');
                    if (copyStatus) copyStatus.textContent = '已复制到剪贴板';
                }
            });
        }
        if (dlExportBtn && exportBox) {
            dlExportBtn.addEventListener('click', function () {
                var blob = new Blob([exportBox.value], { type: 'application/json;charset=utf-8' });
                var url = URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = 'argon-settings.json';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            });
        }
    })();
    </script>
</div>
    <?php
}
