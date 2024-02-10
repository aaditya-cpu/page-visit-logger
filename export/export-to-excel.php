<?php

// Function to export logs to CSV
function export_logs_to_csv() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'page_visit_logs';

    // Set the filename with the current date
    $filename = 'page-visits-' . date('Ymd') . '.csv';

    // Set the header to download the file instead of displaying it
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // Open PHP output stream as file
    $output = fopen('php://output', 'w');

    // Column headers in the CSV file
    fputcsv($output, ['Time', 'URL', 'IP Address', 'Country', 'Region', 'City', 'ISP', 'Mobile']);

    // Fetch logs from the database
    $logs = $wpdb->get_results("SELECT * FROM $table_name ORDER BY time DESC", ARRAY_A);

    // Loop through each log entry and add it to the CSV
    foreach ($logs as $log) {
        fputcsv($output, [
            $log['time'],
            $log['url'],
            $log['ip'],
            $log['country'],
            $log['regionName'],
            $log['city'],
            $log['isp'],
            $log['mobile'] ? 'Yes' : 'No' // Assuming 'mobile' is stored as a boolean
        ]);
    }

    // Close the output stream
    fclose($output);
    exit;
}

// Check if the current request is for the CSV export
if (isset($_GET['action']) && $_GET['action'] == 'export_csv') {
    export_logs_to_csv();
}
?>
