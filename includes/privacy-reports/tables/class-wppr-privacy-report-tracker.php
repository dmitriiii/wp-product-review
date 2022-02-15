<?
include_once WPPR_PATH . '/includes/privacy-reports/abstracts/abstract-class-wppr-bind-table.php';
include_once 'class-wppr-privacy-report.php';
include_once 'class-wppr-privacy-tracker.php';

class WPPR_Privacy_Report_Tracker extends WPPR_Abstract_Bind_Table
{
    function __construct()
    {
        parent::__construct('wppr_privacy_report_tracker', 'report_id', 'tracker_id');
    }

    public function create_table()
    {
        $report_db = new WPPR_Privacy_Report();
        $tracker_db = new WPPR_Privacy_Tracker();

        $this->__create_table($report_db, $tracker_db);
    }

    /**
     * @param int $report_id
     * @param int $tracker_id
     */
    public function insert($report_id, $tracker_id)
    {
        return $this->__insert($report_id, $tracker_id);
    }

    /**
     * @param int $report_id
     * @param int $tracker_id
     */
    public function delete($report_id, $tracker_id)
    {
        return $this->__delete($report_id, $tracker_id);
    }

    /**
     * @param int $report_id
     * @param int $tracker_id
     */
    public function is_exist($report_id, $tracker_id)
    {
        return $this->__is_exist($report_id, $tracker_id);
    }

    public function get_all_report_binds($report_id)
    {
        return $this->__get_all_binds($this->first_bind, $report_id);
    }

    public function get_all_tracker_binds($tracker_id)
    {
        return $this->__get_all_binds($this->second_bind, $tracker_id);
    }
}
