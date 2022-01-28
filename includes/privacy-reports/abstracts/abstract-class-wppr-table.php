<?
abstract  class WPPR_Abstract_Table
{
    protected $table_name = '';

    function __construct($dbname)
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . $dbname;
    }

    abstract function create_table();

    protected function checkTableExist()
    {
        $this->create_table();
    }

    protected function bulk_insert($rows)
    {
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

    /**
     * @param string|int $value
     */
    protected function get_by($field_name, $value)
    {
        global $wpdb;

        if (!$value) return [];

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $this->table_name WHERE $field_name = {is_numeric($value) ? '%d' : '%s'}",
            $value
        ), ARRAY_A);
    }

    protected function get_all_by($field_name, $values)
    {
        global $wpdb;
        
        if (!is_array($values)) $values = [$values];

        if (empty($values)) return [];
        
        $params = implode(' OR ', array_map(function ($val) use ($field_name) {
            $type = is_numeric($val) ? '%d' : '%s';
            return "$field_name = $type";
        }, $values));

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $this->table_name WHERE $params",
            $values
        ), ARRAY_A);
    }

    public function get_name()
    {
        return $this->table_name;
    }

    public function get_by_id($id)
    {
        global $wpdb;

        $data = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $this->table_name WHERE id = %d",
                $id
            ),
            ARRAY_A
        );

        return $data;
    }

    /**
     * @param (int|string)[] $ids
     */
    public function get_all_by_ids(array $ids) {
        return $this->get_all_by('id', $ids);
    }

    public function get_all()
    {
        global $wpdb;

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $this->table_name"
        ), ARRAY_A);
    }
}
