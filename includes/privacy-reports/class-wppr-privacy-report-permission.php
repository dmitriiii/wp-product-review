<?
class WPPR_Privacy_Report_Permission
{
    private $table_name = '';

    function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'wppr_privacy_report_permission';
        $this->checkTableExist();
    }


    public function create_table()
    {
        global $charset_collate;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
			`id` int NOT NULL,
            `report_id` int NOT NULL,
            `permission_id` int NOT NULL,
            PRIMARY KEY (`id`),
            KEY `report_id` (`report_id`),
            KEY `permission_id` (`permission_id`)
		)
        $charset_collate;";

        dbDelta($sql);
    }

    public function replace($opts)
    {
    }

    public function get($pid)
    {
    }

    private function checkTableExist()
    {
        global $wpdb;

        $query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($this->table_name));

        if ($wpdb->get_var($query) === $this->table_name) {
            return true;
        }
        $this->create_table();
    }
}
