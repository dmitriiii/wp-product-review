<?
include_once WPPR_PATH . '/includes/privacy-reports/abstracts/abstract-class-wppr-data-table.php';

class WPPR_Privacy_Permission extends WPPR_Abstract_Data_Table
{
    private $perms_map = null;

    function __construct()
    {
        parent::__construct('wppr_privacy_permission');
    }

    private function pick_prop_permission($permission)
    {
        $prep_permission = array_intersect_key(
            $permission,
            array_flip(
                [
                    'id', 'name', 'description', 'protection_level'
                ]
            )
        );

        return $prep_permission;
    }


    public function create_table()
    {
        global $charset_collate;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sql = "CREATE TABLE {$this->table_name} (
			`id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(128) NOT NULL,
            `description` text NOT NULL,
            `protection_level` varchar(256) NOT NULL,
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

        $prep_permission = $this->get_prepared_permission($permission);
        $exist_permission = $this->get_by_name($prep_permission['name']);

        if (!$exist_permission) return false;
        if (!$this->is_need_update($prep_permission, $exist_permission)) return false;

        if ($wpdb->update(
            $this->table_name,
            $prep_permission,
            [
                'id' => $exist_permission['id']
            ]
        )) return true;

        return false;
    }

    private function is_need_update($prep_permission, $exist_permission)
    {
        if ($prep_permission['description'] == $exist_permission['description'] && $prep_permission['protection_level'] == $exist_permission['protection_level'])
            return false;
        return true;
    }

    /**
     * @param string $permission
     */
    public function insert($permission)
    {
        global $wpdb;

        $prep_permission = $this->get_prepared_permission($permission);
        $exist_permission = $this->get_by_name($prep_permission['name']);
        
        if ($exist_permission) return false;

        if ($wpdb->insert(
            $this->table_name,
            $prep_permission
        )) return true;

        return false;
    }

    private function get_prepared_permission($permission)
    {
        $map = $this->load_perms_map();

        if (is_string($permission))
            return isset($map[$permission]) ? $map[$permission] : [
                'name' => $permission
            ];
        else return isset($map[$permission['name']]) ?
            array_merge($map[$permission['name']], $this->pick_prop_permission($permission)) :
            $this->pick_prop_permission($permission);
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
    public function get_all_by_names($cats)
    {
        return $this->get_all_by('name', $cats);
    }

    private function load_perms_map()
    {
        if ($this->perms_map) return $this->perms_map;
        $this->perms_map = json_decode(file_get_contents(WPPR_PATH . '/includes/privacy-reports/consts/android-permissions.json'), true);
        return $this->perms_map;
    }
}
