<?
include_once WPPR_PATH . '/includes/privacy-reports/abstracts/abstract-class-wppr-data-table.php';

class WPPR_Privacy_Report extends WPPR_Abstract_Data_Table
{
    function __construct()
    {
        parent::__construct('wppr_privacy_report');
    }

    function get_prepared_report($report)
    {
        $prepared_report = array_intersect_key(
            $report,
            array_flip(
                [
                    'id', 'apk_hash', 'app_name', 'created',
                    'creator', 'downloads', 'handle',
                    'icon_hash', 'source',
                    'uaid', 'updated', 'version_code', 'version_name'
                ]
            )
        );

        if (isset($report['report'])) $prepared_report['id'] = $report['report'];

        return $prepared_report;
    }

    private function get_format($key)
    {
        switch ($key) {
            case 'downloads':
                return '%d';
            case 'id':
                return '%d';
            default:
                return '%s';
        }
    }

    private function is_need_update($new_report, $old_report) {
        if ($new_report['updated'] == $old_report['updated']) return false;
        return true;
    }

    function create_table()
    {
        global $charset_collate;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
			`id` int(11) NOT NULL AUTO_INCREMENT,
            `apk_hash` varchar(128) NOT NULL,
            `app_name` varchar(128) NOT NULL,
            `created` datetime NOT NULL,
            `creator` varchar(100) NOT NULL,
            `downloads` int NOT NULL,
            `handle` varchar(64) NOT NULL,
            `icon_hash` varchar(128) NOT NULL,
            `source` varchar(64) NOT NULL,
            `uaid` varchar(128) NOT NULL,
            `updated` datetime NOT NULL,
            `version_code` varchar(64) NOT NULL,
            `version_name` varchar(64) NOT NULL,
            PRIMARY KEY (`id`)
		)
        $charset_collate;";

        dbDelta($sql);
    }

    public function add($report)
    {
        return $this->update($report) || $this->insert($report);
    }

    public function update($report)
    {
        global $wpdb;

        $prepared_report = $this->get_prepared_report($report);
        $exist_report = $this->get_by_id($prepared_report['id']);

        if (!$exist_report) return false;

        if (!$this->is_need_update($prepared_report, $exist_report)) return false;

        if ($wpdb->update(
            $this->table_name,
            $prepared_report,
            [
                'id' => $prepared_report['id']
            ],
            array_map([$this, 'get_format'], array_keys($prepared_report))
        )) return true;
        
        return false;
    }

    public function insert($report)
    {
        global $wpdb;

        $prepared_report = $this->get_prepared_report($report);
        if ($this->get_by_id($prepared_report['id'])) return false;

        if ($wpdb->insert(
            $this->table_name,
            $prepared_report,
            array_map(
                [$this, 'get_format'],
                array_keys($prepared_report)
            )
        )) return true;
        return false;
    }

    public function get_by_id($report_id)
    {
        global $wpdb;

        $report = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $this->table_name WHERE id = %d",
                $report_id
            ), ARRAY_A
        );

        return $report;
    }

    public function get_by_name($name)
    {
        
    }
    public function get_all_by_names(array $names)
    {
        
    }
}
