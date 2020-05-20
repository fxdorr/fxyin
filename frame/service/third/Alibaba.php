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
 * 阿里巴巴
 * @return mixed
 */
class Alibaba extends Third
{
    /**
     * 服务
     * @param string $name 服务名称
     * @return mixed
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
        //初始化变量
        $tran = $this->data;
        $conf['param'] = '';
        $result = fsi_result();
        $predefined = [
            'url_redirect',
        ];
        $tran = fsi_param([$tran, $predefined], '1.2.2');
        //应用ID
        $conf['app_id'] = fxy_config('third.alipay.web_grant_token.app_id');
        //应用授权作用域
        $conf['scope'] = fxy_config('third.alipay.web_grant_token.scope');
        //重定向状态码
        $conf['state'] = fxy_config('third.alipay.web_grant_token.state');
        //重定向地址
        $conf['url_redirect'] = $tran['url_redirect'];
        $predefined = [
            'url_redirect' => fxy_config('third.alipay.web_grant_token.url_redirect'),
        ];
        $conf = fsi_param([$conf, $predefined], '1.1.2');
        //接口域
        $conf['domain'] = fxy_config('third.alipay.web_grant_token.domain');
        //拼接请求域
        $conf['param'] = dso_splice($conf['param'], 'app_id=' . $conf['app_id'], '&');
        $conf['param'] = dso_splice($conf['param'], 'redirect_uri=' . $conf['url_redirect'], '&');
        $conf['param'] = dso_splice($conf['param'], 'scope=' . $conf['scope'], '&');
        $conf['param'] = dso_splice($conf['param'], 'state=' . $conf['state'], '&');
        $conf['domain'] = dso_splice($conf['domain'], $conf['param'], '?');
        $result[2] = fxy_lang(['request', 'success']);
        $result[3] = $conf;
        return $result;
    }

    /**
     * WebAuthToken
     * @param string $tran['auth_code'] 授权码
     * @return mixed
     */
    public function webAuthToken()
    {
        //初始化变量
        $tran = $this->data;
        $result = fsi_result();
        $record = $this->_webAuthToken($tran);
        if (isset($record[0]) && is_bool($record[0])) {
            return $record;
        } else if (isset($record['error_response']['code'])) {
            $result[0] = false;
            $result[1] = 1004;
            $result[2] = $record['error_response']['sub_msg'];
            $result[3] = $record;
            return $result;
        } else {
            $result[2] = fxy_lang(['request', 'success']);
            $result[3] = $record;
            return $result;
        }
    }

    /**
     * WebAuthToken
     * @param string $tran['auth_code'] 授权码
     * @return mixed
     */
    private function _webAuthToken($tran)
    {
        //初始化变量
        $conf['param'] = '';
        $result = fsi_result();
        $predefined = [
            'auth_code',
        ];
        $tran = fsi_param([$tran, $predefined], '1.2.2');
        $parm['auth_code'] = $tran['auth_code'];
        $pempty = dsc_pempty($parm);
        if (!$pempty[0]) return $pempty;
        //应用ID
        $conf['app_id'] = fxy_config('third.alipay.web_auth_token.app_id');
        //签名类型
        $conf['sign_type'] = fxy_config('third.alipay.web_auth_token.sign_type');
        //授权类型
        $conf['grant_type'] = fxy_config('third.alipay.web_auth_token.grant_type');
        //商户私钥
        $conf['url_pri_key'] = fxy_config('third.alipay.web_auth_token.url_pri_key');
        //SDK地址
        $conf['url_sdk'] = fxy_config('third.alipay.web_auth_token.url_sdk');
        fxy_load($conf['url_sdk']);
        //服务处理
        $client = new \AopClient();
        //支付宝分配给开发者的应用ID
        $client->appId = $conf['app_id'];
        //商户生成签名字符串所使用的签名算法类型
        $client->signType = $conf['sign_type'];
        //读取私钥文件
        $client->rsaPrivateKeyFilePath = $conf['url_pri_key'];
        $request = new \AlipaySystemOauthTokenRequest();
        $request->setCode($parm['auth_code']);
        $request->setGrantType($conf['grant_type']);
        $record = $client->execute($request);
        $record = fcf_json(fcf_json($record, 'encode'), 'decode');
        return $record;
    }

    /**
     * WebRefreshToken
     * @param string $tran['auth_code'] 授权码
     * @return mixed
     */
    public function webRefreshToken()
    {
        //初始化变量
        $tran = $this->data;
        $result = fsi_result();
        $record = $this->_webRefreshToken($tran);
        if (isset($record[0]) && is_bool($record[0])) {
            return $record;
        } else if (isset($record['error_response']['code'])) {
            $result[0] = false;
            $result[1] = 1004;
            $result[2] = $record['error_response']['sub_msg'];
            $result[3] = $record;
            return $result;
        } else {
            $result[2] = fxy_lang(['request', 'success']);
            $result[3] = $record;
            return $result;
        }
    }

