<?
class WPPR_Review_Scores {
    private $table_name = 'wppr_review_scores';

    public function create_table() {
        global $wpdb;
		global $charset_collate;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$table_name = $wpdb->prefix . $this->table_name;
		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
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
}
