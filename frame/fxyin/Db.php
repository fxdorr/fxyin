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

use fxyin\db\Connection;

class Db
{
    //连接实例
    private static $instance = [];
    //查询次数
    public static $queryTimes = 0;
    //执行次数
    public static $executeTimes = 0;

    /**
     * 数据库初始化
     * @param array $config 配置
     * @param string $name 标识
     * @return Connection
     */
    public static function connect($config = [], $name = false)
    {
        if (false === $name) {
            $name = md5(serialize($config));
        }
        if (true === $name || !isset(self::$instance[$name])) {
            $options = $config;
            if (empty($options['type'])) {
                throw new \Exception('Undefined db type');
            }
            $class = false !== strpos($options['type'], '\\') ? $options['type'] : '\\fxyin\\db\\driver\\' . ucfirst($options['type']);
            if (true === $name) {
                return new $class($options);
            } else {
                self::$instance[$name] = new $class($options);
            }
        }
        return self::$instance[$name];
    }
}
