<?
abstract class WPPR_Abstract_Rest_Route
{
    static protected $route = '';

    static function get_route()
    {
        return static::$route ? static::$route : self::$route;
    }

    abstract static function get_route_option();

    abstract static function permission_handler(WP_REST_Request $request);

    abstract static function handler(WP_REST_Request $request);

    abstract static function get_schema();
}