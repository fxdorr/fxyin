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
 * 加载器类
 */
class Loader
{
    /**
     * 自动加载
     * @param string $class 类名
     * @return mixed
     */
    public static function autoload($class)
    {
        // 初始化变量
        $name = explode('\\', $class);
        // 获取加载器配置
        $loader = $class != 'fxyin\\Config' ? Config::get('loader') : null;
        if (empty($loader)) {
            $loader = (require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config.php')['loader'] ?? [];
        }
        $loader = $loader[$name[0]] ?? null;
        if (!is_array($loader)) return;
        // 执行加载器
        array_shift($name);
        $name = implode(DIRECTORY_SEPARATOR, $name);
        foreach ($loader as $path) {
            $path = realpath($path);
            if (false === $path) return;
            $file = $path . DIRECTORY_SEPARATOR . $name . '.php';
            if (is_file($file)) {
                return require $file;
            }
        }
    }

    /**
     * 注册自动加载机制
     * @return void
     */
    public static function register($autoload = '')
    {
        // 注册加载器
        spl_autoload_register($autoload ?: 'fxyin\\Loader::autoload', true, false);
    }
}
