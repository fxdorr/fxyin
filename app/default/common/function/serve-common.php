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

use fxyin\cache\driver\Redis;
use fxyin\cache\driver\File;
use fxyin\Db;

/**
 * 框架-公共-检查-格式
 * @param array $data 数据
 * @param string $type 类型
 * @return mixed
 */
function fcc_format($data, $type)
{
    //初始化变量
    $param = ftc_param(['base' => ['debug']]);
    $echo = [];
    switch ($type) {
        default:
            //默认
            $echo = $data;
            break;
        case 1:
            //通用
            $base = fxy_config('result.format');
            //处理数据
            $data[2] = fxy_lang($data[2]);
            foreach ($base as $key => $value) {
                if (array_key_exists($key, $data)) {
                    $echo[$value] = $data[$key];
                }
            }
            //调试模式
            $debug = fxy_config('fxy_debug');
            if ($debug && $param['base']['debug']) {
                $echo['debug'] = [
                    'get' => $_GET,
                    'post' => $_POST,
                    'request' => $_REQUEST,
                    'files' => $_FILES,
                    'input' => file_get_contents('php://input'),
                    'server' => $_SERVER,
                ];
            }
            //空对象处理
            $echo = fmo_oempty($echo);
            break;
    }
    return $echo;
}

/**
 * 框架-公共-操作-返回
 * @param mixed $var 变量
 * @param string $type 类型
 * @return mixed
 */
function fco_return($var, $type = '')
{
    //返回格式
    $type = !empty($type) ? $type : 'json';
    $type = strtolower($type);
    switch ($type) {
        default:
            //默认
        case 'json':
            //返回JSON数据格式到客户端，包含状态信息
            exit(fxy_json($var)->send());
        case 'xml':
            //返回xml格式数据
            exit(fxy_xml($var)->send());
    }
}

/**
 * 框架-服务-初始化-结果
 * @return array
 */
function fsi_result()
{
    //初始化变量
    $data = fxy_config('result.template');
    return $data;
}

/**
 * 框架-服务-初始化-参数
 * @param array $trans 参数数组
 * @param integer $mode 模式
 * @return array
 */
