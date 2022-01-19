<?
include_once WPPR_PATH . '/includes/privacy-reports/abstracts/abstract-class-wppr-data-table.php';

class WPPR_Privacy_Category extends WPPR_Abstract_Data_Table
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

    /**
     * @param string[] $cats
     */
    public function get_all_by_names($cats) {
        return $this->get_all_by('name', $cats);
    }

    public function get_all()
    {
        global $wpdb;

        $category = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $this->table_name"
            ),
            ARRAY_A
        );

        return $category;
    }
}
