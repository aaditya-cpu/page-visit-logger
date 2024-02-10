<?php

// Check for current user capabilities
if (!current_user_can('manage_options')) {
    wp_die('You do not have sufficient permissions to access this page.');
}

function export_logs_to_csv() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'page_visit_logs';

    $filename = 'page-visits-' . date('Ymd') . '.csv';

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');

    // Column headers
    fputcsv($output, ['Time', 'URL', 'Query Params', 'IP Address']);

    // Get data from database
    $logs = $wpdb->get_results("SELECT * FROM $table_name ORDER BY time DESC", ARRAY_A);

    foreach ($logs as $log) {
        fputcsv($output, [$log['time'], $log['url'], $log['params'], $log['ip']]);
    }

    fclose($output);
    exit;
}

// Trigger export function based on a custom action or condition
if (isset($_GET['action']) && $_GET['action'] == 'export_to_csv') {
    add_action('admin_init', 'export_logs_to_csv');
}
