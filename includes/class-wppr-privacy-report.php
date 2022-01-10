<?
class WPPR_Privacy_Report
{
    private $table_name = '';

    function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'wppr_privacy_reports';
        $this->checkTableExist();
    }


    public function create_table()
    {
        global $charset_collate;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
			`id` int NOT NULL,
            `apk_hash` varchar(128) NOT NULL,
            `app_name` varchar(128) NOT NULL,
            `created` datetime NOT NULL,
            `creator` varchar(100) NOT NULL,
            `downloads` int NOT NULL,
            `handle` varchar(64) NOT NULL,
            `icon_hash` varchar(128) NOT NULL,
            `permissions` json DEFAULT NULL,
            `report` int NOT NULL,
            `source` varchar(64) NOT NULL,
            `trackers` json DEFAULT NULL,
            `uaid` varchar(128) NOT NULL,
            `updated` datetime NOT NULL,
            `version_code` varchar(64) NOT NULL,
            `version_name` varchar(64) NOT NULL,
            PRIMARY KEY (`id`)
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
