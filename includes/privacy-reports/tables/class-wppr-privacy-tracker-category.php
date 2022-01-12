<?
include_once WPPR_PATH . '/includes/abstracts/abstract-class-wppr-table.php';

class WPPR_Privacy_Tracker_Category extends WPPR_Abstract_Table
{
    function __construct()
    {
        parent::__construct('wppr_privacy_tracker_category');
    }

    public function create_table()
    {
        global $charset_collate;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        include_once WPPR_PATH . '/includes/privacy-reports/class-wppr-privacy-category.php';
        include_once WPPR_PATH . '/includes/privacy-reports/class-wppr-privacy-tracker.php';

        $category_db = new WPPR_Privacy_Category();
        $tracker_db = new WPPR_Privacy_Tracker();

        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
			`id` int NOT NULL,
            `tracker_id` int NOT NULL,
            `category_id` int NOT NULL,
            PRIMARY KEY (`id`),
            KEY `category_id` (`category_id`),
            KEY `tracker_id` (`tracker_id`)
		)
        $charset_collate;
        
        ALTER TABLE {$this->table_name}
            ADD CONSTRAINT `category_b1` FOREIGN KEY (`category_id`) REFERENCES {$category_db->get_name()} (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
            ADD CONSTRAINT `tracker_b1` FOREIGN KEY (`tracker_id`) REFERENCES {$tracker_db->get_name()} (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
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