function fsi_param($trans, $mode = null)
{
    //初始化变量
    switch ($mode) {
        default:
            //默认
            $result = $trans;
            break;
        case '1.1.1':
            //数组覆盖-融合-值不存在
            // $predefined = [
            //     'data' => null,
            // ];
            // data => mixed
            foreach ($trans[1] as $key => $value) {
                if (!isset($trans[0][$key])) {
                    $trans[0][$key] = $value;
                }
            }
            $result = $trans[0];
            break;
        case '1.1.2':
            //数组覆盖-融合-值不存在或为空字符串
            // $predefined = [
            //     'data' => null,
            // ];
            // data => mixed
            foreach ($trans[1] as $key => $value) {
                if (!isset($trans[0][$key]) || '' === $trans[0][$key]) {
                    $trans[0][$key] = $value;
                }
            }
            $result = $trans[0];
            break;
        case '1.1.3':
            //数组覆盖-融合-值不存在或为非数组
            // $predefined = [
            //     'data' => null,
            // ];
            // data => array
            // data => json
            foreach ($trans[1] as $key => $value) {
                if (!isset($trans[0][$key])) {
                    $trans[0][$key] = $value;
                } else if (is_json($trans[0][$key])) {
                    $trans[0][$key] = fmf_json($trans[0][$key], 'decode');
                } else if (!is_array($trans[0][$key])) {
                    $trans[0][$key] = $value;
                }
            }
            foreach ($trans[1] as $key => $value) {
                if (!isset($trans[0][$value])) {
                    $trans[0][$value] = [];
                } else if (is_json($trans[0][$value])) {
                    $trans[0][$value] = fmf_json($trans[0][$value], 'decode');
                } else if (!is_array($trans[0][$value])) {
                    $trans[0][$value] = [];
                }
            }
            $result = $trans[0];
            break;
        case '1.2.1':
            //数组覆盖-赋空-值不存在
            // $predefined = [
            //     'data',
            // ];
            // data => mixed
            foreach ($trans[1] as $key => $value) {
                if (!isset($trans[0][$value])) {
                    $trans[0][$value] = null;
                }
            }
            $result = $trans[0];
            break;
        case '1.2.2':
            //数组覆盖-赋空-值不存在或为空字符串
            // $predefined = [
            //     'data',
            // ];
            // data => mixed
            foreach ($trans[1] as $key => $value) {
                if (!isset($trans[0][$value]) || '' === $trans[0][$value]) {
                    $trans[0][$value] = null;
                }
            }
            $result = $trans[0];
            break;
        case '1.2.3':
            //数组覆盖-赋空-值不存在或为非数组
            // $predefined = [
            //     'data',
            // ];
            // data => array
            // data => json
            foreach ($trans[1] as $key => $value) {
                if (!isset($trans[0][$value])) {
                    $trans[0][$value] = [];
                } else if (is_json($trans[0][$value])) {
                    $trans[0][$value] = fmf_json($trans[0][$value], 'decode');
                } else if (!is_array($trans[0][$value])) {
                    $trans[0][$value] = [];
                }
            }
            $result = $trans[0];
            break;
        case '1.2.4':
            //数组覆盖-赋空-值拆分数组
            // $predefined = [
            //     'data',
            // ];
            // data => 1,2,3,4
            foreach ($trans[1] as $key => $value) {
                if (!isset($trans[0][$value]) || '' === $trans[0][$value]) {
                    $trans[0][$value] = [];
                } else if (is_string($trans[0][$value]) || is_numeric($trans[0][$value])) {
                    $trans[0][$value] = explode(',', $trans[0][$value]);
                } else if (!is_array($trans[0][$value])) {
                    $trans[0][$value] = [];
                }
            }
            $result = $trans[0];
            break;
        case '1.3.1':
            //数组覆盖-倒融合-值不存在
            // $predefined = [
            //     'data' => null,
            // ];
            // data => mixed
            foreach ($trans[1] as $key => $value) {
                if (!is_null($value)) {
                    $trans[0][$key] = $value;
                }
            }
            $result = $trans[0];
            break;
        case '1.3.2':
            //数组覆盖-倒融合-赋空-值不存在
            // $predefined = [
            //     'data' => null,
            // ];
            // data => mixed
            foreach ($trans[1] as $key => $value) {
                if (!is_null($value)) {
                    $trans[0][$key] = $value;
                } else if (!isset($trans[0][$key]) || '' === $trans[0][$key]) {
                    $trans[0][$key] = null;
                }
            }
            $result = $trans[0];
            break;
        case '2.1.1':
            //数组新建-融合-值不存在
            // $predefined = [
            //     'data' => null,
            // ];
            // data => mixed
            $trans[2] = [];
            foreach ($trans[1] as $key => $value) {
                if (!isset($trans[0][$key])) {
                    $trans[2][$key] = $value;
                } else {
                    $trans[2][$key] = $trans[0][$key];
                }
            }
            $result = $trans[2];
            break;
        case '2.1.2':
            //数组新建-融合-值不存在或为空字符串
            // $predefined = [
            //     'data' => null,
            // ];
            // data => mixed
            $trans[2] = [];
            foreach ($trans[1] as $key => $value) {
                if (!isset($trans[0][$key]) || '' === $trans[0][$key]) {
                    $trans[2][$key] = $value;
                } else {
                    $trans[2][$key] = $trans[0][$key];
                }
            }
            $result = $trans[2];
            break;
        case '2.2.1':
            //数组新建-赋空-值不存在
            // $predefined = [
            //     'data',
            // ];
            // data => mixed
            $trans[2] = [];
            foreach ($trans[1] as $key => $value) {
                if (!isset($trans[0][$value])) {
                    $trans[2][$value] = null;
                } else {
                    $trans[2][$value] = $trans[0][$value];
                }
            }
            $result = $trans[2];
            break;
        case '2.2.2':
            //数组新建-赋空-值不存在或为空字符串
            // $predefined = [
            //     'data',
            // ];
            // data => mixed
            $trans[2] = [];
            foreach ($trans[1] as $key => $value) {
                if (!isset($trans[0][$value]) || '' === $trans[0][$value]) {
                    $trans[2][$value] = null;
                } else {
                    $trans[2][$value] = $trans[0][$value];
                }
            }
            $result = $trans[2];
            break;
    }
    return $result;
}

