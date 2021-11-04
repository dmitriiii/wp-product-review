<?
class WPPR_TPR_Cron_Manager
{
    function schedule()
    {
        if (!wp_next_scheduled('wppr_tpr_parse')) {
            wp_schedule_event(strtotime('today midnight'), 'daily', 'wppr_tpr_parse');
        }
    }
    function unschedule()
    {
        if (wp_next_scheduled('wppr_tpr_parse')) {
            wp_clear_scheduled_hook('wppr_tpr_parse');
        }
    }
}
