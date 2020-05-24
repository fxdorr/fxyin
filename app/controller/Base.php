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
 * 基础类
 * @see \fxapp\facade\Base
 * @package fxapp\facade
 * @method boolean load(string $file) 加载-[文件]
 * @method string lang(array|string $name, array $vars = [], string $lang = '') 语言
 * @method string langList(array|string $name, array $vars = [], string $lang = '') 语言-列表
 * @method string langParse(array|string $name) 语言-解析
 * @method mixed config(array|string $name = '', mixed $data = null, string $range = '') 配置参数-[获取|设置]
 * @method mixed env(array|string $name = '', mixed $data = null) 环境参数-[获取|设置]
 * @method void dump(array ...$vars) 浏览器友好的变量输出
 * @method mixed json(mixed $var, string $type) 解析Json
 * @method mixed crypt(mixed $var, string $type, string $param = null) 加密解密
 */
class Base extends \fxyin\Facade
{
}
