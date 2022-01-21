<?
include_once WPPR_PATH . '/includes/privacy-reports/abstracts/abstract-class-wppr-data-table.php';

class WPPR_Privacy_Tracker extends WPPR_Abstract_Data_Table
{
    function __construct()
    {
        parent::__construct('wppr_privacy_tracker');
    }

    private function get_prepared_tracker($tracker)
    {
        $tracker_tracker = array_intersect_key(
            $tracker,
            array_flip(
                [
                    'id', 'code_signature', 'creation_date',
                    'description', 'name', 'network_signature',
                    'website'
                ]
            )
        );

        $prepared_tracker['id'] = $tracker['id'];

        return $tracker_tracker;
    }

    private function get_format($key)
    {
        switch ($key) {
            case 'id':
                return '%d';
            default:
                return '%s';
        }
    }

    private function is_need_update($new_tracker, $old_tracker)
    {
        if (
            $new_tracker['code_signature'] == $old_tracker['code_signature'] &&
            $new_tracker['name'] == $old_tracker['name'] &&
            $new_tracker['network_signature'] == $old_tracker['network_signature'] &&
            $new_tracker['website'] == $old_tracker['website'] &&
            $new_tracker['description'] == $old_tracker['description']
        ) return false;
        return true;
    }

    public function create_table()
    {
        global $charset_collate;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
			`id` int(11) NOT NULL AUTO_INCREMENT,
            `code_signature` varchar(512) NOT NULL,
            `creation_date` date NOT NULL,
            `description` text NOT NULL,
            `name` varchar(128) NOT NULL,
            `network_signature` varchar(512) NOT NULL,
            `website` varchar(128) NOT NULL,
            PRIMARY KEY (`id`)
		)
        $charset_collate;";

        dbDelta($sql);
    }

    public function add($tracker)
    {
        return $this->update($tracker) || $this->insert($tracker);
    }

    public function update($tracker)
    {
        global $wpdb;

        $exist_tracker = $this->get_by_id($tracker['id']);

        if (!$exist_tracker) return false;

        $prepared_tracker = $this->get_prepared_tracker($tracker);

        if (!$this->is_need_update($prepared_tracker, $exist_tracker)) return false;

        if ($wpdb->update(
            $this->table_name,
            $prepared_tracker,
            [
                'id' => $prepared_tracker['id']
            ],
            array_map([$this, 'get_format'], array_keys($prepared_tracker))
        )) return true;
        
        return false;
    }

    public function insert($tracker)
    {
        global $wpdb;

        if ($this->get_by_id($tracker['id'])) return false;

        $prepared_tracker = $this->get_prepared_tracker($tracker);
        
        if ($wpdb->insert(
            $this->table_name,
            $prepared_tracker,
            array_map(
                [$this, 'get_format'],
                array_keys($prepared_tracker)
            )
        )) return true;
        return false;
    }
    
    public function get_by_name($name)
    {
        
    }

    public function get_all_by_names(array $names)
    {
        
    }
}
