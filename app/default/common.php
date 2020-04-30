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

use fxyin\Config;
use fxyin\Lang;

if (!function_exists('fxy_load')) {
    /**
     * 系统-加载-[文件] <p>
     * fxyin load
     * </p>
     * @param string $file 文件路径
     * @return boolean
     */
    function fxy_load($file)
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
}

if (!function_exists('fxy_lang')) {
    /**
     * 系统-语言 <p>
     * fxyin language
     * </p>
     * @param array|string $name 语言变量名
     * @param array $vars 动态变量值
     * @param string $lang 语言
     * @return string
     */
    function fxy_lang($name, $vars = [], $lang = '')
    {
        $langs = fxy_lang_list($name, $vars, $lang);
        $string = fxy_lang_parse($langs);
        return ucfirst($string);
    }
}

if (!function_exists('fxy_lang_list')) {
    /**
     * 系统-语言-列表 <p>
     * fxyin language parse
     * </p>
     * @param array|string $name 语言变量名
     * @param array $vars 动态变量值
     * @param string $lang 语言
     * @return string
     */
    function fxy_lang_list($name, $vars = [], $lang = '')
    {
        $langs = [];
        $lang = Lang::range();
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $langs[] = fxy_lang_list($value, $vars, $lang);
            }
        } else {
            if (is_null($name)) {
                $langs = 'null';
            } else if (strpos($name, fxy_config('lang')['prefix']) === 0) {
                $langs = ltrim($name, fxy_config('lang')['prefix']);
            } else if (strpos($name, fxy_config('lang')['ignore']) === 0) {
                $langs = '';
            } else {
                if (is_numeric($name)) {
                    $name = strval($name);
                }
                $langs = Lang::get($name, $vars, $lang);
            }
        }
        return $langs;
    }
}

if (!function_exists('fxy_lang_parse')) {
    /**
     * 系统-语言-解析 <p>
     * fxyin language parse
     * </p>
     * @param array|string $name 语言变量名
     * @return string
     */
    function fxy_lang_parse($name)
    {
        $type = Lang::range();
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                if (is_array($value)) {
                    $name[$key] = fxy_lang_parse($value);;
                }
            }
            switch ($type) {
                default:
                case 'en-us':
                    //英语（美国）
                    $string = implode(' ', $name);
                    break;
                case 'zh-cn':
                    //中文（简体）
                    $string = implode('', $name);
                    break;
            }
        } else {
            $string = $name;
        }
        return $string;
    }
}

if (!function_exists('fxy_config')) {
    /**
     * 系统-配置参数-[获取|设置] <p>
     * fxyin config
     * </p>
     * @param array|string $name 参数名
     * @param mixed $value 参数值
     * @param string $range 作用域
     * @return mixed
     */
    function fxy_config($name = '', $value = null, $range = '')
    {
        if (is_null($value) && is_string($name)) {
            return 0 === strpos($name, '?') ? Config::has(substr($name, 1), $range) : Config::get($name, $range);
        } else {
            return Config::set($name, $value, $range);
        }
    }
}
