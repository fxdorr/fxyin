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
 * 数据-查询-组装-Where
 * @param array $var 变量
 * @param string $key 键
 * @param string $value 值
 * @param string $comparison 表达式
 * @param string $logic 逻辑
 * @param int $rank 排序
 * @return string
 */
function dqa_where($var, $key, $value, $comparison, $logic, $rank)
{
    if (!is_array($var) || is_null($key) || is_null($value) || is_null($comparison) || is_null($logic)) {
        return $var;
    }
    //过滤非法字符
    $safe = dqa_where_safe($value, $comparison);
    //搭建函数组合
    $build = dqa_where_build($key, $safe, $comparison);
    $data = [
        'build' => $build,
        'logic' => $logic,
        'rank' => $rank,
    ];
    $var[] = $data;
    return $var;
}

/**
 * 数据-查询-组装-Where-Safe
 * @param string $var 变量
 * @param string $comparison 表达式
 * @return string
 */
function dqa_where_safe($var, &$comparison)
{
    if (is_null($var)) {
        return $var;
    }
    //拆解表达式
    switch ($comparison) {
        default:
            if (!is_array($var)) {
                $var = [$var];
            }
            break;
        case 'in':
        case 'not in':
            if (!is_array($var)) {
                $var = explode(',', $var);
            }
            break;
        case 'like':
        case 'not like':
            if (!is_array($var)) {
                $var = [$var];
            }
            break;
        case 'like fuzzy':
        case 'not like fuzzy':
            if (!is_array($var)) {
                $var = [$var];
            }
            break;
        case 'between':
        case 'not between':
            if (is_string($var) && mb_strpos($var, ' and ', null, 'utf-8') !== false) {
                $var = explode(' and ', $var);
            } else if (!is_array($var)) {
                $var = explode(',', $var);
            }
            break;
        case 'find_in_set':
            if (!is_array($var)) {
                $var = explode(',', $var);
            }
            break;
    }
    //过滤表达式
    $search = ["\\", "\'", "&", "\"", "<", ">"];
    $replace = ["\\\\", "\\\'", "&amp;", "&quot;", "&lt;", "&gt;"];
    foreach ($var as $key => $value) {
        //包装表达式
        switch ($comparison) {
            case 'like':
            case 'not like':
                $search = array_merge($search, ['/', '_']);
                $replace = array_merge($replace, ['//', '/_']);
                break;
            case 'like fuzzy':
            case 'not like fuzzy':
                $search = array_merge($search, ['/', '%', '_']);
                $replace = array_merge($replace, ['//', '/%', '/_']);
                break;
        }
        foreach ($search as $key2 => $value2) {
            $var[$key] = str_replace($value2, $replace[$key2], $var[$key]);
        }
        //包装表达式
        switch ($comparison) {
            default:
                $var[$key] = '\'' . $var[$key] . '\'';
                break;
            case 'like fuzzy':
            case 'not like fuzzy':
                $var[$key] = '\'%' . $var[$key] . '%\'';
                break;
            case 'find_in_set':
                $var[$key] = $var[$key];
                break;
        }
    }
    //组装表达式
    switch ($comparison) {
        default:
            $var = implode('', $var);
            break;
        case 'in':
        case 'not in':
            if (!count($var)) {
                $var[] = "''";
            }
            $var = implode(',', $var);
            break;
        case 'like':
        case 'not like':
            $var = implode('', $var) . " escape '/'";
            break;
        case 'like fuzzy':
        case 'not like fuzzy':
            $var = implode('', $var) . " escape '/'";
            break;
        case 'between':
        case 'not between':
            $var = implode(' and ', $var);
            break;
        case 'find_in_set':
            if (!count($var)) {
                $var[] = "''";
            }
            $var = implode(',', $var);
            break;
    }
    //解析表达式
    switch ($comparison) {
        case 'like fuzzy':
            $comparison = 'like';
            break;
        case 'not like fuzzy':
            $comparison = 'not like';
            break;
    }
    return $var;
}

/**
 * 数据-查询-组装-Where-Build
 * @param string $key 键
 * @param string $value 值
 * @param string $comparison 表达式
 * @return string
 */
function dqa_where_build($key, $value, $comparison)
{
    //初始化变量
    $data = '';
    //组装函数
    switch ($comparison) {
        default:
            $data = "{$key} {$comparison} {$value}";
            break;
        case 'in':
        case 'not in':
            $data = "{$key} {$comparison} ({$value})";
            break;
        case 'find_in_set':
            $value = explode(',', $value);
            foreach ($value as $key2 => $value2) {
                if ($data) {
                    $data .= " or {$comparison}({$value2},{$key})";
                } else {
                    $data = "{$comparison}({$value2},{$key})";
                }
            }
            $data = "({$data})";
            break;
    }
    return $data;
}

