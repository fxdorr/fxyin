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
 * 文本类
 */
class Text
{
    /**
     * 生成UUID
     * @param string $mode 模式
     * @param array $param 参数
     * @return array
     */
    public function uuid($mode = null, $param = [])
    {
        // 初始化变量
        $uuid = '';
        $predefined = [
            'uuid' => '', 'prefix' => '', 'style' => '',
        ];
        $param = \fxapp\Param::define([$param, $predefined], '1.1.2');
        switch ($mode) {
            default:
            case 1:
                // 随机生成标准UUID
                if (empty($param['uuid'])) {
                    $chars = strtoupper(md5(uniqid(mt_rand(), true)));
                } else {
                    $chars = strtoupper($param['uuid']);
                }
                $uuid = substr($chars, 0, 8) . '-';
                $uuid .= substr($chars, 8, 4) . '-';
                $uuid .= substr($chars, 12, 4) . '-';
                $uuid .= substr($chars, 16, 4) . '-';
                $uuid .= substr($chars, 20, 12);
                $uuid = $param['prefix'] . $uuid;
                break;
            case 2:
                // 随机生成大写UUID或将现有UUID大写
                if (empty($param['uuid'])) {
                    $chars = strtoupper(md5(uniqid(mt_rand(), true)));
                } else {
                    $chars = strtoupper($param['uuid']);
                }
                $uuid = $chars;
                break;
            case 3:
                // 随机生成大写UUID或将现有UUIDMD5并大写
                if (empty($param['uuid'])) {
                    $chars = strtoupper(md5(uniqid(mt_rand(), true)));
                } else {
                    $chars = strtoupper(md5($param['uuid']));
                }
                $uuid = $chars;
                break;
        }
        $param['style'] = $this->explode(',', $param['style']);
        foreach ($param['style'] as $value) {
            switch ($value) {
                case 1:
                    // 32位-大写
                    $uuid = strtoupper($uuid);
                    break;
                case 2:
                    // 32位-小写
                    $uuid = strtolower($uuid);
                    break;
                case 3:
                    // 16位-大写
                    $uuid = strtoupper(substr($uuid, 8, 16));
                    break;
                case 4:
                    // 16位-小写
                    $uuid = strtolower(substr($uuid, 8, 16));
                    break;
            }
        }
        return $uuid;
    }

    /**
     * 打散字符串
     * @param string $separator 分割字符串
     * @param string $string 字符串
     * @return array
     */
    public function explode($separator, $string)
    {
        // 初始化变量
        if (is_null($string) || '' == $string) {
            return [];
        } else if (!is_string($string) && !is_numeric($string)) {
            return [$string];
        }
        return array_values(array_unique(explode($separator, $string)));
    }

    /**
     * 拼接字符串
     * @param string $string 字符串
     * @param string $value 键值
     * @param string $separator 分隔符
     * @return string
     */
    public function splice($string, $value, $separator = '')
    {
        // 初始化变量
        if (is_null($value) || is_null($separator)) {
            return $string;
        }
        if ($string) {
            $string .= $separator . $value;
        } else {
            $string = $value;
        }
        return $string;
    }

    /**
     * 检查字符串长度
     * @param string $string 字符串
     * @param int $start 开始长度
     * @param int $end 结束长度
     * @return mixed
     */
    public function strlen($string = null, $start = 0, $end = 0)
    {
        // 初始化变量
        $echo = \fxapp\Server::echo();
        $length = mb_strlen($string, 'utf-8');
        $end == 0 && $end = $length;
        $start > $end && $start = $end;
        if (is_string($string)) {
            if ($end > 0 && !($length >= $start && $length <= $end)) {
                $echo[0] = false;
                $echo[1] = 1004;
                $echo[2] = \fxapp\Base::lang(['string', 'length', 'should', 'be', ($start == $end ? $end : $start . '-' . $end)]);
            }
        } else {
            $echo[0] = false;
            $echo[1] = 1000;
            $echo[2] = \fxapp\Base::lang(['string', 'not2', 'input']);
        }
        return $echo;
    }

    /**
     * 首字母组
     * @param string $var 变量
     * @param string $in_charset 输入字符集
     * @param string $out_charset 输出字符集
     * @return string
     */
    public function letters($var, $in_charset = 'utf-8', $out_charset = 'gb2312')
    {
        // 初始化变量
        $data = [];
        if (!is_string($var)) return false;
        for ($i = 0; $i < mb_strlen($var, 'utf-8'); $i++) {
            $char = mb_substr($var, $i, 1);
            $data[] = $this->letter($char, $in_charset, $out_charset);
        }
        $data = implode('', $data);
        return $data;
    }