/**
 * 框架-服务-初始化-UUID
 * @param string $mode 模式
 * @param array $tran 参数
 * @return array
 */
function fsi_uuid($mode = null, $tran = [])
{
    //初始化变量
    $uuid = '';
    $predefined = [
        'uuid' => '', 'prefix' => '', 'style' => '',
    ];
    $tran = fsi_param([$tran, $predefined], '1.1.2');
    switch ($mode) {
        case 1:
        default:
            //随机生成标准UUID
            if (empty($tran['uuid'])) {
                $chars = strtoupper(md5(uniqid(mt_rand(), true)));
            } else {
                $chars = strtoupper($tran['uuid']);
            }
            $uuid = substr($chars, 0, 8) . '-';
            $uuid .= substr($chars, 8, 4) . '-';
            $uuid .= substr($chars, 12, 4) . '-';
            $uuid .= substr($chars, 16, 4) . '-';
            $uuid .= substr($chars, 20, 12);
            $uuid = $tran['prefix'] . $uuid;
            break;
        case 2:
            //随机生成大写UUID或将现有UUID大写
            if (empty($tran['uuid'])) {
                $chars = strtoupper(md5(uniqid(mt_rand(), true)));
            } else {
                $chars = strtoupper($tran['uuid']);
            }
            $uuid = $chars;
            break;
        case 3:
            //随机生成大写UUID或将现有UUIDMD5并大写
            if (empty($tran['uuid'])) {
                $chars = strtoupper(md5(uniqid(mt_rand(), true)));
            } else {
                $chars = strtoupper(md5($tran['uuid']));
            }
            $uuid = $chars;
            break;
    }
    $tran['style'] = fmo_explode(',', $tran['style']);
    foreach ($tran['style'] as $key => $value) {
        switch ($value) {
            case 1:
                //32位-大写
                $uuid = strtoupper($uuid);
                break;
            case 2:
                //32位-小写
                $uuid = strtolower($uuid);
                break;
            case 3:
                //16位-大写
                $uuid = strtoupper(substr($uuid, 8, 16));
                break;
            case 4:
                //16位-小写
                $uuid = strtolower(substr($uuid, 8, 16));
                break;
        }
    }
    return $uuid;
}

/**
 * 框架-公共-服务-缓存
 * @param array $type 类型
 * @param array $options 参数
 * @return File|Redis
 */
function fcs_cache($type, $options = [])
{
    //初始化变量
    $data = null;
    if (!is_string($type)) {
        return $data;
    }
    $type = strtolower($type);
    if (empty($options)) {
        $options = fxy_config('cache')[$type];
    }
    switch ($type) {
        case 'redis':
            //redis数据库
            $data = new Redis($options);
            break;
        case 'file':
            //文件系统
            $data = new File($options);
            break;
    }
    return $data;
}

/**
 * 框架-公共-服务-数据库
 * @param array $type 类型
 * @param array $options 参数
 * @return MongoDB|Redis
 */
function fcs_database($type, $options = [])
{
    //初始化变量
    $data = null;
    if (!is_string($type)) {
        return $data;
    }
    $type = strtolower($type);
    switch ($type) {
        case 'mongodb':
            //mongodb数据库
            if (empty($options)) {
                $options = fxy_config('database')['mongodb'];
            }
            $data = Db::connect($options);
            break;
    }
    return $data;
}

/**
 * 框架-模块-操作-打散字符串
 * @param string $separator 分割字符串
 * @param string $string 字符串
 * @return mixed
 */
function fmo_explode($separator, $string)
{
    //初始化变量
    return array_values(array_unique(explode($separator, $string)));
}

/**
 * 框架-模块-操作-空数组转对象
 * @param array $param 参数
 * @return mixed
 */
function fmo_oempty($param)
{
    //初始化变量
    if (is_array($param)) {
        if (isset($param['']) && count($param) == 1) {
            $param = new \StdClass();
        } else {
            foreach ($param as $key => $value) {
                $param[$key] = fmo_oempty($value);
            }
        }
    }
    return $param;
}

/**
 * 框架-模块-操作-合并数组
 * @param array $args 数组集合
 * @return mixed
 */