    /**
     * WebRefreshToken
     * @param string $tran['refresh_token'] 刷新令牌
     * @return mixed
     */
    private function _webRefreshToken($tran)
    {
        //初始化变量
        $conf['param'] = '';
        $result = fsi_result();
        $predefined = [
            'refresh_token',
        ];
        $tran = fsi_param([$tran, $predefined], '1.2.2');
        $parm['refresh_token'] = $tran['refresh_token'];
        $pempty = dsc_pempty($parm);
        if (!$pempty[0]) return $pempty;
        //应用ID
        $conf['app_id'] = fxy_config('third.alipay.web_refresh_token.app_id');
        //签名类型
        $conf['sign_type'] = fxy_config('third.alipay.web_refresh_token.sign_type');
        //授权类型
        $conf['grant_type'] = fxy_config('third.alipay.web_refresh_token.grant_type');
        //商户私钥
        $conf['url_pri_key'] = fxy_config('third.alipay.web_refresh_token.url_pri_key');
        //SDK地址
        $conf['url_sdk'] = fxy_config('third.alipay.web_refresh_token.url_sdk');
        fxy_load($conf['url_sdk']);
        //服务处理
        $client = new \AopClient();
        //支付宝分配给开发者的应用ID
        $client->appId = $conf['app_id'];
        //商户生成签名字符串所使用的签名算法类型
        $client->signType = $conf['sign_type'];
        //读取私钥文件
        $client->rsaPrivateKeyFilePath = $conf['url_pri_key'];
        $request = new \AlipaySystemOauthTokenRequest();
        $request->setGrantType($conf['grant_type']);
        $request->setRefreshToken($parm['refresh_token']);
        $record = $client->execute($request);
        $record = fcf_json(fcf_json($record, 'encode'), 'decode');
        return $record;
    }

    /**
     * WebAuthInfo
     * @param string $tran['access_token'] 授权令牌
     * @return mixed
     */
    public function webAuthInfo()
    {
        //初始化变量
        $tran = $this->data;
        $result = fsi_result();
        $record = $this->_webAuthInfo($tran);
        if (isset($record[0]) && is_bool($record[0])) {
            return $record;
        } else if (isset($record['error_response']['code'])) {
            $result[0] = false;
            $result[1] = 1004;
            $result[2] = $record['error_response']['sub_msg'];
            $result[3] = $record;
            return $result;
        } else if (isset($record['alipay_user_info_share_response']['code']) && $record['alipay_user_info_share_response']['code'] != '10000') {
            $result[0] = false;
            $result[1] = 1004;
            $result[2] = $record['alipay_user_info_share_response']['sub_msg'];
            $result[3] = $record;
            return $result;
        } else {
            $result[2] = fxy_lang(['request', 'success']);
            $result[3] = $record;
            return $result;
        }
    }

    /**
     * WebAuthInfo
     * @param string $tran['access_token'] 授权令牌
     * @return mixed
     */
    private function _webAuthInfo($tran)
    {
        //初始化变量
        $conf['param'] = '';
        $result = fsi_result();
        $predefined = [
            'access_token',
        ];
        $tran = fsi_param([$tran, $predefined], '1.2.2');
        $parm['access_token'] = $tran['access_token'];
        $pempty = dsc_pempty($parm);
        if (!$pempty[0]) return $pempty;
        //应用ID
        $conf['app_id'] = fxy_config('third.alipay.web_auth_info.app_id');
        //签名类型
        $conf['sign_type'] = fxy_config('third.alipay.web_auth_info.sign_type');
        //商户私钥
        $conf['url_pri_key'] = fxy_config('third.alipay.web_auth_info.url_pri_key');
        //SDK地址
        $conf['url_sdk'] = fxy_config('third.alipay.web_auth_info.url_sdk');
        fxy_load($conf['url_sdk']);
        //服务处理
        $client = new \AopClient();
        //支付宝分配给开发者的应用ID
        $client->appId = $conf['app_id'];
        //商户生成签名字符串所使用的签名算法类型
        $client->signType = $conf['sign_type'];
        //读取私钥文件
        $client->rsaPrivateKeyFilePath = $conf['url_pri_key'];
        $request = new \AlipayUserInfoShareRequest();
        $record = $client->execute($request, $parm['access_token']);
        $record = fcf_json(fcf_json($record, 'encode'), 'decode');
        return $record;
    }

