<?php
/*
Plugin Name: AI Analytics
Plugin URI: https://usehall.com/ai-agent-analytics?utm_source=wordpress_plugin
Description: Measure and understand how AI agents and assistants are accessing your WordPress site. Track referrals and clicks from conversational AI platforms like ChatGPT.
Version: 1.0.0
Author: Hall
Author URI: https://usehall.com?utm_source=wordpress_plugin
Text Domain: hall-analytics
Domain Path: /languages
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.0
Network: false
License: MIT
License URI: https://opensource.org/licenses/MIT
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('HALL_ANALYTICS_VERSION', '1.0.0');
define('HALL_ANALYTICS_PLUGIN_FILE', __FILE__);
define('HALL_ANALYTICS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('HALL_ANALYTICS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('HALL_ANALYTICS_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Check WordPress and PHP version requirements
if (version_compare(PHP_VERSION, '7.0', '<')) {
    add_action('admin_notices', 'hall_analytics_php_version_notice');
    return;
}

if (version_compare(get_bloginfo('version'), '5.0', '<')) {
    add_action('admin_notices', 'hall_analytics_wp_version_notice');
    return;
}

function hall_analytics_php_version_notice() {
    echo '<div class="notice notice-error"><p>';
    echo esc_html__('Hall Analytics requires PHP version 7.0 or higher.', 'hall-analytics');
    echo '</p></div>';
}

function hall_analytics_wp_version_notice() {
    echo '<div class="notice notice-error"><p>';
    echo esc_html__('Hall Analytics requires WordPress version 5.0 or higher.', 'hall-analytics');
    echo '</p></div>';
}

// Load plugin files
require_once HALL_ANALYTICS_PLUGIN_DIR . 'includes/constants.php';
require_once HALL_ANALYTICS_PLUGIN_DIR . 'includes/settings.php';
require_once HALL_ANALYTICS_PLUGIN_DIR . 'includes/analytics.php';

// Activation and deactivation hooks
register_activation_hook(__FILE__, 'hall_analytics_activate');
register_deactivation_hook(__FILE__, 'hall_analytics_deactivate');

function hall_analytics_activate() {
    // Set default options
    if (!get_option(HALL_ANALYTICS_ENABLED)) {
        add_option(HALL_ANALYTICS_ENABLED, '1');
    }
    
    // Flush rewrite rules
    flush_rewrite_rules();
}

function hall_analytics_deactivate() {
    // Flush rewrite rules
    flush_rewrite_rules();
}