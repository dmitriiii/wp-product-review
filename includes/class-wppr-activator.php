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
		self::create_extend_review();
		self::create_privacy_report();
	}

	private static function create_extend_review()
	{
		include_once WPPR_PATH . '/includes/class-wppr-review-score.php';
		include_once WPPR_PATH . '/includes/cron/third-party-reviews/class-wppr-tpr-manager.php';
		
		new WPPR_Review_Scores();

		$tpr_cron_manager = new WPPR_TPR_Cron_Manager();
		$tpr_cron_manager->schedule();
	}

	private static function create_privacy_report()
	{
		include_once WPPR_PATH . '/includes/privacy-reports/tables/class-wppr-privacy-report.php';
		include_once WPPR_PATH . '/includes/privacy-reports/tables/class-wppr-privacy-permission.php';
		include_once WPPR_PATH . '/includes/privacy-reports/tables/class-wppr-privacy-category.php';
		include_once WPPR_PATH . '/includes/privacy-reports/tables/class-wppr-privacy-tracker.php';
		include_once WPPR_PATH . '/includes/privacy-reports/tables/class-wppr-privacy-report-permission.php';
		include_once WPPR_PATH . '/includes/privacy-reports/tables/class-wppr-privacy-tracker-category.php';
		include_once WPPR_PATH . '/includes/privacy-reports/tables/class-wppr-privacy-report-tracker.php';
		include_once WPPR_PATH . '/includes/privacy-reports/cron/class-wppr-ppr-manager.php';

		(new WPPR_Privacy_Report())->create_table();
		(new WPPR_Privacy_Tracker())->create_table();
		(new WPPR_Privacy_Category())->create_table();
		(new WPPR_Privacy_Permission())->create_table();
		(new WPPR_Privacy_Tracker_Category())->create_table();
		(new WPPR_Privacy_Report_Permission())->create_table();
		(new WPPR_Privacy_Report_Tracker())->create_table();

		(new WPPR_Product_Privacy_Cron_Manager())->schedule();
	}
}
