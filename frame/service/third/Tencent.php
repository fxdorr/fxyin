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
namespace fxyin\service\third;

use fxyin\service\Third;

/**
 * 腾讯
 * @return mixed
 */
class Tencent extends Third
{
    /**
     * 服务
     * @param string $name 服务名称
     * @return void|Qq|WeChat
     */
    public function service($name)
    {
        $data = $this->data;
        $supplier = $this->supplier;
        $name = strtolower($name);
        switch ($name) {
            case 'qq':
                return new Qq($data, $supplier);
            case 'wechat':
                return new WeChat($data, $supplier);
        }
    }
}

/**
 * QQ
 * @return mixed
 */
class Qq extends Tencent
{
    /**
     * WebInfo
     * @param string $entry['access_token'] 接口调用凭证
     * @param string $entry['openid'] 授权用户唯一标识
     * @return mixed
     */
    public function webAuthInfo()
    {
        // 初始化变量
        $entry = $this->data;
        $echo = \fxapp\Server::echo();
        $record = $this->_webAuthInfo($entry);
        if (isset($record[0]) && is_bool($record[0])) {
            return $record;
        } else if (isset($record['data']['ret']) && $record['data']['ret']) {
            $echo[0] = false;
            $echo[1] = 1002;
            $echo[2] = $record['data']['msg'];
            $echo[3] = $record;
            return $echo;
        } else {
            $echo[2] = \fxapp\Base::lang(['request', 'success']);
            $echo[3] = $record;
            return $echo;
        }
    }