    /**
     * 支付-申请
     * @param string $tran['sn'] 订单SN
     * @param string $tran['money'] 支付金额
     * @return mixed
     */
    public function payApply()
    {
        //初始化变量
        $tran = $this->data;
        $result = fsi_result();
        $predefined = [
            'sn', 'money',
        ];
        $tran = fsi_param([$tran, $predefined], '1.2.2');
        $parm['sn'] = $tran['sn'];
        $parm['money'] = $tran['money'];
        $pempty = dsc_pempty($parm);
        if (!$pempty[0]) return $pempty;
        //应用ID
        $conf['app_id'] = fxy_config('third.alipay.app_pay.app_id');
        //商品描述
        $conf['body'] = fxy_config('third.alipay.app_pay.body');
        //请求字符集
        $conf['charset'] = fxy_config('third.alipay.app_pay.charset');
        //合作伙伴ID
        $conf['partner'] = fxy_config('third.alipay.app_pay.partner');
        //请求格式
        $conf['format'] = fxy_config('third.alipay.app_pay.format');
        //服务版本
        $conf['version'] = fxy_config('third.alipay.app_pay.version');
        //卖家账号
        $conf['seller_id'] = fxy_config('third.alipay.app_pay.seller_id');
        //应用服务
        $conf['service'] = fxy_config('third.alipay.app_pay.service');
        //签名
        $conf['sign'] = fxy_config('third.alipay.app_pay.sign');
        //签名类型
        $conf['sign_type'] = fxy_config('third.alipay.app_pay.sign_type');
        //商品名称
        $conf['subject'] = fxy_config('third.alipay.app_pay.subject');
        //销售产品码
        $conf['product_code'] = fxy_config('third.alipay.app_pay.product_code');
        //回调地址
        $conf['url_notify'] = fxy_config('third.alipay.app_pay.url_notify');
        //商户私钥
        $conf['url_pri_key'] = fxy_config('third.alipay.app_pay.url_pri_key');
        //SDK地址
        $conf['url_sdk'] = fxy_config('third.alipay.app_pay.url_sdk');
        //初始化环境变量
        $predefined = [
            'app_id' => $conf['app_id'], 'service' => $conf['service'], 'partner' => $conf['partner'],
            'charset' => $conf['charset'], 'sign_type' => $conf['sign_type'], 'sign' => $conf['sign'],
            'url_notify' => $conf['url_notify'], 'subject' => $conf['subject'], 'format' => $conf['format'],
            'version' => $conf['version'], 'seller_id' => $conf['seller_id'], 'body' => $conf['body'],
            'product_code' => $conf['product_code'], 'url_pri_key' => $conf['url_pri_key'], 'url_sdk' => $conf['url_sdk'],
        ];
        $tran = fsi_param([$tran, $predefined], '1.1.2');
        $tray['2_1']['app_id'] = $tran['app_id'];
        $tray['2_1']['service'] = $tran['service'];
        $tray['2_1']['partner'] = $tran['partner'];
        $tray['2_1']['charset'] = $tran['charset'];
        $tray['2_1']['sign_type'] = $tran['sign_type'];
        $tray['2_1']['sign'] = $tran['sign'];
        $tray['2_1']['url_notify'] = $tran['url_notify'];
        $tray['2_1']['sn'] = $parm['sn'];
        $tray['2_1']['subject'] = $tran['subject'];
        $tray['2_1']['format'] = $tran['format'];
        $tray['2_1']['version'] = $tran['version'];
        $tray['2_1']['seller_id'] = $tran['seller_id'];
        $tray['2_1']['money'] = $parm['money'];
        $tray['2_1']['body'] = $tran['body'];
        $tray['2_1']['timestamp'] = date('Y-m-d H:i:s');
        $tray['2_1']['product_code'] = $tran['product_code'];
        $tray['2_1']['url_pri_key'] = $tran['url_pri_key'];
        $tray['2_1']['url_sdk'] = $tran['url_sdk'];
        //生成签名
        $tray['2_2'] = $this->_paySignApply($tray['2_1']);
        $data['sign'] = $tray['2_2']['param'];
        $result[2] = fxy_lang(['request', 'success']);
        $result[3] = $data;
        return $result;
    }

