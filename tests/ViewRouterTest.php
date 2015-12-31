<?php
/**
 * @author      Huw Jones <huwcbjones@gmail.com>
 * @date        31/12/2015
 */

namespace WebApp;


use WebApp\base\Router;

class ViewRouterTest extends \PHPUnit_Framework_TestCase
{
    public function testRoute(){
        $router = new Router();
        $router->route('/test/*', RouteTypes::VIEW, new TestPage());
    }
}
