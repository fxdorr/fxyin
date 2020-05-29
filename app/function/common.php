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

/**
 * 电子邮箱格式校验
 * @param string $data 数据
 * @return boolean
 */
function is_email($data)
{
    // 初始化变量
    if (!is_string($data) || empty($data)) return false;
    return preg_match('/^[a-z0-9]+([\+_\-\.]?[a-z0-9]+)*@([a-z0-9]+[\-]?[a-z0-9]+\.)+[a-z]{2,6}$/i', $data);
}

/**
 * 手机号码格式校验
 * @param string $data 数据
 * @return boolean
 */
function is_mobile($data)
{
    // 初始化变量
    if (!is_string($data) || empty($data)) return false;
    return preg_match('/^1\d{10}$/', $data);
}

/**
 * 邮政编码格式校验
 * @param string $data 数据
 * @return boolean
 */
function is_zipcode($data)
{
    // 初始化变量
    if (!is_string($data) || empty($data)) return false;
    return preg_match('/^[1-9][0-9]{5}$/', $data);
}

/**
 * Json格式校验
 * @param string $data 数据
 * @return mixed
 */
function is_json($data)
{
    // 初始化变量
    if (!is_string($data)) return false;
    $data = json_decode($data, true);
    if (null !== $data && is_array($data)) return true;
    return false;
}

/**
 * 语言
 * @param array|string $name 语言变量名
 * @param array $vars 动态变量值
 * @param string $lang 语言
 * @return string
 */
function fxy_lang($name, $vars = [], $lang = '')
{
    return \fxapp\Base::lang($name, $vars, $lang);
}

/**
 * 配置参数-[获取|设置]
 * @param array $vars 参数
 * @return mixed
 */
function fxy_config(...$vars)
{
    return \fxapp\Base::config(...$vars);
}

/**
 * 环境参数-[获取|设置]
 * @param array $vars 参数
 * @return mixed
 */
function fxy_env(...$vars)
{
    return \fxapp\Base::env(...$vars);
}

/**
 * 浏览器友好的变量输出
 * @param array $vars 要输出的变量
 * @return void
 */
function fxy_dump(...$vars)
{
    \fxapp\Base::dump(...$vars);
}

/**
 * 解析Json
 * @param mixed $var 变量
 * @param string $type 类型
 * @return mixed
 */
function fxy_json($var, $type)
{
    return \fxapp\Base::json($var, $type);
}
