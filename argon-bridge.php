<?php
/**
 * Plugin Name: Argon Bridge
 * Description: WordPress headless bridge for Argon.
 * Version: 0.1.1
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Argon
 * License: GPL-3.0-or-later
 * Text Domain: argon-bridge
 */

if (!defined('ABSPATH')) {
    exit;
}

define('ARGON_ASTRO_BRIDGE_FILE', __FILE__);
define('ARGON_ASTRO_BRIDGE_DIR', plugin_dir_path(__FILE__));

require_once ARGON_ASTRO_BRIDGE_DIR . 'includes/signature.php';
require_once ARGON_ASTRO_BRIDGE_DIR . 'includes/auth.php';
require_once ARGON_ASTRO_BRIDGE_DIR . 'includes/cpt.php';
require_once ARGON_ASTRO_BRIDGE_DIR . 'includes/media.php';
require_once ARGON_ASTRO_BRIDGE_DIR . 'includes/admin-schema.php';
require_once ARGON_ASTRO_BRIDGE_DIR . 'includes/rest-config.php';
require_once ARGON_ASTRO_BRIDGE_DIR . 'includes/rest-content.php';
require_once ARGON_ASTRO_BRIDGE_DIR . 'includes/rest-comments.php';
require_once ARGON_ASTRO_BRIDGE_DIR . 'includes/rest-upvote.php';
require_once ARGON_ASTRO_BRIDGE_DIR . 'includes/rest-comment-upvote.php';
require_once ARGON_ASTRO_BRIDGE_DIR . 'includes/webhooks.php';
require_once ARGON_ASTRO_BRIDGE_DIR . 'includes/routes.php';
require_once ARGON_ASTRO_BRIDGE_DIR . 'includes/admin-view.php';
require_once ARGON_ASTRO_BRIDGE_DIR . 'includes/admin.php';

function argon_astro_register_nav_locations() {
    if (function_exists('register_nav_menus')) {
        register_nav_menus(array(
            'toolbar_menu' => '顶部导航',
            'leftbar_menu' => '左侧栏菜单',
            'leftbar_author_links' => '左侧栏作者个人链接',
            'leftbar_friend_links' => '左侧栏友情链接',
        ));
    }
}

add_filter('pre_option_link_manager_enabled', '__return_true');
add_action('init', 'argon_astro_register_shuoshuo');
add_action('after_setup_theme', 'argon_astro_register_nav_locations', 20);
add_action('rest_api_init', 'argon_astro_register_routes');
add_action('argon_astro_deploy_event', 'argon_astro_run_scheduled_deploy');
