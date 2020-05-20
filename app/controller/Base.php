<?php
// +----------------------------------------------------------------------
// | Name 风音框架
// +----------------------------------------------------------------------
// | Author 唐启云 <tqy@fxri.net>
// +----------------------------------------------------------------------
// | Copyright Copyright © 2016-2099 方弦研究所. All rights reserved.
// +----------------------------------------------------------------------
// | Link http://www.fxri.net
// +----------------------------------------------------------------------
namespace fxapp;

class Base
{
    /**
     * 加载-[文件]
     * @param string $file 文件路径
     * @return boolean
     */
    public static function load($file)
    {
        static $_file = [];
        $file = realpath($file);
        if (!isset($_file[$file])) {
            if (!is_file($file)) {
                $_file[$file] = false;
            } else {
                require $file;
                $_file[$file] = true;
            }
        }
        return $_file[$file];
    }

    /**
     * 语言
     * @param array|string $name 语言变量名
     * @param array $vars 动态变量值
     * @param string $lang 语言
     * @return string
     */
    public static function lang($name, $vars = [], $lang = '')
    {
        $langs = static::lang_list($name, $vars, $lang);
        $string = static::lang_parse($langs);
        return ucfirst($string);
    }

    /**
     * 语言-列表
     * @param array|string $name 语言变量名
     * @param array $vars 动态变量值
     * @param string $lang 语言
     * @return string
     */
    public static function lang_list($name, $vars = [], $lang = '')
    {
        $langs = [];
        $lang = \fxyin\Lang::range();
        if (is_array($name)) {
            foreach ($name as $value) {
                $langs[] = static::lang_list($value, $vars, $lang);
            }
        } else {
            if (is_null($name)) {
                $langs = 'null';
            } else if (strpos($name, static::config('app.lang.prefix')) === 0) {
                $langs = ltrim($name, static::config('app.lang.prefix'));
            } else if (strpos($name, static::config('app.lang.ignore')) === 0) {
                $langs = '';
            } else {
                if (is_numeric($name)) {
                    $name = strval($name);
                }
                $langs = \fxyin\Lang::get($name, $vars, $lang);
            }
        }
        return $langs;
    }

    /**
     * 语言-解析
     * @param array|string $name 语言变量名
     * @return string
     */
    public static function lang_parse($name)
    {
        $type = \fxyin\Lang::range();
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                if (is_array($value)) {
                    $name[$key] = static::lang_parse($value);;
                }
            }
            switch ($type) {
                default:
                case 'en-us':
                    // 英语（美国）
                    $string = implode(' ', $name);
                    break;
                case 'zh-cn':
                    // 中文（简体）
                    $string = implode('', $name);
                    break;
            }
        } else {
            $string = $name;
        }
        return $string;
    }

    /**
     * 配置参数-[获取|设置]
     * @param array|string $name 参数名
     * @param mixed $value 参数值
     * @param string $range 作用域
     * @return mixed
     */
    public static function config($name = '', $value = null, $range = '')
    {
        if (is_null($value) && is_string($name)) {
            return 0 === strpos($name, '?') ? \fxyin\Config::has(substr($name, 1), $range) : \fxyin\Config::get($name, $range);
        } else {
            return \fxyin\Config::set($name, $value, $range);
        }
    }

    /**
     * 浏览器友好的变量输出
     * @param mixed $vars 要输出的变量
     * @return void
     */
    public static function dump(...$vars)
    {
        // 提取数据
        ob_start();
        var_dump(...$vars);
        $output = ob_get_clean();
        $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
        // 处理数据
        if (PHP_SAPI == 'cli') {
            $output = PHP_EOL . $output . PHP_EOL;
        } else {
            if (!extension_loaded('xdebug')) {
                $output = htmlspecialchars($output, ENT_SUBSTITUTE);
            }
            $output = '<pre>' . $output . '</pre>';
        }
        // 输出数据
        echo $output;
    }
}
