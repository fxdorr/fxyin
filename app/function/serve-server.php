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
 * @param int $type 类型
 * @return mixed
 */
function fts_system($type = -1)
{
    $echo = php_uname();
    // 开始检测
    if (preg_match("/win/i", $echo) && preg_match("/95/i", $echo)) {
        $echo = 'Windows 95';
    } else if (preg_match("/win 9x/i", $echo) && preg_match("/4.90/i", $echo)) {
        $echo = 'Windows ME';
    } else if (preg_match("/win/i", $echo) && preg_match("/98/i", $echo)) {
        $echo = 'Windows 98';
    } else if (preg_match("/win/i", $echo) && preg_match("/nt 5.1/i", $echo)) {
        $echo = 'Windows XP';
    } else if (preg_match("/win/i", $echo) && preg_match("/nt 5.2/i", $echo)) {
        $echo = 'Windows 2003';
    } else if (preg_match("/win/i", $echo) && preg_match("/nt 5/i", $echo)) {
        $echo = 'Windows 2000';
    } else if (preg_match("/win/i", $echo) && preg_match("/nt/i", $echo)) {
        $echo = 'Windows NT';
    } else if (preg_match("/win/i", $echo) && preg_match("/32/i", $echo)) {
        $echo = 'Windows 32';
    } else if (preg_match("/linux/i", $echo)) {
        $echo = 'Linux';
    } else if (preg_match("/unix/i", $echo)) {
        $echo = 'Unix';
    } else if (preg_match("/sun/i", $echo) && preg_match("/os/i", $echo)) {
        $echo = 'SunOS';
    } else if (preg_match("/ibm/i", $echo) && preg_match("/os/i", $echo)) {
        $echo = 'IBM OS/2';
    } else if (preg_match("/Mac/i", $echo) && preg_match("/PC/i", $echo)) {
        $echo = 'Macintosh';
    } else if (preg_match("/PowerPC/i", $echo)) {
        $echo = 'PowerPC';
    } else if (preg_match("/AIX/i", $echo)) {
        $echo = 'AIX';
    } else if (preg_match("/HPUX/i", $echo)) {
        $echo = 'HPUX';
    } else if (preg_match("/NetBSD/i", $echo)) {
        $echo = 'NetBSD';
    } else if (preg_match("/BSD/i", $echo)) {
        $echo = 'BSD';
    } else if (preg_match("/OSF1/i", $echo)) {
        $echo = 'OSF1';
    } else if (preg_match("/IRIX/i", $echo)) {
        $echo = 'IRIX';
    } else if (preg_match("/FreeBSD/i", $echo)) {
        $echo = 'FreeBSD';
    } else if (preg_match("/teleport/i", $echo)) {
        $echo = 'teleport';
    } else if (preg_match("/flashget/i", $echo)) {
        $echo = 'flashget';
    } else if (preg_match("/webzip/i", $echo)) {
        $echo = 'webzip';
    } else if (preg_match("/offline/i", $echo)) {
        $echo = 'offline';
    } else {
        $echo = 'Others';
    }
    switch ($type) {
        case 1:
            // 格式化-小写
            $echo = strtolower($echo);
            break;
        case 2:
            // 格式化-大写
            $echo = strtoupper($echo);
            break;
    }
    return $echo;
}
