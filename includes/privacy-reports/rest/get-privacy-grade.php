<?
add_action('rest_api_init', function () {
    register_rest_route('wppr/v1', '/privacy/grade', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'rest_get_privacy_grades'
    ));
});


function rest_get_privacy_grades()
{
    $grades = get_field('privacy_permission_levels', 'option');;
    wp_send_json_success($grades);
}