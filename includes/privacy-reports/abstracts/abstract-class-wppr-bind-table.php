<?
include_once 'abstract-class-wppr-table.php';

abstract  class WPPR_Abstract_Bind_Table extends WPPR_Abstract_Table {
    protected $first_bind = '';
    protected $second_bind = '';

    abstract function insert($a, $b);
    abstract function delete($a, $b);
    abstract function is_exist($a, $b);

    function __construct($table_name, $first_bind, $second_bind) {
        parent::__construct($table_name);
        $this->first_bind =  $first_bind;
        $this->second_bind =  $second_bind;
    }

    public function __create_table($first_bind_table, $second_bind_table)
    {
        global $charset_collate;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `{$this->first_bind}` int(11) NOT NULL,
            `{$this->second_bind}` int(11) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `{$this->first_bind}` (`{$this->first_bind}`),
            KEY `{$this->second_bind}` (`{$this->second_bind}`)
		) $charset_collate;

        ALTER TABLE {$this->table_name}
            ADD CONSTRAINT `{$this->table_name}_{$this->first_bind}` FOREIGN KEY (`{$this->first_bind}`) REFERENCES {$first_bind_table->get_name()} (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
            ADD CONSTRAINT `{$this->table_name}_{$this->second_bind}` FOREIGN KEY (`{$this->second_bind}`) REFERENCES {$second_bind_table->get_name()} (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
        COMMIT;";

        dbDelta($sql);
    }

    protected function __insert($value_a, $value_b) {
        global $wpdb;

        if ($this->__is_exist($value_a, $value_b)) return false;

        if ($wpdb->insert(
            $this->table_name,
            [
                "$this->first_bind" => $value_a,
                "$this->second_bind" => $value_b
            ]
        )) return true;

        return false;
    }

    protected function __delete($value_a, $value_b) {
        global $wpdb;

        if (!$this->__is_exist($value_a, $value_b)) return false;

        if ($wpdb->delete(
            $this->table_name,
            [
                "$this->first_bind" => $value_a,
                "$this->second_bind" => $value_b
            ]
        )) return true;

        return false;
    }

    protected function __is_exist($value_a, $value_b)
    {
        global $wpdb;

        $bind = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $this->table_name WHERE $this->first_bind = %d AND $this->second_bind = %d",
                $value_a,
                $value_b
            ),
            ARRAY_A
        );

        return !!$bind;
    }

    protected function __get_all_binds($bind, $value)
    {
        global $wpdb;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $this->table_name WHERE $bind = %d",
                $value
            ),
            ARRAY_A
        );
    }
}