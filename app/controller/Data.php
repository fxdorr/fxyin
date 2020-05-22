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
namespace fxapp;

class Data extends \fxyin\Facade
{
    /**
     * SQL-条件
     * @param array $var 变量
     * @param string $key 键
     * @param string $value 值
     * @param string $comparison 表达式
     * @param string $logic 逻辑
     * @param int $rank 排序
     * @return string
     */
    public static function where($var, $key, $value, $comparison, $logic, $rank)
    {
        if (!is_array($var) || is_null($key) || is_null($value) || is_null($comparison) || is_null($logic)) {
            return $var;
        }
        // 过滤非法字符
        $safe = static::whereSafe($value, $comparison);
        if (false === $safe) {
            return $var;
        }
        // 搭建函数组合
        $build = static::whereBuild($key, $safe, $comparison);
        $data = [
            'build' => $build,
            'logic' => $logic,
            'rank' => $rank,
        ];
        $var[] = $data;
        return $var;
    }

    /**
     * SQL-条件安检
     * @param string $var 变量
     * @param string $comparison 表达式
     * @return string
     */
    public static function whereSafe($var, &$comparison)
    {
        if (is_null($var)) {
            return $var;
        }
        // 拆解表达式
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
                if (count($var) < 2) {
                    return false;
                }
                break;
            case 'find_in_set':
                if (!is_array($var)) {
                    $var = explode(',', $var);
                }
                break;
        }
        // 过滤表达式
        $search = ["\\", "\'", "&", "\"", "<", ">"];
        $replace = ["\\\\", "\\\'", "&amp;", "&quot;", "&lt;", "&gt;"];
        foreach ($var as $key => $value) {
            // 包装表达式
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
            // 包装表达式
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
        // 组装表达式
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
        // 解析表达式
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
     * SQL-条件搭建
     * @param string $key 键
     * @param string $value 值
     * @param string $comparison 表达式
     * @return string
     */
    public static function whereBuild($key, $value, $comparison)
    {
        // 初始化变量
        $data = '';
        // 组装函数
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
     * SQL-条件组装
     * @param array $var 变量
     * @param int $type 类型
     * @return string
     */
    public static function whereMake($var, $type = 1)
    {
        $data = [];
        if (!is_array($var)) {
            return $var;
        }
        // 搭建查询组合
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
        switch ($type) {
            default:
            case 1:
                // 无处理
                break;
            case 2:
                // 拼接Where
                $data = strlen($data) > 0 ? 'where ' . $data : $data;
                break;
        }
        return $data;
    }

    /**
     * SQL-更新
     * @param string $table 表名
     * @param array $data 数据
     * @param string $field string 字段
     * @param array $param array 参数
     * @return bool|string
     */
    public static function update($table, $data, $field = 'id', $param = [])
    {
        if (!is_string($table) || !is_array($data) || !is_string($field) || !is_array($param)) {
            return false;
        }
        $case = static::updateCase($data, $field);
        $where = static::updateParam($param);
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
     * SQL-更新条件-将二维数组转换成CASE WHEN THEN的批量更新条件
     * @param $data array 二维数组
     * @param $field string 列名
     * @return string sql语句
     */
    public static function updateCase($data, $field)
    {
        $sql = '';
        $keys = array_keys(current($data));
        foreach ($keys as $column) {
            $sql .= sprintf("`%s` = CASE `%s` \n", $column, $field);
            foreach ($data as $line) {
                // 过滤表达式
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
     * SQL-查询解析where条件
     * @param $param
     * @return array|string
     */
    public static function updateParam($param)
    {
        $where = [];
        foreach ($param as $key => $value) {
            $where[] = sprintf("`%s` = '%s'", $key, $value);
        }
        return $where ? ' AND ' . implode(' AND ', $where) : '';
    }

    /**
     * 处理参数-更新比较
     * @param array $data_new 新数据
     * @param array $data_old 旧数据
     * @return mixed
     */
    public static function updateContrast($data_new, $data_old)
    {
        // 初始化变量
        $echo = \fxapp\Server::echo();
        if (!isset($data_new)) {
            $echo[0] = false;
            $echo[2] = \fxapp\Base::lang(['lack', 'new', 'data']);
        } else if (!isset($data_old)) {
            $echo[0] = false;
            $echo[2] = \fxapp\Base::lang(['lack', 'old', 'data']);
        } else if (is_array($data_new) && is_array($data_old)) {
            foreach ($data_new as $key => $value) {
                if ($value != $data_old[$key]) {
                    $echo[0] = false;
                    $echo[2] = \fxapp\Base::lang(['data', '[', \fxapp\Base::config('app.lang.prefix') . $key, ']', 'not', 'same']);
                    break;
                }
            }
        } else {
            $echo[0] = false;
            $echo[2] = \fxapp\Base::lang(['data', 'format', 'error']);
        }
        return $echo;
    }

    /**
     * 处理字段-计算经纬度距离
     * @param string $lngs 经度起点-longitude start
     * @param string $lats 维度起点-latitude start
     * @param string $lnge 经度终点-longitude end
     * @param string $late 维度终点-latitude end
     * @return string
     */
    public static function fieldDistance($lngs, $lats, $lnge, $late)
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
        $echo = str_replace(array(" ", "　", "\t", "\n", "\r"), '', $form);
        return $echo;
    }

    /**
     * 处理字段-首字母
     * @param string $field 字段名
     * @return string
     */
    public static function fieldInitial($field)
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
        $echo = str_replace(array("  ", "　", "\t", "\n", "\r"), '', $form);
        return $echo;
    }

    /**
     * 处理字段-Json
     * @param string $field 字段名
     * @param string $param 参数
     * @param int $mode 模式
     * @return string
     */
    public static function fieldJson($field, $param, $mode = null)
    {
        switch ($mode) {
            default:
            case 1:
                // 默认
                $echo = "if(json_valid({$field}), trim(both '\"' from {$field}->'{$param}'), {$field})";
                break;
            case 2:
                // 非Json则替换
                $echo = "if(json_valid({$field}), {$field}, '{}')";
                break;
        }
        return $echo;
    }

    /**
     * 处理字段-文本
     * @param string $field 字段名
     * @param string $replace 替换值
     * @param int $mode 模式
     * @return string
     */
    public static function fieldText($field, $replace = '', $mode = 1)
    {
        if (!static::paramEmpty([$field])[0]) {
            $field = '\'' . $field . '\'';
        }
        switch ($mode) {
            default:
            case 1:
                // 默认
                if (is_numeric($replace)) {
                    $echo = 'if(length(' . $field . ')=0 or isnull(' . $field . '),' . $replace . ',' . $field . ')';
                } else {
                    $echo = 'if(length(' . $field . ')=0 or isnull(' . $field . '),\'' . $replace . '\',' . $field . ')';
                }
                break;
            case 2:
                // 为空则替换
                $echo = 'if(length(' . $field . ')=0 or isnull(' . $field . '),' . $replace . ',' . $field . ')';
                break;
        }
        return $echo;
    }

    /**
     * 处理字段-日期
     * @param string $field 字段名
     * @param string $replace 替换值
     * @param int $type 类型
     * @return string
     */
    public static function fieldDate($field, $replace = '', $type = 1)
    {
        if (!static::paramEmpty([$field])[0]) {
            $field = '\'' . $field . '\'';
        }
        switch ($type) {
            default:
            case 1:
                // 日期时间
                $bool = 'from_unixtime(' . $field . ')';
                break;
            case 2:
                // 日期
                $bool = 'from_unixtime(' . $field . ',\'%Y-%m-%d\')';
                break;
            case 3:
                // 时间
                $bool = 'from_unixtime(' . $field . ',\'%H:%i:%S\')';
                break;
        }
        if (is_null($replace)) {
            $replace = $bool;
        } else {
            $replace = '\'' . $replace . '\'';
        }
        $echo = 'if(' . $field . '=0 or isnull(' . $field . '),' . $replace . ',' . $bool . ')';
        return $echo;
    }

    /**
     * 处理HTML-过滤
     * @param string $string 字符串
     * @param string $flags 标签
     * @return mixed
     */
    public static function htmlFilter($string, $flags = null)
    {
        // 初始化变量
        if (is_array($string)) {
            foreach ($string as $key => $val) {
                $string[$key] = static::htmlFilter($val, $flags);
            }
        } else {
            if ($flags === null) {
                $string = str_replace(array('&', '<', '>'), array('&amp;', '&lt;', '&gt;'), $string);
                if (strpos($string, '&amp;#') !== false) {
                    $string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
                }
            } else {
                if (PHP_VERSION < '5.4.0') {
                    $string = htmlspecialchars($string, $flags);
                } else {
                    $string = htmlspecialchars($string, $flags, 'utf-8');
                }
            }
        }
        return $string;
    }

    /**
     * 处理HTML-移除
     * @param string $string 字符串
     * @param string $flags 标签
     * @return mixed
     */
    public static function htmlRemove($string, $flags = null)
    {
        // 初始化变量
        if (is_array($string)) {
            foreach ($string as $key => $val) {
                $string[$key] = static::htmlRemove($val, $flags);
            }
        } else {
            if ($flags === null) {
                $string = strip_tags($string, $flags);
            } else {
                $string = strip_tags($string, $flags);
            }
        }
        return $string;
    }

    /**
     * 列表格式树
     * @param array $list 未树化数组
     * @param int $parent_id 初始父ID
     * @param string $parnet_name 父名称
     * @param string $index 索引
     * @param string $child_name 子名称
     * @return mixed
     */
    public static function listToTree($list, $parent_id = 0, $parnet_name = 'parent_id', $index = 'id', $child_name = '_child')
    {
        // 初始化变量
        $tree = array();
        if (is_array($list)) {
            $refer = array();
            foreach ($list as $key => $value) {
                $refer[$value[$index]] = &$list[$key];
            }
            foreach ($list as $key => $value) {
                $parentId = $value[$parnet_name];
                if ($parentId == $parent_id) {
                    $tree[] = &$list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent = &$refer[$parentId];
                        $parent[$child_name][] = &$list[$key];
                    }
                }
            }
        }
        return $tree;
    }

    /**
     * 处理参数-过滤
     * @param array $param 参数
     * @return mixed
     */
    public static function paramFilter($param)
    {
        // 初始化变量
        $echo = \fxapp\Server::echo();
        if (!isset($param)) {
            $echo[0] = false;
            $echo[1] = 1000;
            $echo[2] = \fxapp\Base::lang(['lack', 'parameter']);
        } else if (is_array($param)) {
            foreach ($param as $key => $value) {
                if (is_null($value)) {
                    unset($param[$key]);
                }
            }
            $echo[2] = \fxapp\Base::lang(['request', 'success']);
            $echo[3] = $param;
        } else {
            $echo[0] = false;
            $echo[1] = 1001;
            $echo[2] = \fxapp\Base::lang(['parameter', 'format', 'error']);
        }
        return $echo;
    }

    /**
     * 检查参数-空参
     * @param array $param 参数
     * @return mixed
     */
    public static function paramEmpty($param)
    {
        // 初始化变量
        $echo = \fxapp\Server::echo();
        if (!isset($param)) {
            $echo[0] = false;
            $echo[1] = 1000;
            $echo[2] = \fxapp\Base::lang(['lack', 'parameter']);
        } else if (is_array($param)) {
            foreach ($param as $key => $value) {
                if (is_null($value) || $value === '') {
                    $name = is_numeric($key) ? 'param' : $key;
                    $echo[0] = false;
                    $echo[1] = 1000;
                    $echo[2] = \fxapp\Base::lang(['lack', $name]);
                    break;
                }
            }
        } else {
            $echo[0] = false;
            $echo[1] = 1001;
            $echo[2] = \fxapp\Base::lang(['parameter', 'format', 'error']);
        }
        return $echo;
    }

    /**
     * 检查参数-非空
     * @param array $param 参数
     * @return mixed
     */
    public static function paramExist($param)
    {
        // 初始化变量
        $echo = \fxapp\Server::echo();
        if (!isset($param)) {
            $echo[0] = false;
            $echo[1] = 1000;
            $echo[2] = \fxapp\Base::lang(['lack', 'parameter']);
        } else if (is_array($param)) {
            foreach ($param as $key => $value) {
                if (is_null($value) || $value === '') {
                    $echo[0] = false;
                    $echo[1] = 1000;
                    $echo[2] = \fxapp\Base::lang(['lack', 'param']);
                } else {
                    $echo[0] = true;
                    $echo[1] = 0;
                    $echo[2] = \fxapp\Base::lang(['check', 'success']);
                    break;
                }
            }
        } else {
            $echo[0] = false;
            $echo[1] = 1001;
            $echo[2] = \fxapp\Base::lang(['parameter', 'format', 'error']);
        }
        return $echo;
    }
}
