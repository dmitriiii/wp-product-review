<?
function init_reviews_rest_api()
{
    add_action("wp_ajax_get_ext_reviews", "get_ext_reviews");
    add_action("wp_ajax_nopriv_get_ext_reviews", "get_ext_reviews");

    function get_ext_reviews()
    {
        include_once WPPR_PATH . '/includes/class-wppr-review-score.php';

        if (!$_GET['id']) {
            wp_send_json_error('No id');
            return;
        }
        try {
            $scores_db = new WPPR_Review_Scores();
            $data = $scores_db->get($_GET['id']);
            if ($data)
                wp_send_json_success($data);
            else
                wp_send_json_error('No information');
        } catch (\Throwable $th) {
            wp_send_json_error($th->getMessage());
        }
    }
}

init_reviews_rest_api();
