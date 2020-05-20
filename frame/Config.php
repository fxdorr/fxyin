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

class Config
{
    // 配置参数
    private static $config = [];
    // 参数作用域
    private static $range = '_sys_';

    // 设定配置参数的作用域
    public static function range($range)
    {
        self::$range = $range;
        if (!isset(self::$config[$range])) {
            self::$config[$range] = [];
        }
    }

    /**
     * 加载配置文件（PHP格式）
     * @param string    $file 配置文件名
     * @param string    $name 配置名（如设置即表示二级配置）
     * @param string    $range  作用域
     * @return mixed
     */
    public static function load($file, $name = '', $range = '')
    {
        $range = $range ?: self::$range;
        if (!isset(self::$config[$range])) {
            self::$config[$range] = [];
        }
        if (is_file($file)) {
            $name = strtolower($name);
            return self::set(include $file, $name, $range);
        } else {
            return self::$config[$range];
        }
    }

    /**
     * 检测配置是否存在
     * @param string    $name 配置参数名（支持多级配置 .号分割）
     * @param string    $range  作用域
     * @return bool
     */
    public static function has($name, $range = '')
    {
        // 获取作用域
        $range = $range ?: self::$range;
        // 解析配置参数名
        $name = explode('.', $name);
        $config = self::$config[$range] ?? [];
        foreach ($name as $elem) {
            if (isset($config[$elem])) {
                $config = $config[$elem];
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * 获取配置参数 为空则获取所有配置
     * @param string    $name 配置参数名（支持多级配置 .号分割）
     * @param string    $range  作用域
     * @return mixed
     */
    public static function get($name = null, $range = '')
    {
        // 获取作用域
        $range = $range ?: self::$range;
        // 无参数时获取所有
        if (empty($name) && isset(self::$config[$range])) {
            return self::$config[$range];
        }
        // 解析配置参数名
        $name = explode('.', $name);
        $config = self::$config[$range] ?? [];
        foreach ($name as $elem) {
            if (isset($config[$elem])) {
                $config = $config[$elem];
            } else {
                return null;
            }
        }
        return $config;
    }

    /**
     * 设置配置参数 name为数组则为批量设置
     * @param array|string  $name 配置参数名（支持多级配置 .号分割）
     * @param mixed         $value 配置值
     * @param string        $range  作用域
     * @return mixed
     */
    public static function set($name, $value = null, $range = '')
    {
        // 获取作用域
        $range = $range ?: self::$range;
        if (!isset(self::$config[$range])) {
            self::$config[$range] = [];
        }
        if (is_string($name)) {
            // 解析配置参数名
            $name = array_reverse(explode('.', $name));
            foreach ($name as $elem) {
                $value = [$elem => $value];
            }
            self::$config[$range] = fmo_merge(self::$config[$range], $value);
            return;
        } else if (is_array($name)) {
            // 批量设置
            if (!empty($value)) {
                self::$config[$range][$value] = isset(self::$config[$range][$value]) ? array_merge(self::$config[$range][$value], $name) : self::$config[$range][$value] = $name;
                return self::$config[$range][$value];
            } else {
                return self::$config[$range] = array_merge(self::$config[$range], array_change_key_case($name));
            }
        } else {
            // 为空直接返回 已有配置
            return self::$config[$range];
        }
    }

    /**
     * 重置配置参数
     */
    public static function reset($range = '')
    {
        $range = $range ?: self::$range;
        if (true === $range) {
            self::$config = [];
        } else {
            self::$config[$range] = [];
        }
    }
}
