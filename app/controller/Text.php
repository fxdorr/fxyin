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
 * 文本类
 * @see \fxapp\facade\Text
 * @package fxapp\facade
 * @method array uuid(string $mode = null, array $param = []) 生成UUID
 * @method mixed explode(string $separator, string $string) 打散字符串
 * @method string splice(string $string, string $value, string $separator = '') 拼接字符串
 * @method mixed strlen(string $string = null, int $start = 0, int $end = 0) 检查字符串长度
 * @method string letters(string $var, string $in_charset = 'utf-8', string $out_charset = 'gb2312') 首字母组
 * @method string letter(string $var, string $in_charset = 'utf-8', string $out_charset = 'gb2312') 首字母
 * @method string timeChange(int|string $time, string $type = null) 处理时间-转换
 * @method string timeMilli(string $mtime = null) 处理时间-毫米
 * @method string timeFormat(string $time = null, string $type = null) 处理时间-格式化
 * @method mixed ipv4(mixed $var, string $type) 解析Ipv4
 * @method mixed convert(mixed $var, string $type) 进制转换
 * @method string throwable(\Throwable $th) 提取抛出
 */
class Text extends \fxyin\Facade
{
}
