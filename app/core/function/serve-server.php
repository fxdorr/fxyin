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
 * 框架-终端-服务器-IP
 * @return mixed
 */
function fts_ip()
{
    $ip = fss_http("http://httpbin.org/ip", '', [], 'get');
    if (!is_json($ip)) return;
    $ip = json_decode($ip, true);
    $ip['origin'] = explode(',', $ip['origin'])[0];
    return $ip['origin'];
}

/**
 * 框架-终端-服务器-系统
 * @param int $size 选择大小写
 * @return mixed
 */
function fts_system($size = -1)
{
    $agent = php_uname();
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
