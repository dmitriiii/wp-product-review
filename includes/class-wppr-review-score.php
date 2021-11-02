<?
class WPPR_Review_Scores
{
    private $table_name = '';

    function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'wppr_review_scores';
    }


    public function create_table()
    {
        global $charset_collate;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
			`id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            `pid` bigint NOT NULL,
            `rating` int NOT NULL DEFAULT '0',
            `source` varchar(30) NOT NULL DEFAULT '',
            `url` varchar(100) NOT NULL DEFAULT '',
            `votes` bigint NOT NULL DEFAULT '0',
            `lastUpdate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `pid` (`pid`)
		)
        $charset_collate;";

        dbDelta($sql);
    }

    public function replace($opts)
    {
        global $wpdb;
        if (!isset($opts['pid'], $opts['rating'], $opts['votes'])) return false;
        $wpdb->replace($this->table_name, [
            'lastUpdate' => date_create()->format('Y-m-d H:i:s'),
            ...$opts
        ]);
        return true;
    }

    public function get($pid)
    {
        global $wpdb;
        if (!isset($pid)) return false;

        $data = $wpdb->get_results("SELECT * FROM {$this->table_name} WHERE pid = $pid");
        return $data;
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
