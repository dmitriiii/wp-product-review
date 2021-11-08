<?
class WPPR_TPR_Cron_Manager
{
    function schedule()
    {
        if (!wp_next_scheduled('wppr_third_party_reviews_cron')) {
            wp_schedule_event(strtotime('today midnight'), 'daily', 'wppr_third_party_reviews_cron');
        }
    }
    function unschedule()
    {
        if (wp_next_scheduled('wppr_third_party_reviews_cron')) {
            wp_clear_scheduled_hook('wppr_third_party_reviews_cron');
        }
    }
}
