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
        WPPR_Privacy_Tracker $tracker_db,
        WPPR_Privacy_Report_Permission $report_permission_db,
        WPPR_Privacy_Report_Tracker $report_tracker_db,
        WPPR_Privacy_Tracker_API $tracker_api,
        WPPR_Privacy_Report_Fetch $report_fetch_api
    ) {
        $this->report_db = $report_db;
        $this->permission_db = $permission_db;
        $this->tracker_db = $tracker_db;
        $this->report_permission_db = $report_permission_db;
        $this->report_tracker_db = $report_tracker_db;
        $this->tracker_api = $tracker_api;
        $this->report_fetch_api = $report_fetch_api;
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

        $this->bind_trackers($reports);
        $this->unbind_dead_trackers($reports);
    }


    function update_reports(array $reports)
    {
        $this->update_table($reports, $this->report_db);
    }

    function update_permissions(array $perms)
    {
        $this->update_light_table($perms, 'name', $this->permission_db);
    }

    /**
     * @param array<int, string|int> $tracker_ids
     */
    private function update_trackers(array $tracker_ids)
    {
        $all_trackers = array_filter($this->report_fetch_api::get_trackers(), function ($tracker) use ($tracker_ids) {
            return in_array($tracker['id'], $tracker_ids);
        });

        $this->tracker_api->update($all_trackers);
    }

    function bind_permissions(array $reports)
    {
        $this->bulk_update_bind_table(
            $reports,
            'permissions',
            'get_all_report_binds',
            $this->report_permission_db,
            $this->permission_db
        );
    }

    function unbind_dead_permissions(array $reports)
    {
        $this->bulk_garbage_bind_table(
            $reports,
            'permissions',
            'get_all_report_binds',
            $this->report_permission_db,
            $this->permission_db
        );
    }

    function bind_trackers(array $reports)
    {

        $this->fill_non_exist_trackers($reports);

        foreach ($reports as $report) {
            $this->bind_report_trackers($report, $report['trackers']);
        }
    }

    private function fill_non_exist_trackers(array $reports)
    {
        $tracker_ids = array_reduce($reports, function ($carr, $report) {
            if (!isset($report['trackers'])) return $carr;
            return array_unique(
                [...$carr, ...$report['trackers']]
            );
        }, []);

        $db_trackers = $this->tracker_db->get_all_by_ids($tracker_ids);

        if (count($tracker_ids) != count($db_trackers)) {
            $db_tracker_ids = array_map(function ($tracker) {
                return $tracker['id'];
            }, $db_trackers);

            $new_trackers = array_filter($tracker_ids, function ($tracker_id) use ($db_tracker_ids) {
                return !in_array($tracker_id, $db_tracker_ids);
            });

            $this->update_trackers($new_trackers);
        }
    }

    private function bind_report_trackers(array $report, array $tracker_ids)
    {
        $exist_binds = $this->report_tracker_db->get_all_report_binds($report['report']);
        $exist_bind_tracker_ids = array_map(function ($exist_bind) {
            return $exist_bind['tracker_id'];
        }, $exist_binds);

        foreach ($tracker_ids as $tracker_id) {
            if (in_array($tracker_id, $exist_bind_tracker_ids)) continue;
            $this->report_tracker_db->insert($report['report'], $tracker_id);
        }
    }

    function unbind_dead_trackers(array $reports)
    {
        $this->bulk_garbage_bind_table(
            $reports,
            'trackers',
            'get_all_report_binds',
            $this->report_tracker_db,
            $this->tracker_db
        );
    }

    function get_latest_report_by_handle($handle)
    {
        $report = $this->report_db->get_last_version_by_handle($handle);
        if (!$report) return null;
        $tracker_binds = $this->report_tracker_db->get_all_report_binds($report['id']);
        $permission_binds = $this->report_permission_db->get_all_report_binds($report['id']);
        return array_merge($report, [
            'tracker_count' => count($tracker_binds),
            'permission_count' => count($permission_binds)
        ]);
    }

    function get_reports_by_handle($handle)
    {
        $reports = $this->report_db->get_all_by_handle($handle);
        return array_map(function ($report) {
            $tracker_binds = $this->report_tracker_db->get_all_report_binds($report['id']);
            $permission_binds = $this->report_permission_db->get_all_report_binds($report['id']);
            return array_merge($report, [
                'tracker_count' => count($tracker_binds),
                'permission_count' => count($permission_binds)
            ]);
        }, $reports);
    }

    function get_report_detail($handle, $version_code)
    {
        $report = $this->report_db->get_by_version($handle, $version_code);
        $report['trackers'] = $this->get_trackers_by_report_id($report['id']);
        $report['permissions'] = array_map(function ($category) {
            return $category['name'];
        }, $this->get_permissions_by_report_id($report['id']));

        return $report;
    }

    function get_trackers_by_report_id($report_id)
    {
        $tracker_binds = $this->report_tracker_db->get_all_report_binds($report_id);

        return array_map(function ($bind) {
            return $this->tracker_api->get_tracker_detail_by_id($bind['tracker_id']);
        }, $tracker_binds);
    }

    function get_permissions_by_report_id($report_id)
    {
        return $this->permission_db->get_all_by_ids(array_map(function ($bind) {
            return $bind['permission_id'];
        }, $this->report_permission_db->get_all_report_binds($report_id)));
    }
}
