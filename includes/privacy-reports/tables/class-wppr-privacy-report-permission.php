<?
include_once WPPR_PATH . '/includes/abstracts/abstract-class-wppr-table.php';

class WPPR_Privacy_Report_Permission extends WPPR_Abstract_Table
{
    function __construct()
    {
        parent::__construct('wppr_privacy_report_permission');
    }


    public function create_table()
    {
        global $charset_collate;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        include_once WPPR_PATH . '/includes/privacy-reports/class-wppr-privacy-report.php';
        include_once WPPR_PATH . '/includes/privacy-reports/class-wppr-privacy-permission.php';

        $report_db = new WPPR_Privacy_Report();
        $perms_db = new WPPR_Privacy_Permission();

        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
			`id` int(11) NOT NULL AUTO_INCREMENT,
            `report_id` int(11) NOT NULL,
            `permission_id` int(11) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `report_id` (`report_id`),
            KEY `permission_id` (`permission_id`)
		)
        $charset_collate;
        
        ALTER TABLE {$this->table_name}
            ADD CONSTRAINT `report_b3` FOREIGN KEY (`report_id`) REFERENCES {$report_db->get_name()} (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
            ADD CONSTRAINT `permission_b3` FOREIGN KEY (`permission_id`) REFERENCES {$perms_db->get_name()} (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
        COMMIT;";

        dbDelta($sql);
    }

    public function replace($opts)
    {
    }

    public function get($pid)
    {
    }
}
