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
 * @method string where(array $var, string $key, string $value, string $comparison, string $logic, int $rank) SQL-条件
 * @method string whereSafe(string $var, string &$comparison) SQL-条件安检
 * @method string whereBuild(string $key, string $value, string $comparison) SQL-条件搭建
 * @method string whereMake(array $var, int $type = 1) SQL-条件组装
 * @method bool|string update(string $table, array $data, string $field = 'id', array $param = []) SQL-更新
 * @method string updateCase(array $data, string $field) SQL-更新条件-将二维数组转换成CASE WHEN THEN的批量更新条件
 * @method string updateParam(array $param) SQL-查询解析where条件
 * @method mixed updateContrast(array $data_new, array $data_old) 处理参数-更新比较
 * @method string fieldDistance(string $lngs, string $lats, string $lnge, string $late) 处理字段-计算经纬度距离
 * @method string fieldInitial(string $field) 处理字段-首字母
 * @method string fieldJson(string $field, string $param, int $mode = null) 处理字段-Json
 * @method string fieldText(string $field, string $replace = '', int $mode = 1) 处理字段-文本
 * @method string fieldDate(string $field, string $replace = '', int $type = 1) 处理字段-日期
 * @method mixed htmlFilter(string $string, string $flags = null) 处理HTML-过滤
 * @method mixed htmlRemove(string $string, string $flags = null) 处理HTML-移除
 * @method mixed listToTree(array $list, int $parent_id = 0, string $parnet_name = 'parent_id', string $index = 'id', string $child_name = '_child') 列表格式树
 * @method mixed paramFilter(array $param) 处理参数-过滤
 * @method mixed paramEmpty(array $param) 检查参数-空参
 * @method mixed paramExist(array $param) 检查参数-非空
 */
class Data extends \fxyin\Facade
{
}
