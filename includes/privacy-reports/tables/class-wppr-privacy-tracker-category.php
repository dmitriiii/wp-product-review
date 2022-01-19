<?
include_once WPPR_PATH . '/includes/privacy-reports/abstracts/abstract-class-wppr-bind-table.php';
include_once 'class-wppr-privacy-category.php';
include_once 'class-wppr-privacy-tracker.php';

class WPPR_Privacy_Tracker_Category extends WPPR_Abstract_Bind_Table
{
    function __construct()
    {
        parent::__construct('wppr_privacy_tracker_category', 'tracker_id', 'category_id');
    }

    public function create_table()
    {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $tracker_db = new WPPR_Privacy_Tracker();
        $category_db = new WPPR_Privacy_Category();

        $this->__create_table($tracker_db, $category_db);
    }

    /**
     * @param int $report_id
     * @param int $tracker_id
     */
    public function insert($tracker_id, $category_id)
    {
        return $this->__insert($tracker_id, $category_id);
    }

    /**
     * @param int $report_id
     * @param int $tracker_id
     */
    public function delete($tracker_id, $category_id)
    {
        return $this->__delete($tracker_id, $category_id);
    }

    /**
     * @param int $report_id
     * @param int $tracker_id
     */
    public function is_exist($tracker_id, $category_id)
    {
        return $this->__is_exist($tracker_id, $category_id);
    }

    public function get_all_tracker_binds($tracker_id)
    {
        return $this->__get_all_binds($this->first_bind, $tracker_id);
    }

    public function get_all_category_binds($category_id)
    {
        return $this->__get_all_binds($this->second_bind, $category_id);
    }
}
