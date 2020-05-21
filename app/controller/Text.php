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

class Text
{
    /**
     * 生成UUID
     * @param string $mode 模式
     * @param array $param 参数
     * @return array
     */
    public static function uuid($mode = null, $param = [])
    {
        // 初始化变量
        $uuid = '';
        $predefined = [
            'uuid' => '', 'prefix' => '', 'style' => '',
        ];
        $param = fsi_param([$param, $predefined], '1.1.2');
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
        $param['style'] = \fxapp\Text::explode(',', $param['style']);
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
     * @return mixed
     */
    public static function explode($separator, $string)
    {
        // 初始化变量
        if (is_null($string) || '' == $string) {
            return [];
        }
        return array_values(array_unique(explode($separator, $string)));
    }

    /**
     * 处理日期时间
     * @param string $time 时间
     * @param int $type 类型
     * @return string
     */
    public static function datetime($time = null, $type = -1)
    {
        $time = \fxapp\Data::paramEmpty([$time])[0] ? $time : time();
        if (is_string($time) && !is_numeric($time)) {
            $time = strtotime($time);
        }
        switch ($type) {
            case 1:
                // 日期转时间戳
                $time = strtotime(date('Y-m-d 00:00:00', $time)) . " and " . strtotime(date('Y-m-d 23:59:59', $time));
                break;
            case 2:
                // 日期转时间戳
                $time = strtotime(date('Y-m-d 00:00:00', $time));
                break;
            case 3:
                // 日期转时间戳
                $time = strtotime(date('Y-m-d 23:59:59', $time));
                break;
            case 4:
                // 日期转时间戳
                $time = strtotime(date('Y-m-d H:i:s', $time));
                break;
            case 5:
                // 时间戳转日期
                $time = date('Y-m-d 00:00:00', $time);
                break;
            case 6:
                // 时间戳转日期
                $time = date('Y-m-d 23:59:59', $time);
                break;
            case 7:
                // 时间戳转日期
                $time = date('Y-m-d H:i:s', $time);
                break;
        }
        return $time;
    }

    /**
     * 拼接字符串
     * @param string $string 字符串
     * @param string $value 值
     * @param string $separator 分隔符
     * @return string
     */
    public static function splice($string, $value, $separator = '')
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
    public static function strlen($string = null, $start = 0, $end = 0)
    {
        // 初始化变量
        $result = fsi_result();
        $length = mb_strlen($string, 'utf-8');
        $end == 0 && $end = $length;
        $start > $end && $start = $end;
        if (is_string($string)) {
            if ($end > 0 && !($length >= $start && $length <= $end)) {
                $result[0] = false;
                $result[1] = 1004;
                $result[2] = \fxapp\Base::lang(['string', 'length', 'should', 'be', ($start == $end ? $end : $start . '-' . $end)]);
            }
        } else {
            $result[0] = false;
            $result[1] = 1000;
            $result[2] = \fxapp\Base::lang(['string', 'not2', 'input']);
        }
        return $result;
    }

    /**
     * 首字母组
     * @param string $var 变量
     * @param string $in_charset 输入字符集
     * @param string $out_charset 输出字符集
     * @return string
     */
    public static function letters($var, $in_charset = 'utf-8', $out_charset = 'gb2312')
    {
        // 初始化变量
        $data = [];
        if (!is_string($var)) return false;
        for ($i = 0; $i < mb_strlen($var, 'utf-8'); $i++) {
            $char = mb_substr($var, $i, 1);
            $data[] = static::letter($char, $in_charset, $out_charset);
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
    public static function letter($var, $in_charset = 'utf-8', $out_charset = 'gb2312')
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
        } catch (\Throwable $e) {
            return $var;
        }
    }
}
