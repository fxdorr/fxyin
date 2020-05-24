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
 * 参数类
 */
class Param
{
    /**
     * 初始化配置
     * @return mixed
     */
    public function init()
    {
        // 初始化CLI变量
        $param = $_SERVER['argv'] ?? [];
        $data = [];
        foreach ($param as $elem) {
            if (strpos($elem, '-') === 0) {
                $elem = preg_replace('/^-/', '', $elem);
                $elem = explode('=', $elem, 2);
                $elem[1] = $elem[1] ?? null;
                $data[$elem[0]] = $elem[1];
            }
        }
        \fxapp\Base::config('debug.data.cli', $data);
    }

    /**
     * 定义参数
     * @param array $param 参数
     * @param int $mode 模式
     * @return array
     */
    public function define($param, $mode = null)
    {
        // 初始化变量
        $echo = [];
        switch ($mode) {
            default:
                // 默认
                $echo = $param;
                break;
            case '1.1.1':
                // 数组覆盖-融合-值不存在
                // $predefined = [
                //     'data' => null,
                // ];
                // data => mixed
                $echo = $param[0];
                foreach ($param[1] as $key => $value) {
                    if (!isset($echo[$key])) {
                        $echo[$key] = $value;
                    }
                }
                break;
            case '1.1.2':
                // 数组覆盖-融合-值不存在或为空字符串
                // $predefined = [
                //     'data' => null,
                // ];
                // data => mixed
                $echo = $param[0];
                foreach ($param[1] as $key => $value) {
                    if (!isset($echo[$key]) || '' === $echo[$key]) {
                        $echo[$key] = $value;
                    }
                }
                break;
            case '1.1.3':
                // 数组覆盖-融合-值不存在或为非数组
                // $predefined = [
                //     'data' => null,
                // ];
                // data => array
                // data => json
                $echo = $param[0];
                foreach ($param[1] as $key => $value) {
                    if (!isset($echo[$key])) {
                        $echo[$key] = $value;
                    }
                }
                foreach ($param[1] as $key => $value) {
                    if (is_json($echo[$key])) {
                        $echo[$key] = \fxapp\Base::json($echo[$key], 'decode');
                    } else if (is_string($echo[$key])) {
                        parse_str($echo[$key], $echo[$key]);
                    } else if (!is_array($echo[$key])) {
                        $echo[$key] = [];
                    }
                }
                break;
            case '1.2.1':
                // 数组覆盖-赋空-值不存在
                // $predefined = [
                //     'data',
                // ];
                // data => mixed
                $echo = $param[0];
                foreach ($param[1] as $key => $value) {
                    if (!isset($echo[$value])) {
                        $echo[$value] = null;
                    }
                }
                break;
            case '1.2.2':
                // 数组覆盖-赋空-值不存在或为空字符串
                // $predefined = [
                //     'data',
                // ];
                // data => mixed
                $echo = $param[0];
                foreach ($param[1] as $key => $value) {
                    if (!isset($echo[$value]) || '' === $echo[$value]) {
                        $echo[$value] = null;
                    }
                }
                break;
            case '1.2.3':
                // 数组覆盖-赋空-值不存在或为非数组
                // $predefined = [
                //     'data',
                // ];
                // data => array
                // data => json
                $echo = $param[0];
                foreach ($param[1] as $key => $value) {
                    if (!isset($echo[$value])) {
                        $echo[$value] = [];
                    } else if (is_json($echo[$value])) {
                        $echo[$value] = \fxapp\Base::json($echo[$value], 'decode');
                    } else if (is_string($echo[$value])) {
                        parse_str($echo[$value], $echo[$value]);
                    } else if (!is_array($echo[$value])) {
                        $echo[$value] = [];
                    }
                }
                break;
            case '1.2.4':
                // 数组覆盖-赋空-值拆分数组
                // $predefined = [
                //     'data',
                // ];
                // data => 1,2,3,4
                $echo = $param[0];
                foreach ($param[1] as $key => $value) {
                    if (!isset($echo[$value]) || '' === $echo[$value]) {
                        $echo[$value] = [];
                    } else if (is_string($echo[$value]) || is_numeric($echo[$value])) {
                        $echo[$value] = explode(',', $echo[$value]);
                    } else if (!is_array($echo[$value])) {
                        $echo[$value] = [];
                    }
                }
                break;
            case '1.3.1':
                // 数组覆盖-倒融合-值不存在
                // $predefined = [
                //     'data' => null,
                // ];
                // data => mixed
                $echo = $param[0];
                foreach ($param[1] as $key => $value) {
                    if (!is_null($value)) {
                        $echo[$key] = $value;
                    }
                }
                break;
            case '1.3.2':
                // 数组覆盖-倒融合-赋空-值不存在
                // $predefined = [
                //     'data' => null,
                // ];
                // data => mixed
                $echo = $param[0];
                foreach ($param[1] as $key => $value) {
                    if (!is_null($value)) {
                        $echo[$key] = $value;
                    } else if (!isset($echo[$key]) || '' === $echo[$key]) {
                        $echo[$key] = null;
                    }
                }
                break;
            case '2.1.1':
                // 数组新建-融合-值不存在
                // $predefined = [
                //     'data' => null,
                // ];
                // data => mixed
                foreach ($param[1] as $key => $value) {
                    if (!isset($param[0][$key])) {
                        $echo[$key] = $value;
                    } else {
                        $echo[$key] = $param[0][$key];
                    }
                }
                break;
            case '2.1.2':
                // 数组新建-融合-值不存在或为空字符串
                // $predefined = [
                //     'data' => null,
                // ];
                // data => mixed
                foreach ($param[1] as $key => $value) {
                    if (!isset($param[0][$key]) || '' === $param[0][$key]) {
                        $echo[$key] = $value;
                    } else {
                        $echo[$key] = $param[0][$key];
                    }
                }
                break;
            case '2.2.1':
                // 数组新建-赋空-值不存在
                // $predefined = [
                //     'data',
                // ];
                // data => mixed
                foreach ($param[1] as $key => $value) {
                    if (!isset($param[0][$value])) {
                        $echo[$value] = null;
                    } else {
                        $echo[$value] = $param[0][$value];
                    }
                }
                break;
            case '2.2.2':
                // 数组新建-赋空-值不存在或为空字符串
                // $predefined = [
                //     'data',
                // ];
                // data => mixed
                foreach ($param[1] as $key => $value) {
                    if (!isset($param[0][$value]) || '' === $param[0][$value]) {
                        $echo[$value] = null;
                    } else {
                        $echo[$value] = $param[0][$value];
                    }
                }
                break;
        }
        return $echo;
    }