    /**
     * 支付-签名-申请
     * @param array $tran['service'] 应用服务
     * @param array $tran['partner'] 合作伙伴ID
     * @param array $tran['charset'] 请求字符集
     * @param array $tran['sign_type'] 签名类型
     * @param array $tran['sign'] 签名
     * @param array $tran['url_notify'] 回调地址
     * @param array $tran['sn'] 商户网站唯一订单号
     * @param array $tran['subject'] 商品名称
     * @param array $tran['payment_type'] 支付类型
     * @param array $tran['seller_id'] 卖家账号
     * @param array $tran['money'] 支付金额
     * @param array $tran['body'] 商品描述
     * @return mixed
     */
    private function _paySignApply($tran)
    {
        //加载SDK
        fxy_load($tran['url_sdk']);
        //公共参数
        $param = [];
        //实例化支付宝sdk里面的AopClient类,下单时需要的操作,都在这个类里面
        $client = new \AopClient();
        //支付宝分配给开发者的应用ID
        $param['app_id'] = $tran['app_id'];
        //接口名称
        $param['method'] = $tran['service'];
        //收款人账号
        $param['seller_id'] = $tran['seller_id'];
        //商户网站唯一订单号
        $param['out_trade_no'] = $tran['sn'];
        //订单总金额(必须定义成浮点型)
        $param['total_fee'] = floatval($tran['money']);
        //请求使用的编码格式
        $param['charset'] = $tran['charset'];
        //销售产品码，商家和支付宝签约的产品码，为固定值QUICK_MSECURITY_PAY
        $param['product_code'] = $tran['product_code'];
        //商户生成签名字符串所使用的签名算法类型
        $param['sign_type'] = $tran['sign_type'];
        //商品的标题/交易标题/订单标题/订单关键字等
        $param['subject'] = $tran['subject'];
        //商品描述
        $param['body'] = $tran['body'];
        //请求时间
        $param['timestamp'] = $tran['timestamp'];
        //调用的接口版本，固定为：1.0
        $param['version'] = $tran['version'];
        //支付宝服务器主动通知地址
        $param['notify_url'] = $tran['url_notify'];
        //读取私钥文件
        //私钥文件路径
        $pri_key = file_get_contents($tran['url_pri_key']);
        $pri_key = str_replace("-----BEGIN RSA PRIVATE KEY-----\n", '', $pri_key);
        $pri_key = str_replace("\n-----END RSA PRIVATE KEY-----", '', $pri_key);
        //生成签名
        $paramStr = $client->getSignContent($param);
        $sign = $client->alonersaSign($paramStr, $tran['url_pri_key'], $tran['sign_type'], true);
        $param['sign'] = $sign;
        $str = $client->getSignContentUrlencode($param);
        $tran['param'] = $str;
        return $tran;
    }

    /**
     * 支付-回调
     * @return mixed
     */
    public function payCallback()
    {
        //初始化变量
        $tran = $this->data;
        $result = fsi_result();
        //支付宝公钥
        $conf['url_pub_key'] = fxy_config('third.alipay.app_pay.url_pub_key');
        //签名类型
        $conf['sign_type'] = fxy_config('third.alipay.app_pay.sign_type');
        //SDK地址
        $conf['url_sdk'] = fxy_config('third.alipay.app_pay.url_sdk');
        fxy_load($conf['url_sdk']);
        //服务处理
        $client = new \AopClient();
        //读取支付宝公钥文件
        //支付宝公钥文件路径
        $pub_key = file_get_contents($conf['url_pub_key']);
        $pub_key = str_replace("-----BEGIN PUBLIC KEY-----\n", '', $pub_key);
        $pub_key = str_replace("\n-----END PUBLIC KEY-----", '', $pub_key);
        $client->alipayPublicKey = $pub_key;
        $verify_result = $client->rsaCheckV1($tran, $conf['url_pub_key'], $conf['sign_type']);
        if (!$verify_result) {
            if (!isset($tran['trade_status'])) {
            } else if ($tran['trade_status'] == 'TRADE_SUCCESS') {
                $verify_result = true;
            } else if ($tran['trade_status'] == 'TRADE_FINISHED') {
                $result[2] = fxy_lang(['pay', 'complete']);
                $result[3] = $tran;
                return $result;
            }
        }
        //验证支付结果
        if ($verify_result) {
            $result[2] = fxy_lang(['pay', 'success']);
            $result[3] = $tran;
            return $result;
        } else {
            $result[0] = false;
            $result[1] = 1002;
            $result[2] = fxy_lang(['pay', 'fail']);
            $result[3]['result'] = $verify_result;
            $result[3]['param'] = $tran;
            return $result;
        }
    }

