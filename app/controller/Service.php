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
namespace fxapp;

/**
 * 服务类
 * @see \fxapp\facade\Service
 * @package fxapp\facade
 * @method static mixed http(string $url, array $data = '', array $header = [], string $method = null) HTTP请求
 * @method static mixed httpDown(string $url, string $file) HTTP请求-下载
 * @method static \fxyin\service\Notify notify(array $param = '', string $supplier = '') 配置服务-通知
 * @method static \fxyin\service\Third third(array $param = '', string $supplier = '') 配置服务-第三方
 */
class Service extends \fxyin\Facade
{
}
