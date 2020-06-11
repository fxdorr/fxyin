<?php
// +----------------------------------------------------------------------
// | Name 风音框架
// +----------------------------------------------------------------------
// | Author 唐启云 <tqy@fxri.net>
// +----------------------------------------------------------------------
// | Copyright Copyright © 2016-2099 方弦研究所. All rights reserved.
// +----------------------------------------------------------------------
// | Link https://www.fxri.net
// +----------------------------------------------------------------------
namespace fxyin;

/**
 * Cookie类
 */
class Cookie
{
    /**
     * 获取数据
     * @param string $name 名称
     * @return mixed
     */
    public static function get($name)
    {
        // 初始化变量
        if (isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        }
        return;
    }

    /**
     * 设置数据
     * @param array $vars 参数
     * @return void
     */
    public static function set(...$vars)
    {
        // 初始化变量
        $predefined = [
            // name-value-expire
            0 => null, 1 => '', 2 => 0,
            // path-domain-secure
            3 => '/', 4 => '', 5 => false,
            // httponly
            6 => false,
        ];
        $vars = \fxapp\Param::define([$vars, $predefined], '1.1.1');
        if (is_null($vars[0])) return false;
        $vars[2] = !empty($vars[2]) ? $_SERVER['REQUEST_TIME'] + intval($vars[2]) : 0;
        setcookie(...$vars);
        // 设置Cookie
        $_COOKIE[$vars[0]] = $vars[1];
        return true;
    }
}
