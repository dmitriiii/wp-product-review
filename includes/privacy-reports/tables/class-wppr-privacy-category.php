<?
include_once WPPR_PATH . '/includes/abstracts/abstract-class-wppr-table.php';

class WPPR_Privacy_Category extends WPPR_Abstract_Table
{
    function __construct()
    {
        parent::__construct('wppr_privacy_category');
    }


    public function create_table()
    {
        global $charset_collate;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
			`id` int NOT NULL,
            `name` varchar(128) NOT NULL,
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
}
