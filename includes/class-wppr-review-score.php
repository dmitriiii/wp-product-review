<?
class WPPR_Review_Scores
{
    private $table_name = '';

    function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'wppr_review_scores';
        $this->checkTableExist();
    }


    public function create_table()
    {
        global $charset_collate;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
			`id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            `pid` bigint NOT NULL,
            `rating` int NOT NULL DEFAULT '0',
            `source` varchar(40) NOT NULL DEFAULT '',
            `url` varchar(200) NOT NULL DEFAULT '',
            `votes` bigint NOT NULL DEFAULT '0',
            `lastUpdate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
		)
        $charset_collate;";

        dbDelta($sql);
    }

    public function replace($opts)
    {
        global $wpdb;

        $wpdb->show_errors();
        if (!isset($opts['pid'], $opts['rating'], $opts['votes'], $opts['source'])) return false;
        $up_res = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $this->table_name WHERE pid = %d AND source = %s",
                $opts['pid'],
                $opts['source']
            )
        );
        $data = $opts;

        if ($up_res && count($up_res))
            $wpdb->update(
                $this->table_name,
                $data,
                [
                    'pid' => $opts['pid'],
                    'source' => $opts['source']
                ]
            );
        else
            $wpdb->insert($this->table_name, $data, array_map(function ($field) {
                switch ($field) {
                    case 'pid':
                        return '%d';
                    case 'rating':
                        return '%d';
                    case 'votes':
                        return '%d';
                    default:
                        return '%s';
                }
            }, array_keys($data)));

        return true;
    }

    public function get($pid)
    {
        global $wpdb;

        if (!isset($pid)) return false;

        $data = $wpdb->get_results("SELECT * FROM {$this->table_name} WHERE pid = $pid");
        return $data;
    }

    private function checkTableExist()
    {
        global $wpdb;

        $query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($this->table_name));

        if ($wpdb->get_var($query) === $this->table_name) {
            return true;
        }
        $this->create_table();
    }

    public function get_total_votes_count($pid)
    {
        $data = $this->get($pid);
        if (!$data) return -1;
        return array_reduce($data, function ($carr, $item) {
            return $carr + floatval($item->votes);
        }, 0);
    }
    public function get_avg_rating($pid)
    {
        $data = $this->get($pid);
        if (!$data) return -1;
        return round(array_reduce($data, function ($carr, $item) {
            return $carr + floatval($item->rating);
        }, 0) / count($data), 2);
    }
}
