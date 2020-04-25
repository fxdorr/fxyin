<?php
// +----------------------------------------------------------------------
// | Name fxyin
// +----------------------------------------------------------------------
// | Author wztqy <tqy@fxri.net>
// +----------------------------------------------------------------------
// | Copyright Copyright © 2016-2099 fxri. All rights reserved.
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
        if (strpos($class, 'fxyin') !== false) {
            $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
            $file = $_ENV['fxy']['lib_path'] . $class . '.php';
            if (is_file($file)) {
                require $file;
            }
        }
    }

    /**
     * 注册自动加载机制
     * @return void
     */
    public static function register($autoload = '')
    {
        spl_autoload_register($autoload ? : 'fxyin\\Loader::autoload', true, false);
    }
}
