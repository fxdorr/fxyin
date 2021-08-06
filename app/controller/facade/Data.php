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

namespace fxapp\facade;

/**
 * 数据类
 */
class Data
{
    /**
     * SQL-条件
     * @param array $data 数据
     * @param array $param 参数
     * @return array
     */
    public function where($data, $param)
    {
        // 初始化变量
        $predefined = [
            // 表达式
            -1 => null,
            // 键名
            0 => null,
            // 键值
            1 => null,
            // 方法
            2 => null,
            // 逻辑
            3 => 'and',
            // 分组
            4 => '1',
            // 取反
            5 => 0,
            // 大小写敏感
            6 => 1,
        ];
        $param = \fxapp\Param::define([$param, $predefined], '1.1.1');
        $predefined = [
            // 表达式
            'build' => $param[-1],
            // 键名
            'key' => $param[0],
            // 键值
            'value' => $param[1],
            // 方法
            'method' => $param[2],
            // 逻辑
            'logic' => $param[3],
            // 分组
            'group' => $param[4],
            // 取反
            'not' => $param[5],
            // 大小写敏感
            'case' => $param[6],
        ];
        $param = \fxapp\Param::define([$param, $predefined], '1.1.1');
        if (is_null($param['build'])) {
            if (!is_array($data)) {
                return $data;
            }
            // 过滤非法字符
            $param['value'] = $this->whereSafe($param['value'], $param['method'], $param['case']);
            if (false === $param['value']) {
                return $data;
            }
            // 搭建表达式
            $param['build'] = $this->whereBuild($param['key'], $param['value'], $param['method']);
        }
        $elem = [
            // 表达式
            'build' => $param['build'],
            // 逻辑
            'logic' => $param['logic'],
            // 分组
            'group' => $param['group'],
            // 取反
            'not' => $param['not'],
        ];
        $data[] = $elem;
        return $data;
    }

    /**
     * SQL-条件安检
     * @param string $value 键值
     * @param string $method 方法
     * @param int $case 大小写敏感
     * @return string
     */
    public function whereSafe($value, &$method, $case = 0)
    {
        // 初始化变量
        if (!(is_string($value) || is_numeric($value) || is_array($value) || is_object($value))) {
            return false;
        } else if (is_null($method)) {
            return $value;
        }
        // 条件过滤
        $value = $this->whereFilter($value, $method, $case);
        // 解析方法
        switch ($method) {
            case 'like fuzzy':
                // 模糊
                $method = 'like';
                break;
            case 'not like fuzzy':
                // 模糊-取反
                $method = 'not like';
                break;
        }
        return $value;
    }

