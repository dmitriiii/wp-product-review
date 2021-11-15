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
            $vpn_id =  $_GET['id'];
            $scores_db = new WPPR_Review_Scores();
            $data = $scores_db->get($vpn_id);
            [$product_post_map] = array_values(array_filter(wppr_get_product_post_map('vpn'), function ($map) use ($vpn_id) {
                return $map['vpnid'] == $vpn_id;
            }));

            if ($data) {
                $link_map = array_values(get_field('third_party_review_portal_links', $product_post_map['pid']));

                usort($data, function ($a, $b) use ($link_map) {
                    $a_ind = array_search($a->url, $link_map);
                    $b_ind = array_search($b->url, $link_map);
                    if ($a_ind == $b_ind) {
                        return 0;
                    }
                    return ($a_ind < $b_ind) ? -1 : 1;
                });
                wp_send_json_success($data);
            } else
                wp_send_json_error('No information');
        } catch (\Throwable $th) {
            wp_send_json_error($th->getMessage());
        }
    }
}

init_reviews_rest_api();