    /**
     * 手机-支付-申请
     * @param string $tran['sn'] 订单SN
     * @param string $tran['money'] 支付金额
     * @return mixed
     */
    public function wapPayApply()
    {
        //初始化变量
        $tran = $this->data;
        $result = fsi_result();
        $predefined = [
            'sn', 'money',
        ];
        $tran = fsi_param([$tran, $predefined], '1.2.2');
        $parm['sn'] = $tran['sn'];
        $parm['money'] = $tran['money'];
        $pempty = dsc_pempty($parm);
        if (!$pempty[0]) return $pempty;
        //应用ID
        $conf['app_id'] = fxy_config('third.alipay.wap_pay.app_id');
        //商品描述
        $conf['body'] = fxy_config('third.alipay.wap_pay.body');
        //请求字符集
        $conf['charset'] = fxy_config('third.alipay.wap_pay.charset');
        //合作伙伴ID
        $conf['partner'] = fxy_config('third.alipay.wap_pay.partner');
        //请求格式
        $conf['format'] = fxy_config('third.alipay.wap_pay.format');
        //服务版本
        $conf['version'] = fxy_config('third.alipay.wap_pay.version');
        //卖家账号
        $conf['seller_id'] = fxy_config('third.alipay.wap_pay.seller_id');
        //应用服务
        $conf['service'] = fxy_config('third.alipay.wap_pay.service');
        //签名
        $conf['sign'] = fxy_config('third.alipay.wap_pay.sign');
        //签名类型
        $conf['sign_type'] = fxy_config('third.alipay.wap_pay.sign_type');
        //商品名称
        $conf['subject'] = fxy_config('third.alipay.wap_pay.subject');
        //销售产品码
        $conf['product_code'] = fxy_config('third.alipay.wap_pay.product_code');
        //返回地址
        $conf['url_return'] = fxy_config('third.alipay.wap_pay.url_return');
        //回调地址
        $conf['url_notify'] = fxy_config('third.alipay.wap_pay.url_notify');
        //商户私钥
        $conf['url_pri_key'] = fxy_config('third.alipay.wap_pay.url_pri_key');
        //SDK地址
        $conf['url_sdk'] = fxy_config('third.alipay.wap_pay.url_sdk');
        //初始化环境变量
        $predefined = [
            'app_id' => $conf['app_id'], 'service' => $conf['service'], 'partner' => $conf['partner'],
            'charset' => $conf['charset'], 'sign_type' => $conf['sign_type'], 'sign' => $conf['sign'],
            'url_notify' => $conf['url_notify'], 'url_return' => $conf['url_return'], 'subject' => $conf['subject'],
            'format' => $conf['format'], 'version' => $conf['version'], 'seller_id' => $conf['seller_id'],
            'body' => $conf['body'], 'product_code' => $conf['product_code'], 'url_pri_key' => $conf['url_pri_key'],
            'url_sdk' => $conf['url_sdk'],
        ];
        $tran = fsi_param([$tran, $predefined], '1.1.2');
        $tray['2_1']['app_id'] = $tran['app_id'];
        $tray['2_1']['service'] = $tran['service'];
        $tray['2_1']['partner'] = $tran['partner'];
        $tray['2_1']['charset'] = $tran['charset'];
        $tray['2_1']['sign_type'] = $tran['sign_type'];
        $tray['2_1']['sign'] = $tran['sign'];
        $tray['2_1']['url_return'] = $tran['url_return'];
        $tray['2_1']['url_notify'] = $tran['url_notify'];
        $tray['2_1']['sn'] = $parm['sn'];
        $tray['2_1']['subject'] = $tran['subject'];
        $tray['2_1']['format'] = $tran['format'];
        $tray['2_1']['version'] = $tran['version'];
        $tray['2_1']['seller_id'] = $tran['seller_id'];
        $tray['2_1']['money'] = $parm['money'];
        $tray['2_1']['body'] = $tran['body'];
        $tray['2_1']['timestamp'] = date('Y-m-d H:i:s');
        $tray['2_1']['product_code'] = $tran['product_code'];
        $tray['2_1']['url_pri_key'] = $tran['url_pri_key'];
        $tray['2_1']['url_sdk'] = $tran['url_sdk'];
        //生成签名
        $tray['2_2'] = $this->_wapPaySignApply($tray['2_1']);
        $data['sign'] = $tray['2_2']['param'];
        $result[2] = fxy_lang(['request', 'success']);
        $result[3] = $data;
        return $result;
    }

    /**
     * 手机-支付-签名申请
     * @param array $tran['service'] 应用服务
     * @param array $tran['partner'] 合作伙伴ID
     * @param array $tran['charset'] 请求字符集
     * @param array $tran['sign_type'] 签名类型
     * @param array $tran['sign'] 签名
     * @param array $tran['url_notify'] 回调地址
     * @param array $tran['sn'] 商户网站唯一订单号
     * @param array $tran['subject'] 商品名称
     * @param array $tran['payment_type'] 支付类型
     * @param array $tran['seller_id'] 卖家账号
     * @param array $tran['money'] 支付金额
     * @param array $tran['body'] 商品描述
     * @return mixed
     */
    private function _wapPaySignApply($tran)
    {
        //加载SDK
        fxy_load($tran['url_sdk']);
        //公共参数
        $param = [];
        //实例化支付宝sdk里面的AopClient类,下单时需要的操作,都在这个类里面
        $client = new \AopClient();
        //支付宝分配给开发者的应用ID
        $client->appId = $tran['app_id'];
        //商户生成签名字符串所使用的签名算法类型
        $client->signType = $tran['sign_type'];
        //读取私钥文件
        $client->rsaPrivateKeyFilePath = $tran['url_pri_key'];
        $request = new \AlipayTradeWapPayRequest();
        $request->setReturnUrl($tran['url_return']);
        $request->setNotifyUrl($tran['url_notify']);
        //商户网站唯一订单号
        $param_2['out_trade_no'] = $tran['sn'];
        //订单总金额(必须定义成浮点型)
        $param_2['total_amount'] = floatval($tran['money']);
        //商品的标题/交易标题/订单标题/订单关键字等
        $param_2['subject'] = $tran['subject'];
        //销售产品码，商家和支付宝签约的产品码，为固定值QUICK_WAP_PAY
        $param_2['product_code'] = $tran['product_code'];
        $request->setBizContent(fcf_json($param_2, 'encode'));
        $record = $client->pageExecute($request);
        $tran['param'] = $record;
        return $tran;
    }

