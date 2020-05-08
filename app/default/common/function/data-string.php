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

/**
 * 数据-结构-检查-过滤HTML <p>
 * dsc filter html
 * </p>
 * @param string $string 字符串
 * @param string $flags 标签
 * @return mixed
 */
function dsc_fhtml($string, $flags = null)
{
    //初始化变量
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[$key] = dsc_fhtml($val, $flags);
        }
    } else {
        if ($flags === null) {
            $string = str_replace(array('&', '<', '>'), array('&amp;', '&lt;', '&gt;'), $string);
            if (strpos($string, '&amp;#') !== false) {
                $string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
            }
        } else {
            if (PHP_VERSION < '5.4.0') {
                $string = htmlspecialchars($string, $flags);
            } else {
                // if(strtolower(CHARSET) == 'utf-8') {
                $charset = 'UTF-8';
                // } else {
                //     $charset = 'ISO-8859-1';
                // }
                $string = htmlspecialchars($string, $flags, $charset);
            }
        }
    }
    return $string;
}

/**
 * 数据-结构-检查-移除HTML <p>
 * dsc filter html
 * </p>
 * @param string $string 字符串
 * @param string $flags 标签
 * @return mixed
 */
function dsc_rhtml($string, $flags = null)
{
    //初始化变量
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[$key] = dsc_rhtml($val, $flags);
        }
    } else {
        if ($flags === null) {
            $string = strip_tags($string, $flags);
        } else {
            $string = strip_tags($string, $flags);
        }
    }
    return $string;
}

/**
 * 数据-结构-操作-格式树 <p>
 * dso list to tree
 * </p>
 * @param array $list 未树化数组
 * @param int $pId 初始父ID
 * @param string $pName 父名称
 * @param string $index 索引
 * @param string $cName 子名称
 * @return mixed
 */
function dso_listtotree($list, $pId = 0, $pName = 'parent_id', $index = 'id', $cName = '_child')
{
    //初始化变量
    $tree = array();
    if (is_array($list)) {
        $refer = array();
        foreach ($list as $key => $value) {
            $refer[$value[$index]] = &$list[$key];
        }
        foreach ($list as $key => $value) {
            $parentId = $value[$pName];
            if ($parentId == $pId) {
                $tree[] = &$list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent = &$refer[$parentId];
                    $parent[$cName][] = &$list[$key];
                }
            }
        }
    }
    return $tree;
}

if (!function_exists('is_email')) {
    /**
     * 电子邮箱格式校验
     * @param string $var 变量
     * @return boolean
     */
    function is_email($var)
    {
        //初始化变量
        if (is_string($var)) {
            if (!empty($var)) {
                return preg_match('/^[a-z0-9]+([\+_\-\.]?[a-z0-9]+)*@([a-z0-9]+[\-]?[a-z0-9]+\.)+[a-z]{2,6}$/i', $var);
            }
        }
        return false;
    }
}

if (!function_exists('is_mobile')) {
    /**
     * 手机号码格式校验
     * @param string $var 变量
     * @return boolean
     */
    function is_mobile($var)
    {
        //初始化变量
        if (is_string($var)) {
            if (!empty($var)) {
                return preg_match('/^1[0-9]{2}\d{8}$/', $var);
            }
        }
        return false;
    }
}

if (!function_exists('is_zipcode')) {
    /**
     * 邮政编码格式校验
     * @param string $var 变量
     * @return boolean
     */
    function is_zipcode($var)
    {
        //初始化变量
        if (is_string($var)) {
            if (!empty($var)) {
                return preg_match('/^[1-9][0-9]{5}$/', $var);
            }
        }
        return false;
    }
}

if (!function_exists('is_json')) {
    /**
     * Json格式校验
     * @param string $var 变量
     * @param string $format 格式
     * @return mixed
     */
    function is_json($var, $format = null)
    {
        //初始化变量
        if (is_string($var)) {
            switch ($format) {
                default:
                case 1:
                    //数组格式
                    if (null !== json_decode($var) && is_array(json_decode($var, true))) {
                        return true;
                    }
                    break;
                case 2:
                    //标准格式
                    if (null !== json_decode($var)) {
                        return true;
                    }
                    break;
            }
        }
        return false;
    }
}

if (!function_exists('is_string_large')) {
    /**
     * 大写字母格式校验
     * @param string $var 变量
     * @return mixed
     */
    function is_string_large($var)
    {
        //初始化变量
        if (preg_match('/[A-Z]/', $var)) {
            return true;
        }
        return false;
    }
}

