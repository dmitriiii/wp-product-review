<?
include_once WPPR_PATH . '/includes/abstracts/abstract-class-wppr-rest-route.php';

add_action('rest_api_init', function () {
    register_rest_route('wppr/v1', 
    WPPR_Privacy_Grade_Rest_Route::get_route(),
    WPPR_Privacy_Grade_Rest_Route::get_route_option());
});

class WPPR_Privacy_Grade_Rest_Route extends WPPR_Abstract_Rest_Route
{
    static protected $route = '/privacy/grade';

    static function get_route_option()
    {
        return  [
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => ['WPPR_Privacy_Grade_Rest_Route', 'handler'],
                'permission_callback' => ['WPPR_Privacy_Grade_Rest_Route', 'permission_handler']
            ],
            'schema' => ['WPPR_Privacy_Grade_Rest_Route', 'get_schema']
        ];
    }

    static function permission_handler(WP_REST_Request $request)
    {
        return !!$request->get_header('X-WP-Nonce');
    }

    static function handler(WP_REST_Request $request)
    {
        $grades = get_field('privacy_permission_levels', 'option');
        return [
            'data' => $grades
        ];
    }

    static function get_schema()
    {
        return [
            '$schema'              => 'http://json-schema.org/draft-04/schema#',
            'title'                => 'grades',
            'description'          => esc_html__('Get privacy grade levels.', 'wp-product-review'),
        ];
    }
}
