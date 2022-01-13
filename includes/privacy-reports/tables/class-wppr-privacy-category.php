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
			`id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(128) NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `name` (`name`)
		)
        $charset_collate;";

        dbDelta($sql);
    }

    /**
     * @param string $category
     */
    public function add($category)
    {
        return $this->update($category) || $this->insert($category);
    }

    /**
     * @param string $category
     */
    public function update($category)
    {
        global $wpdb;

        $exist_category = $this->get_by_name($category);

        if (!$exist_category) return false;

        if ($wpdb->update(
            $this->table_name,
            [
                'name' => $category
            ],
            [
                'id' => $exist_category['id']
            ]
        )) return true;

        return false;
    }

    /**
     * @param string $category
     */
    public function insert($category)
    {
        global $wpdb;

        $exist_category = $this->get_by_name($category);

        if ($exist_category) return false;

        if ($wpdb->insert(
            $this->table_name,
            [
                'name' => $category
            ]
        )) return true;

        return false;
    }

    public function get_by_id($id)
    {
        global $wpdb;

        $category = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $this->table_name WHERE id = %d",
                $id
            ),
            ARRAY_A
        );

        return $category;
    }

    public function get_by_name($name)
    {
        global $wpdb;

        $category = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $this->table_name WHERE name = %s",
                $name
            ),
            ARRAY_A
        );

        return $category;
    }
}