    /**
     * WebInfo
     * @param string $entry['access_token'] 接口调用凭证
     * @param string $entry['openid'] 授权用户唯一标识
     * @return mixed
     */
    private function _webAuthInfo($entry)
    {
        // 初始化变量
        $conf['param'] = '';
        $echo = \fxapp\Server::echo();
        $predefined = [
            'access_token', 'openid',
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.2.2');
        $tray['access_token'] = $entry['access_token'];
        $tray['openid'] = $entry['openid'];
        $pempty = \fxapp\Data::paramEmpty($tray);
        if (!$pempty[0]) return $pempty;
        // 应用钥匙
        $conf['app_key'] = \fxapp\Base::config('third.qq.web_auth_info.app_key');
        // 接口域
        $conf['domain'] = \fxapp\Base::config('third.qq.web_auth_info.domain');
        $conf['data']['access_token'] = $tray['access_token'];
        $conf['data']['openid'] = $tray['openid'];
        $conf['data']['oauth_consumer_key'] = $conf['app_key'];
        // 拼接请求域
        foreach ($conf['data'] as $key => $value) {
            $conf['param'] = \fxapp\Text::splice($conf['param'], $key . '=' . $value, '&');
        }
        $conf['domain'] = \fxapp\Text::splice($conf['domain'], $conf['param'], '?');
        $response = \fxapp\Service::http($conf['domain'], '', [], 'get');
        $response = json_decode($response, true);
        return $response;
    }
}

/**
 * 微信
 * @return mixed
 */
class WeChat extends Tencent
{
    /**
     * Web授权
     * @return mixed
     */
    public function webGrant()
    {
        // 初始化变量
        $entry = $this->data;
        $conf['param'] = '';
        $echo = \fxapp\Server::echo();
        $predefined = [
            'url_redirect',
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.2.2');
        // 应用钥匙
        $conf['app_key'] = \fxapp\Base::config('third.wechat.web_grant_token.app_key');
        // 应用授权作用域
        $conf['scope'] = \fxapp\Base::config('third.wechat.web_grant_token.scope');
        // 重定向状态码
        $conf['state'] = \fxapp\Base::config('third.wechat.web_grant_token.state');
        // 返回类型
        $conf['response_type'] = \fxapp\Base::config('third.wechat.web_grant_token.response_type');
        // 重定向地址
        $conf['url_redirect'] = $entry['url_redirect'];
        $predefined = [
            'url_redirect' => \fxapp\Base::config('third.wechat.web_grant_token.url_redirect'),
        ];
        $conf = \fxapp\Param::define([$conf, $predefined], '1.1.2');
        // 接口域
        $conf['domain'] = \fxapp\Base::config('third.wechat.web_grant_token.domain');
        // 拼接请求域
        $conf['param'] = \fxapp\Text::splice($conf['param'], 'appid=' . $conf['app_key'], '&');
        $conf['param'] = \fxapp\Text::splice($conf['param'], 'redirect_uri=' . $conf['url_redirect'], '&');
        $conf['param'] = \fxapp\Text::splice($conf['param'], 'response_type=' . $conf['response_type'], '&');
        $conf['param'] = \fxapp\Text::splice($conf['param'], 'scope=' . $conf['scope'], '&');
        $conf['param'] = \fxapp\Text::splice($conf['param'], 'state=' . $conf['state'], '&');
        $conf['domain'] = \fxapp\Text::splice($conf['domain'], $conf['param'] . '#wechat_redirect', '?');
        $echo[2] = \fxapp\Base::lang(['request', 'success']);
        $echo[3] = $conf;
        return $echo;
    }

    /**
     * Web鉴权
     * @param string $entry['code'] 授权码
     * @return mixed
     */
    public function webAuth()
    {
        // 初始化变量
        $entry = $this->data;
        $echo = \fxapp\Server::echo();
        $predefined = [
            'code',
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.2.2');
        $tray['code'] = $entry['code'];
        $pempty = \fxapp\Data::paramEmpty($tray);
        if (!$pempty[0]) return $pempty;
        $tokens = $this->_webAuthToken($entry);
        if (isset($tokens['errcode'])) {
            $echo[0] = false;
            $echo[1] = 1002;
            $echo[2] = $tokens['errmsg'];
            $echo[3] = $tokens;
            return $echo;
        }
        $entrys = $this->_webAuthInfo($tokens);
        $tray['accessToken'] = $tokens['access_token'];
        $tray['openId'] = $tokens['openid'];
        $tray['unionId'] = $tokens['unionid'];
        $tray['nickname'] = $entrys['nickname'];
        $data['account'] = $tray['unionId'];
        $echo[2] = \fxapp\Base::lang(['request', 'success']);
        $echo[3] = $data;
        return $echo;
    }

    /**
     * WebAuthToken
     * @param string $entry['code'] 授权码
     * @return mixed
     */
    public function webAuthToken()
    {
        // 初始化变量
        $entry = $this->data;
        $echo = \fxapp\Server::echo();
        $record = $this->_webAuthToken($entry);
        if (isset($record[0]) && is_bool($record[0])) {
            return $record;
        } else if (isset($record['errcode'])) {
            $echo[0] = false;
            $echo[1] = 1004;
            $echo[2] = $record['errmsg'];
            $echo[3] = $record;
            return $echo;
        } else {
            $echo[2] = \fxapp\Base::lang(['request', 'success']);
            $echo[3] = $record;
            return $echo;
        }
    }

    /**
     * WebAuthToken
     * @param string $entry['code'] 授权码
     * @return mixed
     */
    private function _webAuthToken($entry)
    {
        // 初始化变量
        $conf['param'] = '';
        $echo = \fxapp\Server::echo();
        $predefined = [
            'code',
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.2.2');
        $tray['code'] = $entry['code'];
        $pempty = \fxapp\Data::paramEmpty($tray);
        if (!$pempty[0]) return $pempty;
        // 应用钥匙
        $conf['app_key'] = \fxapp\Base::config('third.wechat.web_auth_token.app_key');
        // 应用密钥
        $conf['app_secret'] = \fxapp\Base::config('third.wechat.web_auth_token.app_secret');
        // 授权类型
        $conf['grant_type'] = \fxapp\Base::config('third.wechat.web_auth_token.grant_type');
        // 接口域
        $conf['domain'] = \fxapp\Base::config('third.wechat.web_auth_token.domain');
        $conf['data']['appid'] = $conf['app_key'];
        $conf['data']['secret'] = $conf['app_secret'];
        $conf['data']['code'] = $tray['code'];
        $conf['data']['grant_type'] = $conf['grant_type'];
        // 拼接请求域
        foreach ($conf['data'] as $key => $value) {
            $conf['param'] = \fxapp\Text::splice($conf['param'], $key . '=' . $value, '&');
        }
        $conf['domain'] = \fxapp\Text::splice($conf['domain'], $conf['param'], '?');
        $response = \fxapp\Service::http($conf['domain'], '', [], 'get');
        $response = json_decode($response, true);
        return $response;
    }

    /**
     * WebAuthInfo
     * @param string $entry['access_token'] 接口调用凭证
     * @param string $entry['openid'] 授权用户唯一标识
     * @return mixed
     */
    public function webAuthInfo()
    {
        // 初始化变量
        $entry = $this->data;
        $echo = \fxapp\Server::echo();
        $record = $this->_webAuthInfo($entry);
        if (isset($record[0]) && is_bool($record[0])) {
            return $record;
        } else {
            $echo[2] = \fxapp\Base::lang(['request', 'success']);
            $echo[3] = $record;
            return $echo;
        }
    }

    /**
     * WebAuthInfo
     * @param string $entry['access_token'] 接口调用凭证
     * @param string $entry['openid'] 授权用户唯一标识
     * @return mixed
     */
    private function _webAuthInfo($entry)
    {
        // 初始化变量
        $conf['param'] = '';
        $echo = \fxapp\Server::echo();
        $predefined = [
            'access_token', 'openid',
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.2.2');
        $tray['access_token'] = $entry['access_token'];
        $tray['openid'] = $entry['openid'];
        $pempty = \fxapp\Data::paramEmpty($tray);
        if (!$pempty[0]) return $pempty;
        // 语言
        $conf['lang'] = \fxapp\Base::config('third.wechat.web_auth_info.lang');
        // 接口域
        $conf['domain'] = \fxapp\Base::config('third.wechat.web_auth_info.domain');
        $conf['data']['access_token'] = $tray['access_token'];
        $conf['data']['openid'] = $tray['openid'];
        $conf['data']['lang'] = $conf['lang'];
        // 拼接请求域
        foreach ($conf['data'] as $key => $value) {
            $conf['param'] = \fxapp\Text::splice($conf['param'], $key . '=' . $value, '&');
        }
        $conf['domain'] = \fxapp\Text::splice($conf['domain'], $conf['param'], '?');
        $response = \fxapp\Service::http($conf['domain'], '', [], 'get');
        $response = json_decode($response, true);
        return $response;
    }

    /**
     * JssdkAuthToken
     * @return mixed
     */
    public function jssdkAuthToken()
    {
        // 初始化变量
        $entry = $this->data;
        $echo = \fxapp\Server::echo();
        $record = $this->_jssdkAuthToken($entry);
        if (isset($record[0]) && is_bool($record[0])) {
            return $record;
        } else if (isset($record['errcode'])) {
            $echo[0] = false;
            $echo[1] = 1004;
            $echo[2] = $record['errmsg'];
            $echo[3] = $record;
            return $echo;
        } else {
            $echo[2] = \fxapp\Base::lang(['request', 'success']);
            $echo[3] = $record;
            return $echo;
        }
    }

    /**
     * JssdkAuthToken
     * @return mixed
     */
    private function _jssdkAuthToken($entry)
    {
        // 初始化变量
        $conf['param'] = '';
        $echo = \fxapp\Server::echo();
        // 应用钥匙
        $conf['app_key'] = \fxapp\Base::config('third.wechat.jssdk_grant_access_token.app_key');
        // 应用密钥
        $conf['app_secret'] = \fxapp\Base::config('third.wechat.jssdk_grant_access_token.app_secret');
        // 授权类型
        $conf['grant_type'] = \fxapp\Base::config('third.wechat.jssdk_grant_access_token.grant_type');
        // 接口域
        $conf['domain'] = \fxapp\Base::config('third.wechat.jssdk_grant_access_token.domain');
        $conf['data']['appid'] = $conf['app_key'];
        $conf['data']['secret'] = $conf['app_secret'];
        $conf['data']['grant_type'] = $conf['grant_type'];
        // 拼接请求域
        foreach ($conf['data'] as $key => $value) {
            $conf['param'] = \fxapp\Text::splice($conf['param'], $key . '=' . $value, '&');
        }
        $conf['domain'] = \fxapp\Text::splice($conf['domain'], $conf['param'], '?');
        $response = \fxapp\Service::http($conf['domain'], '', [], 'get');
        $response = json_decode($response, true);
        return $response;
    }

    /**
     * JssdkAuthTicket
     * @param string $entry['code'] 授权码
     * @return mixed
     */
    public function jssdkAuthTicket()
    {
        // 初始化变量
        $entry = $this->data;
        $echo = \fxapp\Server::echo();
        $record = $this->_jssdkAuthTicket($entry);
        if (isset($record[0]) && is_bool($record[0])) {
            return $record;
        } else if (isset($record['errcode']) && $record['errcode'] != 0) {
            $echo[0] = false;
            $echo[1] = 1004;
            $echo[2] = $record['errmsg'];
            $echo[3] = $record;
            return $echo;
        } else {
            $echo[2] = \fxapp\Base::lang(['request', 'success']);
            $echo[3] = $record;
            return $echo;
        }
    }

    /**
     * JssdkAuthTicket
     * @param string $entry['access_token'] 授权凭证
     */
    private function _jssdkAuthTicket($entry)
    {
        // 初始化变量
        $conf['param'] = '';
        $echo = \fxapp\Server::echo();
        $predefined = [
            'access_token',
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.2.2');
        $tray['access_token'] = $entry['access_token'];
        $pempty = \fxapp\Data::paramEmpty($tray);
        if (!$pempty[0]) return $pempty;
        // 授权类型
        $conf['type'] = \fxapp\Base::config('third.wechat.jssdk_grant_jsapi_ticket.type');
        // 接口域
        $conf['domain'] = \fxapp\Base::config('third.wechat.jssdk_grant_jsapi_ticket.domain');
        $conf['data']['access_token'] = $tray['access_token'];
        $conf['data']['type'] = $conf['type'];
        // 拼接请求域
        foreach ($conf['data'] as $key => $value) {
            $conf['param'] = \fxapp\Text::splice($conf['param'], $key . '=' . $value, '&');
        }
        $conf['domain'] = \fxapp\Text::splice($conf['domain'], $conf['param'], '?');
        $response = \fxapp\Service::http($conf['domain'], '', [], 'get');
        $response = json_decode($response, true);
        return $response;
    }

    /**
     * 支付申请
     * @param string $entry['sn'] 订单SN
     * @param string $entry['money'] 支付金额
     * @param string $entry['dateline'] 记录时间
     * @param string $entry['openid'] 开发ID
     * @return mixed
     */
    public function payApply()
    {
        // 初始化变量
        $entry = $this->data;
        $echo = \fxapp\Server::echo();
        $predefined = [
            'sn', 'money', 'dateline',
            'openid',
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.2.2');
        $tray['sn'] = $entry['sn'];
        $tray['money'] = $entry['money'];
        $tray['dateline'] = $entry['dateline'];
        $pempty = \fxapp\Data::paramEmpty($tray);
        if (!$pempty[0]) return $pempty;
        $tray['openid'] = $entry['openid'];
        if ($tray['openid']) {
            $record = $this->_webPayApply($entry);
        } else {
            $record = $this->_appPayApply($entry);
        }
        return $record;
    }

    /**
     * Web支付申请
     * @param array $entry['sn'] 订单SN
     * @param array $entry['money'] 支付金额
     * @param array $entry['dateline'] 记录时间
     * @param array $entry['openid'] 开放ID
     * @return mixed
     */
    private function _webPayApply($entry)
    {
        // 初始化变量
        $entry = $this->data;
        $echo = \fxapp\Server::echo();
        $predefined = [
            'sn', 'money', 'dateline',
            'openid',
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.2.2');
        $tray['sn'] = $entry['sn'];
        $tray['money'] = $entry['money'];
        $tray['dateline'] = $entry['dateline'];
        $tray['openid'] = $entry['openid'];
        $pempty = \fxapp\Data::paramEmpty($tray);
        if (!$pempty[0]) return $pempty;
        // 附加数据
        $conf['attach'] = \fxapp\Base::config('third.wechat.app_pay.attach');
        // 商品描述
        $conf['body'] = \fxapp\Base::config('third.wechat.app_pay.body');
        // 回调地址
        $conf['url_notify'] = \fxapp\Base::config('third.wechat.app_pay.url_notify');
        // SDK地址
        $conf['url_sdk'] = \fxapp\Base::config('third.wechat.app_pay.url_sdk');
        // 初始化环境变量
        $predefined = [
            'body' => $conf['body'], 'attach' => $conf['attach'], 'url_notify' => $conf['url_notify'],
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.1.2');
        $tray['2_1']['body'] = $entry['body'];
        $tray['2_1']['attach'] = $entry['attach'];
        $tray['2_1']['sn'] = $tray['sn'];
        $tray['2_1']['money'] = $tray['money'] * 100;
        $tray['2_1']['dateline'] = date("YmdHis", $tray['dateline']);
        $tray['2_1']['url_notify'] = $entry['url_notify'];
        $tray['2_1']['openid'] = $tray['openid'];
        // 在微信系统中下单
        ini_set('date.timezone', 'Asia/Shanghai');
        // 加载SDK
        \fxapp\Base::load($conf['url_sdk']);
        // 统一下单
        $input = new \WxPayUnifiedOrder();
        // 设置商品或支付单简要描述
        $input->SetBody($tray['2_1']['body']);
        // 设置附加数据
        $input->SetAttach($tray['2_1']['attach']);
        // 设置商户系统内部的订单号
        $input->SetOut_trade_no($tray['2_1']['sn']);
        // 设置订单总金额
        $input->SetTotal_fee($tray['2_1']['money']);
        // 设置订单生成时间
        $input->SetTime_start($tray['2_1']['dateline']);
        // 设置接收微信支付异步通知回调地址
        $input->SetNotify_url($tray['2_1']['url_notify']);
        // 用户在商户appid下的唯一标识
        $input->SetOpenid($tray['2_1']['openid']);
        // 设置取值如下：JSAPI，NATIVE，APP
        $input->SetTrade_type("JSAPI");
        // 交互订单
        $order = \WxPayApi::unifiedOrder($input, 15);
        if ($order && $order['return_code'] == 'SUCCESS' && $order['result_code'] == 'SUCCESS') {
            // 签名参数
            $tray['3_1']['appId'] = $order['appid'];
            $tray['3_1']['timeStamp'] = time();
            $tray['3_1']['nonceStr'] = $order['nonce_str'];
            $tray['3_1']['package'] = 'prepay_id=' . $order['prepay_id'];
            $tray['3_1']['signType'] = 'MD5';
            ksort($tray['3_1']);
            $buff = "";
            foreach ($tray['3_1'] as $k => $v) {
                if ($k != "sign" && $v != "" && !is_array($v)) {
                    $buff .= $k . "=" . $v . "&";
                }
            }
            $buff = trim($buff, "&");
            // 签名步骤二：在string后加入KEY
            $string = $buff . "&key=" . \WxPayConfig::KEY;
            // 签名步骤三：MD5加密
            $string = md5($string);
            // 签名步骤四：所有字符转为大写
            $getsign = strtoupper($string);
            // 返回参数
            $tray['3_2']['app_id'] = $tray['3_1']['appId'];
            $tray['3_2']['time_stamp'] = $tray['3_1']['timeStamp'];
            $tray['3_2']['nonce_str'] = $tray['3_1']['nonceStr'];
            $tray['3_2']['package'] = $tray['3_1']['package'];
            $tray['3_2']['sign_type'] = $tray['3_1']['signType'];
            $tray['3_2']['sign'] = $getsign;
            $echo[2] = \fxapp\Base::lang(['pay', 'success']);
            $echo[3] = $tray['3_2'];
            return $echo;
        } else {
            $echo[0] = false;
            $echo[1] = 1002;
            $echo[2] = $order['return_msg'];
            $echo[3] = $order;
            return $echo;
        }
    }

    /**
     * App支付申请
     * @param array $entry['sn'] 订单SN
     * @param array $entry['money'] 支付金额
     * @param array $entry['dateline'] 记录时间
     * @return mixed
     */
    private function _appPayApply($entry)
    {
        // 初始化变量
        $entry = $this->data;
        $echo = \fxapp\Server::echo();
        $predefined = [
            'sn', 'money', 'dateline',
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.2.2');
        $tray['sn'] = $entry['sn'];
        $tray['money'] = $entry['money'];
        $tray['dateline'] = $entry['dateline'];
        $pempty = \fxapp\Data::paramEmpty($tray);
        if (!$pempty[0]) return $pempty;
        // 附加数据
        $conf['attach'] = \fxapp\Base::config('third.wechat.app_pay.attach');
        // 商品描述
        $conf['body'] = \fxapp\Base::config('third.wechat.app_pay.body');
        // 回调地址
        $conf['url_notify'] = \fxapp\Base::config('third.wechat.app_pay.url_notify');
        // SDK地址
        $conf['url_sdk'] = \fxapp\Base::config('third.wechat.app_pay.url_sdk');
        // 初始化环境变量
        $predefined = [
            'body' => $conf['body'], 'attach' => $conf['attach'], 'url_notify' => $conf['url_notify'],
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.1.2');
        $tray['2_1']['body'] = $entry['body'];
        $tray['2_1']['attach'] = $entry['attach'];
        $tray['2_1']['sn'] = $tray['sn'];
        $tray['2_1']['money'] = $tray['money'] * 100;
        $tray['2_1']['dateline'] = date("YmdHis", $tray['dateline']);
        $tray['2_1']['url_notify'] = $entry['url_notify'];
        // 在微信系统中下单
        ini_set('date.timezone', 'Asia/Shanghai');
        // 加载SDK
        \fxapp\Base::load($conf['url_sdk']);
        // 统一下单
        $input = new \WxPayUnifiedOrder();
        // 设置商品或支付单简要描述
        $input->SetBody($tray['2_1']['body']);
        // 设置附加数据
        $input->SetAttach($tray['2_1']['attach']);
        // 设置商户系统内部的订单号
        $input->SetOut_trade_no($tray['2_1']['sn']);
        // 设置订单总金额
        $input->SetTotal_fee($tray['2_1']['money']);
        // 设置订单生成时间
        $input->SetTime_start($tray['2_1']['dateline']);
        // 设置接收微信支付异步通知回调地址
        $input->SetNotify_url($tray['2_1']['url_notify']);
        // 设置取值如下：JSAPI，NATIVE，APP
        $input->SetTrade_type("APP");
        // 交互订单
        $order = \WxPayApi::unifiedOrder($input, 15);
        if ($order && $order['return_code'] == 'SUCCESS' && $order['result_code'] == 'SUCCESS') {
            // 签名参数
            $tray['3_1']['appid'] = $order['appid'];
            $tray['3_1']['partnerid'] = $order['mch_id'];
            $tray['3_1']['prepayid'] = $order['prepay_id'];
            $tray['3_1']['package'] = 'Sign=WXPay';
            $tray['3_1']['noncestr'] = $order['nonce_str'];
            $tray['3_1']['timestamp'] = time();
            ksort($tray['3_1']);
            $buff = "";
            foreach ($tray['3_1'] as $k => $v) {
                if ($k != "sign" && $v != "" && !is_array($v)) {
                    $buff .= $k . "=" . $v . "&";
                }
            }
            $buff = trim($buff, "&");
            // 签名步骤二：在string后加入KEY
            $string = $buff . "&key=" . \WxPayConfig::KEY;
            // 签名步骤三：MD5加密
            $string = md5($string);
            // 签名步骤四：所有字符转为大写
            $getsign = strtoupper($string);
            // 返回参数
            $tray['3_2']['app_id'] = $order['appid'];
            $tray['3_2']['mch_id'] = $order['mch_id'];
            $tray['3_2']['nonce_str'] = $order['nonce_str'];
            $tray['3_2']['prepay_id'] = $order['prepay_id'];
            $tray['3_2']['sign'] = $getsign;
            $tray['3_2']['sign_type'] = 'MD5';
            $tray['3_2']['time_stamp'] = $tray['3_1']['timestamp'];
            $tray['3_2']['package'] = $tray['3_1']['package'];
            $tray['3_2']['trade_type'] = $order['trade_type'];
            $echo[2] = \fxapp\Base::lang(['pay', 'success']);
            $echo[3] = $tray['3_2'];
            return $echo;
        } else {
            $echo[0] = false;
            $echo[1] = 1002;
            $echo[2] = $order['return_msg'];
            $echo[3] = $order;
            return $echo;
        }
    }

    /**
     * 支付回调
     * @return mixed
     */
    public function payCallback()
    {
        // 初始化变量
        $echo = \fxapp\Server::echo();
        // 商户号
        $conf['mch_id'] = \fxapp\Base::config('third.wechat.app_pay.mch_id');
        // 服务处理
        if (isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
            $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        } else {
            $xml = file_get_contents('php://input');
        }
        if (empty($xml)) {
            $echo[0] = false;
            $echo[1] = 1002;
            $echo[2] = \fxapp\Base::lang(['not2', 'find', 'parameter']);
            return $echo;
        }
        $res = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $order = get_object_vars($res);
        // 通过匹配商户号确定是来自微信公众号还是APP,然后引入相对应的配置文件
        if ($order['mch_id'] == $conf['mch_id']) {
            // 公众号支付返回，由微信支付系统触发回调
            // SDK地址
            $conf['url_sdk'] = \fxapp\Base::config('third.wechat.app_pay.url_sdk');
            // SDK通知
            $conf['url_sdk_notify'] = \fxapp\Base::config('third.wechat.app_pay.url_sdk_notify');
        } else {
            // APP支付回调，由微信支付系统触发回调
            // SDK地址
            $conf['url_sdk'] = \fxapp\Base::config('third.wechat.app_pay.url_sdk');
            // SDK通知
            $conf['url_sdk_notify'] = \fxapp\Base::config('third.wechat.app_pay.url_sdk_notify');
        }
        // 加载SDK
        \fxapp\Base::load($conf['url_sdk']);
        \fxapp\Base::load($conf['url_sdk_notify']);
        $input = new \WxPayOrderQuery();
        $input->SetTransaction_id($order['transaction_id']);
        // 设置超时时间为15s
        $order = \WxPayApi::orderQuery($input, 15);
        if (array_key_exists("return_code", $order) && array_key_exists("result_code", $order) && $order["return_code"] == "SUCCESS" && $order["result_code"] == "SUCCESS") {
            $echo[2] = \fxapp\Base::lang(['pay', 'success']);
            $echo[3] = $order;
            return $echo;
        } else {
            $echo[0] = false;
            $echo[1] = 1002;
            $echo[2] = $order['return_msg'];
            $echo[3] = $order;
            return $echo;
        }
    }

    /**
     * 退款申请
     * @param string $entry['pay_sn'] 支付订单SN
     * @param string $entry['deal_sn'] 交易订单SN
     * @param string $entry['refund_sn'] 退款订单SN
     * @param string $entry['deal_money'] 交易订单金额
     * @param string $entry['refund_money'] 退款订单金额
     * @param string $entry['refund_remark'] 退款订单备注
     * @return mixed
     */
    public function refundApply()
    {
        // 初始化变量
        $entry = $this->data;
        $echo = \fxapp\Server::echo();
        $predefined = [
            'pay_sn', 'deal_sn', 'refund_sn',
            'deal_money', 'refund_money', 'refund_remark',
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.2.2');
        $tray['refund_sn'] = $entry['refund_sn'];
        $tray['deal_money'] = $entry['deal_money'];
        $tray['refund_money'] = $entry['refund_money'];
        $pempty = \fxapp\Data::paramEmpty($tray);
        if (!$pempty[0]) return $pempty;
        $tray['pay_sn'] = $entry['pay_sn'];
        $tray['deal_sn'] = $entry['deal_sn'];
        $pempty = \fxapp\Data::paramExist([$tray['pay_sn'], $tray['deal_sn']]);
        if (!$pempty[0]) return $pempty;
        $tray['refund_remark'] = $entry['refund_remark'];
        $record = $this->_refundApply($entry);
        return $record;
    }

    /**
     * 退款申请
     * @param string $entry['pay_sn'] 支付订单SN
     * @param string $entry['deal_sn'] 交易订单SN
     * @param string $entry['refund_sn'] 退款订单SN
     * @param string $entry['deal_money'] 交易订单金额
     * @param string $entry['refund_money'] 退款订单金额
     * @param string $entry['refund_remark'] 退款订单备注
     * @return mixed
     */
    private function _refundApply($entry)
    {
        // 初始化变量
        $entry = $this->data;
        $echo = \fxapp\Server::echo();
        $predefined = [
            'pay_sn', 'deal_sn', 'refund_sn',
            'deal_money', 'refund_money', 'refund_remark',
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.2.2');
        $tray['refund_sn'] = $entry['refund_sn'];
        $tray['deal_money'] = $entry['deal_money'];
        $tray['refund_money'] = $entry['refund_money'];
        $pempty = \fxapp\Data::paramEmpty($tray);
        if (!$pempty[0]) return $pempty;
        $tray['pay_sn'] = $entry['pay_sn'];
        $tray['deal_sn'] = $entry['deal_sn'];
        $pempty = \fxapp\Data::paramExist([$tray['pay_sn'], $tray['deal_sn']]);
        if (!$pempty[0]) return $pempty;
        $tray['refund_remark'] = $entry['refund_remark'];
        // 商户号
        $conf['mch_id'] = \fxapp\Base::config('third.wechat.app_pay.mch_id');
        // SDK地址
        $conf['url_sdk'] = \fxapp\Base::config('third.wechat.app_pay.url_sdk');
        // 初始化环境变量
        $tray['2_1']['mch_id'] = $conf['mch_id'];
        $tray['2_1']['refund_sn'] = $tray['refund_sn'];
        $tray['2_1']['deal_money'] = $tray['deal_money'] * 100;
        $tray['2_1']['refund_money'] = $tray['refund_money'] * 100;
        $tray['2_1']['pay_sn'] = $tray['pay_sn'];
        $tray['2_1']['deal_sn'] = $tray['deal_sn'];
        $tray['2_1']['refund_remark'] = $tray['refund_remark'];
        // 在微信系统中下单
        ini_set('date.timezone', 'Asia/Shanghai');
        // 加载SDK
        \fxapp\Base::load($conf['url_sdk']);
        // 申请退款
        $input = new \WxPayRefund();
        // 设置操作员帐号, 默认为商户号
        $input->SetOp_user_id($tray['2_1']['mch_id']);
        // 设置商户系统内部的订单号
        $input->SetOut_trade_no($tray['2_1']['deal_sn']);
        // 设置微信订单号
        $input->SetTransaction_id($tray['2_1']['pay_sn']);
        // 设置商户系统内部的退款单号
        $input->SetOut_refund_no($tray['2_1']['refund_sn']);
        // 设置订单总金额
        $input->SetTotal_fee($tray['2_1']['deal_money']);
        // 设置退款总金额
        $input->SetRefund_fee($tray['2_1']['refund_money']);
        // 交互订单
        $order = \WxPayApi::refund($input, 15);
        if ($order && $order['return_code'] == 'SUCCESS' && $order['result_code'] == 'SUCCESS') {
            $echo[2] = \fxapp\Base::lang(['pay', 'success']);
            $echo[3] = $order;
            return $echo;
        } else if (isset($order['err_code_des'])) {
            $echo[0] = false;
            $echo[1] = 1002;
            $echo[2] = $order['err_code_des'];
            $echo[3] = $order;
            return $echo;
        } else {
            $echo[0] = false;
            $echo[1] = 1002;
            $echo[2] = $order['return_msg'];
            $echo[3] = $order;
            return $echo;
        }
    }

    /**
     * 退款回调
     * @return mixed
     */
    public function refundCallback()
    {
        // 初始化变量
        $echo = \fxapp\Server::echo();
        $echo[0] = false;
        $echo[1] = 1002;
        $echo[2] = \fxapp\Base::lang(['pay', 'not2', 'open3']);
        return $echo;
    }
}
