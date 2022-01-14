<?
abstract  class WPPR_Abstract_Table {
    protected $table_name = '';

    function __construct($dbname)
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . $dbname;
        $this->checkTableExist();
    }

    abstract function create_table();

    public function get_name() {
        return $this->table_name;
    }

    protected function checkTableExist()
    {
        global $wpdb;

        $query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($this->table_name));

        if ($wpdb->get_var($query) === $this->table_name) {
            return true;
        }
        $this->create_table();
    }

    protected function bulk_insert($rows) {
        global $wpdb;
        
        // Extract column list from first row of data
        $columns = array_keys($rows[0]);
        asort($columns);
        $columnList = '`' . implode('`, `', $columns) . '`';
    
        // Start building SQL, initialise data and placeholder arrays
        $sql = "INSERT INTO `{$this->table_name}` ($columnList) VALUES\n";
        $placeholders = array();
        $data = array();
    
        // Build placeholders for each row, and add values to data array
        foreach ($rows as $row) {
            ksort($row);
            $rowPlaceholders = array();
    
            foreach ($row as $key => $value) {
                $data[] = $value;
                $rowPlaceholders[] = is_numeric($value) ? '%d' : '%s';
            }
    
            $placeholders[] = '(' . implode(', ', $rowPlaceholders) . ')';
        }
    
        // Stitch all rows together
        $sql .= implode(",\n", $placeholders);
    
        // Run the query.  Returns number of affected rows.
        return $wpdb->query($wpdb->prepare($sql, $data));
    }
}