    /**
     * 首字母
     * @param string $var 变量
     * @param string $in_charset 输入字符集
     * @param string $out_charset 输出字符集
     * @return string
     */
    public function letter($var, $in_charset = 'utf-8', $out_charset = 'gb2312')
    {
        try {
            // 初始化变量
            // 如果程序是gbk的，此行就要注释掉
            $data = iconv($in_charset, $out_charset, $var);
            if (preg_match('/^[\x7f-\xff]/', $data)) {
                $fchar = ord($data[0]);
                if ($fchar >= ord('A') and $fchar <= ord('z')) return strtoupper($data[0]);
                $char = ord($data[0]) * 256 + ord($data[1]) - 65536;
                if ($char >= -20319 and $char <= -20284) return 'A';
                if ($char >= -20283 and $char <= -19776) return 'B';
                if ($char >= -19775 and $char <= -19219) return 'C';
                if ($char >= -19218 and $char <= -18711) return 'D';
                if ($char >= -18710 and $char <= -18527) return 'E';
                if ($char >= -18526 and $char <= -18240) return 'F';
                if ($char >= -18239 and $char <= -17923) return 'G';
                if ($char >= -17922 and $char <= -17418) return 'H';
                if ($char >= -17417 and $char <= -16475) return 'J';
                if ($char >= -16474 and $char <= -16213) return 'K';
                if ($char >= -16212 and $char <= -15641) return 'L';
                if ($char >= -15640 and $char <= -15166) return 'M';
                if ($char >= -15165 and $char <= -14923) return 'N';
                if ($char >= -14922 and $char <= -14915) return 'O';
                if ($char >= -14914 and $char <= -14631) return 'P';
                if ($char >= -14630 and $char <= -14150) return 'Q';
                if ($char >= -14149 and $char <= -14091) return 'R';
                if ($char >= -14090 and $char <= -13319) return 'S';
                if ($char >= -13318 and $char <= -12839) return 'T';
                if ($char >= -12838 and $char <= -12557) return 'W';
                if ($char >= -12556 and $char <= -11848) return 'X';
                if ($char >= -11847 and $char <= -11056) return 'Y';
                if ($char >= -11055 and $char <= -10247) return 'Z';
            }
            return $var;
        } catch (\Throwable $th) {
            return $var;
        }
    }

    /**
     * 处理时间-范围
     * @param array|string $time 时间
     * @param array $start 开始时间
     * @param array $end 结束时间
     * @return string
     */
    public function timeRange($time, $start = [], $end = [])
    {
        // 初始化变量
        if (is_string($time)) {
            $time = explode(',', $time);
        } else if (!is_array($time) || !is_array($start) || !is_array($end)) {
            return $time;
        }
        $time = array_values($time);
        // 疏理开始时间
        $predefined = [
            // 默认值
            0,
            // 类型
            '1',
            // 格式
            'Y-m-d H:i:s',
        ];
        $start = \fxapp\Param::define([$start, $predefined], '2.1.2');
        // 疏理结束时间
        $predefined = [
            // 默认值
            time(),
            // 类型
            '1',
            // 格式
            'Y-m-d H:i:s',
        ];
        $end = \fxapp\Param::define([$end, $predefined], '2.1.2');
        // 疏理时间
        $predefined = [
            // 开始时间
            $start[0],
            // 结束时间
            $end[0],
        ];
        $time = \fxapp\Param::define([$time, $predefined], '2.1.2');
        $time[0] = $this->timeChange(trim($time[0]), $start[1], $start[2]);
        $time[1] = $this->timeChange(trim($time[1]), $end[1], $end[2]);
        return $time;
    }

    /**
     * 处理时间-转换
     * @param int|string $time 时间
     * @param string $type 类型
     * @param string $format 格式
     * @return string
     */
    public function timeChange($time, $type = null, $format = 'Y-m-d H:i:s')
    {
        // 初始化变量
        if (is_string($time) && !is_numeric($time)) {
            $time = strtotime($time);
        }
        if (is_null($time) || false === $time) return;
        // 格式化时间
        switch ($type) {
            case '1':
                // 解析时间戳
                $time = strtotime(date($format, $time));
                break;
            case '2':
                // 格式化日期
                $time = date($format, $time);
        }
        return $time;
    }

