<?php
// +----------------------------------------------------------------------
// | Name fxyin
// +----------------------------------------------------------------------
// | Author wztqy <wztqy@139.com>
// +----------------------------------------------------------------------
// | Copyright Copyright © 2016-2099 fxri. All rights reserved.
// +----------------------------------------------------------------------
// | Link http://www.fxri.net
// +----------------------------------------------------------------------

/**
 * 框架-终端-客户端-IP
 * @return mixed
 */
function ftc_ip($type = 0)
{
    $type = $type ? 1 : 0;
    static $ip = null;
    if ($ip !== null) return $ip[$type];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos = array_search('unknown', $arr);
        if (false !== $pos) unset($arr[$pos]);
        $ip = trim($arr[0]);
    } else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } else if (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    //IP地址合法验证
    $long = sprintf("%u", fcf_ipv4($ip, 'encode'));
    $ip = $long ? [$ip, $long] : ['0.0.0.0', 0];
    return $ip[$type];
}

/**
 * 框架-终端-客户端-浏览器
 * @param integer $size 选择大小写
 * @return mixed
 */
function ftc_browser($size = -1)
{
    $agent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : null;
    //开始检测
    if (strpos($agent, strtolower("MicroMessenger"))) {
        //微信内置浏览器
        $record = "MicroMessenger";
    } else if (strpos($agent, strtolower("Alipay"))) {
        //支付宝内置浏览器
        $record = "Alipay";
    } else if (strpos($agent, strtolower("MSIE 8.0"))) {
        //IE8.0
        $record = "Internet Explorer 8.0";
    } else if (strpos($agent, strtolower("MSIE 7.0"))) {
        //IE7.0
        $record = "Internet Explorer 7.0";
    } else if (strpos($agent, strtolower("MSIE 6.0"))) {
        //IE6.0
        $record = "Internet Explorer 6.0";
    } else if (strpos($agent, strtolower("Firefox/3"))) {
        //火狐浏览器
        $record = "Firefox 3";
    } else if (strpos($agent, strtolower("Firefox/2"))) {
        //火狐浏览器
        $record = "Firefox 2";
    } else if (strpos($agent, strtolower("Chrome"))) {
        //谷歌浏览器
        $record = "Google Chrome";
    } else if (strpos($agent, strtolower("Safari"))) {
        //游猎浏览器
        $record = "Safari";
    } else if (strpos($agent, strtolower("Opera"))) {
        //欧朋浏览器
        $record = "Opera";
    } else {
        //其他
        $record = "Others";
    }
    switch ($size) {
        case 1:
            $result = strtolower($record);
            break;
        case 2:
            $result = strtoupper($record);
            break;
        default:
            $result = $record;
            break;
    }
    return $result;
}

/**
 * 框架-终端-客户端-系统
 * @param integer $size 选择大小写
 * @return mixed
 */
function ftc_system($size = -1)
{
    $agent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : null;
    //开始检测
    if (preg_match("/win/i", $agent) && preg_match("/95/i", $agent)) {
        $record = 'Windows 95';
    } else if (preg_match("/win 9x/i", $agent) && preg_match("/4.90/i", $agent)) {
        $record = 'Windows ME';
    } else if (preg_match("/win/i", $agent) && preg_match("/98/i", $agent)) {
        $record = 'Windows 98';
    } else if (preg_match("/win/i", $agent) && preg_match("/nt 5.1/i", $agent)) {
        $record = 'Windows XP';
    } else if (preg_match("/win/i", $agent) && preg_match("/nt 5.2/i", $agent)) {
        $record = 'Windows 2003';
    } else if (preg_match("/win/i", $agent) && preg_match("/nt 5/i", $agent)) {
        $record = 'Windows 2000';
    } else if (preg_match("/win/i", $agent) && preg_match("/nt/i", $agent)) {
        $record = 'Windows NT';
    } else if (preg_match("/win/i", $agent) && preg_match("/32/i", $agent)) {
        $record = 'Windows 32';
    } else if (preg_match("/linux/i", $agent)) {
        $record = 'Linux';
    } else if (preg_match("/unix/i", $agent)) {
        $record = 'Unix';
    } else if (preg_match("/sun/i", $agent) && preg_match("/os/i", $agent)) {
        $record = 'SunOS';
    } else if (preg_match("/ibm/i", $agent) && preg_match("/os/i", $agent)) {
        $record = 'IBM OS/2';
    } else if (preg_match("/Mac/i", $agent) && preg_match("/PC/i", $agent)) {
        $record = 'Macintosh';
    } else if (preg_match("/PowerPC/i", $agent)) {
        $record = 'PowerPC';
    } else if (preg_match("/AIX/i", $agent)) {
        $record = 'AIX';
    } else if (preg_match("/HPUX/i", $agent)) {
        $record = 'HPUX';
    } else if (preg_match("/NetBSD/i", $agent)) {
        $record = 'NetBSD';
    } else if (preg_match("/BSD/i", $agent)) {
        $record = 'BSD';
    } else if (preg_match("/OSF1/i", $agent)) {
        $record = 'OSF1';
    } else if (preg_match("/IRIX/i", $agent)) {
        $record = 'IRIX';
    } else if (preg_match("/FreeBSD/i", $agent)) {
        $record = 'FreeBSD';
    } else if (preg_match("/teleport/i", $agent)) {
        $record = 'teleport';
    } else if (preg_match("/flashget/i", $agent)) {
        $record = 'flashget';
    } else if (preg_match("/webzip/i", $agent)) {
        $record = 'webzip';
    } else if (preg_match("/offline/i", $agent)) {
        $record = 'offline';
    } else {
        $record = 'Others';
    }
    switch ($size) {
        case 1:
            $result = strtolower($record);
            break;
        case 2:
            $result = strtoupper($record);
            break;
        default:
            $result = $record;
            break;
    }
    return $result;
}

/**
 * 框架-终端-客户端-请求方案
 * @param integer $size 选择大小写
 * @return mixed
 */
function ftc_scheme($size = -1)
{
    $agent = isset($_SERVER['REQUEST_SCHEME']) ? strtolower($_SERVER['REQUEST_SCHEME']) : null;
    //开始检测
    $record = $agent;
    switch ($size) {
        case 1:
            $result = strtolower($record);
            break;
        case 2:
            $result = strtoupper($record);
            break;
        default:
            $result = $record;
            break;
    }
    return $result;
}

/**
 * 框架-终端-客户端-请求方法
 * @param integer $size 选择大小写
 * @return mixed
 */
function ftc_method($size = -1)
{
    $agent = isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : null;
    //开始检测
    $record = $agent;
    switch ($size) {
        case 1:
            $result = strtolower($record);
            break;
        case 2:
            $result = strtoupper($record);
            break;
        default:
            $result = $record;
            break;
    }
    return $result;
}

/**
 * 框架-终端-客户端-请求参数 <p>
 * fsc parameter
 * </p>
 * @return mixed
 */
function ftc_param($tran = null)
{
    $paramter = array_merge($_GET, $_POST);
    $data = null;
    if (empty($tran)) {
        //获取全部参数
        $data = $paramter;
    } else if (is_array($tran)) {
        //获取指定参数数组
        foreach ($tran as $key => $value) {
            array_key_exists($value, $paramter) && $data[$value] = $paramter[$value];
        }
    } else if (is_string($tran)) {
        //获取指定参数
        isset($paramter[$tran]) && $data = $paramter[$tran];
    }
    return $data;
}
