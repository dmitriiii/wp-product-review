<?
include_once WPPR_PATH . '/includes/privacy-reports/abstracts/abstract-class-wppr-privacy-api.php';

class WPPR_Privacy_Report_API extends WPPR_Abstract_Privacy_API
{
    /**
     * @var WPPR_Privacy_Report
     */
    private $report_db;
    /**
     * @var WPPR_Privacy_Permission
     */
    private $permission_db;
    /**
     * @var WPPR_Privacy_Tracker
     */
    private $tracker_db;
    /**
     * @var WPPR_Privacy_Report_Permission
     */
    private $report_permission_db;
    /**
     * @var WPPR_Privacy_Report_Tracker
     */
    private $report_tracker_db;
    /**
     * @var WPPR_Privacy_Tracker_API
     */
    private $tracker_api;
    /**
     * @var WPPR_Privacy_Report_Fetch
     */
    private $report_fetch_api;

    function __construct(
        WPPR_Privacy_Report $report_db,
        WPPR_Privacy_Permission $permission_db,
        WPPR_Privacy_Report_Permission $report_permission_db,
        WPPR_Privacy_Report_Tracker $report_tracker_db,
        WPPR_Privacy_Tracker_API $tracker_api,
    ) {
        $this->report_db = $report_db;
        $this->permission_db = $permission_db;
        $this->report_permission_db = $report_permission_db;
        $this->report_tracker_db = $report_tracker_db;
        $this->tracker_api = $tracker_api;
    }

    function update($raw_reports = [])
    {
        $reports = is_array($raw_reports) ? $raw_reports : [$raw_reports];

        $perms = array_reduce($reports, function ($carr, $report) {
            if (!isset($report['permissions'])) return $carr;
            return array_unique(
                [...$carr, ...$report['permissions']]
            );
        }, []);

        foreach ($reports as &$report) {
            $report['id'] = $report['report'];
        }

        $this->update_reports($reports);
        $this->update_permissions($perms);
        $this->bind_permissions($reports);
        $this->unbind_dead_permissions($reports);
    }


    function update_reports($reports)
    {
        $this->update_table($reports, $this->report_db);
    }

    function update_permissions($perms)
    {
        $this->update_light_table($perms, 'name', $this->permission_db);
    }

    function bind_permissions($reports)
    {
        $this->bulk_update_bind_table(
            $reports,
            'permissions',
            'get_all_report_binds',
            $this->report_permission_db,
            $this->permission_db
        );
    }

    function unbind_dead_permissions($reports)
    {
        $this->bulk_garbage_bind_table(
            $reports,
            'permissions',
            'get_all_report_binds',
            $this->report_permission_db,
            $this->permission_db
        );
    }
}
