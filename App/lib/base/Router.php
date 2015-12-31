<?php
/**
 * @author      Huw Jones <huwcbjones@gmail.com>
 * @date        31/12/2015
 */

namespace WebApp\base;

use WebApp\RouteTypes;

class Router
{
    private $routes = array();

    public function __construct()
    {
        $this->routes = array(RouteTypes::VIEW, RouteTypes::ACTION, RouteTypes::RESTAPI);
    }

    public function route($path, $type, Page $view)
    {
        if (!is_string($path)) {
            trigger_error(getArgumentErrorMessage(__FUNCTION__, 'string', gettype($path), __CLASS__), E_USER_ERROR);
        }

        // Remove prefixed /
        if (substr($path, 0, 1) == '/') {
            $path = substr($path, 1);
        }

        $routes = explode('/', $path);
        $reference_array = $this->routes[$type];

        foreach ($routes as $route_part) {
            if (array_key_exists($route_part, $reference_array)) {
                $reference_array = $reference_array[$route_part];
            } else {
                $reference_array[$route_part] = array();
            }
        }
    }
}