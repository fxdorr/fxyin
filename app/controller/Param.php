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
 * 参数类
 * @see \fxapp\facade\Param
 * @package fxapp\facade
 * @method mixed init() 初始化配置
 * @method array define(array $param, int $mode = null) 定义参数
 * @method mixed object(array $param) 空数组转对象
 * @method array pick(array $base, array $data) 提取数组
 * @method mixed append(array ...$args) 追加数组
 * @method mixed merge(array ...$args) 合并数组
 * @method array cover(array $args) 覆盖数组
 * @method mixed json(mixed $var, string $type, string $param = null) 解析Json
 */
class Param extends \fxyin\Facade
{
}
