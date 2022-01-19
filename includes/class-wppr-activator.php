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
		include_once WPPR_PATH . '/includes/cron/privacy-reports/class-wppr-ppr-manager.php';
		
		new WPPR_Privacy_Report();
		new WPPR_Privacy_Tracker();
		new WPPR_Privacy_Category();
		new WPPR_Privacy_Permission();
		new WPPR_Privacy_Tracker_Category();
		new WPPR_Privacy_Report_Permission();
		new WPPR_Privacy_Report_Tracker();

		$ppr_cron_manager = new WPPR_PPR_Cron_Manager();
		$ppr_cron_manager->schedule();
	}
}
