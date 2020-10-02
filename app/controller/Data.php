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
 * 数据类
 * @see \fxapp\facade\Data
 * @package fxapp\facade
 * @method static array where(array $var, array $param) SQL-条件
 * @method static string whereSafe(string $var, string &$comparison) SQL-条件安检
 * @method static string whereBuild(string $key, string $value, string $comparison) SQL-条件搭建
 * @method static string whereMake(array $var, int $type = 1) SQL-条件组装
 * @method static bool|string insert(string $table, array $data) SQL-插入
 * @method static bool|string update(string $table, array $data, array $param = []) SQL-更新
 * @method static string updateCase(array $data, string $field) SQL-更新条件-将二维数组转换成CASE WHEN THEN的批量更新条件
 * @method static string updateParam(array $param) SQL-查询解析where条件
 * @method static array updateContrast(array $data_new, array $data_old) 处理参数-更新比较
 * @method static string fieldDistance(string $lngs, string $lats, string $lnge, string $late) 处理字段-计算经纬度距离
 * @method static string fieldInitial(string $field) 处理字段-首字母
 * @method static string fieldJson(string $field, string $param, int $mode = null) 处理字段-Json
 * @method static string fieldText(string $field, string $replace = '', int $mode = 1) 处理字段-文本
 * @method static string fieldDate(string $field, string $replace = '', int $type = 1) 处理字段-日期
 * @method static string htmlFilter(string $string, string $flags = null) 处理HTML-过滤
 * @method static string htmlRemove(string $string, string $flags = null) 处理HTML-移除
 * @method static array listToTree(array $list, int $parent_id = 0, string $parnet_name = 'parent_id', string $index = 'id', string $child_name = '_child') 列表格式树
 * @method static array paramRepair(array $param) 处理参数-补全
 * @method static array paramFilter(array $param) 处理参数-过滤
 * @method static array paramEmpty(array $param) 检查参数-空参
 * @method static array paramExist(array $param) 检查参数-非空
 */
class Data extends \fxyin\Facade
{
}