    /**
     * SQL-条件过滤
     * @param string $value 键值
     * @param string $method 方法
     * @param int $case 大小写敏感
     * @return string
     */
    public function whereFilter($value, $method, $case = 0)
    {
        // 初始化变量
        if (!(is_string($value) || is_numeric($value) || is_array($value) || is_object($value))) {
            return false;
        } else if (is_null($method)) {
            return $value;
        }
        if (is_numeric($value)) {
            $value = (string) $value;
        } else if (is_object($value)) {
            $value = (array) $value;
        }
        // 拆解方法
        switch ($method) {
            default:
                // 默认
                if (!is_array($value)) {
                    $value = [$value];
                }
                break;
            case 'in':
                // 批量
            case 'not in':
                // 批量-取反
                if (!is_array($value)) {
                    $value = explode(',', $value);
                }
                break;
            case 'like':
                // 模糊
            case 'not like':
                // 模糊-取反
            case 'like fuzzy':
                // 模糊
            case 'not like fuzzy':
                // 模糊-取反
                if (!is_array($value)) {
                    $value = [$value];
                }
                break;
            case 'between':
                // 范围
            case 'not between':
                // 范围-取反
                if (is_string($value) && mb_strpos($value, ' and ', 0, 'utf-8') !== false) {
                    $value = explode(' and ', $value);
                } else if (!is_array($value)) {
                    $value = explode(',', $value);
                }
                if (count($value) < 2) {
                    return false;
                }
                break;
            case 'find_in_set':
                // 批量
                if (!is_array($value)) {
                    $value = explode(',', $value);
                }
                break;
            case 'json in':
                // 对象-批量
                if (!is_array($value)) {
                    $value = explode(',', $value);
                }
                break;
        }
        // 过滤键值
        $search = ['\\', '\''];
        $replace = ['\\\\', '\\\''];
        foreach ($value as $index => $elem) {
            // 包装键值
            switch ($method) {
                case 'like':
                    // 模糊
                case 'not like':
                    // 模糊-取反
                    $search = array_merge($search, ['/', '_']);
                    $replace = array_merge($replace, ['//', '/_']);
                    break;
                case 'like fuzzy':
                    // 模糊
                case 'not like fuzzy':
                    // 模糊-取反
                    $search = array_merge($search, ['/', '%', '_']);
                    $replace = array_merge($replace, ['//', '/%', '/_']);
                    break;
            }
            // 过滤键值
            foreach ($search as $key2 => $value2) {
                $elem = str_replace($value2, $replace[$key2], $elem);
            }
            // 包装键值
            switch ($method) {
                default:
                    // 默认
                    $elem = '\'' . $elem . '\'';
                    break;
                case 'like fuzzy':
                    // 模糊
                case 'not like fuzzy':
                    // 模糊-取反
                    $elem = '\'%' . $elem . '%\'';
                    break;
                case 'field':
                    // 字段
                    break;
            }
            $value[$index] = $elem;
        }
        // 组装方法
        $tray['glue'] = '';
        $tray['tail'] = '';
        switch ($method) {
            default:
                // 默认
                break;
            case 'in':
                // 批量
            case 'not in':
                // 批量-取反
                if (!count($value)) {
                    $value[] = '\'\'';
                }
                $tray['glue'] = ',';
                break;
            case 'like':
                // 模糊
            case 'not like':
                // 模糊-取反
            case 'like fuzzy':
                // 模糊
            case 'not like fuzzy':
                // 模糊-取反
                $tray['tail'] = ' escape \'/\'';
                break;
            case 'between':
                // 范围
            case 'not between':
                // 范围-取反
                $tray['glue'] = ' and ';
                break;
            case 'find_in_set':
                // 批量
                if (!count($value)) {
                    $value[] = '\'\'';
                }
                $tray['glue'] = ',';
                break;
            case 'json in':
                // 对象-批量
                if (!count($value)) {
                    $value[] = '\'\'';
                }
                $case = 0;
                $tray['glue'] = ',';
                break;
        }
        // 组装方法
        $value = array_map(function ($value) use ($case) {
            if ($case) $value = 'binary ' . $value;
            return $value;
        }, $value);
        $value = implode($tray['glue'], $value) . $tray['tail'];
        return $value;
    }

    /**
     * SQL-条件搭建
     * @param string $key 键名
     * @param string $value 键值
     * @param string $method 方法
     * @return string
     */
    public function whereBuild($key, $value, $method)
    {
        // 初始化变量
        // 疏理类型
        if (strpos($key, '->') !== false) {
            $tray['type'] = 'json';
        } else {
            $tray['type'] = 'field';
        }
        // 处理数据
        switch ($tray['type']) {
            case 'json':
                // 对象
                $key = explode('->', $key, 2);
                $key[0] = explode('.', $key[0]);
                $key[0] = array_map(function ($value) {
                    // 疏理数据
                    if (strpos($value, '`') === false) {
                        $value = '`' . $value . '`';
                    }
                    return $value;
                }, $key[0]);
                $key[0] = implode('.', $key[0]);
                if (strpos($method, 'json') !== 0) {
                    $key = 'if(json_valid(' . $key[0] . '), trim(both \'"\' from ' . $key[0] . '->\'$.' . $key[1] . '\'), ' . $key[0] . ')';
                } else {
                    $key = 'concat(' . $key[0] . '->\'$.' . $key[1] . '\')';
                }
                break;
        }
        // 组装函数
        switch ($method) {
            default:
                // 默认
                $echo = [$key, $method, $value];
                $echo = array_filter($echo, function ($value) {
                    return !is_blank($value);
                });
                $echo = implode(' ', $echo);
                break;
            case 'in':
                // 批量
            case 'not in':
                // 批量
                $echo = $key . ' ' . $method . ' (' . $value . ')';
                break;
            case 'find_in_set':
                // 批量
                $value = explode(',', $value);
                $echo = [];
                foreach ($value as $cell) {
                    $echo[] = $method . '(' . $cell . ',' . $key . ')';
                }
                $echo = '(' . implode(' or ', $echo) . ')';
                break;
            case 'json in':
                // 对象-批量
                $echo = 'json_contains(json_array(' . $value . '), ' . $key . ')';
                break;
        }
        return $echo;
    }