    /**
     * 手机-支付-回调
     * @return mixed
     */
    public function wapPayCallback()
    {
        //初始化变量
        $tran = $this->data;
        $result = fsi_result();
        //支付宝公钥
        $conf['url_pub_key'] = fxy_config('third.alipay.wap_pay.url_pub_key');
        //签名类型
        $conf['sign_type'] = fxy_config('third.alipay.wap_pay.sign_type');
        //SDK地址
        $conf['url_sdk'] = fxy_config('third.alipay.wap_pay.url_sdk');
        fxy_load($conf['url_sdk']);
        //服务处理
        $client = new \AopClient();
        //读取支付宝公钥文件
        //支付宝公钥文件路径
        $pub_key = file_get_contents($conf['url_pub_key']);
        $pub_key = str_replace("-----BEGIN PUBLIC KEY-----\n", '', $pub_key);
        $pub_key = str_replace("\n-----END PUBLIC KEY-----", '', $pub_key);
        $client->alipayPublicKey = $pub_key;
        $verify_result = $client->rsaCheckV1($tran, $conf['url_pub_key'], $conf['sign_type']);
        if (!$verify_result) {
            if (!isset($tran['trade_status'])) {
            } else if ($tran['trade_status'] == 'TRADE_SUCCESS') {
                $verify_result = true;
            } else if ($tran['trade_status'] == 'TRADE_FINISHED') {
                $result[2] = fxy_lang(['pay', 'complete']);
                $result[3] = $tran;
                return $result;
            }
        }
        //验证支付结果
        if ($verify_result) {
            $result[2] = fxy_lang(['pay', 'success']);
            $result[3] = $tran;
            return $result;
        } else {
            $result[0] = false;
            $result[1] = 1002;
            $result[2] = fxy_lang(['pay', 'fail']);
            $result[3]['result'] = $verify_result;
            $result[3]['param'] = $tran;
            return $result;
        }
    }

    /**
     * 退款申请
     * @param string $tran['pay_sn'] 支付订单SN
     * @param string $tran['deal_sn'] 交易订单SN
     * @param string $tran['refund_sn'] 退款订单SN
     * @param string $tran['deal_money'] 交易订单金额
     * @param string $tran['refund_money'] 退款订单金额
     * @param string $tran['refund_remark'] 退款订单备注
     * @return mixed
     */
    public function refundApply()
    {
        //初始化变量
        $tran = $this->data;
        $result = fsi_result();
        $predefined = [
            'pay_sn', 'deal_sn', 'refund_sn',
            'deal_money', 'refund_money', 'refund_remark',
        ];
        $tran = fsi_param([$tran, $predefined], '1.2.2');
        $parm['refund_sn'] = $tran['refund_sn'];
        $parm['deal_money'] = $tran['deal_money'];
        $parm['refund_money'] = $tran['refund_money'];
        $pempty = dsc_pempty($parm);
        if (!$pempty[0]) return $pempty;
        $parm['pay_sn'] = $tran['pay_sn'];
        $parm['deal_sn'] = $tran['deal_sn'];
        $pempty = dsc_unpempty([$parm['pay_sn'], $parm['deal_sn']]);
        if (!$pempty[0]) return $pempty;
        $parm['refund_remark'] = $tran['refund_remark'];
        $record = $this->_refundApply($tran);
        if (isset($record[0]) && is_bool($record[0])) {
            return $record;
        } else if (isset($record['error_response']['code'])) {
            $result[0] = false;
            $result[1] = 1004;
            $result[2] = $record['error_response']['sub_msg'];
            $result[3] = $record;
            return $result;
        } else if (isset($record['alipay_trade_refund_response']['code']) && $record['alipay_trade_refund_response']['code'] != '10000') {
            $result[0] = false;
            $result[1] = 1004;
            $result[2] = $record['alipay_trade_refund_response']['sub_msg'];
            $result[3] = $record;
            return $result;
        } else {
            $result[2] = fxy_lang(['request', 'success']);
            $result[3] = $record;
            return $result;
        }
        return $record;
    }