    /**
     * 处理时间-毫秒
     * @param string $mtime 毫秒时间
     * @return string
     */
    public function timeMilli($mtime = null)
    {
        // 初始化变量
        $echo = null;
        if (is_numeric($mtime)) {
            if (strlen($mtime) != 13) {
                return false;
            }
            $echo = date('Y-m-d H:i:s', substr($mtime, 0, 10));
            $echo = $echo . '.' . substr($mtime, 10, 3);
        } else if (is_string($mtime)) {
            $echo = explode('.', $mtime);
            $echo = strtotime($echo[0]) . $echo[1];
            $echo = intval($echo);
        } else {
            $now = explode(' ', microtime());
            $echo = substr($now[1] . substr($now[0], 2), 0, 13);
            $echo = intval($echo);
        }
        return $echo;
    }

    /**
     * 处理时间-格式化
     * @param string $time 时间
     * @param string $type 格式
     * @return string
     */
    public function timeFormat($time = null, $type = null)
    {
        // 初始化变量
        $echo = [];
        if (!is_numeric($time)) {
            return false;
        }
        // 时间列表
        $time_list = [];
        // 时间开关
        $time_switch = [
            0 => false,
            1 => true,
            2 => true,
            3 => true,
            4 => true,
            5 => true,
            6 => true,
        ];
        // 时间名称
        $time_name = [
            0 => 'millisecond',
            1 => 'sec',
            2 => 'minute',
            3 => 'hour',
            4 => 'day',
            5 => 'month',
            6 => 'year',
        ];
        $time_all = $time;
        switch ($type) {
            default:
            case '1.1':
                // 秒
                break;
            case '1.2':
                // 毫秒
                $time_switch[0] = true;
                break;
        }
        // 毫秒
        if ($time_switch[0]) {
            $time_list[0] = $time_all % 1000;
            $time_all = intval($time_all / 1000);
        }
        // 秒
        if ($time_switch[1]) {
            $time_list[1] = $time_all % 60;
            $time_all = intval($time_all / 60);
        }
        // 分钟
        if ($time_switch[2]) {
            $time_list[2] = $time_all % 60;
            $time_all = intval($time_all / 60);
        }
        // 小时
        if ($time_switch[3]) {
            $time_list[3] = $time_all % 24;
            $time_all = intval($time_all / 24);
        }
        // 天
        if ($time_switch[4]) {
            $time_list[4] = $time_all % 30;
            $time_all = intval($time_all / 30);
        }
        // 月
        if ($time_switch[5]) {
            $time_list[5] = $time_all % 12;
            $time_all = intval($time_all / 12);
        }
        // 年
        if ($time_switch[6]) {
            $time_list[6] = $time_all;
        }
        krsort($time_list);
        // 去除0
        foreach ($time_list as $key => $value) {
            if ($value == 0) {
                unset($time_list[$key]);
            }
        }
        foreach ($time_list as $key => $value) {
            $echo[] = $value;
            $echo[] = $time_name[$key];
        }
        $echo = \fxapp\Base::lang($echo);
        return $echo;
    }

    /**
     * 处理字符串-编码
     * @param array $data 数据
     * @return string
     */
    public function strEncode($data)
    {
        // 初始化变量
        $echo = [];
        // 解析数据
        foreach ($data as $key => $value) {
            // 解析数据
            $value = $this->strEncodeMerge($value);
            if (!is_array($value)) {
                $value = [$value];
            }
            // 疏理数据
            foreach ($value as $key2 => $value2) {
                $echo[] = $key . $value2;
            }
        }
        $echo = implode('&', $echo);
        return $echo;
    }

    /**
     * 处理字符串-编码-合并数组
     * @param array $data 数据
     * @return array
     */
    public function strEncodeMerge($data)
    {
        // 初始化变量
        $tray = [];
        if (!is_array($data)) {
            $data = urlencode($data);
            return '=' . $data;
        }
        foreach ($data as $key => $value) {
            $value = $this->strEncodeMerge($value);
            if (array_keys($data) != array_flip(array_keys($data))) {
                $tray[] = '[' . $key . ']' . $value;
            } else {
                $tray[] = '[]' . $value;
            }
        }
        return $tray;
    }

    /**
     * 处理字符串-解码
     * @param string $data 数据
     * @return array
     */
    public function strDecode($data)
    {
        // 初始化变量
        $echo = [];
        // 解析数据
        $data = explode('&', $data);
        foreach ($data as $key => $value) {
            // 解析数据
            $value = explode('=', $value, 2);
            $predefined = [
                // 键钥
                0,
                // 键值
                1,
            ];
            $value = \fxapp\Param::define([$value, $predefined], '1.2.1');
            if (is_blank($value[0]) && is_null($value[1])) continue;
            // 疏理数据
            $value[0] = !is_null($value[0]) ? urldecode($value[0]) : $value[0];
            $value[1] = !is_null($value[1]) ? urldecode($value[1]) : $value[1];
            // 解析键钥
            $value[0] = preg_replace('/^([^\[]*)/', '[$1]', $value[0]);
            $value[2] = $echo;
            $value = $this->strDecodeMerge($value);
            // 合并数据
            $echo = \fxapp\Param::merge($echo, $value[2]);
        }
        return $echo;
    }

