<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://themeisle.com/
 * @since      3.0.0
 *
 * @package    WPPR
 * @subpackage WPPR/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      3.0.0
 * @package    WPPR
 * @subpackage WPPR/includes
 * @author     ThemeIsle <friends@themeisle.com>
 */
class WPPR_Deactivator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    3.0.0
	 */
	public static function deactivate()
	{
		self::deactivate_extend_review_cron();
		self::deactivate_privacy_product_app_cron();
	}


	private static function deactivate_extend_review_cron()
	{
		include_once WPPR_PATH . '/includes/cron/third-party-reviews/class-wppr-tpr-manager.php';

		$tpr_cron_manager = new WPPR_TPR_Cron_Manager();
		$tpr_cron_manager->unschedule();
	}

	private static function deactivate_privacy_product_app_cron()
	{
		include_once WPPR_PATH . '/includes/privacy-reports/cron/class-wppr-ppr-manager.php';
		
		$pp_cron_manager = new WPPR_Product_Privacy_Cron_Manager();
		$pp_cron_manager->unschedule();
	}
}