function fmo_merge(...$args)
{
    //初始化变量
    $data = [];
    if (count($args) < 2) {
        return array_shift($args);
    } else if (count($args) > 2) {
        $data[0] = array_shift($args);
        $data[1] = fmo_merge(...$args);
    } else {
        $data = $args;
    }
    return fmo_cover($data);
}

/**
 * 框架-模块-操作-覆盖数组
 * @param array $args 数组集合
 * @return array
 */
function fmo_cover($args)
{
    //初始化变量
    if (!is_array($args[0])) {
        $args[0] = $args[1];
    } else if (is_array($args[1])) {
        //数组融合，已存在配置参数则覆盖
        foreach ($args[1] as $key => $value) {
            if (!isset($args[0][$key])) {
                $args[0][$key] = $value;
            } else if (is_array($value)) {
                $args[0][$key] = fmo_cover([$args[0][$key], $args[1][$key]]);
            } else {
                $args[0][$key] = $value;
            }
        }
    }
    return $args[0];
}

/**
 * 框架-模块-操作-打印语言 <p>
 * fmc print lang
 * </p>
 * @param array|string $name 语言变量名
 * @param integer $mode 模式
 * @return mixed
 */
function fmo_plang($name, $mode = null)
{
    //初始化变量
    $string = fxy_lang($name);
    if (is_null($mode)) {
        if (is_array($string)) {
            $mode = 2;
        }
    }
    switch ($mode) {
        case 1:
        default:
            //模式-字符串
            echo $string;
            break;
        case 2:
            //模式-数组
            print_r($string);
            break;
    }
}

/**
 * 框架-公共-函数-Json
 * @param mixed $var 变量
 * @param string $type 类型
 * @param string $param 参数
 * @return mixed
 */
function fcf_json($var, $type, $param = null)
{
    //初始化变量
    $data = null;
    $type = strtolower($type);
    switch ($type) {
        case 'encode':
            //编码
            if (is_null($param)) {
                $param = JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES;
            }
            $data = json_encode($var, $param);
            break;
        case 'decode':
            //解码
            if (is_null($param)) {
                $param = true;
            }
            $data = json_decode($var, $param);
            break;
    }
    return $data;
}

/**
 * 框架-公共-函数-调试输出
 * @param mixed $param 参数
 * @param boolean $echo 是否输出
 * @return string
 */
function fcf_dump($param, $echo = true)
{
    //初始化变量
    fxy_dump($param, $echo, fcf_mtime(fcf_mtime()));
}

/**
 * 框架-公共-函数-毫秒时间 <p>
 * fcf milli time
 * </p>
 * @param string $mtime 毫秒时间
 * @return string
 */
function fcf_mtime($mtime = null)
{
    //初始化变量
    $data = null;
    if (is_numeric($mtime)) {
        if (strlen($mtime) != 13) {
            return false;
        }
        $data = date("Y-m-d H:i:s", substr($mtime, 0, 10));
        $data = $data . '.' . substr($mtime, 10, 3);
    } else if (is_string($mtime)) {
        $data = explode('.', $mtime);
        $data = strtotime($data[0]) . $data[1];
        $data = intval($data);
    } else {
        $now = explode(' ', microtime());
        $data = substr($now[1] . substr($now[0], 2), 0, 13);
        $data = intval($data);
    }
    return $data;
}

/**
 * 框架-公共-函数-格式化时间 <p>
 * fcf format time
 * </p>
 * @param string $time 时间
 * @param string $type 格式
 * @return string
 */
