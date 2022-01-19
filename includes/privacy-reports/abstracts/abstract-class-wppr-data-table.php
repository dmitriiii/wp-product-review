<?
include_once 'abstract-class-wppr-table.php';

abstract  class WPPR_Abstract_Data_Table extends WPPR_Abstract_Table {
    
    function __construct($table_name)
    {
        parent::__construct($table_name);
    }

    abstract function add(array $data);
    abstract function get_by_name(string $name);
    abstract function get_all_by_names(array $names);
}