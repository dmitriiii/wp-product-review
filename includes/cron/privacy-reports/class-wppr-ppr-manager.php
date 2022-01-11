<?
class WPPR_PPR_Cron_Manager
{
    function schedule()
    {
        if (!wp_next_scheduled('wppr_privacy_reports_cron')) {
            wp_schedule_event(strtotime('tomorrow midnight'), 'wppr_tpr_update', 'wppr_privacy_reports_cron');
        }
    }
    function unschedule()
    {
        wp_unschedule_hook('wppr_privacy_reports_cron');
        wp_unschedule_hook('wppr_privacy_reports_job');
    }
}