    /**
     * 退款申请
     * @param string $tran['pay_sn'] 支付订单SN
     * @param string $tran['deal_sn'] 交易订单SN
     * @param string $tran['refund_sn'] 退款订单SN
     * @param string $tran['deal_money'] 交易订单金额
     * @param string $tran['refund_money'] 退款订单金额
     * @param string $tran['refund_remark'] 退款订单备注
     * @return mixed
     */
    private function _refundApply($tran)
    {
        //初始化变量
        $tran = $this->data;
        $result = fsi_result();
        $predefined = [
            'pay_sn', 'deal_sn', 'refund_sn',
            'deal_money', 'refund_money', 'refund_remark',
        ];
        $tran = fsi_param([$tran, $predefined], '1.2.2');
        $parm['refund_sn'] = $tran['refund_sn'];
        $parm['deal_money'] = $tran['deal_money'];
        $parm['refund_money'] = $tran['refund_money'];
        $pempty = dsc_pempty($parm);
        if (!$pempty[0]) return $pempty;
        $parm['pay_sn'] = $tran['pay_sn'];
        $parm['deal_sn'] = $tran['deal_sn'];
        $pempty = dsc_unpempty([$parm['pay_sn'], $parm['deal_sn']]);
        if (!$pempty[0]) return $pempty;
        $parm['refund_remark'] = $tran['refund_remark'];
        //应用ID
        $conf['app_id'] = fxy_config('third.alipay.web_auth_info.app_id');
        //签名类型
        $conf['sign_type'] = fxy_config('third.alipay.web_auth_info.sign_type');
        //商户私钥
        $conf['url_pri_key'] = fxy_config('third.alipay.web_auth_info.url_pri_key');
        //SDK地址
        $conf['url_sdk'] = fxy_config('third.alipay.web_auth_info.url_sdk');
        //加载SDK
        fxy_load($conf['url_sdk']);
        //公共参数
        $param = [];
        //实例化支付宝sdk里面的AopClient类,下单时需要的操作,都在这个类里面
        $client = new \AopClient();
        //支付宝分配给开发者的应用ID
        $client->appId = $conf['app_id'];
        //商户生成签名字符串所使用的签名算法类型
        $client->signType = $conf['sign_type'];
        //读取私钥文件
        $client->rsaPrivateKeyFilePath = $conf['url_pri_key'];
        $request = new \AlipayTradeRefundRequest();
        //支付时传入的商户订单号
        $param_2['out_trade_no'] = $parm['deal_sn'];
        //支付时返回的支付宝交易号
        $param_2['trade_no'] = $parm['pay_sn'];
        //本次退款请求流水号
        $param_2['out_request_no'] = $parm['refund_sn'];
        //本次退款金额
        $param_2['refund_amount'] = floatval($parm['refund_money']);
        $request->setBizContent(fcf_json($param_2, 'encode'));
        $record = $client->execute($request);
        $record = fcf_json(fcf_json($record, 'encode'), 'decode');
        return $record;
    }

