<?
function wppr_tracker_cron()
{
    if (!get_field('enable_privacy_reports', 'option')) return;

    include_once WPPR_PATH . '/includes/privacy-reports/api/class-wppr-privacy-api-factory.php';
    $tracker_api = WPPR_Privacy_API_Factory::get_tracker_api();
    $fetch_api = WPPR_Privacy_API_Factory::get_fetch_api();
    ['trackers' => $trackers] = $fetch_api::get_trackers();

    $tracker_api->update($trackers);
}

add_action('wppr_privacy_tracker_cron', 'wppr_tracker_cron');