    /**
     * SQL-条件装配
     * @param array $data 数据
     * @return string
     */
    public function whereAssemble($data)
    {
        // 初始化变量
        if (!is_array($data)) {
            return $data;
        }
        $echo = [];
        foreach ($data as $key => $value) {
            if (isset($value['self'])) {
                $echo[$key][] = $value['self'];
            }
            if (isset($value['child'])) {
                $value['child'] = $this->whereAssemble($value['child']);
                $echo[$key][] = $value['child'];
            }
            $echo[$key] = implode(' and ', $echo[$key]);
            $echo[$key] = '(' . $echo[$key] . ')';
        }
        $echo = implode(' and ', $echo);
        return $echo;
    }

    /**
     * SQL-条件组装
     * @param array $data 数据
     * @param int $type 类型
     * @return string
     */
    public function whereMake($data, $type = 1)
    {
        // 初始化变量
        if (!is_array($data)) {
            return $data;
        }
        // 疏理条件
        $where = [];
        foreach ($data as $key => $value) {
            $where = $this->where($where, $value);
        }
        // 打包条件
        $tray['save'] = [];
        foreach ($where as $key => $value) {
            // 校验表达式
            if (is_blank($value['build'])) continue;
            // 配置取反
            if ($value['not']) $value['build'] = '!(' . $value['build'] . ')';
            // 疏理分组
            if (isset($tray['save'][$value['group']])) {
                $tray['save'][$value['group']] .= ' ' . $value['logic'] . ' ' . $value['build'];
            } else {
                $tray['save'][$value['group']] = $value['build'];
            }
        }
        // 分拣条件
        $echo = [];
        foreach ($tray['save'] as $key => $value) {
            $key = explode('.', $key);
            $key = array_reverse($key);
            foreach ($key as $key2 => $value2) {
                $tray['echo'] = [];
                if ($key2 == 0) {
                    $tray['echo'][$value2]['self'] = $value;
                    $value = $tray['echo'];
                } else {
                    $tray['echo'][$value2]['child'] = $value;
                    $value = $tray['echo'];
                }
            }
            $echo = \fxapp\Param::merge($echo, $value);
        }
        // 装配条件
        $echo = $this->whereAssemble($echo);
        switch ($type) {
            default:
            case 1:
                // 无处理
                break;
            case 2:
                // 拼接Where
                $echo = strlen($echo) > 0 ? 'where ' . $echo : $echo;
                break;
        }
        return $echo;
    }

    /**
     * SQL-插入
     * @param string $table 表名
     * @param array $data 数据
     * @return bool|string
     */
    public function insert($table, $data)
    {
        if (!is_string($table) || !is_array($data) || count($data) < 1) {
            return false;
        }
        // 疏理键名
        $tray['key'] = array_keys(current($data));
        $tray['key'] = array_map(function ($value) {
            return '`' . $value . '`';
        }, $tray['key']);
        $tray['key'] = implode(',', $tray['key']);
        // 疏理键值
        $tray['value'] = [];
        foreach ($data as $elem) {
            $elem = array_map(function ($value) {
                if (is_null($value)) $value = 'null';
                // 过滤键值
                $search = ['\\', '\''];
                $replace = ['\\\\', '\\\''];
                foreach ($search as $key2 => $value2) {
                    $value = str_replace($value2, $replace[$key2], $value);
                }
                $value = '\'' . $value . '\'';
                return $value;
            }, $elem);
            $elem = '(' . implode(',', $elem) . ')';
            $tray['value'][] = $elem;
        }
        $tray['value'] = implode(',', $tray['value']);
        // 拼接Insert
        $echo = 'insert into `' . $table . '` (' . $tray['key'] . ')' . ' values' . $tray['value'];
        return $echo;
    }

