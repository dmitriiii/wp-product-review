<?
class WPPR_Privacy_Tracker
{
    private $table_name = '';

    function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'wppr_privacy_trackers';
        $this->checkTableExist();
    }


    public function create_table()
    {
        global $charset_collate;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
			`id` int NOT NULL,
            `categories` json NOT NULL,
            `code_signature` varchar(128) NOT NULL,
            `creation_date` date NOT NULL,
            `description` text NOT NULL,
            `tracker_id` int NOT NULL,
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
