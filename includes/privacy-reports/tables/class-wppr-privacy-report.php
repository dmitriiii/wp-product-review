<?
include_once WPPR_PATH . '/includes/abstracts/abstract-class-wppr-table.php';

class WPPR_Privacy_Report extends WPPR_Abstract_Table
{
    function __construct()
    {
        parent::__construct('wppr_privacy_report');
    }

    private function get_prepared_report($report)
    {
        $prepared_report = array_intersect_key(
            $report,
            array_flip(
                [
                    'apk_hash', 'app_name', 'created',
                    'creator', 'downloads', 'handle',
                    'icon_hash', 'report', 'source',
                    'uaid', 'updated', 'version_code', 'version_name'
                ]
            )
        );
        return $prepared_report;
    }

    private function get_format($key)
    {
        switch ($key) {
            case 'downloads':
                return '%d';
            case 'report':
                return '%d';
            default:
                return '%s';
        }
    }

    function create_table()
    {
        global $charset_collate;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
			`id` int NOT NULL,
            `apk_hash` varchar(128) NOT NULL,
            `app_name` varchar(128) NOT NULL,
            `created` datetime NOT NULL,
            `creator` varchar(100) NOT NULL,
            `downloads` int NOT NULL,
            `handle` varchar(64) NOT NULL,
            `icon_hash` varchar(128) NOT NULL,
            `report` int NOT NULL,
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
        global $wpdb;
        $wpdb->show_errors();

        $prepared_report = $this->get_prepared_report($report);

        $up_res = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $this->table_name WHERE report = %d",
                $prepared_report['report']
            )
        );

        if ($up_res && count($up_res))
            return $this->update($report);
        else
            return $this->insert($report);
    }

    public function update($report)
    {
        global $wpdb;
        $wpdb->show_errors();

        $prepared_report = $this->get_prepared_report($report);

        $wpdb->update(
            $this->table_name,
            $prepared_report,
            [
                'report' => $prepared_report['report']
            ],
            array_map([$this, 'get_format'], array_keys($prepared_report))
        );

        return true;
    }

    public function insert($report)
    {
        global $wpdb;
        $wpdb->show_errors();

        $prepared_report = $this->get_prepared_report($report);

        $wpdb->insert($this->table_name, $prepared_report, array_map([$this, 'get_format'], array_keys($prepared_report)));

        return true;
    }

    public function get($pid)
    {
    }
}
