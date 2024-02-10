<?php
// Check user permissions
if (!current_user_can('manage_options')) {
  wp_die(__('You do not have sufficient permissions to access this page.'));
}

// Get current page number
$current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;

// Use the Page_Visit_Logger class to get visits
$visits = Page_Visit_Logger::get_visits(100, $current_page);

// Calculate total pages
global $wpdb;
$table_name = $wpdb->prefix . 'page_visit_logs';
$total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");
$total_pages = ceil($total_items / 100);
?>

<div class="wrap">
  <h1>Page Visit Logs</h1>
  <table class="wp-list-table widefat fixed striped">
    <thead>
      <tr>
        <th>Date & Time</th>
        <th>URL</th>
        <th>IP Address & Geolocation</th>
        <th>Parameters</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($visits as $visit): ?>
        <tr>
            <td><?php echo esc_html($visit->time); ?></td>
            <td><?php echo esc_url($visit->url); ?></td>
            <td>
                <?php 
                echo esc_html($visit->ip);
                if (isset($visit->country) || isset($visit->city)) {
                    echo "<br>";
                    echo "Country: " . esc_html($visit->country ?? 'Unknown') . ", City: " . esc_html($visit->city ?? 'Unknown');
                }
                if (isset($visit->isp)) {
                    echo "<br>";
                    echo "ISP: " . esc_html($visit->isp);
                }
                if (isset($visit->mobile)) {
                    echo ", Mobile: " . (esc_html($visit->mobile) ? "Yes" : "No");
                }
                ?>
            </td>
            <td><?php echo esc_html($visit->params); ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($visits)): ?>
        <tr>
            <td colspan="4">No visits logged yet.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <?php if ($total_pages > 1): ?>
    <div class="tablenav">
      <div class="tablenav-pages">
        <span class="pagination-links">
          <?php if ($current_page > 1): ?>
            <a class="first-page button" href="?page=page-visit-logger&paged=1"><span class="screen-reader-text">First page</span></a>
            <a class="prev-page button" href="?page=page-visit-logger&paged=<?php echo $current_page - 1; ?>"><span class="screen-reader-text">Previous page</span></a>
          <?php endif; ?>
          <span class="paging-input">
            <label for="current-page-selector" class="screen-reader-text">Current Page</label>
            <input class="current-page" id="current-page-selector" type="text" name="paged" value="<?php echo $current_page; ?>" size="2" aria-describedby="table-paging"> of <span class="total-pages"><?php echo $total_pages; ?></span>
          </span>
          <?php if ($current_page < $total_pages): ?>
            <a class="next-page button" href="?page=page-visit-logger&paged=<?php echo $current_page + 1; ?>"><span class="screen-reader-text">Next page</span></a>
            <a class="last-page button" href="?page=page-visit-logger&paged=<?php echo $total_pages; ?>"><span class="screen-reader-text">Last page</span></a>
          <?php endif; ?>
        </span>
      </div>
    </div>
  <?php endif; ?>
</div>
