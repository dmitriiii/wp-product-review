<?
include_once WPPR_PATH . '/includes/privacy-reports/abstracts/abstract-class-wppr-bind-table.php';
include_once 'class-wppr-privacy-report.php';
include_once 'class-wppr-privacy-permission.php';

class WPPR_Privacy_Report_Permission extends WPPR_Abstract_Bind_Table
{
    function __construct()
    {
        parent::__construct('wppr_privacy_report_permission', 'report_id', 'permission_id');
    }

    public function create_table()
    {
        $report_db = new WPPR_Privacy_Report();
        $perms_db = new WPPR_Privacy_Permission();

        $this->__create_table($report_db, $perms_db);
    }

    /**
     * @param int $report_id
     * @param int $permission_id
     */
    public function insert($report_id, $permission_id)
    {
        return $this->__insert($report_id, $permission_id);
    }

    /**
     * @param int $report_id
     * @param int $permission_id
     */
    public function delete($report_id, $permission_id)
    {
        return $this->__delete($report_id, $permission_id);
    }

    /**
     * @param int $report_id
     * @param int $permission_id
     */
    public function is_exist($report_id, $permission_id)
    {
        return $this->__is_exist($report_id, $permission_id);
    }

    public function get_all_report_binds($report_id)
    {
        return $this->__get_all_binds($this->first_bind, $report_id);
    }

    public function get_all_permission_binds($permission_id)
    {
        return $this->__get_all_binds($this->second_bind, $permission_id);
    }
}