if (!function_exists('is_string_small')) {
    /**
     * 小写字母格式校验
     * @param string $var 变量
     * @return mixed
     */
    function is_string_small($var)
    {
        //初始化变量
        if (preg_match('/[a-z]/', $var)) {
            return true;
        }
        return false;
    }
}

/**
 * 数据-结构-操作-拼接
 * @param string $string 字符串
 * @param string $value 值
 * @param string $separator 分隔符
 * @return string
 */
function dso_splice($string, $value, $separator = '')
{
    //初始化变量
    if (is_null($value) || is_null($separator)) {
        return $string;
    }
    if ($string) {
        $string .= $separator . $value;
    } else {
        $string = $value;
    }
    return $string;
}

/**
 * 数据-结构-操作-过滤空参数 <p>
 * dsc filter parameter empty
 * </p>
 * @param array $param 参数
 * @return mixed
 */
function dso_fpempty($param)
{
    //初始化变量
    $result = fsi_result();
    if (!isset($param)) {
        $result[0] = false;
        $result[1] = 1000;
        $result[2] = fxy_lang(['lack', 'parameter']);
    } else if (is_array($param)) {
        foreach ($param as $key => $value) {
            if (is_null($value)) {
                unset($param[$key]);
            }
        }
        $result[2] = fxy_lang(['request', 'success']);
        $result[3] = $param;
    } else {
        $result[0] = false;
        $result[1] = 1001;
        $result[2] = fxy_lang(['parameter', 'format', 'error']);
    }
    return $result;
}

/**
 * 数据-结构-检查-空参数 <p>
 * dsc parameter empty
 * </p>
 * @param array $param 参数
 * @return mixed
 */
function dsc_pempty($param)
{
    //初始化变量
    $result = fsi_result();
    if (!isset($param)) {
        $result[0] = false;
        $result[1] = 1000;
        $result[2] = fxy_lang(['lack', 'parameter']);
    } else if (is_array($param)) {
        foreach ($param as $key => $value) {
            if (is_null($value) || $value === '') {
                $varname = is_numeric($key) ? 'param' : $key;
                $result[0] = false;
                $result[1] = 1000;
                $result[2] = fxy_lang(['lack', $varname]);
                break;
            }
        }
    } else {
        $result[0] = false;
        $result[1] = 1001;
        $result[2] = fxy_lang(['parameter', 'format', 'error']);
    }
    return $result;
}

/**
 * 数据-结构-检查-非空参数 <p>
 * dsc un parameter empty
 * </p>
 * @param array $param 参数
 * @return mixed
 */
function dsc_unpempty($param)
{
    //初始化变量
    $result = fsi_result();
    if (!isset($param)) {
        $result[0] = false;
        $result[1] = 1000;
        $result[2] = fxy_lang(['lack', 'parameter']);
    } else if (is_array($param)) {
        foreach ($param as $key => $value) {
            if (is_null($value) || $value === '') {
                $result[0] = false;
                $result[1] = 1000;
                $result[2] = fxy_lang(['lack', 'param']);
            } else {
                $result[0] = true;
                $result[1] = 0;
                $result[2] = fxy_lang(['check', 'success']);
                break;
            }
        }
    } else {
        $result[0] = false;
        $result[1] = 1001;
        $result[2] = fxy_lang(['parameter', 'format', 'error']);
    }
    return $result;
}

/**
 * 数据-结构-检查-字符串长度 <p>
 * dsc string length
 * </p>
 * @param string $string 字符串
 * @param int $slen 开始长度-start length
 * @param int $elen 结束长度-end length
 * @return mixed
 */
function dsc_strlen($string = null, $slen = 0, $elen = 0)
{
    //初始化变量
    $result = fsi_result();
    $str_len = mb_strlen($string, 'utf-8');
    $elen == 0 && $elen = $str_len;
    $slen > $elen && $slen = $elen;
    if (is_string($string)) {
        if ($elen > 0 && !($str_len >= $slen && $str_len <= $elen)) {
            $result[0] = false;
            $result[1] = 1004;
            $result[2] = fxy_lang(['string', 'length', 'should', 'be', ($slen == $elen ? $elen : $slen . '-' . $elen)]);
        }
    } else {
        $result[0] = false;
        $result[1] = 1000;
        $result[2] = fxy_lang(['string', 'not2', 'input']);
    }
    return $result;
}

/**
 * 数据-结构-操作-组装
 * @param array $param 参数
 * @param int $mode 模式
 * @return string
 */
