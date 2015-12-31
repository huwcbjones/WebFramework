<?php
/**
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @date 31/12/2015
 */

namespace WebApp;

require dirname(__DIR__) . '/App/lib/_init.php';

class DebuggerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testLog_int()
    {
        Debugger::log(123, __CLASS__);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testLog_bool()
    {
        Debugger::log(false, __CLASS__);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testLog_object()
    {
        Debugger::log(new \stdClass(), __CLASS__);
    }

    public function testLog_string()
    {
        Debugger::log("Success!", __CLASS__);
    }

    public function testLog()
    {
        Debugger::log("Success!", __CLASS__);

        Debugger::compile();
        $this->assertRegExp('/[success]/', Debugger::get_debugLog());
    }

}
