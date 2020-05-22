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
 * 电子邮箱格式校验
 * @param string $var 变量
 * @return boolean
 */
function is_email($var)
{
    // 初始化变量
    if (is_string($var)) {
        if (!empty($var)) {
            return preg_match('/^[a-z0-9]+([\+_\-\.]?[a-z0-9]+)*@([a-z0-9]+[\-]?[a-z0-9]+\.)+[a-z]{2,6}$/i', $var);
        }
    }
    return false;
}

/**
 * 手机号码格式校验
 * @param string $var 变量
 * @return boolean
 */
function is_mobile($var)
{
    // 初始化变量
    if (is_string($var)) {
        if (!empty($var)) {
            return preg_match('/^1[0-9]{2}\d{8}$/', $var);
        }
    }
    return false;
}

/**
 * 邮政编码格式校验
 * @param string $var 变量
 * @return boolean
 */
function is_zipcode($var)
{
    // 初始化变量
    if (is_string($var)) {
        if (!empty($var)) {
            return preg_match('/^[1-9][0-9]{5}$/', $var);
        }
    }
    return false;
}

/**
 * Json格式校验
 * @param string $var 变量
 * @param string $format 格式
 * @return mixed
 */
function is_json($var, $format = null)
{
    // 初始化变量
    if (is_string($var)) {
        switch ($format) {
            default:
            case 1:
                // 数组格式
                if (null !== json_decode($var) && is_array(json_decode($var, true))) {
                    return true;
                }
                break;
            case 2:
                // 标准格式
                if (null !== json_decode($var)) {
                    return true;
                }
                break;
        }
    }
    return false;
}
