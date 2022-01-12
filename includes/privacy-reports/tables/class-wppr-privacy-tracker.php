<?
include_once WPPR_PATH . '/includes/abstracts/abstract-class-wppr-table.php';

class WPPR_Privacy_Tracker extends WPPR_Abstract_Table
{
    function __construct()
    {
        parent::__construct('wppr_privacy_tracker');
    }

    public function get_name() {
        return $this->table_name;
    }

    public function create_table()
    {
        global $charset_collate;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
			`id` int NOT NULL,
            `code_signature` varchar(128) NOT NULL,
            `creation_date` date NOT NULL,
            `description` text NOT NULL,
            `name` varchar(128) NOT NULL,
            `network_signature` varchar(128) NOT NULL,
            `website` varchar(128) NOT NULL,
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
