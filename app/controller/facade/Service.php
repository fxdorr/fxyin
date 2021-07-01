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
 * 服务类
 */
class Service
{
    /**
     * HTTP请求
     * @param string $url 网址
     * @param string $data 数据
     * @param array $header 请求头
     * @param string $method 请求方式
     * @return mixed
     */
    public function http($url, $data = '', $header = [], $method = null)
    {
        // 初始化变量
        if (is_null($url)) {
            return false;
        }
        if (is_null($method)) {
            $method = 'GET';
        }
        $method = strtoupper($method);
        // 初始化请求
        $request = curl_init();
        // 配置请求参数
        curl_setopt($request, CURLOPT_URL, $url);
        curl_setopt($request, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_HTTPHEADER, $header);
        curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);
        // HTTPS请求，不验证证书和主机名
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($request, CURLOPT_SSL_VERIFYHOST, false);
        // 识别请求方法
        switch ($method) {
            default:
            case 'POST':
            case 'PUT':
            case 'PATCH':
            case 'DELETE':
                // 默认
                curl_setopt($request, CURLOPT_POST, true);
                curl_setopt($request, CURLOPT_POSTFIELDS, $data);
                break;
            case 'GET':
                // GET
                curl_setopt($request, CURLOPT_POST, false);
                break;
        }
        // 发送请求
        $response = curl_exec($request);
        // 清除BOM头
        $response = ltrim($response, "\XEF\XBB\XBF");
        curl_close($request);
        return $response;
    }

    /**
     * HTTP请求-下载
     * @param string $url 文件远程路径
     * @param string $file 文件本地路径
     * @return mixed
     */
    public function httpDown($url, $file)
    {
        if (!is_dir(dirname($file))) {
            \fxyin\Dir::create(dirname($file));
        }
        // 请求文件
        $response = $this->http($url, '', [], 'get');
        // 保存文件
        $downloaded_file = fopen($file, 'w');
        fwrite($downloaded_file, $response);
        fclose($downloaded_file);
        if (\fxapp\Data::paramEmpty([$response], 1)[0]) {
            return $file;
        } else {
            return false;
        }
    }

    /**
     * 配置服务-通知
     * @param array $param 请求参数
     * @param string $supplier 服务供应商
     * @return \fxyin\service\Notify
     */
    public function notify($param = '', $supplier = '')
    {
        return \fxyin\Service::create($param, 'notify', $supplier);
    }

    /**
     * 配置服务-第三方
     * @param array $param 请求参数
     * @param string $supplier 服务供应商
     * @return \fxyin\service\Third
     */
    public function third($param = '', $supplier = '')
    {
        return \fxyin\Service::create($param, 'third', $supplier);
    }
}