/**
 * 数据-查询-组装-Where-Make
 * @param array $var 变量
 * @return string
 */
function dqa_where_make($var)
{
    $data = [];
    if (!is_array($var)) {
        return $var;
    }
    //搭建查询组合
    foreach ($var as $key => $value) {
        if (isset($data[$value['rank']])) {
            $data[$value['rank']] .= " {$value['logic']} {$value['build']}";
        } else {
            $data[$value['rank']] = $value['build'];
        }
    }
    foreach ($data as $key => $value) {
        if ($key == 1) continue;
        $value = "({$value})";
        $data[$key] = $value;
    }
    $data = implode(' and ', $data);
    return $data;
}

/**
 * 数据-查询-组装-Update
 * @param string $table 表名
 * @param array $data 数据
 * @param string $field string 字段
 * @param array $param array 参数
 * @return bool|string
 */
function dqa_update($table, $data, $field = 'id', $param = [])
{
    if (!is_string($table) || !is_array($data) || !is_string($field) || !is_array($param)) {
        return false;
    }
    $case = dqa_update_case($data, $field);
    $where = dqa_update_param($param);
    // 获取所有键名为$field列的值，值两边加上单引号，保存在$fields数组中
    // array_column()函数需要PHP5.5.0+，如果小于这个版本，可以自己实现，
    // 参考地址：http://php.net/manual/zh/function.array-column.php#118831
    $fields = array_column($data, $field);
    $fields = implode(',', array_map(function ($value) {
        return "'" . $value . "'";
    }, $fields));
    $sql = sprintf("UPDATE `%s` SET %s WHERE `%s` IN (%s) %s", $table, $case, $field, $fields, $where);
    return $sql;
}

/**
 * 数据-查询-组装-将二维数组转换成CASE WHEN THEN的批量更新条件
 * @param $data array 二维数组
 * @param $field string 列名
 * @return string sql语句
 */
function dqa_update_case($data, $field)
{
    $sql = '';
    $keys = array_keys(current($data));
    foreach ($keys as $column) {
        $sql .= sprintf("`%s` = CASE `%s` \n", $column, $field);
        foreach ($data as $line) {
            //过滤表达式
            $search = ["\\", "\'", "\""];
            $replace = ["\\\\", "\\\'", "\\\""];
            foreach ($search as $key2 => $value2) {
                $line[$column] = str_replace($value2, $replace[$key2], $line[$column]);
            }
            $sql .= sprintf("WHEN '%s' THEN '%s' \n", $line[$field], $line[$column]);
        }
        $sql .= "END,";
    }
    return rtrim($sql, ',');
}

/**
 * 数据-查询-组装-解析where条件
 * @param $param
 * @return array|string
 */
function dqa_update_param($param)
{
    $where = [];
    foreach ($param as $key => $value) {
        $where[] = sprintf("`%s` = '%s'", $key, $value);
    }
    return $where ? ' AND ' . implode(' AND ', $where) : '';
}

/**
 * 数据-查询-组装-距离
 * @param string $lngs 经度起点-longitude start
 * @param string $lats 维度起点-latitude start
 * @param string $lnge 经度终点-longitude end
 * @param string $late 维度终点-latitude end
 * @return string
 */
function dqa_distance($lngs, $lats, $lnge, $late)
{
    if (is_null($lngs) || is_null($lats) || is_null($lnge) || is_null($late)) {
        return false;
    }
    $form = "ACOS(
                SIN(($lats * 3.1415) / 180 ) *
                SIN(($late * 3.1415) / 180 ) +
                COS(($lats * 3.1415) / 180 ) *
                COS(($late * 3.1415) / 180 ) *
                COS(($lngs * 3.1415) / 180 - ($lnge * 3.1415) / 180 ) 
            ) * 6378.137";
    $result = str_replace(array(" ", "　", "\t", "\n", "\r"), '', $form);
    return $result;
}

/**
 * 数据-查询-组装-首字母 <p>
 * dqa english letter first
 * </p>
 * @param string $field 字段名
 * @return string
 */
function dqa_elfirst($field)
{
    if (is_null($field)) {
        return false;
    }
    $form = "ELT(
                INTERVAL(CONV(HEX(left(CONVERT({$field} USING gbk),'1')),'16','10'),
                0,
                0xB0A1,0xB0C5,0xB2C1,0xB4EE,0xB6EA,0xB7A2,0xB8C1,0xB9FE,0xBBF7,
                0xBFA6,0xC0AC,0xC2E8,0xC4C3,0xC5B6,0xC5BE,0xC6DA,0xC8BB,0xC8F6,
                0xCBFA,0xCDDA,0xCEF4,0xD1B9,0xD4D1),
                UPPER(left({$field},'1')),
                'A','B','C','D','E','F','G','H','J','K','L','M','N','O','P',
                'Q','R','S','T','W','X','Y','Z'
            )";
    $result = str_replace(array("  ", "　", "\t", "\n", "\r"), '', $form);
    return $result;
}

