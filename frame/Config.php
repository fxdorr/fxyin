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
 * 配置类
 */
class Config
{
    /**
     * 数据
     * @var array
     */
    private static $data = [];

    /**
     * 加载数据
     * @param string $file 文件
     * @param string $name 名称
     * @return bool
     */
    public static function load($file, $name = null)
    {
        if (is_file($file)) {
            $data = require $file;
            if (is_string($name)) {
                // 解析名称
                $name = array_reverse(explode('.', $name));
                foreach ($name as $elem) {
                    $elem = str_replace('/_', '.', $elem);
                    $data = [$elem => $data];
                }
            }
            self::set($data);
            return true;
        }
        return false;
    }

    /**
     * 检测数据
     * @param string $name 名称
     * @return bool
     */
    public static function has($name)
    {
        // 解析名称
        $name = explode('.', $name);
        $data = self::$data;
        foreach ($name as $elem) {
            $elem = str_replace('/_', '.', $elem);
            if (array_key_exists($elem, $data)) {
                $data = $data[$elem];
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * 获取数据
     * @param string $name 名称
     * @return mixed
     */
    public static function get($name = null)
    {
        // 获取所有
        if (is_null($name)) {
            return self::$data;
        }
        // 解析名称
        $name = explode('.', $name);
        $data = self::$data;
        foreach ($name as $elem) {
            $elem = str_replace('/_', '.', $elem);
            if (!isset($data[$elem])) {
                $data = null;
                break;
            }
            $data = $data[$elem];
        }
        return $data;
    }

    /**
     * 设置数据
     * @param array|string $name 名称
     * @param string $data 数据
     * @return void
     */
    public static function set($name, $data = null)
    {
        // 初始化变量
        if (is_string($name)) {
            // 解析名称
            $name = array_reverse(explode('.', $name));
            foreach ($name as $elem) {
                $elem = str_replace('/_', '.', $elem);
                $data = [$elem => $data];
            }
            // 融合数据
            self::$data = \fxapp\Param::merge(self::$data, $data);
        } else if (is_array($name)) {
            // 融合数据
            self::$data = \fxapp\Param::merge(self::$data, $name);
        }
    }
}