function fcf_ftime($time = null, $type = null)
{
    //初始化变量
    $data = [];
    if (!is_numeric($time)) {
        return false;
    }
    //时间列表
    $time_list = [];
    //时间开关
    $time_switch = [
        0 => false,
        1 => true,
        2 => true,
        3 => true,
        4 => true,
        5 => true,
        6 => true,
    ];
    //时间名称
    $time_name = [
        0 => 'millisecond',
        1 => 'sec',
        2 => 'minute',
        3 => 'hour',
        4 => 'day',
        5 => 'month',
        6 => 'year',
    ];
    $time_all = $time;
    switch ($type) {
        case '1.1':
        default:
            //秒
            break;
        case '1.2':
            //毫秒
            $time_switch[0] = true;
            break;
    }
    //毫秒
    if ($time_switch[0]) {
        $time_list[0] = $time_all % 1000;
        $time_all = intval($time_all / 1000);
    }
    //秒
    if ($time_switch[1]) {
        $time_list[1] = $time_all % 60;
        $time_all = intval($time_all / 60);
    }
    //分钟
    if ($time_switch[2]) {
        $time_list[2] = $time_all % 60;
        $time_all = intval($time_all / 60);
    }
    //小时
    if ($time_switch[3]) {
        $time_list[3] = $time_all % 24;
        $time_all = intval($time_all / 24);
    }
    //天
    if ($time_switch[4]) {
        $time_list[4] = $time_all % 30;
        $time_all = intval($time_all / 30);
    }
    //月
    if ($time_switch[5]) {
        $time_list[5] = $time_all % 12;
        $time_all = intval($time_all / 12);
    }
    //年
    if ($time_switch[6]) {
        $time_list[6] = $time_all;
    }
    krsort($time_list);
    //去除0
    foreach ($time_list as $key => $value) {
        if ($value == 0) {
            unset($time_list[$key]);
        }
    }
    foreach ($time_list as $key => $value) {
        $data[] = $value;
        $data[] = $time_name[$key];
    }
    $data = fxy_lang($data);
    return $data;
}

/**
 * 框架-公共-函数-Ipv4
 * @param mixed $var 变量
 * @param string $type 类型
 * @return mixed
 */
function fcf_ipv4($var, $type)
{
    //初始化变量
    $data = null;
    $type = strtolower($type);
    switch ($type) {
        case 'encode':
            //编码
            $var = explode('.', $var);
            foreach ($var as $key => $value) {
                $value = ltrim($value, 0);
                if ($value == '') {
                    $value = '0';
                }
                $var[$key] = $value;
            }
            $var = implode('.', $var);
            $data = ip2long($var);
            if ($data !== false) {
                $data = bindec(decbin($data));
            }
            break;
        case 'decode':
            //解码
            $data = long2ip($var);
            break;
    }
    return $data;
}

/**
 * 框架-公共-函数-随机字符
 * @param numeric $length 长度
 * @param numeric $numeric 类型(0：混合；1：纯数字)
 * @return string
 */
function fcf_rand($length, $numeric = 0)
{
    //初始化变量
    $seed = base_convert(md5(microtime() . __DIR__), 16, $numeric ? 10 : 35);
    $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
    if ($numeric) {
        $hash = '';
    } else {
        $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
        $length--;
    }
    $max = strlen($seed) - 1;
    for ($i = 0; $i < $length; $i++) {
        $hash .= $seed{
            mt_rand(0, $max)};
    }
    return $hash;
}

/**
 * 框架-公共-函数-加密解密
 * @param mixed $var 变量
 * @param string $type 类型
 * @param string $param 参数
 * @return mixed
 */
function fcf_crypt($var, $type, $param = null)
{
    //初始化变量
    $data = null;
    $type = strtolower($type);
    switch ($type) {
        case 'encode':
            //编码
            if (!is_array($param)) {
                $param = null;
            }
            $predefined = [
                'method' => 'des-ecb', 'password' => '00000000', 'options' => null,
                'iv' => null,
            ];
            $param = fsi_param([$param, $predefined], '1.1.2');
            $data = openssl_encrypt($var, $param['method'], $param['password'], $param['options'], $param['iv']);
            break;
        case 'decode':
            //解码
            if (!is_array($param)) {
                $param = null;
            }
            $predefined = [
                'method' => 'des-ecb', 'password' => '00000000', 'options' => null,
                'iv' => null,
            ];
            $param = fsi_param([$param, $predefined], '1.1.2');
            $data = openssl_decrypt($var, $param['method'], $param['password'], $param['options'], $param['iv']);
            break;
    }
    return $data;
}

/**
 * 框架-公共-函数-16进制转字符串
 * @param mixed $var 变量
 * @param string $type 类型
 * @return mixed
 */
