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

class App
{
    /**
     * @var bool 是否初始化过
     */
    protected static $init = false;

    /**
     * 执行程序
     */
    public static function run()
    {
        self::initCommon();
    }

    /**
     * 系统-初始化-应用
     * @return boolean
     */
    public static function initCommon()
    {
        if (empty(self::$init)) {
            //初始化默认模块
            self::initModule('default');
            self::$init = true;
        }
        return self::$init;
    }

    /**
     * 系统-初始化-模块
     * @param string    $module 模块
     * @return boolean
     */
    public static function initModule($module = '', $app_path = null)
    {
        //初始化变量
        if (is_null($app_path)) $app_path = $_ENV['fxy']['app_path'];
        
        static $_module = [];
        if (!isset($_module[$module])) {
            self::init($module, $app_path);
            $_module[$module] = true;
        }
        return $_module[$module];
    }

    /**
     * 系统-初始化-[应用|模块]
     * @param string    $module 模块
     * @return mixed
     */
    private static function init($module = '', $app_path = null)
    {
        //初始化变量
        if (is_null($app_path)) $app_path = $_ENV['fxy']['app_path'];

        //定位模块目录
        $module = $module ? $module . DIRECTORY_SEPARATOR : '';

        //模块目录
        $path = $app_path . $module;
        
        //加载函数文件
        if (is_file($path . 'common.php')) {
            require $path . 'common.php';
        }

        //加载配置文件
        if (is_file($path . 'config.php')) {
            Config::load($path . 'config.php');
        }
        
        //加载扩展函数文件
        if (is_dir($path . 'common' . DIRECTORY_SEPARATOR . 'function')) {
            $dir = $path . 'common' . DIRECTORY_SEPARATOR . 'function';
            $files = scandir($dir);
            foreach ($files as $file) {
                if (strpos($file, '.php')) {
                    $filename = $dir . DIRECTORY_SEPARATOR . $file;
                    require $filename;
                }
            }
        }
        
        //加载扩展配置文件
        if (is_dir($path . 'common' . DIRECTORY_SEPARATOR . 'config')) {
            $dir = $path . 'common' . DIRECTORY_SEPARATOR . 'config';
            $files = scandir($dir);
            foreach ($files as $file) {
                if (strpos($file, '.php')) {
                    $filename = $dir . DIRECTORY_SEPARATOR . $file;
                    Config::load($filename);
                }
            }
        }

        //加载模块语言包
        if (is_dir($path . 'common' . DIRECTORY_SEPARATOR . 'language')) {
            $lang = Lang::detect();
            $dir = $path . 'common' . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . $lang;
            if (!is_dir($dir)) {
                $lang = 'zh-cn';
            }
            Lang::range($lang);
            $dir = $path . 'common' . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . $lang;
            if (is_dir($dir)) {
                $files = scandir($dir);
                foreach ($files as $file) {
                    if (strpos($file, '.php')) {
                        $filename = $dir . DIRECTORY_SEPARATOR . $file;
                        Lang::load($filename, $lang);
                    }
                }
            }
        }
    }
}
