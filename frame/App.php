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
 * 应用类
 */
class App
{
    /**
     * 系统-初始化
     * @param string $name 名称
     * @return boolean
     */
    public static function init($name = null)
    {
        // 初始化变量
        static $data = [];
        // 加载应用
        if (!isset($data[$name])) {
            self::load($name);
            $data[$name] = true;
        }
        return $data[$name];
    }

    /**
     * 系统-加载
     * @param string $name 名称
     * @return mixed
     */
    private static function load($name = null)
    {
        // 初始化变量
        $app = Config::get('env.app');
        if (!is_array($app)) return;
        if (isset($app[$name])) {
            $app = [$app[$name]];
        }
        foreach ($app as $path) {
            $path = realpath($path);
            if (false === $path) return;
            $path .= DIRECTORY_SEPARATOR;
            // 加载函数文件
            if (is_file($path . 'common.php')) {
                require $path . 'common.php';
            }
            // 加载配置文件
            if (is_file($path . 'config.php')) {
                Config::load($path . 'config.php');
            }
            // 加载扩展函数文件
            if (is_dir($path . 'function')) {
                $dir = $path . 'function';
                $files = scandir($dir);
                foreach ($files as $file) {
                    if (strpos($file, '.php')) {
                        $filename = $dir . DIRECTORY_SEPARATOR . $file;
                        require $filename;
                    }
                }
            }
            // 加载扩展配置文件
            if (is_dir($path . 'config')) {
                $dir = $path . 'config';
                $files = scandir($dir);
                foreach ($files as $file) {
                    if (strpos($file, '.php')) {
                        $filename = $dir . DIRECTORY_SEPARATOR . $file;
                        Config::load($filename, pathinfo($file, PATHINFO_FILENAME));
                    }
                }
            }
            // 加载应用语言包
            if (is_dir($path . 'language')) {
                $lang = Lang::detect();
                $dir = $path . 'language' . DIRECTORY_SEPARATOR . $lang;
                if (!is_dir($dir)) {
                    $lang = 'zh-cn';
                }
                Lang::range($lang);
                $dir = $path . 'language' . DIRECTORY_SEPARATOR . $lang;
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
}
