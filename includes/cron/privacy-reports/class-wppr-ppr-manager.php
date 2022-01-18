<?
class WPPR_PPR_Cron_Manager
{
    function schedule()
    {
        if (!wp_next_scheduled('wppr_privacy_report_cron')) {
            wp_schedule_event(strtotime('tomorrow midnight'), 'wppr_report_update', 'wppr_privacy_report_cron');
        }

        if (!wp_next_scheduled('wppr_privacy_tracker_cron')) {
            wp_schedule_event(time(), 'wppr_tracker_update', 'wppr_privacy_tracker_cron');
        }
    }
    function unschedule()
    {
        wp_unschedule_hook('wppr_privacy_report_cron');
        wp_unschedule_hook('wppr_privacy_report_job');

        wp_unschedule_hook('wppr_privacy_tracker_cron');
    }
}
