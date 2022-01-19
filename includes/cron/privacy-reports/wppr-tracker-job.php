<?
function wppr_tracker_cron()
{
    include_once WPPR_PATH . '/includes/privacy-reports/api/class-wppr-privacy-tracker-api.php';
    include_once WPPR_PATH . '/includes/privacy-reports/api/class-wppr-privacy-report-fetch.php';
    include_once WPPR_PATH . '/includes/privacy-reports/tables/class-wppr-privacy-category.php';
    include_once WPPR_PATH . '/includes/privacy-reports/tables/class-wppr-privacy-tracker.php';
    include_once WPPR_PATH . '/includes/privacy-reports/tables/class-wppr-privacy-tracker-category.php';

    $category_db = new WPPR_Privacy_Category();
    $tracker_db = new WPPR_Privacy_Tracker();
    $tracker_category_db = new WPPR_Privacy_Tracker_Category();
    $tracker_api = new WPPR_Privacy_Tracker_API($tracker_db, $category_db, $tracker_category_db);

    ['trackers' => $trackers] = WPPR_Privacy_Report_Fetch::get_trackers();

    /*foreach ($trackers as $key => &$tracker) {
        $ind = array_search('Analytics', $tracker['categories']);
        if ($ind !== false) unset($tracker['categories'][$ind]);
    }*/

    $tracker_api->update($trackers);
}

add_action('wppr_privacy_tracker_cron', 'wppr_tracker_cron');