    /**
     * 处理字符串-解码-合并数组
     * @param array $data 数据
     * @return array
     */
    public function strDecodeMerge($data)
    {
        // 初始化变量
        $tray = [];
        $tray['find'] = '^\[([^\]]*)\]';
        preg_match('/' . $tray['find'] . '/', $data[0], $tray['key']);
        $data[0] = preg_replace('/^\\[[^\\]]*\\]/', '', $data[0]);
        if (empty($tray['key'])) {
            return [];
        }
        // 解析数据
        $data[2] = !is_null($data[2]) ? $data[2] : [];
        if ($tray['key'][0] == '[]') {
            $tray['key'][1] = -1;
            foreach ($data[2] as $key => $value) {
                if (!is_numeric($key) || floor($key) != $key) continue;
                $tray['key'][1] = $tray['key'][1] > $key ? $tray['key'][1] : $key;
            }
            $tray['key'][1]++;
        }
        // 疏理数据
        if (!empty(preg_match('/' . $tray['find'] . '/', $data[0]))) {
            $data[2][$tray['key'][1]] = $this->strDecodeMerge([$data[0], $data[1], $data[2][$tray['key'][1]] ?? null])[2] ?? null;
        } else {
            $data[2][$tray['key'][1]] = $data[1];
        }
        return $data;
    }

    /**
     * 解析Ipv4
     * @param mixed $var 变量
     * @param string $type 类型
     * @return mixed
     */
    public function ipv4($var, $type)
    {
        // 初始化变量
        $echo = null;
        $type = strtolower($type);
        switch ($type) {
            case 'encode':
                // 编码
                $var = explode('.', $var);
                foreach ($var as $key => $value) {
                    $value = ltrim($value, 0);
                    if ($value == '') {
                        $value = '0';
                    }
                    $var[$key] = $value;
                }
                $var = implode('.', $var);
                $echo = ip2long($var);
                if ($echo !== false) {
                    $echo = bindec(decbin($echo));
                }
                break;
            case 'decode':
                // 解码
                $echo = long2ip($var);
                break;
        }
        return $echo;
    }

    /**
     * 进制转换
     * @param mixed $var 变量
     * @param string $type 类型
     * @return mixed
     */
    public function convert($var, $type)
    {
        // 初始化变量
        $echo = null;
        $type = strtolower($type);
        switch ($type) {
            case 'hexstr':
                // 16进制转字符串
                if (!$var) {
                    // 空字符串
                    return false;
                }
                $strs = [];
                $len = strlen($var);
                for ($i = 0; $i < $len; $i++) {
                    $str = substr($var, $i, 1);
                    $strs[] = bin2hex($str);
                }
                $strs = implode('', $strs);
                $echo = $strs;
                break;
            case 'strhex':
                // 字符串转16进制
                if (!$var) {
                    // 空字符串
                    return false;
                } else if (mb_strlen($var, 'utf-8') != strlen($var)) {
                    // 不解析混编数据
                    return false;
                } else if (strlen($var) % 2 != 0) {
                    // 不解析单数字符串
                    return false;
                }
                $strs = [];
                $len = strlen($var);
                for ($i = 0; $i < $len; $i = $i + 2) {
                    $str = substr($var, $i, 2);
                    $strs[] = chr(hexdec($str));
                }
                $strs = implode('', $strs);
                $echo = $strs;
                break;
        }
        return $echo;
    }

    /**
     * 提取抛出
     * @param \Throwable $th 抛出对象
     * @param string $type 类型
     * @param array $param 参数
     * @return mixed
     */
    public function throwable($th, $type, $param = [])
    {
        // 疏理数据
        switch ($type) {
            default:
                // 默认
            case '1.1':
                // 简略
                $echo = [
                    $th->getMessage(),
                    $th->getFile(),
                    $th->getLine(),
                ];
                $echo = implode(' ', $echo);
                break;
            case '1.2':
                // 详细
                $echo = \fxapp\Server::echo();
                $echo[0] = false;
                $echo[1] = -1;
                $echo[2] = $this->throwable($th, '1.1');
                $echo[4]['throwable'] = $this->throwable($th, '1.1');
                $echo[4]['trace'] = array_map(function ($value) {
                    unset($value['args']);
                    return $value;
                }, array_slice($th->getTrace(), 0, 6));
                $echo = \fxapp\Param::merge(2, $echo, $param);
                break;
        }
        return $echo;
    }
}
