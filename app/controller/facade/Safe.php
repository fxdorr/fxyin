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
 * 安全类
 */
class Safe
{
    /**
     * 解析数据-加密
     * @param mixed $var 变量
     * @param string $type 类型
     * @param string $param 参数
     * @return mixed
     */
    public function crypt($var, $type, $param = null)
    {
        // 初始化变量
        $echo = null;
        $type = strtolower($type);
        switch ($type) {
            case 'encode':
                // 编码
                if (!is_array($param)) {
                    $param = null;
                }
                $predefined = [
                    'method' => 'des-ecb', 'password' => '00000000', 'options' => null,
                    'iv' => null,
                ];
                $param = \fxapp\Param::define([$param, $predefined], '1.1.2');
                $echo = openssl_encrypt($var, $param['method'], $param['password'], $param['options'], $param['iv']);
                break;
            case 'decode':
                // 解码
                if (!is_array($param)) {
                    $param = null;
                }
                $predefined = [
                    'method' => 'des-ecb', 'password' => '00000000', 'options' => null,
                    'iv' => null,
                ];
                $param = \fxapp\Param::define([$param, $predefined], '1.1.2');
                $echo = openssl_decrypt($var, $param['method'], $param['password'], $param['options'], $param['iv']);
                break;
        }
        return $echo;
    }

    /**
     * 生成MD5
     * @param mixed $var 变量
     * @param int $type 类型
     * @return mixed
     */
    public function md5($var, $type = null)
    {
        // 初始化变量
        $echo = null;
        $type = intval($type);
        switch ($type) {
            default:
            case 1:
                // 32位
                $echo = md5($var);
                break;
            case 2:
                // 16位
                $echo = substr(md5($var), 8, 16);
                break;
        }
        return $echo;
    }

    /**
     * 解析数据-令牌
     * @param mixed $var 变量
     * @param string $type 类型
     * @return mixed
     */
    public function token($var, $type)
    {
        // 初始化变量
        $echo = null;
        $type = strtolower($type);
        switch ($type) {
            case 'encode':
                // 编码
                if (!$var) {
                    // 空字符串
                    return false;
                } else if (is_array($var)) {
                    // 数组
                    $var = implode(',', $var);
                } else if (!is_string($var)) {
                    // 非字符串
                    return false;
                }
                // 计算加密长度
                $param = ',' . $var . ',';
                $strlen = strlen($param);
                $exp = 5;
                do {
                    $pow = pow(2, $exp);
                    $strdiff = $pow - $strlen;
                    ++$exp;
                } while ($strdiff < 0);
                $strmax = $pow;
                // 填充令牌
                $param = str_pad($param, $strmax, \fxapp\Math::rand($strdiff / 2), STR_PAD_BOTH);
                // 加密令牌
                $param = \fxapp\Base::crypt($param, 'encode');
                $echo = bin2hex($param);
                break;
            case 'decode':
                // 解码
                if (!$var) {
                    // 空字符串
                    return false;
                } else if (!is_string($var)) {
                    // 非字符串
                    return false;
                } else if (strlen($var) % 2 != 0) {
                    // 不解析单数字符串
                    return false;
                } else if (!ctype_xdigit($var)) {
                    // 非纯16进制字符串
                    return false;
                }
                // 解密令牌
                $param = hex2bin($var);
                $param = \fxapp\Base::crypt($param, 'decode');
                $param = explode(',', $param);
                array_shift($param);
                array_pop($param);
                $param = implode(',', $param);
                $echo = $param;
                break;
        }
        return $echo;
    }

