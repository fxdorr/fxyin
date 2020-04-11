<?php
// +----------------------------------------------------------------------
// | Name fxyin
// +----------------------------------------------------------------------
// | Author wztqy <wztqy@139.com>
// +----------------------------------------------------------------------
// | Copyright Copyright © 2016-2099 fxri. All rights reserved.
// +----------------------------------------------------------------------
// | Link http://www.fxri.net
// +----------------------------------------------------------------------
namespace fxyin;

class Log
{
    /**
     * 日志打印控制台
     * @return void
     */
    public static function print($info, $mode = null)
    {
        $modes = array(1, 2, 3);
        if (is_array($info)) {
            $info = self::splice($info);
        }
        $info = str_replace('"', '\'', $info);
        if (!in_array($mode, $modes)) {
            $mode = $modes[0];
        }
        switch ($mode) {
            case 1:
                print_r('<script type="text/javascript">console.log("' . $info . '");</script>');
                break;
            case 2:
                print_r('<!--' . $info . '-->');
                break;
            case 3:
                print_r($info);
                break;
        }
    }

    /**
     * 日志拼接数组
     * @return mixed
     */
    public static function splice($array)
    {
        static $level = 0;
        ++$level;
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $current_parent = $key;
                $current_level = $level;
                if (is_array($value)) {
                    $value = self::splice($value);
                    $log = dso_splice($log, $key . '=\'' . $value . '\'', ',');
                } else {
                    $value = dsc_fhtml($value);
                    $log = dso_splice($log, $key . '=\'' . $value . '\'', ',');
                }
                if ($key == $current_parent) {
                    $level = $current_level;
                }
            }
        }
        if ($level == 1) {
            $level = 0;
            return $log;
        }
        return $log;
    }
}