    /**
     * 退款回调
     * @return mixed
     */
    public function refundCallback()
    {
        //初始化变量
        $result = fsi_result();
        $result[0] = false;
        $result[1] = 1002;
        $result[2] = fxy_lang(['pay', 'not2', 'open3']);
        return $result;
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
     * @param string $tran['ip'] 目标IP
     * @return mixed
     */
    public function location()
    {
        //初始化变量
        $tran = $this->data;
        $result = fsi_result();
        $predefined = [
            'ip',
        ];
        $tran = fsi_param([$tran, $predefined], '1.2.2');
        $parm['ip'] = $tran['ip'];
        $pempty = dsc_pempty($parm);
        if (!$pempty[0]) return $pempty;
        //接口域
        $conf['domain'] = fxy_config('third.taobao.location.domain');
        $conf['data']['ip'] = $parm['ip'];
        $response = fss_http($conf['domain'], $conf['data'], [], 'post');
        $response = json_decode($response, true);
        if (!$response['code']) {
            $result[2] = fxy_lang(['request', 'success']);
            $result[3] = $response;
            return $result;
        } else {
            $result[0] = false;
            $result[1] = 1002;
            $result[2] = fxy_lang(['request', 'fail']);
            $result[3] = $response;
            return $result;
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
     * @param string $tran['domain'] 域名
     * @return mixed
     */
    public function describeDomainRecords()
    {
        //初始化变量
        $tran = $this->data;
        $result = fsi_result();
        $predefined = [
            'domain',
        ];
        $tran = fsi_param([$tran, $predefined], '1.2.2');
        $parm['domain'] = $tran['domain'];
        $pempty = dsc_pempty($parm);
        if (!$pempty[0]) return $pempty;
        //配置参数
        $param = [
            'Action' => 'DescribeDomainRecords',
            'DomainName' => $parm['domain'],
        ];
        $record = $this->ddns($param);
        if (!isset($record['Code'])) {
            $result[2] = fxy_lang(['request', 'success']);
            $result[3] = [$record];
            return $result;
        } else {
            $result[0] = false;
            $result[1] = 1002;
            $result[2] = $record['Message'];
            $result[3] = [$record];
            return $result;
        }
    }

    /**
     * 查询域名解析ID
     * @param string $tran['domain'] 域名
     * @param string $tran['rr'] 主机记录
     * @return mixed
     */
    public function getRecordId()
    {
        //初始化变量
        $tran = $this->data;
        $result = fsi_result();
        $predefined = [
            'domain', 'rr',
        ];
        $tran = fsi_param([$tran, $predefined], '1.2.2');
        $parm['domain'] = $tran['domain'];
        $parm['rr'] = $tran['rr'];
        $pempty = dsc_pempty($parm);
        if (!$pempty[0]) return $pempty;
        //配置参数
        $record = $this->describeDomainRecords();
        if (!$record[0]) return $record;
        $data = $record[3][0];
        $list = $data['DomainRecords']['Record'];
        //获取指定解析
        $RR = null;
        $list = fmf_json($list, 'decode');
        foreach ($list as $key => $value) {
            if ($parm['rr'] === $value['RR']) {
                $RR = $value;
            }
        }
        if ($RR !== null) {
            $result[2] = fxy_lang(['request', 'success']);
            $result[3] = [$RR];
            return $result;
        } else {
            $result[0] = false;
            $result[1] = 1002;
            $result[2] = fxy_lang(['record', 'not', 'exists']);
            return $result;
        }
    }

    /**
     * 更新域名解析
     * @param string $tran['domain'] 域名
     * @param string $tran['rr'] 主机记录
     * @param string $tran['type'] 记录类型
     * @param string $tran['value'] 记录值
     * @return mixed
     */
    public function updateDomainRecord()
    {
        //初始化变量
        $tran = $this->data;
        $result = fsi_result();
        $predefined = [
            'domain', 'rr', 'type',
            'value',
        ];
        $tran = fsi_param([$tran, $predefined], '1.2.2');
        $parm['domain'] = $tran['domain'];
        $parm['rr'] = $tran['rr'];
        $parm['type'] = $tran['type'];
        $parm['value'] = $tran['value'];
        $pempty = dsc_pempty($parm);
        if (!$pempty[0]) return $pempty;
        //查询域名解析ID
        $record = $this->getRecordId();
        if (!$record[0]) return $record;
        $RecordId = $record[3][0]['RecordId'];
        //配置参数
        $param = [
            'Action' => 'UpdateDomainRecord',
            'RecordId' => $RecordId,
            'RR' => $parm['rr'],
            'Type' => $parm['type'],
            'Value' => $parm['value'],
        ];
        $record = $this->ddns($param);
        if (!isset($record['Code'])) {
            $result[2] = fxy_lang(['request', 'success']);
            $result[3] = [$record];
            return $result;
        } else {
            $result[0] = false;
            $result[1] = 1002;
            $result[2] = $record['Message'];
            $result[3] = [$record];
            return $result;
        }
    }

    /**
     * 动态域名服务
     * @param string $request 请求参数
     * @param string $tran['key'] 访问ID
     * @param string $tran['secret'] 访问密钥
     * @return mixed
     */
    private function ddns($request)
    {
        //初始化变量
        $tran = $this->data;
        $result = fsi_result();
        $predefined = [
            'key' => fxy_config('third.aliyun.ddns.access_key_id'), 'secret' => fxy_config('third.aliyun.ddns.access_key_secret'),
        ];
        $tran = fsi_param([$tran, $predefined], '1.1.2');
        $parm['key'] = $tran['key'];
        $parm['secret'] = $tran['secret'];
        $timezone = date_default_timezone_get();
        date_default_timezone_set('GMT');
        $param = [
            'Format' => 'JSON',
            'Version' => '2015-01-09',
            'AccessKeyId' => $parm['key'],
            'Timestamp' => date('Y-m-d\TH:i:s\Z'),
            'SignatureMethod' => 'HMAC-SHA1',
            'SignatureVersion' => '1.0',
            'SignatureNonce' => substr(md5(rand(1, 99999999)), rand(1, 9), 14),
        ];
        date_default_timezone_set($timezone);
        $param = array_merge($param, $request);
        //签名
        $param['Signature'] = $this->sign($param, $parm['secret']);
        $uri = http_build_query($param);
        $url = 'http://alidns.aliyuncs.com/?' . $uri;
        $response = fss_http($url, '', [], 'get');
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
