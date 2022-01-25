<?
add_action('rest_api_init', function () {
    register_rest_route('wppr/v1', '/privacy/reports/(?P<handle>[a-z|.]+)', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'rest_get_privacy_reports',
        'args' => array(
            'handle' => array(
                'type' => 'string',
                'required' => true,
                'validate_callback' => function ($param, $request, $key) {
                    return is_string($param);
                }
            ),
            'version_code' => array(
                'type' => 'integer'
            )
        ),
    ));
});


function rest_get_privacy_reports($request)
{
    include_once WPPR_PATH . '/includes/privacy-reports/api/class-wppr-privacy-api-factory.php';
    if (!isset($request['handle']))
        wp_send_json_error([
            'message' => 'Missing handle'
        ], 400);

    $api = WPPR_Privacy_API_Factory::get_report_api();

    $reports = $api->get_reports_by_handle($request['handle']);

    wp_send_json_success($reports);
}