    /**
     * 解析数据-RSA私钥
     * @param mixed $var 变量
     * @param string $type 类型
     * @param array $param 参数
     * @return mixed
     */
    public function rsapri($var, $type, $param = [])
    {
        // 初始化变量
        $echo = [];
        if (!is_string($var) || !is_array($param)) return false;
        $predefined = [
            'type', 'secret',
        ];
        $param = \fxapp\Param::define([$param, $predefined], '1.2.2');
        $param['secret'] = $this->rsapem($param['secret'], 'private');
        $param['secret'] = openssl_pkey_get_private($param['secret']);
        $type = strtolower($type);
        switch ($type) {
            case 'encode':
                // 编码
                $var = str_split($var, 117);
                foreach ($var as $value) {
                    $entrys = null;
                    // 解析填充字符
                    switch ($param['type']) {
                        case OPENSSL_NO_PADDING:
                            // 填充-自定义
                            $predefined = [
                                'pad' => "\0",
                            ];
                            $param = \fxapp\Param::define([$param, $predefined], '1.1.2');
                            $value = str_pad($value, 128, $param['pad'], STR_PAD_LEFT);
                            break;
                    }
                    openssl_private_encrypt($value, $entrys, $param['secret'], $param['type']);
                    $echo[] = $entrys;
                }
                break;
            case 'decode':
                // 解码
                $var = str_split($var, 128);
                foreach ($var as $value) {
                    $entrys = null;
                    openssl_private_decrypt($value, $entrys, $param['secret'], $param['type']);
                    // 解析填充字符
                    switch ($param['type']) {
                        case OPENSSL_NO_PADDING:
                            // 填充-自定义
                            $predefined = [
                                'pad' => "\0",
                            ];
                            $param = \fxapp\Param::define([$param, $predefined], '1.1.2');
                            $entrys = ltrim($entrys, $param['pad']);
                            break;
                    }
                    $echo[] = $entrys;
                }
                break;
        }
        $echo = implode('', $echo);
        return $echo;
    }

    /**
     * 解析数据-RSA公钥
     * @param mixed $var 变量
     * @param string $type 类型
     * @param array $param 参数
     * @return mixed
     */
    public function rsapub($var, $type, $param = [])
    {
        // 初始化变量
        $echo = [];
        if (!is_string($var) || !is_array($param)) return false;
        $predefined = [
            'type', 'secret',
        ];
        $param = \fxapp\Param::define([$param, $predefined], '1.2.2');
        $param['secret'] = $this->rsapem($param['secret'], 'public');
        $param['secret'] = openssl_pkey_get_public($param['secret']);
        $type = strtolower($type);
        switch ($type) {
            case 'encode':
                // 编码
                $var = str_split($var, 117);
                foreach ($var as $value) {
                    $entrys = null;
                    // 解析填充字符
                    switch ($param['type']) {
                        case OPENSSL_NO_PADDING:
                            // 填充-自定义
                            $predefined = [
                                'pad' => "\0",
                            ];
                            $param = \fxapp\Param::define([$param, $predefined], '1.1.2');
                            $value = str_pad($value, 128, $param['pad'], STR_PAD_LEFT);
                            break;
                    }
                    openssl_public_encrypt($value, $entrys, $param['secret'], $param['type']);
                    $echo[] = $entrys;
                }
                break;
            case 'decode':
                // 解码
                $var = str_split($var, 128);
                foreach ($var as $value) {
                    $entrys = null;
                    openssl_public_decrypt($value, $entrys, $param['secret'], $param['type']);
                    // 解析填充字符
                    switch ($param['type']) {
                        case OPENSSL_NO_PADDING:
                            // 填充-自定义
                            $predefined = [
                                'pad' => "\0",
                            ];
                            $param = \fxapp\Param::define([$param, $predefined], '1.1.2');
                            $entrys = ltrim($entrys, $param['pad']);
                            break;
                    }
                    $echo[] = $entrys;
                }
                break;
        }
        $echo = implode('', $echo);
        return $echo;
    }

    /**
     * 解析数据-RSA密钥
     * @param mixed $var 变量
     * @param string $type 类型
     * @return mixed
     */
    public function rsapem($var, $type)
    {
        // 初始化变量
        if (!is_string($var)) return false;
        $type = strtolower($type);
        switch ($type) {
            case 'private':
                // 私钥
                if (is_file($var)) {
                    $var = file_get_contents($var);
                }
                $var = str_replace("\n", '', $var);
                $var = str_replace("-----BEGIN RSA PRIVATE KEY-----", '', $var);
                $var = str_replace("-----END RSA PRIVATE KEY-----", '', $var);
                $var = "-----BEGIN RSA PRIVATE KEY-----\n" .
                    wordwrap($var, 64, "\n", true) .
                    "\n-----END RSA PRIVATE KEY-----";
                break;
            case 'public':
                // 公钥
                if (is_file($var)) {
                    $var = file_get_contents($var);
                }
                $var = str_replace("\n", '', $var);
                $var = str_replace("-----BEGIN PUBLIC KEY-----", '', $var);
                $var = str_replace("-----END PUBLIC KEY-----", '', $var);
                $var = "-----BEGIN PUBLIC KEY-----\n" .
                    wordwrap($var, 64, "\n", true) .
                    "\n-----END PUBLIC KEY-----";
                break;
        }
        return $var;
    }
}
