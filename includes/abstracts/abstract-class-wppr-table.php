<?
abstract  class WPPR_Abstract_Table {
    protected $table_name = '';

    function __construct($dbname)
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . $dbname;
        $this->checkTableExist();
    }

    public function get_name() {
        return $this->table_name;
    }

    abstract function create_table();

    protected function checkTableExist()
    {
        global $wpdb;

        $query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($this->table_name));

        if ($wpdb->get_var($query) === $this->table_name) {
            return true;
        }
        $this->create_table();
    }
}