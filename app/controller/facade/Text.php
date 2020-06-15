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
            if (preg_match("/^[\x7f-\xff]/", $data)) {
                $fchar = ord($data[0]);
                if ($fchar >= ord("A") and $fchar <= ord("z")) return strtoupper($data[0]);
                $char = ord($data[0]) * 256 + ord($data[1]) - 65536;
                if ($char >= -20319 and $char <= -20284) return "A";
                if ($char >= -20283 and $char <= -19776) return "B";
                if ($char >= -19775 and $char <= -19219) return "C";
                if ($char >= -19218 and $char <= -18711) return "D";
                if ($char >= -18710 and $char <= -18527) return "E";
                if ($char >= -18526 and $char <= -18240) return "F";
                if ($char >= -18239 and $char <= -17923) return "G";
                if ($char >= -17922 and $char <= -17418) return "H";
                if ($char >= -17417 and $char <= -16475) return "J";
                if ($char >= -16474 and $char <= -16213) return "K";
                if ($char >= -16212 and $char <= -15641) return "L";
                if ($char >= -15640 and $char <= -15166) return "M";
                if ($char >= -15165 and $char <= -14923) return "N";
                if ($char >= -14922 and $char <= -14915) return "O";
                if ($char >= -14914 and $char <= -14631) return "P";
                if ($char >= -14630 and $char <= -14150) return "Q";
                if ($char >= -14149 and $char <= -14091) return "R";
                if ($char >= -14090 and $char <= -13319) return "S";
                if ($char >= -13318 and $char <= -12839) return "T";
                if ($char >= -12838 and $char <= -12557) return "W";
                if ($char >= -12556 and $char <= -11848) return "X";
                if ($char >= -11847 and $char <= -11056) return "Y";
                if ($char >= -11055 and $char <= -10247) return "Z";
            }
            return $var;
        } catch (\Throwable $th) {
            return $var;
        }
    }

    /**
     * 处理时间-转换
     * @param int|string $time 时间
     * @param string $type 类型
     * @return string
     */
    public function timeChange($time, $type = null)
    {
        // 初始化变量
        if (is_string($time) && !is_numeric($time)) {
            $time = strtotime($time);
        }
        if (is_null($time) || false === $time) return;
        switch ($type) {
            case '1.1':
                // 日期转时间戳
                $time = strtotime(date('Y-m-d H:i:s', $time));
                break;
            case '1.2':
                // 日期转时间戳
                $time = strtotime(date('Y-m-d 00:00:00', $time));
                break;
            case '1.3':
                // 日期转时间戳
                $time = strtotime(date('Y-m-d 23:59:59', $time));
                break;
            case '2.1':
                // 时间戳转日期
                $time = date('Y-m-d H:i:s', $time);
                break;
            case '2.2':
                // 时间戳转日期
                $time = date('Y-m-d 00:00:00', $time);
                break;
            case '2.3':
                // 时间戳转日期
                $time = date('Y-m-d 23:59:59', $time);
                break;
        }
        return $time;
    }

    /**
     * 处理时间-毫米
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
            $echo = date("Y-m-d H:i:s", substr($mtime, 0, 10));
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
     * @return string
     */
    public function throwable($th)
    {
        // 返回抛出
        $echo = [
            $th->getMessage(),
            $th->getFile(),
            $th->getLine(),
        ];
        return implode(' ', $echo);
    }
}
