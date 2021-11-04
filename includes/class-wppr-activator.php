<?php

/**
 * Fired during plugin activation
 *
 * @link       https://themeisle.com/
 * @since      3.0.0
 *
 * @package    WPPR
 * @subpackage WPPR/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      3.0.0
 * @package    WPPR
 * @subpackage WPPR/includes
 * @author     ThemeIsle <friends@themeisle.com>
 */
class WPPR_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		$actvator = new WPPR_Activator();
		$actvator->create_extend_review_db();
	}

	private function create_extend_review_db()
	{
		include_once WPPR_PATH . '/includes/class-wppr-review-score.php';
		include_once WPPR_PATH . '/includes/cron/third-party-reviews/class-wppr-tpr-manager.php';

		$scores_db = new WPPR_Review_Scores();
		$scores_db->create_table();

		$tpr_cron_manager = new WPPR_TPR_Cron_Manager();
		$tpr_cron_manager->schedule();
	}
}
