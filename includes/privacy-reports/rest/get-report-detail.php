<?
add_action('rest_api_init', function () {
    register_rest_route('wppr/v1', '/privacy/report', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'rest_get_privacy_report',
        'args' => array(
            'handle' => array(
                'type' => 'string',
                'required' => true,
                'validate_callback' => function ($param, $request, $key) {
                    return is_string($param);
                }
            ),
            'version_code' => array(
                'required' => true,
                'type' => 'integer'
            )
        ),
    ));
});


function rest_get_privacy_report($request)
{
    include_once WPPR_PATH . '/includes/privacy-reports/api/class-wppr-privacy-api-factory.php';

    $api = WPPR_Privacy_API_Factory::get_report_api();

    $report = $api->get_report_detail($request['handle'], $request['version_code']);

    wp_send_json_success($report);
}