function fcf_convert($var, $type)
{
    //初始化变量
    $data = null;
    $type = strtolower($type);
    switch ($type) {
        case 'hexstr':
            //16进制转字符串
            if (!$var) {
                //空字符串
                return false;
            }
            $strs = [];
            $len = strlen($var);
            for ($i = 0; $i < $len; $i++) {
                $str = substr($var, $i, 1);
                $strs[] = bin2hex($str);
            }
            $strs = implode('', $strs);
            $data = $strs;
            break;
        case 'strhex':
            //字符串转16进制
            if (!$var) {
                //空字符串
                return false;
            } else if (mb_strlen($var, 'utf-8') != strlen($var)) {
                //不解析混编数据
                return false;
            } else if (strlen($var) % 2 != 0) {
                //不解析单数字符串
                return false;
            }
            $strs = [];
            $len = strlen($var);
            for ($i = 0; $i < $len; $i = $i + 2) {
                $str = substr($var, $i, 2);
                $strs[] = chr(hexdec($str));
            }
            $strs = implode('', $strs);
            $data = $strs;
            break;
    }
    return $data;
}

/**
 * 框架-公共-函数-MD5
 * @param mixed $var 变量
 * @param integer $type 类型
 * @return mixed
 */
function fcf_md5($var, $type = null)
{
    //初始化变量
    $data = null;
    $type = intval($type);
    switch ($type) {
        case 1:
        default:
            //32位
            $data = md5($var);
            break;
        case 2:
            //16位
            $data = substr(md5($var), 8, 16);
            break;
    }
    return $data;
}

/**
 * 框架-公共-函数-异常
 * @param Exception $e 异常信息
 * @return mixed
 */
function fcf_exception($e)
{
    //返回异常
    return $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine();
}

/**
 * 框架-公共-函数-令牌
 * @param mixed $var 变量
 * @param string $type 类型
 * @return mixed
 */
function fcf_token($var, $type)
{
    //初始化变量
    $data = null;
    $type = strtolower($type);
    switch ($type) {
        case 'encode':
            //编码
            if (!$var) {
                //空字符串
                return false;
            } else if (is_array($var)) {
                //数组
                $var = implode(',', $var);
            } else if (!is_string($var)) {
                //非字符串
                return false;
            }
            //计算加密长度
            $param = ',' . $var . ',';
            $strlen = strlen($param);
            $exp = 5;
            do {
                $pow = pow(2, $exp);
                $strdiff = $pow - $strlen;
                ++$exp;
            } while ($strdiff < 0);
            $strmax = $pow;
            //填充令牌
            $param = str_pad($param, $strmax, fcf_rand($strdiff / 2), STR_PAD_BOTH);
            //加密令牌
            $param = fmf_crypt($param, 'encode');
            $data = bin2hex($param);
            break;
        case 'decode':
            //解码
            if (!$var) {
                //空字符串
                return false;
            } else if (!is_string($var)) {
                //非字符串
                return false;
            } else if (strlen($var) % 2 != 0) {
                //不解析单数字符串
                return false;
            } else if (!ctype_xdigit($var)) {
                //非纯16进制字符串
                return false;
            }
            //解密令牌
            $param = hex2bin($var);
            $param = fmf_crypt($param, 'decode');
            $param = explode(',', $param);
            array_shift($param);
            array_pop($param);
            $param = implode(',', $param);
            $data = $param;
            break;
    }
    return $data;
}


/**
 * 框架-公共-函数-RSA-私钥
 * @param mixed $var 变量
 * @param string $type 类型
 * @param array $param 参数
 * @return mixed
 */
