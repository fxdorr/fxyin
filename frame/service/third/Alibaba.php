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
namespace fxyin\service\third;

use fxyin\service\Third;

/**
 * 阿里巴巴
 * @return mixed
 */
class Alibaba extends Third
{
    /**
     * 服务
     * @param string $name 服务名称
     * @return void|Alipay|Taobao|Aliyun
     */
    public function service($name)
    {
        $data = $this->data;
        $supplier = $this->supplier;
        $name = strtolower($name);
        switch ($name) {
            case 'alipay':
                return new Alipay($data, $supplier);
            case 'taobao':
                return new Taobao($data, $supplier);
            case 'aliyun':
                return new Aliyun($data, $supplier);
        }
    }
}

/**
 * 支付宝
 * @return mixed
 */
class Alipay extends Alibaba
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
        // 应用ID
        $conf['app_id'] = \fxapp\Base::config('third.alipay.web_grant_token.app_id');
        // 应用授权作用域
        $conf['scope'] = \fxapp\Base::config('third.alipay.web_grant_token.scope');
        // 重定向状态码
        $conf['state'] = \fxapp\Base::config('third.alipay.web_grant_token.state');
        // 重定向地址
        $conf['url_redirect'] = $entry['url_redirect'];
        $predefined = [
            'url_redirect' => \fxapp\Base::config('third.alipay.web_grant_token.url_redirect'),
        ];
        $conf = \fxapp\Param::define([$conf, $predefined], '1.1.2');
        // 接口域
        $conf['domain'] = \fxapp\Base::config('third.alipay.web_grant_token.domain');
        // 拼接请求域
        $conf['param'] = \fxapp\Text::splice($conf['param'], 'app_id=' . $conf['app_id'], '&');
        $conf['param'] = \fxapp\Text::splice($conf['param'], 'redirect_uri=' . $conf['url_redirect'], '&');
        $conf['param'] = \fxapp\Text::splice($conf['param'], 'scope=' . $conf['scope'], '&');
        $conf['param'] = \fxapp\Text::splice($conf['param'], 'state=' . $conf['state'], '&');
        $conf['domain'] = \fxapp\Text::splice($conf['domain'], $conf['param'], '?');
        $echo[2] = \fxapp\Base::lang(['request', 'success']);
        $echo[3] = $conf;
        return $echo;
    }

    /**
     * WebAuthToken
     * @param string $entry['auth_code'] 授权码
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
        } else if (isset($record['error_response']['code'])) {
            $echo[0] = false;
            $echo[1] = 1004;
            $echo[2] = $record['error_response']['sub_msg'];
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
     * @param string $entry['auth_code'] 授权码
     * @return mixed
     */
    private function _webAuthToken($entry)
    {
        // 初始化变量
        $conf['param'] = '';
        $echo = \fxapp\Server::echo();
        $predefined = [
            'auth_code',
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.2.2');
        $tray['auth_code'] = $entry['auth_code'];
        $pempty = \fxapp\Data::paramEmpty($tray);
        if (!$pempty[0]) return $pempty;
        // 应用ID
        $conf['app_id'] = \fxapp\Base::config('third.alipay.web_auth_token.app_id');
        // 签名类型
        $conf['sign_type'] = \fxapp\Base::config('third.alipay.web_auth_token.sign_type');
        // 授权类型
        $conf['grant_type'] = \fxapp\Base::config('third.alipay.web_auth_token.grant_type');
        // 商户私钥
        $conf['url_pri_key'] = \fxapp\Base::config('third.alipay.web_auth_token.url_pri_key');
        // SDK地址
        $conf['url_sdk'] = \fxapp\Base::config('third.alipay.web_auth_token.url_sdk');
        \fxapp\Base::load($conf['url_sdk']);
        // 服务处理
        $client = new \AopClient();
        // 支付宝分配给开发者的应用ID
        $client->appId = $conf['app_id'];
        // 商户生成签名字符串所使用的签名算法类型
        $client->signType = $conf['sign_type'];
        // 读取私钥文件
        $client->rsaPrivateKeyFilePath = $conf['url_pri_key'];
        $request = new \AlipaySystemOauthTokenRequest();
        $request->setCode($tray['auth_code']);
        $request->setGrantType($conf['grant_type']);
        $record = $client->execute($request);
        $record = \fxapp\Param::json(\fxapp\Param::json($record, 'encode'), 'decode');
        return $record;
    }

    /**
     * WebRefreshToken
     * @param string $entry['auth_code'] 授权码
     * @return mixed
     */
    public function webRefreshToken()
    {
        // 初始化变量
        $entry = $this->data;
        $echo = \fxapp\Server::echo();
        $record = $this->_webRefreshToken($entry);
        if (isset($record[0]) && is_bool($record[0])) {
            return $record;
        } else if (isset($record['error_response']['code'])) {
            $echo[0] = false;
            $echo[1] = 1004;
            $echo[2] = $record['error_response']['sub_msg'];
            $echo[3] = $record;
            return $echo;
        } else {
            $echo[2] = \fxapp\Base::lang(['request', 'success']);
            $echo[3] = $record;
            return $echo;
        }
    }

    /**
     * WebRefreshToken
     * @param string $entry['refresh_token'] 刷新令牌
     * @return mixed
     */
    private function _webRefreshToken($entry)
    {
        // 初始化变量
        $conf['param'] = '';
        $echo = \fxapp\Server::echo();
        $predefined = [
            'refresh_token',
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.2.2');
        $tray['refresh_token'] = $entry['refresh_token'];
        $pempty = \fxapp\Data::paramEmpty($tray);
        if (!$pempty[0]) return $pempty;
        // 应用ID
        $conf['app_id'] = \fxapp\Base::config('third.alipay.web_refresh_token.app_id');
        // 签名类型
        $conf['sign_type'] = \fxapp\Base::config('third.alipay.web_refresh_token.sign_type');
        // 授权类型
        $conf['grant_type'] = \fxapp\Base::config('third.alipay.web_refresh_token.grant_type');
        // 商户私钥
        $conf['url_pri_key'] = \fxapp\Base::config('third.alipay.web_refresh_token.url_pri_key');
        // SDK地址
        $conf['url_sdk'] = \fxapp\Base::config('third.alipay.web_refresh_token.url_sdk');
        \fxapp\Base::load($conf['url_sdk']);
        // 服务处理
        $client = new \AopClient();
        // 支付宝分配给开发者的应用ID
        $client->appId = $conf['app_id'];
        // 商户生成签名字符串所使用的签名算法类型
        $client->signType = $conf['sign_type'];
        // 读取私钥文件
        $client->rsaPrivateKeyFilePath = $conf['url_pri_key'];
        $request = new \AlipaySystemOauthTokenRequest();
        $request->setGrantType($conf['grant_type']);
        $request->setRefreshToken($tray['refresh_token']);
        $record = $client->execute($request);
        $record = \fxapp\Param::json(\fxapp\Param::json($record, 'encode'), 'decode');
        return $record;
    }

    /**
     * WebAuthInfo
     * @param string $entry['access_token'] 授权令牌
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
        } else if (isset($record['error_response']['code'])) {
            $echo[0] = false;
            $echo[1] = 1004;
            $echo[2] = $record['error_response']['sub_msg'];
            $echo[3] = $record;
            return $echo;
        } else if (isset($record['alipay_user_info_share_response']['code']) && $record['alipay_user_info_share_response']['code'] != '10000') {
            $echo[0] = false;
            $echo[1] = 1004;
            $echo[2] = $record['alipay_user_info_share_response']['sub_msg'];
            $echo[3] = $record;
            return $echo;
        } else {
            $echo[2] = \fxapp\Base::lang(['request', 'success']);
            $echo[3] = $record;
            return $echo;
        }
    }

    /**
     * WebAuthInfo
     * @param string $entry['access_token'] 授权令牌
     * @return mixed
     */
    private function _webAuthInfo($entry)
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
        // 应用ID
        $conf['app_id'] = \fxapp\Base::config('third.alipay.web_auth_info.app_id');
        // 签名类型
        $conf['sign_type'] = \fxapp\Base::config('third.alipay.web_auth_info.sign_type');
        // 商户私钥
        $conf['url_pri_key'] = \fxapp\Base::config('third.alipay.web_auth_info.url_pri_key');
        // SDK地址
        $conf['url_sdk'] = \fxapp\Base::config('third.alipay.web_auth_info.url_sdk');
        \fxapp\Base::load($conf['url_sdk']);
        // 服务处理
        $client = new \AopClient();
        // 支付宝分配给开发者的应用ID
        $client->appId = $conf['app_id'];
        // 商户生成签名字符串所使用的签名算法类型
        $client->signType = $conf['sign_type'];
        // 读取私钥文件
        $client->rsaPrivateKeyFilePath = $conf['url_pri_key'];
        $request = new \AlipayUserInfoShareRequest();
        $record = $client->execute($request, $tray['access_token']);
        $record = \fxapp\Param::json(\fxapp\Param::json($record, 'encode'), 'decode');
        return $record;
    }

    /**
     * 支付-申请
     * @param string $entry['sn'] 订单SN
     * @param string $entry['money'] 支付金额
     * @return mixed
     */
    public function payApply()
    {
        // 初始化变量
        $entry = $this->data;
        $echo = \fxapp\Server::echo();
        $predefined = [
            'sn', 'money',
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.2.2');
        $tray['sn'] = $entry['sn'];
        $tray['money'] = $entry['money'];
        $pempty = \fxapp\Data::paramEmpty($tray);
        if (!$pempty[0]) return $pempty;
        // 应用ID
        $conf['app_id'] = \fxapp\Base::config('third.alipay.app_pay.app_id');
        // 商品描述
        $conf['body'] = \fxapp\Base::config('third.alipay.app_pay.body');
        // 请求字符集
        $conf['charset'] = \fxapp\Base::config('third.alipay.app_pay.charset');
        // 合作伙伴ID
        $conf['partner'] = \fxapp\Base::config('third.alipay.app_pay.partner');
        // 请求格式
        $conf['format'] = \fxapp\Base::config('third.alipay.app_pay.format');
        // 服务版本
        $conf['version'] = \fxapp\Base::config('third.alipay.app_pay.version');
        // 卖家账号
        $conf['seller_id'] = \fxapp\Base::config('third.alipay.app_pay.seller_id');
        // 应用服务
        $conf['service'] = \fxapp\Base::config('third.alipay.app_pay.service');
        // 签名
        $conf['sign'] = \fxapp\Base::config('third.alipay.app_pay.sign');
        // 签名类型
        $conf['sign_type'] = \fxapp\Base::config('third.alipay.app_pay.sign_type');
        // 商品名称
        $conf['subject'] = \fxapp\Base::config('third.alipay.app_pay.subject');
        // 销售产品码
        $conf['product_code'] = \fxapp\Base::config('third.alipay.app_pay.product_code');
        // 回调地址
        $conf['url_notify'] = \fxapp\Base::config('third.alipay.app_pay.url_notify');
        // 商户私钥
        $conf['url_pri_key'] = \fxapp\Base::config('third.alipay.app_pay.url_pri_key');
        // SDK地址
        $conf['url_sdk'] = \fxapp\Base::config('third.alipay.app_pay.url_sdk');
        // 初始化环境变量
        $predefined = [
            'app_id' => $conf['app_id'], 'service' => $conf['service'], 'partner' => $conf['partner'],
            'charset' => $conf['charset'], 'sign_type' => $conf['sign_type'], 'sign' => $conf['sign'],
            'url_notify' => $conf['url_notify'], 'subject' => $conf['subject'], 'format' => $conf['format'],
            'version' => $conf['version'], 'seller_id' => $conf['seller_id'], 'body' => $conf['body'],
            'product_code' => $conf['product_code'], 'url_pri_key' => $conf['url_pri_key'], 'url_sdk' => $conf['url_sdk'],
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.1.2');
        $tray['2_1']['app_id'] = $entry['app_id'];
        $tray['2_1']['service'] = $entry['service'];
        $tray['2_1']['partner'] = $entry['partner'];
        $tray['2_1']['charset'] = $entry['charset'];
        $tray['2_1']['sign_type'] = $entry['sign_type'];
        $tray['2_1']['sign'] = $entry['sign'];
        $tray['2_1']['url_notify'] = $entry['url_notify'];
        $tray['2_1']['sn'] = $tray['sn'];
        $tray['2_1']['subject'] = $entry['subject'];
        $tray['2_1']['format'] = $entry['format'];
        $tray['2_1']['version'] = $entry['version'];
        $tray['2_1']['seller_id'] = $entry['seller_id'];
        $tray['2_1']['money'] = $tray['money'];
        $tray['2_1']['body'] = $entry['body'];
        $tray['2_1']['timestamp'] = date('Y-m-d H:i:s');
        $tray['2_1']['product_code'] = $entry['product_code'];
        $tray['2_1']['url_pri_key'] = $entry['url_pri_key'];
        $tray['2_1']['url_sdk'] = $entry['url_sdk'];
        // 生成签名
        $tray['2_2'] = $this->_paySignApply($tray['2_1']);
        $data['sign'] = $tray['2_2']['param'];
        $echo[2] = \fxapp\Base::lang(['request', 'success']);
        $echo[3] = $data;
        return $echo;
    }

    /**
     * 支付-签名-申请
     * @param array $entry['service'] 应用服务
     * @param array $entry['partner'] 合作伙伴ID
     * @param array $entry['charset'] 请求字符集
     * @param array $entry['sign_type'] 签名类型
     * @param array $entry['sign'] 签名
     * @param array $entry['url_notify'] 回调地址
     * @param array $entry['sn'] 商户网站唯一订单号
     * @param array $entry['subject'] 商品名称
     * @param array $entry['payment_type'] 支付类型
     * @param array $entry['seller_id'] 卖家账号
     * @param array $entry['money'] 支付金额
     * @param array $entry['body'] 商品描述
     * @return mixed
     */
    private function _paySignApply($entry)
    {
        // 加载SDK
        \fxapp\Base::load($entry['url_sdk']);
        // 公共参数
        $param = [];
        // 实例化支付宝sdk里面的AopClient类,下单时需要的操作,都在这个类里面
        $client = new \AopClient();
        // 支付宝分配给开发者的应用ID
        $param['app_id'] = $entry['app_id'];
        // 接口名称
        $param['method'] = $entry['service'];
        // 收款人账号
        $param['seller_id'] = $entry['seller_id'];
        // 商户网站唯一订单号
        $param['out_trade_no'] = $entry['sn'];
        // 订单总金额(必须定义成浮点型)
        $param['total_fee'] = floatval($entry['money']);
        // 请求使用的编码格式
        $param['charset'] = $entry['charset'];
        // 销售产品码，商家和支付宝签约的产品码，为固定值QUICK_MSECURITY_PAY
        $param['product_code'] = $entry['product_code'];
        // 商户生成签名字符串所使用的签名算法类型
        $param['sign_type'] = $entry['sign_type'];
        // 商品的标题/交易标题/订单标题/订单关键字等
        $param['subject'] = $entry['subject'];
        // 商品描述
        $param['body'] = $entry['body'];
        // 请求时间
        $param['timestamp'] = $entry['timestamp'];
        // 调用的接口版本，固定为：1.0
        $param['version'] = $entry['version'];
        // 支付宝服务器主动通知地址
        $param['notify_url'] = $entry['url_notify'];
        // 读取私钥文件
        // 私钥文件路径
        $pri_key = file_get_contents($entry['url_pri_key']);
        $pri_key = str_replace("-----BEGIN RSA PRIVATE KEY-----\n", '', $pri_key);
        $pri_key = str_replace("\n-----END RSA PRIVATE KEY-----", '', $pri_key);
        // 生成签名
        $paramStr = $client->getSignContent($param);
        $sign = $client->alonersaSign($paramStr, $entry['url_pri_key'], $entry['sign_type'], true);
        $param['sign'] = $sign;
        $str = $client->getSignContentUrlencode($param);
        $entry['param'] = $str;
        return $entry;
    }

    /**
     * 支付-回调
     * @return mixed
     */
    public function payCallback()
    {
        // 初始化变量
        $entry = $this->data;
        $echo = \fxapp\Server::echo();
        // 支付宝公钥
        $conf['url_pub_key'] = \fxapp\Base::config('third.alipay.app_pay.url_pub_key');
        // 签名类型
        $conf['sign_type'] = \fxapp\Base::config('third.alipay.app_pay.sign_type');
        // SDK地址
        $conf['url_sdk'] = \fxapp\Base::config('third.alipay.app_pay.url_sdk');
        \fxapp\Base::load($conf['url_sdk']);
        // 服务处理
        $client = new \AopClient();
        // 读取支付宝公钥文件
        // 支付宝公钥文件路径
        $pub_key = file_get_contents($conf['url_pub_key']);
        $pub_key = str_replace("-----BEGIN PUBLIC KEY-----\n", '', $pub_key);
        $pub_key = str_replace("\n-----END PUBLIC KEY-----", '', $pub_key);
        $client->alipayPublicKey = $pub_key;
        $verify_result = $client->rsaCheckV1($entry, $conf['url_pub_key'], $conf['sign_type']);
        if (!$verify_result) {
            if (!isset($entry['trade_status'])) {
            } else if ($entry['trade_status'] == 'TRADE_SUCCESS') {
                $verify_result = true;
            } else if ($entry['trade_status'] == 'TRADE_FINISHED') {
                $echo[2] = \fxapp\Base::lang(['pay', 'complete']);
                $echo[3] = $entry;
                return $echo;
            }
        }
        // 验证支付结果
        if ($verify_result) {
            $echo[2] = \fxapp\Base::lang(['pay', 'success']);
            $echo[3] = $entry;
            return $echo;
        } else {
            $echo[0] = false;
            $echo[1] = 1002;
            $echo[2] = \fxapp\Base::lang(['pay', 'fail']);
            $echo[3]['result'] = $verify_result;
            $echo[3]['param'] = $entry;
            return $echo;
        }
    }

    /**
     * 手机-支付-申请
     * @param string $entry['sn'] 订单SN
     * @param string $entry['money'] 支付金额
     * @return mixed
     */
    public function wapPayApply()
    {
        // 初始化变量
        $entry = $this->data;
        $echo = \fxapp\Server::echo();
        $predefined = [
            'sn', 'money',
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.2.2');
        $tray['sn'] = $entry['sn'];
        $tray['money'] = $entry['money'];
        $pempty = \fxapp\Data::paramEmpty($tray);
        if (!$pempty[0]) return $pempty;
        // 应用ID
        $conf['app_id'] = \fxapp\Base::config('third.alipay.wap_pay.app_id');
        // 商品描述
        $conf['body'] = \fxapp\Base::config('third.alipay.wap_pay.body');
        // 请求字符集
        $conf['charset'] = \fxapp\Base::config('third.alipay.wap_pay.charset');
        // 合作伙伴ID
        $conf['partner'] = \fxapp\Base::config('third.alipay.wap_pay.partner');
        // 请求格式
        $conf['format'] = \fxapp\Base::config('third.alipay.wap_pay.format');
        // 服务版本
        $conf['version'] = \fxapp\Base::config('third.alipay.wap_pay.version');
        // 卖家账号
        $conf['seller_id'] = \fxapp\Base::config('third.alipay.wap_pay.seller_id');
        // 应用服务
        $conf['service'] = \fxapp\Base::config('third.alipay.wap_pay.service');
        // 签名
        $conf['sign'] = \fxapp\Base::config('third.alipay.wap_pay.sign');
        // 签名类型
        $conf['sign_type'] = \fxapp\Base::config('third.alipay.wap_pay.sign_type');
        // 商品名称
        $conf['subject'] = \fxapp\Base::config('third.alipay.wap_pay.subject');
        // 销售产品码
        $conf['product_code'] = \fxapp\Base::config('third.alipay.wap_pay.product_code');
        // 返回地址
        $conf['url_return'] = \fxapp\Base::config('third.alipay.wap_pay.url_return');
        // 回调地址
        $conf['url_notify'] = \fxapp\Base::config('third.alipay.wap_pay.url_notify');
        // 商户私钥
        $conf['url_pri_key'] = \fxapp\Base::config('third.alipay.wap_pay.url_pri_key');
        // SDK地址
        $conf['url_sdk'] = \fxapp\Base::config('third.alipay.wap_pay.url_sdk');
        // 初始化环境变量
        $predefined = [
            'app_id' => $conf['app_id'], 'service' => $conf['service'], 'partner' => $conf['partner'],
            'charset' => $conf['charset'], 'sign_type' => $conf['sign_type'], 'sign' => $conf['sign'],
            'url_notify' => $conf['url_notify'], 'url_return' => $conf['url_return'], 'subject' => $conf['subject'],
            'format' => $conf['format'], 'version' => $conf['version'], 'seller_id' => $conf['seller_id'],
            'body' => $conf['body'], 'product_code' => $conf['product_code'], 'url_pri_key' => $conf['url_pri_key'],
            'url_sdk' => $conf['url_sdk'],
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.1.2');
        $tray['2_1']['app_id'] = $entry['app_id'];
        $tray['2_1']['service'] = $entry['service'];
        $tray['2_1']['partner'] = $entry['partner'];
        $tray['2_1']['charset'] = $entry['charset'];
        $tray['2_1']['sign_type'] = $entry['sign_type'];
        $tray['2_1']['sign'] = $entry['sign'];
        $tray['2_1']['url_return'] = $entry['url_return'];
        $tray['2_1']['url_notify'] = $entry['url_notify'];
        $tray['2_1']['sn'] = $tray['sn'];
        $tray['2_1']['subject'] = $entry['subject'];
        $tray['2_1']['format'] = $entry['format'];
        $tray['2_1']['version'] = $entry['version'];
        $tray['2_1']['seller_id'] = $entry['seller_id'];
        $tray['2_1']['money'] = $tray['money'];
        $tray['2_1']['body'] = $entry['body'];
        $tray['2_1']['timestamp'] = date('Y-m-d H:i:s');
        $tray['2_1']['product_code'] = $entry['product_code'];
        $tray['2_1']['url_pri_key'] = $entry['url_pri_key'];
        $tray['2_1']['url_sdk'] = $entry['url_sdk'];
        // 生成签名
        $tray['2_2'] = $this->_wapPaySignApply($tray['2_1']);
        $data['sign'] = $tray['2_2']['param'];
        $echo[2] = \fxapp\Base::lang(['request', 'success']);
        $echo[3] = $data;
        return $echo;
    }

    /**
     * 手机-支付-签名申请
     * @param array $entry['service'] 应用服务
     * @param array $entry['partner'] 合作伙伴ID
     * @param array $entry['charset'] 请求字符集
     * @param array $entry['sign_type'] 签名类型
     * @param array $entry['sign'] 签名
     * @param array $entry['url_notify'] 回调地址
     * @param array $entry['sn'] 商户网站唯一订单号
     * @param array $entry['subject'] 商品名称
     * @param array $entry['payment_type'] 支付类型
     * @param array $entry['seller_id'] 卖家账号
     * @param array $entry['money'] 支付金额
     * @param array $entry['body'] 商品描述
     * @return mixed
     */
    private function _wapPaySignApply($entry)
    {
        // 加载SDK
        \fxapp\Base::load($entry['url_sdk']);
        // 公共参数
        $param = [];
        // 实例化支付宝sdk里面的AopClient类,下单时需要的操作,都在这个类里面
        $client = new \AopClient();
        // 支付宝分配给开发者的应用ID
        $client->appId = $entry['app_id'];
        // 商户生成签名字符串所使用的签名算法类型
        $client->signType = $entry['sign_type'];
        // 读取私钥文件
        $client->rsaPrivateKeyFilePath = $entry['url_pri_key'];
        $request = new \AlipayTradeWapPayRequest();
        $request->setReturnUrl($entry['url_return']);
        $request->setNotifyUrl($entry['url_notify']);
        // 商户网站唯一订单号
        $param_2['out_trade_no'] = $entry['sn'];
        // 订单总金额(必须定义成浮点型)
        $param_2['total_amount'] = floatval($entry['money']);
        // 商品的标题/交易标题/订单标题/订单关键字等
        $param_2['subject'] = $entry['subject'];
        // 销售产品码，商家和支付宝签约的产品码，为固定值QUICK_WAP_PAY
        $param_2['product_code'] = $entry['product_code'];
        $request->setBizContent(\fxapp\Param::json($param_2, 'encode'));
        $record = $client->pageExecute($request);
        $entry['param'] = $record;
        return $entry;
    }

    /**
     * 手机-支付-回调
     * @return mixed
     */
    public function wapPayCallback()
    {
        // 初始化变量
        $entry = $this->data;
        $echo = \fxapp\Server::echo();
        // 支付宝公钥
        $conf['url_pub_key'] = \fxapp\Base::config('third.alipay.wap_pay.url_pub_key');
        // 签名类型
        $conf['sign_type'] = \fxapp\Base::config('third.alipay.wap_pay.sign_type');
        // SDK地址
        $conf['url_sdk'] = \fxapp\Base::config('third.alipay.wap_pay.url_sdk');
        \fxapp\Base::load($conf['url_sdk']);
        // 服务处理
        $client = new \AopClient();
        // 读取支付宝公钥文件
        // 支付宝公钥文件路径
        $pub_key = file_get_contents($conf['url_pub_key']);
        $pub_key = str_replace("-----BEGIN PUBLIC KEY-----\n", '', $pub_key);
        $pub_key = str_replace("\n-----END PUBLIC KEY-----", '', $pub_key);
        $client->alipayPublicKey = $pub_key;
        $verify_result = $client->rsaCheckV1($entry, $conf['url_pub_key'], $conf['sign_type']);
        if (!$verify_result) {
            if (!isset($entry['trade_status'])) {
            } else if ($entry['trade_status'] == 'TRADE_SUCCESS') {
                $verify_result = true;
            } else if ($entry['trade_status'] == 'TRADE_FINISHED') {
                $echo[2] = \fxapp\Base::lang(['pay', 'complete']);
                $echo[3] = $entry;
                return $echo;
            }
        }
        // 验证支付结果
        if ($verify_result) {
            $echo[2] = \fxapp\Base::lang(['pay', 'success']);
            $echo[3] = $entry;
            return $echo;
        } else {
            $echo[0] = false;
            $echo[1] = 1002;
            $echo[2] = \fxapp\Base::lang(['pay', 'fail']);
            $echo[3]['result'] = $verify_result;
            $echo[3]['param'] = $entry;
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
        if (isset($record[0]) && is_bool($record[0])) {
            return $record;
        } else if (isset($record['error_response']['code'])) {
            $echo[0] = false;
            $echo[1] = 1004;
            $echo[2] = $record['error_response']['sub_msg'];
            $echo[3] = $record;
            return $echo;
        } else if (isset($record['alipay_trade_refund_response']['code']) && $record['alipay_trade_refund_response']['code'] != '10000') {
            $echo[0] = false;
            $echo[1] = 1004;
            $echo[2] = $record['alipay_trade_refund_response']['sub_msg'];
            $echo[3] = $record;
            return $echo;
        } else {
            $echo[2] = \fxapp\Base::lang(['request', 'success']);
            $echo[3] = $record;
            return $echo;
        }
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
        // 应用ID
        $conf['app_id'] = \fxapp\Base::config('third.alipay.web_auth_info.app_id');
        // 签名类型
        $conf['sign_type'] = \fxapp\Base::config('third.alipay.web_auth_info.sign_type');
        // 商户私钥
        $conf['url_pri_key'] = \fxapp\Base::config('third.alipay.web_auth_info.url_pri_key');
        // SDK地址
        $conf['url_sdk'] = \fxapp\Base::config('third.alipay.web_auth_info.url_sdk');
        // 加载SDK
        \fxapp\Base::load($conf['url_sdk']);
        // 公共参数
        $param = [];
        // 实例化支付宝sdk里面的AopClient类,下单时需要的操作,都在这个类里面
        $client = new \AopClient();
        // 支付宝分配给开发者的应用ID
        $client->appId = $conf['app_id'];
        // 商户生成签名字符串所使用的签名算法类型
        $client->signType = $conf['sign_type'];
        // 读取私钥文件
        $client->rsaPrivateKeyFilePath = $conf['url_pri_key'];
        $request = new \AlipayTradeRefundRequest();
        // 支付时传入的商户订单号
        $param_2['out_trade_no'] = $tray['deal_sn'];
        // 支付时返回的支付宝交易号
        $param_2['trade_no'] = $tray['pay_sn'];
        // 本次退款请求流水号
        $param_2['out_request_no'] = $tray['refund_sn'];
        // 本次退款金额
        $param_2['refund_amount'] = floatval($tray['refund_money']);
        $request->setBizContent(\fxapp\Param::json($param_2, 'encode'));
        $record = $client->execute($request);
        $record = \fxapp\Param::json(\fxapp\Param::json($record, 'encode'), 'decode');
        return $record;
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

/**
 * 淘宝
 * @return mixed
 */
class Taobao extends Alibaba
{
    /**
     * 定位
     * @param string $entry['ip'] 目标IP
     * @return mixed
     */
    public function location()
    {
        // 初始化变量
        $entry = $this->data;
        $echo = \fxapp\Server::echo();
        $predefined = [
            'ip',
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.2.2');
        $tray['ip'] = $entry['ip'];
        $pempty = \fxapp\Data::paramEmpty($tray);
        if (!$pempty[0]) return $pempty;
        // 接口域
        $conf['domain'] = \fxapp\Base::config('third.taobao.location.domain');
        $conf['data']['ip'] = $tray['ip'];
        $response = \fxapp\Service::http($conf['domain'], $conf['data'], [], 'post');
        $response = json_decode($response, true);
        if (!$response['code']) {
            $echo[2] = \fxapp\Base::lang(['request', 'success']);
            $echo[3] = $response;
            return $echo;
        } else {
            $echo[0] = false;
            $echo[1] = 1002;
            $echo[2] = \fxapp\Base::lang(['request', 'fail']);
            $echo[3] = $response;
            return $echo;
        }
    }
}

/**
 * 阿里云
 * @return mixed
 */
class Aliyun extends Alibaba
{
    /**
     * 查询所有解析
     * @param string $entry['domain'] 域名
     * @return mixed
     */
    public function describeDomainRecords()
    {
        // 初始化变量
        $entry = $this->data;
        $echo = \fxapp\Server::echo();
        $predefined = [
            'domain',
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.2.2');
        $tray['domain'] = $entry['domain'];
        $pempty = \fxapp\Data::paramEmpty($tray);
        if (!$pempty[0]) return $pempty;
        // 配置参数
        $param = [
            'Action' => 'DescribeDomainRecords',
            'DomainName' => $tray['domain'],
        ];
        $record = $this->ddns($param);
        if (!isset($record['Code'])) {
            $echo[2] = \fxapp\Base::lang(['request', 'success']);
            $echo[3] = [$record];
            return $echo;
        } else {
            $echo[0] = false;
            $echo[1] = 1002;
            $echo[2] = $record['Message'];
            $echo[3] = [$record];
            return $echo;
        }
    }

    /**
     * 查询域名解析ID
     * @param string $entry['domain'] 域名
     * @param string $entry['rr'] 主机记录
     * @return mixed
     */
    public function getRecordId()
    {
        // 初始化变量
        $entry = $this->data;
        $echo = \fxapp\Server::echo();
        $predefined = [
            'domain', 'rr',
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.2.2');
        $tray['domain'] = $entry['domain'];
        $tray['rr'] = $entry['rr'];
        $pempty = \fxapp\Data::paramEmpty($tray);
        if (!$pempty[0]) return $pempty;
        // 配置参数
        $record = $this->describeDomainRecords();
        if (!$record[0]) return $record;
        $data = $record[3][0];
        $list = $data['DomainRecords']['Record'];
        // 获取指定解析
        $RR = null;
        $list = \fxapp\Param::json($list, 'decode');
        foreach ($list as $key => $value) {
            if ($tray['rr'] === $value['RR']) {
                $RR = $value;
            }
        }
        if ($RR !== null) {
            $echo[2] = \fxapp\Base::lang(['request', 'success']);
            $echo[3] = [$RR];
            return $echo;
        } else {
            $echo[0] = false;
            $echo[1] = 1002;
            $echo[2] = \fxapp\Base::lang(['record', 'not', 'exists']);
            return $echo;
        }
    }

    /**
     * 更新域名解析
     * @param string $entry['domain'] 域名
     * @param string $entry['rr'] 主机记录
     * @param string $entry['type'] 记录类型
     * @param string $entry['value'] 记录值
     * @return mixed
     */
    public function updateDomainRecord()
    {
        // 初始化变量
        $entry = $this->data;
        $echo = \fxapp\Server::echo();
        $predefined = [
            'domain', 'rr', 'type',
            'value',
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.2.2');
        $tray['domain'] = $entry['domain'];
        $tray['rr'] = $entry['rr'];
        $tray['type'] = $entry['type'];
        $tray['value'] = $entry['value'];
        $pempty = \fxapp\Data::paramEmpty($tray);
        if (!$pempty[0]) return $pempty;
        // 查询域名解析ID
        $record = $this->getRecordId();
        if (!$record[0]) return $record;
        $RecordId = $record[3][0]['RecordId'];
        // 配置参数
        $param = [
            'Action' => 'UpdateDomainRecord',
            'RecordId' => $RecordId,
            'RR' => $tray['rr'],
            'Type' => $tray['type'],
            'Value' => $tray['value'],
        ];
        $record = $this->ddns($param);
        if (!isset($record['Code'])) {
            $echo[2] = \fxapp\Base::lang(['request', 'success']);
            $echo[3] = [$record];
            return $echo;
        } else {
            $echo[0] = false;
            $echo[1] = 1002;
            $echo[2] = $record['Message'];
            $echo[3] = [$record];
            return $echo;
        }
    }

    /**
     * 动态域名服务
     * @param string $request 请求参数
     * @param string $entry['key'] 访问ID
     * @param string $entry['secret'] 访问密钥
     * @return mixed
     */
    private function ddns($request)
    {
        // 初始化变量
        $entry = $this->data;
        $echo = \fxapp\Server::echo();
        $predefined = [
            'key' => \fxapp\Base::config('third.aliyun.ddns.access_key_id'), 'secret' => \fxapp\Base::config('third.aliyun.ddns.access_key_secret'),
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.1.2');
        $tray['key'] = $entry['key'];
        $tray['secret'] = $entry['secret'];
        $timezone = date_default_timezone_get();
        date_default_timezone_set('GMT');
        $param = [
            'Format' => 'JSON',
            'Version' => '2015-01-09',
            'AccessKeyId' => $tray['key'],
            'Timestamp' => date('Y-m-d\TH:i:s\Z'),
            'SignatureMethod' => 'HMAC-SHA1',
            'SignatureVersion' => '1.0',
            'SignatureNonce' => substr(md5(rand(1, 99999999)), rand(1, 9), 14),
        ];
        date_default_timezone_set($timezone);
        $param = array_merge($param, $request);
        // 签名
        $param['Signature'] = $this->sign($param, $tray['secret']);
        $uri = http_build_query($param);
        $url = 'http://alidns.aliyuncs.com/?' . $uri;
        $response = \fxapp\Service::http($url, '', [], 'get');
        if (is_json($response)) {
            return json_decode($response, true);
        } else {
            return false;
        }
    }

    /**
     * 签名
     * @param string $param 参数
     * @param string $accessKeySecret 访问密钥
     * @param string $method 方法
     * @return mixed
     */
    private function sign($param, $accessKeySecret, $method = 'GET')
    {
        ksort($param);
        $stringToSign = strtoupper($method) . '&' . $this->percentEncode('/') . '&';

        $temp = '';
        foreach ($param as $key => $value) {
            $temp .= '&' . $this->percentEncode($key) . '=' . $this->percentEncode($value);
        }
        $temp = trim($temp, '&');
        $stringToSign = $stringToSign . $this->percentEncode($temp);

        $key = $accessKeySecret . '&';
        $hmac = hash_hmac('sha1', $stringToSign, $key, true);

        return base64_encode($hmac);
    }

    /**
     * 百分号编码
     * @param string $value 值
     * @return mixed
     */
    private function percentEncode($value = null)
    {
        $data = urlencode($value);
        $data = str_replace('+', '%20', $data);
        $data = str_replace('*', '%2A', $data);
        $data = str_replace('%7E', '~', $data);
        return $data;
    }
}
