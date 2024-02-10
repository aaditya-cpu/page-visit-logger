<?php
    class Page_Visit_Logger {
        // Adjust your plugin activation method to add geolocation columns to your table
        public static function plugin_activation() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'page_visit_logs';
            $charset_collate = $wpdb->get_charset_collate();
    
            $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                url text NOT NULL,
                ip varchar(100) NOT NULL,
                params text NOT NULL,
                country varchar(100),
                regionName varchar(100),
                city varchar(100),
                zip varchar(20),
                lat float,
                lon float,
                isp varchar(100),
                org varchar(100),
                as_info varchar(100),
                mobile boolean,
                proxy boolean,
                hosting boolean,
                PRIMARY KEY  (id)
            ) $charset_collate;";
    
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    
        // Method to fetch geolocation data from the IP API
        private static function fetch_geolocation_data($ip) {
            $api_url = "http://ip-api.com/json/$ip?fields=status,message,country,countryCode,region,regionName,city,district,zip,lat,lon,offset,isp,org,as,reverse,mobile,proxy,hosting";
            $response = wp_remote_get($api_url);
    
            if (is_wp_error($response)) {
                return false; // Bail early on error
            }
    
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true); // Decode JSON response into an associative array
    
            if ($data['status'] !== 'success') {
                return false; // Bail if the API did not return a success status
            }
    
            return $data;
        }
    
        // Adjust your log_visit method to include geolocation fetching and storing
        public static function log_visit() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'page_visit_logs';
    
            $url = home_url(add_query_arg(null, null));
            $parsed_url = parse_url($url);
            $query_params = isset($parsed_url['query']) ? $parsed_url['query'] : 'None';
            $ip = $_SERVER['REMOTE_ADDR'];
            $current_time = current_time('mysql');
            $geolocationData = self::fetch_geolocation_data($ip);
    
            if ($geolocationData) {
                $wpdb->insert(
                    $table_name,
                    array(
                        'time' => $current_time,
                        'url' => $url,
                        'ip' => $ip,
                        'params' => $query_params,
                        'country' => $geolocationData['country'],
                        'regionName' => $geolocationData['regionName'],
                        'city' => $geolocationData['city'],
                        'zip' => $geolocationData['zip'],
                        'lat' => $geolocationData['lat'],
                        'lon' => $geolocationData['lon'],
                        'isp' => $geolocationData['isp'],
                        'org' => $geolocationData['org'],
                        'as_info' => $geolocationData['as'],
                        'mobile' => $geolocationData['mobile'],
                        'proxy' => $geolocationData['proxy'],
                        'hosting' => $geolocationData['hosting'],
                    )
                );
            }
        }
    // Method to retrieve visits for admin display, including pagination
    public static function get_visits($per_page = 100, $page_number = 1) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'page_visit_logs';

        $offset = ($page_number - 1) * $per_page;
        $query = $wpdb->prepare(
            "SELECT * FROM $table_name ORDER BY time DESC LIMIT %d, %d;",
            $offset, $per_page
        );

        $results = $wpdb->get_results($query);
        return $results;
    }
}