function dso_assemble($param, $mode = null)
{
    //初始化变量
    $data = '';
    $string = '';
    $mode = !empty($param) && is_array($param) ? $mode : null;
    if ($mode) {
        $param = dsc_fhtml($param);
    }
    switch ($mode) {
        case 1:
            //$key='$value',$key='$value'
            foreach ($param as $key => $value) {
                $string = dso_splice($string, $key . '=\'' . $value . '\'', ',');
            }
            $data = $string;
            break;
        case 2:
            //$key,$key
            foreach ($param as $key => $value) {
                $string = dso_splice($string, $key, ',');
            }
            $data = $string;
            break;
        case 3:
            //'$value','$value'
            foreach ($param as $key => $value) {
                $string = dso_splice($string, "'" . $value . "'", ',');
            }
            $data = $string;
            break;
        case 4:
            //$value,$value
            foreach ($param as $key => $value) {
                $string = dso_splice($string, $value, ',');
            }
            $data = $string;
            break;
        case 5:
            //$key=$value,$key=$value
            foreach ($param as $key => $value) {
                $string = dso_splice($string, $key . '=' . $value, ',');
            }
            $data = $string;
            break;
    }
    return $data;
}

/**
 * 框架-公共-操作-字符串加解密 <p>
 * fco authcode
 * </p>
 * @param string $string 原始字符串
 * @param string $operation 加解密类型
 * @param string $key 密钥
 * @param int $expiry 有效期
 * @return string
 */
function fco_authcode($string, $operation = 'decode', $key = '', $expiry = 0)
{
    //初始化变量
    $ckey_length = 4;
    $operation = strtolower($operation);
    $key = md5($key != '' ? $key : fxy_config('authkey'));
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'decode' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);
    $string = $operation == 'decode' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if ($operation == 'decode') {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}

/**
 * 框架-公共-操作-首字母组 <p>
 * fco letters
 * </p>
 * @param string $var 变量
 * @param string $in_charset 输入字符集
 * @param string $out_charset 输出字符集
 * @return string
 */
function fco_letters($var, $in_charset = 'utf-8', $out_charset = 'gb2312')
{
    //初始化变量
    $data = [];
    if (!is_string($var)) return false;
    for ($i = 0; $i < mb_strlen($var, 'utf-8'); $i++) {
        $char = mb_substr($var, $i, 1);
        $data[] = fco_letter($char, $in_charset, $out_charset);
    }
    $data = implode('', $data);
    return $data;
}

/**
 * 框架-公共-操作-首字母 <p>
 * fco letter
 * </p>
 * @param string $var 变量
 * @param string $in_charset 输入字符集
 * @param string $out_charset 输出字符集
 * @return string
 */
function fco_letter($var, $in_charset = 'utf-8', $out_charset = 'gb2312')
{
    try {
        //初始化变量
        //如果程序是gbk的，此行就要注释掉
        $data = iconv($in_charset, $out_charset, $var);
        if (preg_match("/^[\x7f-\xff]/", $data)) {
            $fchar = ord($data[0]);
            if ($fchar >= ord("A") and $fchar <= ord("z")) return strtoupper($data[0]);
            $char = ord($data[0]) * 256 + ord($data[1]) - 65536;
            if ($char >= -20319 and $char <= -20284) return "A";
            if ($char >= -20283 and $char <= -19776) return "B";
            if ($char >= -19775 and $char <= -19219) return "C";
            if ($char >= -19218 and $char <= -18711) return "D";
            if ($char >= -18710 and $char <= -18527) return "E";
            if ($char >= -18526 and $char <= -18240) return "F";
            if ($char >= -18239 and $char <= -17923) return "G";
            if ($char >= -17922 and $char <= -17418) return "H";
            if ($char >= -17417 and $char <= -16475) return "J";
            if ($char >= -16474 and $char <= -16213) return "K";
            if ($char >= -16212 and $char <= -15641) return "L";
            if ($char >= -15640 and $char <= -15166) return "M";
            if ($char >= -15165 and $char <= -14923) return "N";
            if ($char >= -14922 and $char <= -14915) return "O";
            if ($char >= -14914 and $char <= -14631) return "P";
            if ($char >= -14630 and $char <= -14150) return "Q";
            if ($char >= -14149 and $char <= -14091) return "R";
            if ($char >= -14090 and $char <= -13319) return "S";
            if ($char >= -13318 and $char <= -12839) return "T";
            if ($char >= -12838 and $char <= -12557) return "W";
            if ($char >= -12556 and $char <= -11848) return "X";
            if ($char >= -11847 and $char <= -11056) return "Y";
            if ($char >= -11055 and $char <= -10247) return "Z";
        }
        return $var;
    } catch (\Throwable $e) {
        return $var;
    }
}
