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

use fxyin\Service;

/**
 * 框架-服务-发送-HTTP请求
 * @param string $url 网址
 * @param array $data 数据
 * @param array $header 请求头
 * @param string $method 请求方式
 * @return mixed
 */
function fss_http($url, $data = '', $header = [], $method = null)
{
    //初始化变量
    if (is_null($url)) {
        return false;
    }
    if (is_null($method)) {
        $method = 'GET';
    }
    $method = strtoupper($method);
    //初始化请求
    $request = curl_init();
    //配置请求参数
    curl_setopt($request, CURLOPT_URL, $url);
    curl_setopt($request, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($request, CURLOPT_HTTPHEADER, $header);
    curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);
    //HTTPS请求，不验证证书和主机名
    curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($request, CURLOPT_SSL_VERIFYHOST, false);
    //识别请求方法
    switch ($method) {
        default:
            //未知
        case 'GET':
            //GET
            curl_setopt($request, CURLOPT_POST, false);
            break;
        case 'POST':
            //POST
            curl_setopt($request, CURLOPT_POST, true);
            curl_setopt($request, CURLOPT_POSTFIELDS, $data);
            break;
    }
    //发送请求
    $response = curl_exec($request);
    curl_close($request);
    return $response;
}

/**
 * 框架-服务-发送-HTTP请求-下载
 * @param string $url 文件远程路径
 * @param string $file 文件本地路径
 * @return mixed
 */
function fss_http_down($url, $file)
{
    if (!is_dir(dirname($file))) {
        \fxyin\Dir::create(dirname($file));
    }
    //请求文件
    $response = fss_http($url, '', [], 'get');
    //保存文件
    $downloaded_file = fopen($file, 'w');
    fwrite($downloaded_file, $response);
    fclose($downloaded_file);
    if (dsc_pempty([$response])[0]) {
        return $file;
    } else {
        return false;
    }
}

/**
 * 框架-公共-服务-通知
 * @param array $tran 请求参数
 * @param string $supplier 服务供应商
 * @return mixed
 */
function fcs_notify($tran = '', $supplier = '')
{
    return Service::create($tran, 'notify', $supplier);
}

/**
 * 框架-公共-服务-第三方
 * @param array $tran 请求参数
 * @param string $supplier 服务供应商
 * @return mixed
 */
function fcs_third($tran = '', $supplier = '')
{
    return Service::create($tran, 'third', $supplier);
}
