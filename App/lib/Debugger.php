<?php
/**
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @date 30/12/2015
 */

namespace WebApp;


class Debugger
{

    private static $backtrace;
    private static $log = array();
    private static $debugLog = '';

    public static function log($text, $shift = 0, $printToCli = false)
    {
        if(!is_string($text)) {
            trigger_error(getArgumentErrorMessage(__FUNCTION__, 'string', gettype($text), __CLASS__), E_USER_ERROR);
        }
        if (version_compare(PHP_VERSION, '5.3.6', '>=')) {
            $bt = debug_backtrace(~DEBUG_BACKTRACE_PROVIDE_OBJECT & DEBUG_BACKTRACE_IGNORE_ARGS);
        } else {
            $bt = debug_backtrace(false);
        }

        $space = '';
        $spaces = 80 - strlen($text);
        for ($s = 1; $s <= $spaces; $s++) {
            $space .= ' ';
        }

        $caller = array_shift($bt);
        if (strpos($caller['file'], 'autoload.php') === false) {
            $caller = array_shift($bt);
        }

        if (
            $shift != 0
            && count($bt) > 1
            && count($bt) <= $shift
        ) {
            for ($i = 0; $i < $shift; $i++) {
                $caller = array_shift($bt);
            }
        }

        $file = '';
        if (array_key_exists('file', $caller)) {
            $file = str_replace(__EXECDIR__, '', $caller['file']);
        } else {
            $caller['file'] = 'Unknown';
            $caller['line'] = '-';
        }

        $file = self::stripPaths($file);

        if ($spaces < 16 || strlen($file) > 30) {
            $file = explode(DIRECTORY_SEPARATOR, $caller['file']);
            $file = $file[count($file) - 1];
        }

        $msg = $text . $space . '(' . $file . ', ' . $caller['line'] . ')';
        self::$log[microtime(false) . '|' . md5(microtime())] = $msg;

        if (is_CLI() && $printToCli) {
            echo ' # ' . $msg . PHP_EOL;
        }
    }

    /**
     * Strips defined paths a replaces them with the entities
     *
     * @param string|\WebApp\string $path Path to strip from
     * @return string Stripped path
     */
    private static function stripPaths(string $path)
    {
        if (substr($path, 0, 1) == DIRECTORY_SEPARATOR) {
            $path = substr($path, 1);
        }
        $path = str_replace('lib' . DIRECTORY_SEPARATOR . 'modules', '_MODULE_', $path);
        $path = str_replace('lib' . DIRECTORY_SEPARATOR .
            'plugins', '_PLUGIN_', $path);
        $path = str_replace('lib', '_LIB_', $path);
        $path = str_replace('class.', DIRECTORY_SEPARATOR, $path);
        return $path;
    }

    public static function compile()
    {
        $debug = array();
        foreach (self::$log as $time => $event) {
            $date_array = explode(" ", $time);
            $date = date("Y-m-d H:i:s", $date_array[1]);
            $debug[] = "  " . $date . '' . substr(number_format(substr($date_array[0], 2), 5), 1) . " - " . $event . PHP_EOL;
        }

        // Set the array pointer to the end of the array
        end(self::$log);

        // We want the first element as the time is stored as time|hash
        $endTime = explode('|', key(self::$log))[0];

        // Set the array pointer to the beginning of the array
        reset(self::$log);

        // We want the first element as the time is stored as time|hash
        $startTime = explode('|', key(self::$log))[0];

        // Microtime maths is dodgy, so split the format
        $executionTime = (integer)(substr($endTime, 13) - (integer)substr($startTime, 13));
        $executionTime += (integer)(substr($endTime, 2, 9) - (integer)substr($startTime, 2, 9));
        $debug[] = PHP_EOL . '  Execution took: ' . $executionTime . PHP_EOL;

        self::$debugLog = implode('', $debug);
    }

    public function get_debugLog()
    {
        if (self::$debugLog != '') {
            self::compile();
        }
        return self::$debugLog;
    }


}