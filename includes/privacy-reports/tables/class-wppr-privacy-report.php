<?
include_once WPPR_PATH . '/includes/abstracts/abstract-class-wppr-table.php';

class WPPR_Privacy_Report extends WPPR_Abstract_Table
{
    function __construct()
    {
        parent::__construct('wppr_privacy_report');
    }

    function create_table()
    {
        global $charset_collate;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

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

    public function add($data) {
        
    }

    public function replace($opts)
    {
    }

    public function get($pid)
    {
    }
}
