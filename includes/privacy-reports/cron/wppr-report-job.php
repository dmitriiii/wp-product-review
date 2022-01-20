<?
function wppr_report_cron()
{
    if (!get_field('enable_privacy_reports', 'option')) return;

    $product_post_map = wppr_get_product_post_map('vpn');
    $count = 0;
    $day_count = 0;
    $day_group_size = ceil(count($product_post_map) / 20);

    foreach ($product_post_map as $map) {
        $vpn_id = $map['vpnid'];
        $link_map = get_field('third_party_review_portal_links', $map['pid']);
        if (!$link_map) return;
        if (!count($link_map) || !count(array_filter(array_values($link_map), function ($link) {
            return strpos($link, 'google') != false;
        }))) continue;

        $count++;
        if ($count % ($day_group_size + 1) == 0) {
            $count = 1;
            $day_count++;
        }
        
        wp_schedule_single_event(time() + 300 * $count + $day_count * 86400, 'wppr_privacy_report_job', [
            $vpn_id
        ]);
    }
}
add_action('wppr_privacy_report_cron', 'wppr_report_cron');

function wppr_report_job($vpn_id)
{
    include_once WPPR_PATH . '/includes/privacy-reports/api/class-wppr-privacy-api-factory.php';
    $report_api = WPPR_Privacy_API_Factory::get_report_api();
    $privacy_report_api = WPPR_Privacy_API_Factory::get_fetch_api();
    [$product_post_map] = [...array_filter(wppr_get_product_post_map('vpn'), function ($map) use ($vpn_id) {
        return $map['vpnid'] == $vpn_id;
    })];

    $link_map = get_field('third_party_review_portal_links', $product_post_map['pid']);

    if (!$link_map) return;

    [$google_link] = [...array_filter(array_values($link_map), function ($link) {
        return strpos($link, 'google') != false;
    })];

    if (!$google_link) return;

    parse_str(parse_url($google_link, PHP_URL_QUERY), $query_params);

    $app_name = isset($query_params['id']) ? $query_params['id'] : '';

    if (!$app_name) return;

    $reports = $privacy_report_api::get_reports($app_name);
    $report_api->update($reports);
}

add_action('wppr_privacy_report_job', 'wppr_report_job', 10, 1);