    /**
     * SQL-更新
     * @param string $table 表名
     * @param array $data 数据
     * @param array $param 参数
     * @return bool|string
     */
    public function update($table, $data, $param = [])
    {
        if (!is_string($table) || !is_array($data) || !is_array($param)) {
            return false;
        }
        // 疏理主键
        $param['key'] = !is_blank($param['key']) ? $param['key'] : 'id';
        // 疏理数据
        $data = array_map(function ($data) {
            $data = array_map(function ($data) {
                // 过滤表达式
                $search = ['\\', '\''];
                $replace = ['\\\\', '\\\''];
                foreach ($search as $key => $value) {
                    $data = str_replace($value, $replace[$key], $data);
                }
                return '\'' . $data . '\'';
            }, $data);
            return $data;
        }, $data);
        $tray['value'] = [];
        // 疏理替换值
        foreach ($data as $elem) {
            foreach ($elem as $key => $cell) {
                $tray['value'][$key][$elem[$param['key']]] = $cell;
            }
        }
        // 疏理替换值
        $tray['value'] = array_map(function ($value) {
            foreach ($value as $key => $value) {
                $data[] = implode(' ', ['when binary', $key, 'then', $value]);
            }
            $data = implode(PHP_EOL, $data);
            return $data;
        }, $tray['value']);
        $echo = [];
        foreach ($tray['value'] as $key => $value) {
            $echo[] = implode('', [PHP_EOL, '`', $key, '` = case `', $param['key'], '`', PHP_EOL, '', $value, PHP_EOL, 'end']);
        }
        $echo = implode(',', $echo);
        $tray['key'] = array_column($data, $param['key']);
        $tray['key'] = array_map(function ($value) {
            return 'binary ' . $value;
        }, $tray['key']);
        $echo = 'update ' . $table . ' set ' . $echo . PHP_EOL . 'where `' . $param['key'] . '` in (' . implode(',', $tray['key']) . ')';
        return $echo;
    }

    /**
     * 处理参数-更新比较
     * @param array $data_new 新数据
     * @param array $data_old 旧数据
     * @return array
     */
    public function updateContrast($data_new, $data_old)
    {
        // 初始化变量
        $echo = \fxapp\Server::echo();
        if (!isset($data_new)) {
            $echo[0] = false;
            $echo[1] = 1002;
            $echo[2] = \fxapp\Base::lang(['lack', 'new', 'data']);
            return $echo;
        } else if (!isset($data_old)) {
            $echo[0] = false;
            $echo[1] = 1002;
            $echo[2] = \fxapp\Base::lang(['lack', 'old', 'data']);
            return $echo;
        } else if (!is_array($data_new) || !is_array($data_old)) {
            $echo[0] = false;
            $echo[1] = 1002;
            $echo[2] = \fxapp\Base::lang(['data', 'format', 'error']);
            return $echo;
        }
        foreach ($data_new as $key => $value) {
            if ($value == $data_old[$key]) continue;
            $echo[2] = \fxapp\Base::lang(['data', '[', \fxapp\Base::config('app.lang.prefix') . $key, ']', 'not', 'same']);
            return $echo;
        }
        $echo[0] = false;
        $echo[1] = 1002;
        $echo[2] = \fxapp\Base::lang(['data', 'same']);
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
    public function fieldDistance($lngs, $lats, $lnge, $late)
    {
        if (is_null($lngs) || is_null($lats) || is_null($lnge) || is_null($late)) {
            return false;
        }
        $echo = "ACOS(
                    SIN(($lats * 3.1415) / 180 ) *
                    SIN(($late * 3.1415) / 180 ) +
                    COS(($lats * 3.1415) / 180 ) *
                    COS(($late * 3.1415) / 180 ) *
                    COS(($lngs * 3.1415) / 180 - ($lnge * 3.1415) / 180 ) 
                ) * 6378.137";
        $echo = str_replace([" ", "　", "\t", "\n", "\r"], '', $echo);
        return $echo;
    }

