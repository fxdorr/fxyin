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
     * @return void
     */
    public static function autoload($class)
    {
        // 初始化变量
        $name = explode('\\', $class);
        $loader = $class != 'fxyin\\Config' ? Config::get('loader.' . $name[0]) : null;
        switch ($name[0]) {
            case 'fxapp':
                // 应用
            case 'fxyin':
                // 框架
                if (empty($loader)) {
                    $loader = (include dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config.php')['loader'][$name[0]] ?? [];
                }
                array_shift($name);
                $name = implode(DIRECTORY_SEPARATOR, $name);
                foreach ($loader as $dir) {
                    $file = $dir . $name . '.php';
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
