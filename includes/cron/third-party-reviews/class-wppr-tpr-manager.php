<?
class WPPR_TPR_Cron_Manager
{
    function schedule()
    {
        if (!wp_next_scheduled('wppr_third_party_reviews_cron')) {
            wp_schedule_event(strtotime('tomorrow midnight'), 'wppr_tpr_update', 'wppr_third_party_reviews_cron');
        }
    }
    function unschedule()
    {
        wp_unschedule_hook('wppr_third_party_reviews_cron');
        wp_unschedule_hook('wppr_third_party_review_job');
    }
}
