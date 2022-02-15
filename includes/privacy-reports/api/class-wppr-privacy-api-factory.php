<?
class WPPR_Privacy_API_Factory
{
    static function get_report_api()
    {
        include_once WPPR_PATH . '/includes/privacy-reports/api/class-wppr-privacy-tracker-api.php';
        include_once WPPR_PATH . '/includes/privacy-reports/api/class-wppr-privacy-report-api.php';

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
        $privacy_report_api = self::get_fetch_api();

        $tracker_api = new WPPR_Privacy_Tracker_API($tracker_db, $category_db, $tracker_category_db);
        $report_api = new WPPR_Privacy_Report_API(
            $report_db,
            $permission_db,
            $tracker_db,
            $report_permission_db,
            $report_tracker_db,
            $tracker_api,
            $privacy_report_api
        );

        return $report_api;
    }

    static function get_tracker_api()
    {
        include_once WPPR_PATH . '/includes/privacy-reports/api/class-wppr-privacy-tracker-api.php';
        include_once WPPR_PATH . '/includes/privacy-reports/tables/class-wppr-privacy-category.php';
        include_once WPPR_PATH . '/includes/privacy-reports/tables/class-wppr-privacy-tracker.php';
        include_once WPPR_PATH . '/includes/privacy-reports/tables/class-wppr-privacy-tracker-category.php';

        $category_db = new WPPR_Privacy_Category();
        $tracker_db = new WPPR_Privacy_Tracker();
        $tracker_category_db = new WPPR_Privacy_Tracker_Category();
        $tracker_api = new WPPR_Privacy_Tracker_API($tracker_db, $category_db, $tracker_category_db);
        return $tracker_api;
    }

    static function get_fetch_api()
    {
        include_once WPPR_PATH . '/includes/privacy-reports/api/class-wppr-privacy-report-fetch.php';
        $privacy_report_api = new WPPR_Privacy_Report_Fetch();

        return $privacy_report_api;
    }
}
