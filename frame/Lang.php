<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2017 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace fxyin;

/**
 * 语言类
 */
class Lang
{
    /**
     * 语言数据
     * @var array
     */
    private static $lang = [];

    /**
     * 语言作用域
     * @var string
     */
    private static $range = 'zh-cn';

    /**
     * 语言自动侦测的变量
     * @var string
     */
    protected static $langDetectVar = 'lang';

    /**
     * 语言Cookie名称
     * @var string
     */
    protected static $langCookieName = 'lang';

    /**
     * 语言Cookie的过期时间
     * @var int
     */
    protected static $langCookieExpire = 3600;

    /**
     * 允许语言列表
     * @var array
     */
    protected static $allowLangList = [];

    /**
     * 设定当前的语言
     * @return mixed
     */
    public static function range($range = '')
    {
        // 初始化变量
        if ('' === $range) {
            return self::$range;
        } else {
            self::$range = $range;
        }
    }

    /**
     * 设置语言定义(不区分大小写)
     * @param array|string  $name 语言名称
     * @param string        $value 语言值
     * @param string        $range 语言作用域
     * @return mixed
     */
    public static function set($name, $value = null, $range = '')
    {
        // 初始化变量
        $range = $range ?: self::$range;
        // 识别作用域
        if (!isset(self::$lang[$range])) {
            self::$lang[$range] = [];
        }
        // 批量定义
        if (is_array($name)) {
            return self::$lang[$range] = \fxapp\Param::merge(self::$lang[$range], $name);
        } else {
            return self::$lang[$range][$name] = $value;
        }
    }

    /**
     * 加载语言定义(不区分大小写)
     * @param string $file 语言文件
     * @param string $range 语言作用域
     * @return mixed
     */
    public static function load($file, $range = '')
    {
        // 初始化变量
        $range = $range ?: self::$range;
        // 识别作用域
        if (!isset(self::$lang[$range])) {
            self::$lang[$range] = [];
        }
        // 批量定义
        if (is_string($file)) {
            $file = [$file];
        }
        $lang = [];
        foreach ($file as $_file) {
            if (is_file($_file)) {
                $_lang = require $_file;
                if (is_array($_lang)) {
                    $lang = \fxapp\Param::merge($lang, $_lang);
                }
            }
        }
        if (!empty($lang)) {
            self::$lang[$range] = \fxapp\Param::merge(self::$lang[$range], $lang);
        }
        return self::$lang[$range];
    }

    /**
     * 获取语言定义(不区分大小写)
     * @param string|null   $name 语言名称
     * @param array         $vars 变量替换
     * @param string        $range 语言作用域
     * @return mixed
     */
    public static function has($name, $range = '')
    {
        // 初始化变量
        $range = $range ?: self::$range;
        return isset(self::$lang[$range][$name]);
    }

    /**
     * 获取语言定义(不区分大小写)
     * @param string|null   $name 语言名称
     * @param array         $vars 变量替换
     * @param string        $range 语言作用域
     * @return mixed
     */
    public static function get($name = null, $vars = [], $range = '')
    {
        // 初始化变量
        $range = $range ?: self::$range;
        // 识别作用域
        if (!isset(self::$lang[$range])) {
            self::$lang[$range] = [];
        }
        // 空参数返回所有定义
        if (is_null($name)) {
            return self::$lang[$range];
        }
        $key   = $name;
        $value = isset(self::$lang[$range][$key]) ? self::$lang[$range][$key] : $name;

        // 变量解析
        if (!empty($vars) && is_array($vars)) {
            /**
             * Notes:
             * 为了检测的方便，数字索引的判断仅仅是参数数组的第一个元素的key为数字0
             * 数字索引采用的是系统的 sprintf 函数替换，用法请参考 sprintf 函数
             */
            if (key($vars) === 0) {
                // 数字索引解析
                array_unshift($vars, $value);
                $value = call_user_func_array('sprintf', $vars);
            } else {
                // 关联索引解析
                $replace = array_keys($vars);
                foreach ($replace as &$v) {
                    $v = "{:{$v}}";
                }
                $value = str_replace($replace, $vars, $value);
            }
        }
        return $value;
    }

    /**
     * 自动侦测设置获取语言选择
     * @return string
     */
    public static function detect()
    {
        // 初始化变量
        $langSet = '';
        // 自动侦测设置获取语言选择
        if (isset($_GET[self::$langDetectVar])) {
            // url中设置了语言变量
            $langSet = strtolower($_GET[self::$langDetectVar]);
            if (PHP_SAPI !== 'cli') {
                Cookie::set(self::$langCookieName, $langSet, self::$langCookieExpire);
            }
        } else if (Cookie::get(self::$langCookieName)) {
            // 获取上次用户的选择
            $langSet = strtolower(Cookie::get(self::$langCookieName));
        } else if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            // 自动侦测浏览器语言
            preg_match('/^([a-z\d\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
            $langSet = strtolower($matches[1]);
            Cookie::set(self::$langCookieName, $langSet, self::$langCookieExpire);
        }
        if (empty(self::$allowLangList) || in_array($langSet, self::$allowLangList)) {
            // 合法的语言
            self::$range = $langSet ?: self::$range;
        }
        if ('zh-hans-cn' == self::$range) {
            self::$range = 'zh-cn';
        }
        return self::$range;
    }

    /**
     * 设置语言自动侦测的变量
     * @param string $var 变量名称
     * @return void
     */
    public static function setLangDetectVar($var)
    {
        // 初始化变量
        self::$langDetectVar = $var;
    }

    /**
     * 设置语言的cookie保存变量
     * @param string $var 变量名称
     * @return void
     */
    public static function setLangCookieName($var)
    {
        // 初始化变量
        self::$langCookieName = $var;
    }

    /**
     * 设置语言的cookie的过期时间
     * @param string $expire 过期时间
     * @return void
     */
    public static function setLangCookieExpire($expire)
    {
        // 初始化变量
        self::$langCookieExpire = $expire;
    }

    /**
     * 设置允许的语言列表
     * @param array $list 语言列表
     * @return void
     */
    public static function setAllowLangList($list)
    {
        self::$allowLangList = $list;
    }
}
