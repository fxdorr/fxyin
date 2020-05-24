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
 * 安全类
 * @see \fxapp\facade\Safe
 * @package fxapp\facade
 * @method mixed crypt(mixed $var, string $type, string $param = null) 解析数据-加密
 * @method mixed md5(mixed $var, int $type = null) 生成MD5
 * @method mixed token(mixed $var, string $type) 解析数据-令牌
 * @method mixed rsapri(mixed $var, string $type, array $param = []) 解析数据-RSA私钥
 * @method mixed rsapub(mixed $var, string $type, array $param = []) 解析数据-RSA公钥
 * @method mixed rsapem(mixed $var, string $type) 解析数据-RSA密钥
 */
class Safe extends \fxyin\Facade
{
}
