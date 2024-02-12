<?php
/**
 * Plugin Name: Page Visit Logger
 * Description: Logs page visits along with query parameters, IPs, and shows them in an admin dashboard, with export to CSV functionality.
 * Version: 1.0
 * Author: Aashika Goenka
 * Url: https://github.com/aaditya-cpu
 */

defined('ABSPATH') or die('No script kiddies please!');

define('PVL_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Include the logger class file.
require_once PVL_PLUGIN_DIR . 'logger/class-page-visit-logger.php';

// Register activation hook to create the database table.
register_activation_hook(__FILE__, ['Page_Visit_Logger', 'plugin_activation']);

// Hook into WordPress to log page visits.
add_action('template_redirect', ['Page_Visit_Logger', 'log_visit']);

// Add an admin menu item for the plugin.
add_action('admin_menu', function() {
    add_menu_page(
        'Page Visit Logs',             // Page title
        'Visit Logs',                  // Menu title
        'manage_options',              // Capability required to see the page
        'page-visit-logger',           // Menu slug
        'pvl_admin_page_display',      // Function to display the admin page
        'dashicons-visibility',        // Icon URL
        6                              // Position in menu
    );
});

// Add action for admin_init to handle the CSV export.
add_action('admin_init', function() {
    if (isset($_GET['page']) && $_GET['page'] === 'page-visit-logger' && isset($_GET['action']) && $_GET['action'] === 'export_csv') {
        require_once PVL_PLUGIN_DIR . 'export/export-to-csv.php';
    }
});
error_log('Attempting to require: ' . PVL_PLUGIN_DIR . 'export/export-to-csv.php');

// Function to display the admin dashboard page.
function pvl_admin_page_display() {
    include PVL_PLUGIN_DIR . 'views/admin-page.php';
}