/**
 * 数据-查询-组装-日期时间
 * @param string $time 时间
 * @param int $type 类型
 * @return string
 */
function dqa_datetime($time = null, $type = -1)
{
    $time = dsc_pempty([$time])[0] ? $time : time();
    if (is_string($time)) {
        $time = strtotime($time);
    }
    switch ($type) {
        case 1:
            $time = strtotime(date('Y-m-d 00:00:00', $time)) . " and " . strtotime(date('Y-m-d 23:59:59', $time));
            break;
        case 2:
            $time = strtotime(date('Y-m-d 00:00:00', $time));
            break;
        case 3:
            $time = strtotime(date('Y-m-d 23:59:59', $time));
            break;
        case 4:
            $time = strtotime(date('Y-m-d H:i:s', $time));
            break;
        case 5:
            $time = date('Y-m-d 00:00:00', $time);
            break;
        case 6:
            $time = date('Y-m-d 23:59:59', $time);
            break;
        case 7:
            $time = date('Y-m-d H:i:s', $time);
            break;
    }
    return $time;
}

/**
 * 数据-查询-组装-Json字段 <p>
 * dqa field json
 * </p>
 * @param string $field 字段名
 * @param string $param 参数
 * @param int $mode 模式
 * @return string
 */
function dqa_fjson($field, $param, $mode = null)
{
    switch ($mode) {
        default:
        case 1:
            $result = "if(json_valid({$field}), trim(both '\"' from {$field}->'{$param}'), {$field})";
            break;
        case 2:
            $result = "if(json_valid({$field}), {$field}, '{}')";
            break;
    }
    return $result;
}

/**
 * 数据-查询-组装-空字符串字段 <p>
 * dqa field string empty
 * </p>
 * @param string $field 字段名
 * @param string $replace 替换值
 * @param int $mode 模式
 * @return string
 */
function dqa_fsempty($field, $replace = '', $mode = null)
{
    if (!dsc_pempty([$field])[0]) {
        $field = '\'' . $field . '\'';
    }
    switch ($mode) {
        default:
        case 1:
            if (is_numeric($replace)) {
                $result = 'if(length(' . $field . ')=0 or isnull(' . $field . '),' . $replace . ',' . $field . ')';
            } else {
                $result = 'if(length(' . $field . ')=0 or isnull(' . $field . '),\'' . $replace . '\',' . $field . ')';
            }
            break;
        case 2:
            $result = 'if(length(' . $field . ')=0 or isnull(' . $field . '),' . $replace . ',' . $field . ')';
            break;
    }
    return $result;
}

/**
 * 数据-查询-组装-空日期字段 <p>
 * dqa field datetime empty
 * </p>
 * @param string $field 字段名
 * @param string $replace 替换值
 * @param int $type 类型
 * @return string
 */
function dqa_fdempty($field, $replace = '', $type = 1)
{
    if (!dsc_pempty([$field])[0]) {
        $field = '\'' . $field . '\'';
    }
    switch ($type) {
        default:
        case 1:
            //日期时间
            $bool = 'from_unixtime(' . $field . ')';
            break;
        case 2:
            //日期
            $bool = 'from_unixtime(' . $field . ',\'%Y-%m-%d\')';
            break;
        case 3:
            //时间
            $bool = 'from_unixtime(' . $field . ',\'%H:%i:%S\')';
            break;
    }
    if (!dsc_pempty([$replace])[0]) {
        $replace = $bool;
    } else {
        $replace = '\'' . $replace . '\'';
    }
    $result = 'if(' . $field . '=0,' . $replace . ',' . $bool . ')';
    return $result;
}

/**
 * 数据-查询-检查-更新比较 <p>
 * dqc update compare
 * </p>
 * @param array $data_new 新数据
 * @param array $data_old 旧数据
 * @return mixed
 */
function dqc_upcompare($data_new, $data_old)
{
    //初始化变量
    $result = fsi_result();
    if (!isset($data_new)) {
        $result[0] = false;
        $result[2] = fxy_lang(['lack', 'new', 'data']);
    } else if (!isset($data_old)) {
        $result[0] = false;
        $result[2] = fxy_lang(['lack', 'old', 'data']);
    } else if (is_array($data_new) && is_array($data_old)) {
        foreach ($data_new as $key => $value) {
            if ($value != $data_old[$key]) {
                $result[0] = false;
                $result[2] = fxy_lang(['data', '[', fxy_config('app.lang.prefix') . $key, ']', 'not', 'same']);
                break;
            }
        }
    } else {
        $result[0] = false;
        $result[2] = fxy_lang(['data', 'format', 'error']);
    }
    return $result;
}
