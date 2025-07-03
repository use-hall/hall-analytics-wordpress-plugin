<?php
/**
 * Uninstall Hall Analytics
 *
 * This file runs when the plugin is deleted from the WordPress admin.
 * It removes all plugin data from the database.
 *
 * @package Hall AI Analytics for WordPress
 *
 */

// Prevent direct access
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Define plugin constants if not already defined
if (!defined('HALL_ANALYTICS_ACCESS_TOKEN')) {
    define('HALL_ANALYTICS_ACCESS_TOKEN', 'hall_analytics_access_token');
}
if (!defined('HALL_ANALYTICS_ENABLED')) {
    define('HALL_ANALYTICS_ENABLED', 'hall_analytics_enabled');
}

// Remove all plugin options
delete_option(HALL_ANALYTICS_ACCESS_TOKEN);
delete_option(HALL_ANALYTICS_ENABLED);

// For multisite installations, remove options from all sites
if (is_multisite()) {
    $sites = get_sites();
    foreach ($sites as $site) {
        switch_to_blog($site->blog_id);
        
        delete_option(HALL_ANALYTICS_ACCESS_TOKEN);
        delete_option(HALL_ANALYTICS_ENABLED);
        
        restore_current_blog();
    }
}

// Clear any cached data
wp_cache_flush();