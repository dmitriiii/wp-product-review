<?
add_action('rest_api_init', function () {
    register_rest_route('wppr/v1', 
    WPPR_Privacy_Reports_Rest_Route::get_route() . '/(?P<handle>[a-z|.]+)', 
    WPPR_Privacy_Reports_Rest_Route::get_route_option());
});

class WPPR_Privacy_Reports_Rest_Route extends WPPR_Abstract_Rest_Route
{
    static protected $route = '/privacy/reports';

    static function get_route_option()
    {
        return  [
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => ['WPPR_Privacy_Reports_Rest_Route', 'handler'],
                'permission_callback' => ['WPPR_Privacy_Reports_Rest_Route', 'permission_handler'],
                'args' => array(
                    'handle' => array(
                        'type' => 'string',
                        'required' => true,
                        'validate_callback' => ['WPPR_Privacy_Reports_Rest_Route', 'validate_handle']
                    ),
                    'version_code' => array(
                        'type' => 'integer'
                    )
                ),
            ],
            'schema' => ['WPPR_Privacy_Reports_Rest_Route', 'get_schema']
        ];
    }

    static function permission_handler(WP_REST_Request $request)
    {
        return !!$request->get_header('X-WP-Nonce');
    }

    static function validate_handle($param)
    {
        return is_string($param);
    }

    static function handler(WP_REST_Request $request)
    {
        include_once WPPR_PATH . '/includes/privacy-reports/api/class-wppr-privacy-api-factory.php';
        if (!isset($request['handle']))
            wp_send_json_error([
                'message' => 'Missing handle'
            ], 400);

        $api = WPPR_Privacy_API_Factory::get_report_api();

        $reports = $api->get_reports_by_handle($request['handle']);

        return [
            'data' => $reports
        ];
    }

    static function get_schema()
    {
        return [
            '$schema'              => 'http://json-schema.org/draft-04/schema#',
            'title'                => 'reports',
            'description'          => esc_html__('Get all application privacy reports.', 'wp-product-review'),
            'type'                 => 'object',
            'properties'           => array(
                'handle' => array(
                    'description'  => esc_html__('Application Unique ID.', 'wp-product-review'),
                    'type'         => 'string',
                    'required'     => true,
                )
            ),
        ];
    }
}