function fcf_rsapri($var, $type, $param = [])
{
    //初始化变量
    $data = [];
    if (!is_string($var) || !is_array($param)) return false;
    $predefined = [
        'type', 'secret',
    ];
    $param = fsi_param([$param, $predefined], '1.2.2');
    $param['secret'] = fcf_rsapem($param['secret'], 'private');
    $param['secret'] = openssl_pkey_get_private($param['secret']);
    $type = strtolower($type);
    switch ($type) {
        case 'encode':
            //编码
            $var = str_split($var, 117);
            foreach ($var as $value) {
                $trans = null;
                //解析填充字符
                switch ($param['type']) {
                    case OPENSSL_NO_PADDING:
                        //填充-自定义
                        $predefined = [
                            'pad' => "\0",
                        ];
                        $param = fsi_param([$param, $predefined], '1.1.2');
                        $value = str_pad($value, 128, $param['pad'], STR_PAD_LEFT);
                        break;
                }
                openssl_private_encrypt($value, $trans, $param['secret'], $param['type']);
                $data[] = $trans;
            }
            break;
        case 'decode':
            //解码
            $var = str_split($var, 128);
            foreach ($var as $value) {
                $trans = null;
                openssl_private_decrypt($value, $trans, $param['secret'], $param['type']);
                //解析填充字符
                switch ($param['type']) {
                    case OPENSSL_NO_PADDING:
                        //填充-自定义
                        $predefined = [
                            'pad' => "\0",
                        ];
                        $param = fsi_param([$param, $predefined], '1.1.2');
                        $trans = ltrim($trans, $param['pad']);
                        break;
                }
                $data[] = $trans;
            }
            break;
    }
    $data = implode('', $data);
    return $data;
}

/**
 * 框架-公共-函数-RSA-公钥
 * @param mixed $var 变量
 * @param string $type 类型
 * @param array $param 参数
 * @return mixed
 */
function fcf_rsapub($var, $type, $param = [])
{
    //初始化变量
    $data = [];
    if (!is_string($var) || !is_array($param)) return false;
    $predefined = [
        'type', 'secret',
    ];
    $param = fsi_param([$param, $predefined], '1.2.2');
    $param['secret'] = fcf_rsapem($param['secret'], 'public');
    $param['secret'] = openssl_pkey_get_public($param['secret']);
    $type = strtolower($type);
    switch ($type) {
        case 'encode':
            //编码
            $var = str_split($var, 117);
            foreach ($var as $value) {
                $trans = null;
                //解析填充字符
                switch ($param['type']) {
                    case OPENSSL_NO_PADDING:
                        //填充-自定义
                        $predefined = [
                            'pad' => "\0",
                        ];
                        $param = fsi_param([$param, $predefined], '1.1.2');
                        $value = str_pad($value, 128, $param['pad'], STR_PAD_LEFT);
                        break;
                }
                openssl_public_encrypt($value, $trans, $param['secret'], $param['type']);
                $data[] = $trans;
            }
            break;
        case 'decode':
            //解码
            $var = str_split($var, 128);
            foreach ($var as $value) {
                $trans = null;
                openssl_public_decrypt($value, $trans, $param['secret'], $param['type']);
                //解析填充字符
                switch ($param['type']) {
                    case OPENSSL_NO_PADDING:
                        //填充-自定义
                        $predefined = [
                            'pad' => "\0",
                        ];
                        $param = fsi_param([$param, $predefined], '1.1.2');
                        $trans = ltrim($trans, $param['pad']);
                        break;
                }
                $data[] = $trans;
            }
            break;
    }
    $data = implode('', $data);
    return $data;
}

/**
 * 框架-公共-函数-RSA-密钥
 * @param mixed $var 变量
 * @param string $type 类型
 * @param array $param 参数
 * @return mixed
 */
function fcf_rsapem($var, $type)
{
    //初始化变量
    if (!is_string($var)) return false;
    $type = strtolower($type);
    switch ($type) {
        case 'private':
            //私钥
            if (is_file($var)) {
                $var = file_get_contents($var);
            }
            $var = str_replace("\n", '', $var);
            $var = str_replace("-----BEGIN RSA PRIVATE KEY-----", '', $var);
            $var = str_replace("-----END RSA PRIVATE KEY-----", '', $var);
            $var = "-----BEGIN RSA PRIVATE KEY-----\n" .
                wordwrap($var, 64, "\n", true) .
                "\n-----END RSA PRIVATE KEY-----";
            break;
        case 'public':
            //公钥
            if (is_file($var)) {
                $var = file_get_contents($var);
            }
            $var = str_replace("\n", '', $var);
            $var = str_replace("-----BEGIN PUBLIC KEY-----", '', $var);
            $var = str_replace("-----END PUBLIC KEY-----", '', $var);
            $var = "-----BEGIN PUBLIC KEY-----\n" .
                wordwrap($var, 64, "\n", true) .
                "\n-----END PUBLIC KEY-----";
            break;
    }
    return $var;
}
