<?
include_once WPPR_PATH . '/includes/abstracts/abstract-class-wppr-rest-route.php';

add_action('rest_api_init', function () {
    register_rest_route(
        'wppr/v1',
        WPPR_Privacy_Report_Detail_Rest_Route::get_route(),
        WPPR_Privacy_Report_Detail_Rest_Route::get_route_option()
    );
});

class WPPR_Privacy_Report_Detail_Rest_Route extends WPPR_Abstract_Rest_Route
{
    static protected $route = '/privacy/report';

    static function get_route_option()
    {
        return  [
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => ['WPPR_Privacy_Report_Detail_Rest_Route', 'handler'],
                'permission_callback' => ['WPPR_Privacy_Report_Detail_Rest_Route', 'permission_handler'],
                'args' => array(
                    'handle' => array(
                        'type' => 'string',
                        'required' => true,
                        'validate_callback' => ['WPPR_Privacy_Report_Detail_Rest_Route', 'validate_handle']
                    ),
                    'version_code' => array(
                        'required' => true,
                        'type' => 'integer'
                    )
                ),
            ],
            'schema' => ['WPPR_Privacy_Report_Detail_Rest_Route', 'get_schema']
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

        $api = WPPR_Privacy_API_Factory::get_report_api();

        $report = $api->get_report_detail($request['handle'], $request['version_code']);

        return [
            'data' => $report
        ];
    }

    static function get_schema()
    {
        return [
            '$schema'              => 'http://json-schema.org/draft-04/schema#',
            'title'                => 'report detail',
            'description'          => esc_html__('Get detailed privacy report.', 'wp-product-review'),
            'type'                 => 'object',
            'properties'           => array(
                'handle' => array(
                    'description'  => esc_html__( 'Application Unique ID.', 'wp-product-review' ),
                    'type'         => 'string',
                    'required'     => true,
                ),
                'version_code' => array(
                    'description'  => esc_html__( 'Application version.', 'wp-product-review' ),
                    'type'         => 'integer',
                    'required'     => true,
                )
            ),
        ];
    }
}
