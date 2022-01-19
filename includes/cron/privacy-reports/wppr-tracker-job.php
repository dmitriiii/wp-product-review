<?
function wppr_tracker_cron()
{
    include_once WPPR_PATH . '/includes/privacy-reports/api/class-wppr-privacy-api-factory.php';
    $tracker_api = WPPR_Privacy_API_Factory::get_tracker_api();
    $fetch_api = WPPR_Privacy_API_Factory::get_fetch_api();
    ['trackers' => $trackers] = $fetch_api::get_trackers();

    /*foreach ($trackers as $key => &$tracker) {
        $ind = array_search('Analytics', $tracker['categories']);
        if ($ind !== false) unset($tracker['categories'][$ind]);
    }*/

    $tracker_api->update($trackers);
}

add_action('wppr_privacy_tracker_cron', 'wppr_tracker_cron');