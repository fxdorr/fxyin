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
namespace fxapp\facade;

/**
 * 基础类
 */
class Base
{
    /**
     * 加载-[文件]
     * @param string $file 文件路径
     * @return boolean
     */
    public function load($file)
    {
        static $_file = [];
        $file = realpath($file);
        if (!isset($_file[$file])) {
            if (!is_file($file)) {
                $_file[$file] = false;
            } else {
                require $file;
                $_file[$file] = true;
            }
        }
        return $_file[$file];
    }

    /**
     * 语言
     * @param array|string $name 语言变量名
     * @param array $vars 动态变量值
     * @param string $lang 语言
     * @return string
     */
    public function lang($name, $vars = [], $lang = '')
    {
        $langs = $this->langList($name, $vars, $lang);
        $string = $this->langParse($langs);
        return ucfirst($string);
    }

    /**
     * 语言-列表
     * @param array|string $name 语言变量名
     * @param array $vars 动态变量值
     * @param string $lang 语言
     * @return string
     */
    public function langList($name, $vars = [], $lang = '')
    {
        $langs = [];
        $lang = \fxyin\Lang::range();
        if (is_array($name)) {
            foreach ($name as $value) {
                $langs[] = $this->langList($value, $vars, $lang);
            }
        } else {
            if (is_null($name)) {
                $langs = 'null';
            } else if (strpos($name, $this->config('app.lang.prefix')) === 0) {
                $langs = ltrim($name, $this->config('app.lang.prefix'));
            } else if (strpos($name, $this->config('app.lang.ignore')) === 0) {
                $langs = '';
            } else {
                if (is_numeric($name)) {
                    $name = strval($name);
                }
                $langs = \fxyin\Lang::get($name, $vars, $lang);
            }
        }
        return $langs;
    }

    /**
     * 语言-解析
     * @param array|string $name 语言变量名
     * @return string
     */
    public function langParse($name)
    {
        $type = \fxyin\Lang::range();
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                if (is_array($value)) {
                    $name[$key] = $this->langParse($value);;
                }
            }
            switch ($type) {
                default:
                case 'en-us':
                    // 英语（美国）
                    $string = implode(' ', $name);
                    break;
                case 'zh-cn':
                    // 中文（简体）
                    $string = implode('', $name);
                    break;
            }
        } else {
            $string = $name;
        }
        return $string;
    }

    /**
     * 配置参数-[获取|设置]
     * @param array|string $name 参数名
     * @param mixed $data 数据
     * @param string $range 作用域
     * @return mixed
     */
    public function config($name = '', $data = null, $range = '')
    {
        if (is_null($data) && is_string($name)) {
            return 0 === strpos($name, '?') ? \fxyin\Config::has(substr($name, 1), $range) : \fxyin\Config::get($name, $range);
        } else {
            return \fxyin\Config::set($name, $data, $range);
        }
    }

    /**
     * 环境参数-[获取|设置]
     * @param array|string $name 参数名
     * @param mixed $data 数据
     * @return mixed
     */
    public function env($name = '', $data = null)
    {
        if (is_null($data) && is_string($name)) {
            return 0 === strpos($name, '?') ? \fxyin\Env::has(substr($name, 1)) : \fxyin\Env::get($name);
        } else {
            return \fxyin\Env::set($name, $data);
        }
    }

    /**
     * 浏览器友好的变量输出
     * @param mixed $vars 要输出的变量
     * @return void
     */
    public function dump(...$vars)
    {
        // 提取数据
        ob_start();
        var_dump(...$vars);
        $output = ob_get_clean();
        $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
        // 处理数据
        if (PHP_SAPI == 'cli') {
            $output = PHP_EOL . $output . PHP_EOL;
        } else {
            if (!extension_loaded('xdebug')) {
                $output = htmlspecialchars($output, ENT_SUBSTITUTE);
            }
            $output = '<pre>' . $output . '</pre>';
        }
        // 输出数据
        echo $output;
    }

    /**
     * 解析Json
     * @param mixed $var 变量
     * @param string $type 类型
     * @return mixed
     */
    public function json($var, $type)
    {
        // 初始化变量
        $echo = null;
        $type = strtolower($type);
        switch ($type) {
            case 'encode':
                // 编码
                // 检查参数
                if (!is_json($var) && !is_array($var)) {
                    $var = $var;
                } else if (is_array($var)) {
                    $var = \fxapp\Param::json($var, 'encode');
                }
                $echo = $var;
                break;
            case 'decode':
                // 解码
                // 检查参数
                if (!is_json($var) && !is_array($var)) {
                    $var = [];
                } else if (is_json($var)) {
                    $var = \fxapp\Param::json($var, 'decode');
                }
                $echo = $var;
                break;
        }
        return $echo;
    }

    /**
     * 加密解密
     * @param mixed $var 变量
     * @param string $type 类型
     * @param string $param 参数
     * @return mixed
     */
    public function crypt($var, $type, $param = null)
    {
        // 初始化变量
        $config = $this->config('safe.base');
        if (!$config['crypt_switch']) return $var;
        $data = null;
        if (!is_array($param)) {
            $param = null;
        }
        $predefined = [
            'method' => $config['crypt_method'], 'password' => $config['crypt_key'], 'options' => $config['crypt_options'],
            'iv' => $config['crypt_iv'],
        ];
        $param = \fxapp\Param::define([$param, $predefined], '1.1.2');
        $type = strtolower($type);
        switch ($type) {
            case 'encode':
                $data = \fxapp\Safe::crypt($var, 'encode', $param);
                break;
            case 'decode':
                $data = \fxapp\Safe::crypt($var, 'decode', $param);
                break;
        }
        return $data;
    }
}
