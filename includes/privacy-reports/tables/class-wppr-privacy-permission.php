<?
include_once WPPR_PATH . '/includes/privacy-reports/abstracts/abstract-class-wppr-data-table.php';

class WPPR_Privacy_Permission extends WPPR_Abstract_Data_Table
{
    function __construct()
    {
        parent::__construct('wppr_privacy_permission');
    }


    public function create_table()
    {
        global $charset_collate;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
			`id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(128) NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `name` (`name`)
		)
        $charset_collate;";

        dbDelta($sql);
    }

    /**
     * @param string $permission
     */
    public function add($permission)
    {
        return $this->update($permission) || $this->insert($permission);
    }

    /**
     * @param string $permission
     */
    public function update($permission)
    {
        global $wpdb;

        $exist_permission = $this->get_by_name($permission);

        if (!$exist_permission) return false;

        if ($wpdb->update(
            $this->table_name,
            [
                'name' => $permission
            ],
            [
                'id' => $exist_permission['id']
            ]
        )) return true;

        return false;
    }

    /**
     * @param string $permission
     */
    public function insert($permission)
    {
        global $wpdb;

        $exist_permission = $this->get_by_name($permission);

        if ($exist_permission) return false;

        if ($wpdb->insert(
            $this->table_name,
            [
                'name' => $permission
            ]
        )) return true;

        return false;
    }

    public function get_by_name($name)
    {
        global $wpdb;

        $report = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $this->table_name WHERE name = %s",
                $name
            ),
            ARRAY_A
        );

        return $report;
    }

    /**
     * @param string[] $perms
     */
    public function get_all_by_names($cats) {
        return $this->get_all_by('name', $cats);
    }
}
