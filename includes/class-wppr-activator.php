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

		$score_db = new WPPR_Review_Scores();
		$score_db->create_table();
		
		/////
		$report_db = new WPPR_Privacy_Report();
		$report_db->create_table();

		$tracker_db = new WPPR_Privacy_Tracker();
		$tracker_db->create_table();

		$category_db = new WPPR_Privacy_Category();
		$category_db->create_table();

		$permsission_db = new WPPR_Privacy_Permission();
		$permsission_db->create_table();

		$tracker_category_db = new WPPR_Privacy_Tracker_Category();
		$tracker_category_db->create_table();

		$report_permsission_db = new WPPR_Privacy_Report_Permission();
		$report_permsission_db->create_table();

		$report_tracker_db = new WPPR_Privacy_Report_Tracker();
		$report_tracker_db->create_table();
		////

		$tpr_cron_manager = new WPPR_TPR_Cron_Manager();
		$tpr_cron_manager->schedule();
	}
}
