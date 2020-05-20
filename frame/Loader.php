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

class Loader
{
    /**
     * 自动加载
     * @return void
     */
    public static function autoload($class)
    {
        // 初始化变量
        $loader = $class != 'fxyin\\Config' ? Config::get('loader') : [];
        $class = explode('\\', $class);
        $loader = $loader[$class[0]] ?? null;
        switch ($class[0]) {
            default:
                // 匹配
                break;
            case 'fxyin':
                // 风音
                array_shift($class);
                $class = implode(DIRECTORY_SEPARATOR, $class);
                var_dump($class);
                if (!is_array($loader)) {
                    $loader = [__DIR__];
                }
                var_dump($loader);
                foreach ($loader as $dir) {
                    $file = $dir . DIRECTORY_SEPARATOR . $class . '.php';
                    if (is_file($file)) {
                        require $file;
                        break;
                    }
                }
                break;
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
