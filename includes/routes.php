<?php
if (!defined('ABSPATH')) {
    exit;
}

function argon_astro_register_routes() {
    argon_astro_register_config_route();
    argon_astro_register_content_routes();
    argon_astro_register_comment_routes();
    argon_astro_register_upvote_route();
    argon_astro_register_comment_upvote_route();
    argon_astro_register_deploy_route();
}
