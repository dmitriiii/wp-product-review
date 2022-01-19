<?
function wppr_report_cron()
{
    include_once WPPR_PATH . '/includes/privacy-reports/api/class-wppr-privacy-tracker-api.php';
    include_once WPPR_PATH . '/includes/privacy-reports/api/class-wppr-privacy-report-api.php';
    include_once WPPR_PATH . '/includes/privacy-reports/api/class-wppr-privacy-report-fetch.php';

    include_once WPPR_PATH . '/includes/privacy-reports/tables/class-wppr-privacy-report.php';
    include_once WPPR_PATH . '/includes/privacy-reports/tables/class-wppr-privacy-permission.php';
    include_once WPPR_PATH . '/includes/privacy-reports/tables/class-wppr-privacy-category.php';
    include_once WPPR_PATH . '/includes/privacy-reports/tables/class-wppr-privacy-tracker.php';
    include_once WPPR_PATH . '/includes/privacy-reports/tables/class-wppr-privacy-report-permission.php';
    include_once WPPR_PATH . '/includes/privacy-reports/tables/class-wppr-privacy-tracker-category.php';

    $report_db = new WPPR_Privacy_Report();
    $permission_db = new WPPR_Privacy_Permission();
    $category_db = new WPPR_Privacy_Category();
    $tracker_db = new WPPR_Privacy_Tracker();
    $report_permission_db = new WPPR_Privacy_Report_Permission();
    $report_tracker_db = new WPPR_Privacy_Report_Tracker();
    $tracker_category_db = new WPPR_Privacy_Tracker_Category();

    $tracker_api = new WPPR_Privacy_Tracker_API($tracker_db, $category_db, $tracker_category_db);
    $report_api = new WPPR_Privacy_Report_API($report_db, $permission_db,  $report_permission_db, $report_tracker_db, $tracker_api);

    $reports = WPPR_Privacy_Report_Fetch::get_reports('fr.meteo');

    $report_api->update($reports);
}

//add_action('wppr_privacy_report_cron', 'wppr_report_cron');
