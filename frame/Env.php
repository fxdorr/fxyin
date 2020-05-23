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
namespace fxyin;

/**
 * 环境类
 */
class Env
{
    // 框架版本
    const VERSION = '1.1.0';

    // 框架版本时间
    const VERSION_TIME = '2020-05-20';

    // 关键字声明
    const KEYWORD = [
        // // 系统
        // 'fxy' => 'Fxyin',
        // // 数据查询组装
        // 'dqa' => 'Data Query Assemble',
        // // 数据查询检查
        // 'dqc' => 'Data Query Check',
        // // 数据查询操作
        // 'dqo' => 'Data Query Operate',
        // // 数据结构检查
        // 'dsc' => 'Data Structure Check',
        // // 数据结构操作
        // 'dso' => 'Data Structure Operate',
        // // 框架公共检查
        // 'fcc' => 'Frame Common Check',
        // // 框架公共操作
        // 'fco' => 'Frame Common Operate',
        // // 框架公共服务
        // 'fcs' => 'Frame Common Service',
        // // 框架公共函数
        // 'fcf' => 'Frame Common Function',
        // 框架模块检查
        'fmc' => 'Frame Module Check',
        // 框架模块函数
        'fmf' => 'Frame Module Function',
        // 框架模块操作
        'fmo' => 'Frame Module Operate',
        // 框架服务核心
        'fsc' => 'Frame Service Core',
        // // 框架服务初始化
        // 'fsi' => 'Frame Service Init',
        // // 框架服务发送
        // 'fss' => 'Frame Service Send',
        // // 框架服务第三方
        // 'fst' => 'Frame Service Third',
        // // 框架终端客户端
        // 'ftc' => 'Frame Terminal Client',
        // // 框架终端服务器
        // 'fts' => 'Frame Terminal Server',
    ];

    // 数据
    private static $data = [];

    /**
     * 环境-初始化
     * @param string $root 根目录
     * @return boolean
     */
    public static function init($root)
    {
        // 初始化配置
        static::set(Config::get('env'));
        // 根目录
        static::set('base.root', $root);
        // 应用目录
        static::set('base.app', static::get('base.root') . Config::get('env.base.app'));
    }

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
            if (!array_key_exists($elem, $data)) {
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