    /**
     * 处理字段-首字母
     * @param string $field 字段名
     * @return string
     */
    public function fieldInitial($field)
    {
        if (is_null($field)) {
            return false;
        }
        $echo = "ELT(
                    INTERVAL(CONV(HEX(left(CONVERT(" . $field . " USING gbk),'1')),'16','10'),
                    0,
                    0xB0A1,0xB0C5,0xB2C1,0xB4EE,0xB6EA,0xB7A2,0xB8C1,0xB9FE,0xBBF7,
                    0xBFA6,0xC0AC,0xC2E8,0xC4C3,0xC5B6,0xC5BE,0xC6DA,0xC8BB,0xC8F6,
                    0xCBFA,0xCDDA,0xCEF4,0xD1B9,0xD4D1),
                    UPPER(left(" . $field . ",'1')),
                    'A','B','C','D','E','F','G','H','J','K','L','M','N','O','P',
                    'Q','R','S','T','W','X','Y','Z'
                )";
        $echo = str_replace(["  ", "　", "\t", "\n", "\r"], '', $echo);
        return $echo;
    }

    /**
     * 处理字段-除法
     * @param string $dividend 被除数
     * @param string $divisor 除数
     * @return string
     */
    public function fieldDivision($dividend, $divisor)
    {
        if (is_null($dividend) || is_null($divisor)) {
            return false;
        }
        $echo = 'if(' . $divisor . '=0,0,' . $dividend . '/' . $divisor . ')';
        return $echo;
    }

    /**
     * 处理字段-Json
     * @param string $field 字段名
     * @param string $param 参数
     * @param int $mode 模式
     * @return string
     */
    public function fieldJson($field, $param, $mode = null)
    {
        switch ($mode) {
            default:
            case 1:
                // 默认
                $echo = 'if(json_valid(' . $field . '), trim(both \'"\' from ' . $field . '->\'' . $param . '\'), ' . $field . ')';
                break;
            case 2:
                // 非Json则替换
                $echo = 'if(json_valid(' . $field . '), ' . $field . ', \'{}\')';
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
    public function fieldText($field, $replace = '', $mode = 1)
    {
        if (!$this->paramEmpty([$field], 1)[0]) {
            $field = '\'' . $field . '\'';
        }
        switch ($mode) {
            default:
            case 1:
                // 默认
                if (is_numeric($replace) && is_int($replace + 0)) {
                    $echo = 'convert(if(length(' . $field . ')=0 or isnull(' . $field . '),' . $replace . ',' . $field . '),signed)';
                } else if (is_numeric($replace)) {
                    $echo = 'if(length(' . $field . ')=0 or isnull(' . $field . '),' . $replace . ',' . $field . ')';
                } else {
                    $echo = 'if(length(' . $field . ')=0 or isnull(' . $field . '),\'' . $replace . '\',' . $field . ')';
                }
                break;
            case 2:
                // 为空则替换
                $echo = 'if(length(' . $field . ')=0 or isnull(' . $field . '),' . $replace . ',' . $field . ')';
                break;
            case 3:
                // 转为整型
                $echo = 'convert(if(length(' . $field . ')=0 or isnull(' . $field . '),' . $replace . ',' . $field . '),signed)';
                break;
        }
        return $echo;
    }

    /**
     * 处理字段-日期
     * @param string $field 字段名
     * @param string $replace 替换值
     * @param string $param 参数
     * @return string
     */
    public function fieldDate($field, $replace = '', $param = 1)
    {
        if (!$this->paramEmpty([$field], 1)[0]) {
            $field = '\'' . $field . '\'';
        }
        switch ($param) {
            case 1:
                // 日期时间
                $param = '%Y-%m-%d %H:%i:%S';
                break;
            case 2:
                // 日期
                $param = '%Y-%m-%d';
                break;
            case 3:
                // 时间
                $param = '%H:%i:%S';
                break;
        }
        $param = 'from_unixtime(' . $field . ',\'' . $param . '\')';
        if (is_null($replace)) {
            $replace = $param;
        } else {
            $replace = '\'' . $replace . '\'';
        }
        $echo = 'if(' . $field . '=0 or isnull(' . $field . '),' . $replace . ',' . $param . ')';
        return $echo;
    }

    /**
     * 处理HTML-过滤
     * @param string $string 字符串
     * @param string $flags 标签
     * @return string
     */
    public function htmlFilter($string, $flags = null)
    {
        // 初始化变量
        if (is_array($string)) {
            foreach ($string as $key => $val) {
                $string[$key] = $this->htmlFilter($val, $flags);
            }
        } else if (!is_blank($string)) {
            if ($flags === null) {
                $string = str_replace(['&', '<', '>'], ['&amp;', '&lt;', '&gt;'], $string);
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
     * @return string
     */
    public function htmlRemove($string, $flags = null)
    {
        // 初始化变量
        if (is_array($string)) {
            foreach ($string as $key => $val) {
                $string[$key] = $this->htmlRemove($val, $flags);
            }
        } else if (!is_blank($string)) {
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
     * @return array
     */
    public function listToTree($list, $parent_id = 0, $parnet_name = 'parent_id', $index = 'id', $child_name = '_child')
    {
        // 初始化变量
        $tree = [];
        if (!is_array($list)) return $tree;
        $refer = [];
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
        return $tree;
    }

    /**
     * 处理参数-补全
     * @param array $param 参数
     * @return array
     */
    public function paramRepair($param)
    {
        // 初始化变量
        $echo = \fxapp\Server::echo();
        if (!isset($param)) {
            $echo[0] = false;
            $echo[1] = 1000;
            $echo[2] = \fxapp\Base::lang(['lack', 'parameter']);
            return $echo;
        } else if (!is_array($param)) {
            $echo[0] = false;
            $echo[1] = 1001;
            $echo[2] = \fxapp\Base::lang(['parameter', 'format', 'error']);
            return $echo;
        }
        // 抽取主键
        $tray['format'] = [];
        foreach ($param as $key => $value) {
            foreach ($value as $key2 => $value2) {
                $tray['format'][$key2] = '';
            }
        }
        // 补全主键
        foreach ($param as $key => $value) {
            foreach ($tray['format'] as $key2 => $value2) {
                $param[$key][$key2] = $param[$key][$key2] ?? $value2;
            }
            ksort($param[$key]);
        }
        $echo[2] = \fxapp\Base::lang(['request', 'success']);
        $echo[3] = $param;
        return $echo;
    }

    /**
     * 处理参数-过滤
     * @param array $param 参数
     * @return array
     */
    public function paramFilter($param)
    {
        // 初始化变量
        $echo = \fxapp\Server::echo();
        if (!isset($param)) {
            $echo[0] = false;
            $echo[1] = 1000;
            $echo[2] = \fxapp\Base::lang(['lack', 'parameter']);
            return $echo;
        } else if (!is_array($param)) {
            $echo[0] = false;
            $echo[1] = 1001;
            $echo[2] = \fxapp\Base::lang(['parameter', 'format', 'error']);
            return $echo;
        }
        foreach ($param as $key => $value) {
            if (is_null($value)) {
                unset($param[$key]);
            }
        }
        $echo[2] = \fxapp\Base::lang(['request', 'success']);
        $echo[3] = $param;
        return $echo;
    }

    /**
     * 检查参数-空参
     * @param array $param 参数
     * @param int $type 类型
     * @return array
     */
    public function paramEmpty($param, $type = 1)
    {
        // 初始化变量
        $echo = \fxapp\Server::echo();
        $echo[3] = $param;
        if (!isset($param)) {
            $echo[0] = false;
            $echo[1] = 1000;
            $echo[2] = \fxapp\Base::lang(['lack', 'parameter']);
            return $echo;
        } else if (!is_array($param)) {
            $echo[0] = false;
            $echo[1] = 1001;
            $echo[2] = \fxapp\Base::lang(['parameter', 'format', 'error']);
            return $echo;
        }
        // 疏理键值
        foreach ($param as $key => $value) {
            // 识别类型
            switch ($type) {
                default:
                case 1:
                    // 默认
                    if (!is_blank($value)) continue 2;
                    break;
                case 2:
                    // 非NULL
                    if (!is_null($value)) continue 2;
                    break;
            }
            $name = is_numeric($key) ? 'param' : $key;
            if ($echo[0]) {
                $echo[0] = false;
                $echo[1] = 1000;
                $echo[4]['error']['name'] = 'lack';
            }
            $echo[4]['error']['data'][$name] = $value;
        }
        // 疏理消息
        if (!$echo[0]) {
            $echo[2] = array_keys($echo[4]['error']['data']);
            $echo[2] = array_map(function ($value) {
                $value = ['and2', '[', $value, ']'];
                return $value;
            }, $echo[2]);
            unset($echo[2][0][0]);
            $echo[2] = \fxapp\Base::lang(['lack', $echo[2]]);
        }
        return $echo;
    }

    /**
     * 检查参数-非空
     * @param array $param 参数
     * @return array
     */
    public function paramExist($param)
    {
        // 初始化变量
        $echo = \fxapp\Server::echo();
        if (!isset($param)) {
            $echo[0] = false;
            $echo[1] = 1000;
            $echo[2] = \fxapp\Base::lang(['lack', 'parameter']);
            return $echo;
        } else if (!is_array($param)) {
            $echo[0] = false;
            $echo[1] = 1001;
            $echo[2] = \fxapp\Base::lang(['parameter', 'format', 'error']);
            return $echo;
        }
        foreach ($param as $key => $value) {
            if (is_blank($value)) {
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
        return $echo;
    }
}