    /**
     * 空数组转对象
     * @param array $param 参数
     * @return mixed
     */
    public function object($param)
    {
        // 初始化变量
        if (is_array($param)) {
            // 空数组直接返回
            if (!count($param)) {
                return $param;
            }
            // 过滤空元素
            if (!isset($param[''])) {
                unset($param['']);
            }
            // 处理数组
            if (!count($param)) {
                $param = new \StdClass();
            } else {
                foreach ($param as $key => $value) {
                    $param[$key] = $this->object($value);
                }
            }
        }
        return $param;
    }

    /**
     * 提取数组
     * @param array $base 基础
     * @param array $data 数据
     * @return array
     */
    public function pick($base, $data)
    {
        // 初始化变量
        $echo = [];
        if (!is_array($base)) {
            $base = [];
        }
        if (!is_array($data)) {
            return $echo;
        }
        // 处理数据
        foreach ($data as $key => $value) {
            // 提取匹配数据
            if (is_array($value)) {
                if (array_key_exists($key, $base)) {
                    $echo[$key] = $this->pick($base[$key], $value);
                } else {
                    $echo[$key] = $this->pick([], $value);
                }
            } else {
                if (array_key_exists($value, $base)) {
                    $echo[$value] = $base[$value];
                } else {
                    $echo[$value] = null;
                }
            }
        }
        return $echo;
    }

    /**
     * 追加数组
     * @param array $args 数组集合
     * @return mixed
     */
    public function append(...$args)
    {
        // 初始化变量
        $echo = array_shift($args);
        if (!is_array($echo)) {
            $echo = [];
        }
        // 追加数组
        foreach ($args as $data) {
            if (!is_array($data)) continue;
            foreach ($data as $key => $value) {
                if (is_string($key)) {
                    $echo[$key] = $value;
                } else {
                    $echo[] = $value;
                }
            }
        }
        return $echo;
    }

    /**
     * 合并数组
     * @param array $args 数组集合
     * @return mixed
     */
    public function merge(...$args)
    {
        // 初始化变量
        $echo = [];
        if (count($args) < 2) {
            return array_shift($args);
        } else if (count($args) > 2) {
            $echo[0] = array_shift($args);
            $echo[1] = $this->merge(...$args);
        } else {
            $echo = $args;
        }
        return $this->cover($echo);
    }

    /**
     * 覆盖数组
     * @param array $args 数组集合
     * @return array
     */
    public function cover($args)
    {
        // 初始化变量
        if (!is_array($args[0])) {
            $args[0] = $args[1];
        } else if (is_array($args[1])) {
            // 数组融合，已存在配置参数则覆盖
            foreach ($args[1] as $key => $value) {
                if (!isset($args[0][$key])) {
                    $args[0][$key] = $value;
                } else if (is_array($value)) {
                    $args[0][$key] = $this->cover([$args[0][$key], $args[1][$key]]);
                } else {
                    $args[0][$key] = $value;
                }
            }
        }
        return $args[0];
    }

    /**
     * 解析Json
     * @param mixed $var 变量
     * @param string $type 类型
     * @param string $param 参数
     * @return mixed
     */
    public function json($var, $type, $param = null)
    {
        // 初始化变量
        $echo = null;
        $type = strtolower($type);
        switch ($type) {
            case 'encode':
                // 编码
                if (is_null($param)) {
                    $param = JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES;
                }
                $echo = json_encode($var, $param);
                break;
            case 'decode':
                // 解码
                if (is_null($param)) {
                    $param = true;
                }
                $echo = json_decode($var, $param);
                break;
        }
        return $echo;
    }
}
