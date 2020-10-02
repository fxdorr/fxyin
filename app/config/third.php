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

/**
 * 第三方配置
 * @return array
 */
return [
    // 百度配置
    'baidu' => [
        // 定位配置
        'location' => [
            // 应用钥匙
            'app_key' => '',
            // 坐标
            'coor' => 'bd09ll',
            // 接口地址
            'domain' => 'http://api.map.baidu.com/location/ip',
        ],
        // 天气配置
        'weather' => [
            // 应用钥匙
            'app_key' => '',
            // 返回格式
            'format' => 'json',
            // 接口地址
            'domain' => 'http://api.map.baidu.com/telematics/v3/weather',
        ],
    ],
    // 中国天气配置
    'cnwea' => [
        // 天气配置
        'weather' => [
            // 接口地址
            'domain' => 'http://www.weather.com.cn/data/sk/',
        ],
        // 天气配置
        'weather2' => [
            // 接口地址
            'domain' => 'http://www.weather.com.cn/data/cityinfo/',
        ],
    ],
    // 微信配置
    'wechat' => [
        // 网页授权配置
        'web_grant_token' => [
            // 应用钥匙
            'app_key' => '',
            // 应用密钥
            'app_secret' => '',
            // 应用授权作用域
            'scope' => 'snsapi_userinfo',
            // 重定向状态码
            'state' => md5(uniqid(rand(), true)),
            // 返回类型
            'response_type' => 'code',
            // 重定向地址
            'url_redirect' => '',
            // 接口地址
            'domain' => 'https://open.weixin.qq.com/connect/oauth2/authorize',
        ],
        // 网页鉴权配置
        'web_auth_token' => [
            // 应用钥匙
            'app_key' => '',
            // 应用密钥
            'app_secret' => '',
            // 授权类型
            'grant_type' => 'authorization_code',
            // 接口地址
            'domain' => 'https://api.weixin.qq.com/sns/oauth2/access_token',
        ],
        // 网页个人信息配置
        'web_auth_info' => [
            // 语言
            'lang' => 'zh_CN',
            // 接口地址
            'domain' => 'https://api.weixin.qq.com/sns/userinfo',
        ],
        // 应用支付
        'app_pay' => [
            // 应用ID
            'app_id' => '',
            // 应用密钥
            'app_secret' => '',
            // 附加数据
            'attach' => '',
            // 商品描述
            'body' => '',
            // 商户号
            'mch_id' => '',
            // 商户支付密钥
            'mch_key' => '',
            // CA证书
            'url_cacert' => '',
            // 回调地址
            'url_notify' => '',
            // 商户私钥
            'url_pri_key' => null,
            // SDK地址
            'url_sdk' => null,
            // SDK通知
            'url_sdk_notify' => null,
            // 代理地址
            'proxy_host' => '0.0.0.0',
            // 代理端口
            'proxy_port' => 0,
            // 报告等级
            'report_level' => 1,
        ],
        // JSSDK-授权配置-access_token
        'jssdk_grant_access_token' => [
            // 应用钥匙
            'app_key' => '',
            // 应用密钥
            'app_secret' => '',
            // 授权类型
            'grant_type' => 'client_credential',
            // 接口地址
            'domain' => 'https://api.weixin.qq.com/cgi-bin/token',
        ],
        // JSSDK-授权配置-jsapi_ticket
        'jssdk_grant_jsapi_ticket' => [
            // 类型
            'type' => 'jsapi',
            // 接口地址
            'domain' => 'https://api.weixin.qq.com/cgi-bin/ticket/getticket',
        ],
    ],
    // 淘宝配置
    'taobao' => [
        // 定位配置
        'location' => [
            // 接口地址
            'domain' => 'http://ip.taobao.com/service/getIpInfo.php',
        ],
    ],
    // 新浪配置
    'sina' => [
        // 定位配置
        'location' => [
            // 返回格式
            'format' => 'js',
            // 接口地址
            'domain' => 'http://int.dpool.sina.com.cn/iplookup/iplookup.php',
        ],
    ],
    // QQ配置
    'qq' => [
        // 网页鉴权配置
        'web_auth_token' => [
            // 应用钥匙
            'app_key' => '',
            // 应用密钥
            'app_secret' => '',
            // 接口地址
            'domain' => '',
        ],
        // 网页个人信息配置
        'web_auth_info' => [
            // 应用钥匙
            'app_key' => '',
            // 接口地址
            'domain' => 'https://graph.qq.com/user/get_simple_userinfo',
        ],
    ],
    // 支付宝配置
    'alipay' => [
        // 网页授权配置
        'web_grant_token' => [
            // 应用ID
            'app_id' => '',
            // 应用授权作用域
            'scope' => 'auth_user',
            // 重定向状态码
            'state' => md5(uniqid(rand(), true)),
            // 重定向地址
            'url_redirect' => '',
            // 接口地址
            'domain' => 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm',
        ],
        // 网页鉴权配置
        'web_auth_token' => [
            // 应用ID
            'app_id' => '',
            // 签名类型
            'sign_type' => 'RSA2',
            // 授权类型
            'grant_type' => 'authorization_code',
            // 商户私钥
            'url_pri_key' => null,
            // SDK地址
            'url_sdk' => null,
        ],
        // 网页刷新鉴权配置
        'web_refresh_token' => [
            // 应用ID
            'app_id' => '',
            // 签名类型
            'sign_type' => 'RSA2',
            // 授权类型
            'grant_type' => 'refresh_token',
            // 商户私钥
            'url_pri_key' => null,
            // SDK地址
            'url_sdk' => null,
        ],
        // 网页个人信息配置
        'web_auth_info' => [
            // 应用ID
            'app_id' => '',
            // 签名类型
            'sign_type' => 'RSA2',
            // 商户私钥
            'url_pri_key' => null,
            // SDK地址
            'url_sdk' => null,
        ],
        // 应用支付
        'app_pay' => [
            // 应用ID
            'app_id' => '',
            // 商品描述
            'body' => '',
            // 请求字符集
            'charset' => 'utf-8',
            // 合作伙伴ID
            'partner' => '',
            // 请求方案
            'scheme' => 'http',
            // 请求格式
            'format' => 'json',
            // 服务版本
            'version' => '1.0',
            // 卖家账号
            'seller_id' => '',
            // 应用服务
            'service' => 'alipay.trade.app.pay',
            // 签名
            'sign' => '',
            // 签名类型
            'sign_type' => 'RSA2',
            // 商品名称
            'subject' => '',
            // 销售产品码
            'product_code' => 'QUICK_MSECURITY_PAY',
            // 回调地址
            'url_notify' => '',
            // 商户私钥
            'url_pri_key' => null,
            // 支付宝公钥
            'url_pub_key' => null,
            // SDK地址
            'url_sdk' => null,
        ],
        // 手机支付
        'wap_pay' => [
            // 应用ID
            'app_id' => '',
            // 商品描述
            'body' => '',
            // 请求字符集
            'charset' => 'utf-8',
            // 合作伙伴ID
            'partner' => '',
            // 请求方案
            'scheme' => 'http',
            // 请求格式
            'format' => 'json',
            // 服务版本
            'version' => '1.0',
            // 卖家账号
            'seller_id' => '',
            // 应用服务
            'service' => 'alipay.trade.wap.pay',
            // 签名
            'sign' => '',
            // 签名类型
            'sign_type' => 'RSA2',
            // 商品名称
            'subject' => '',
            // 销售产品码
            'product_code' => 'QUICK_WAP_PAY',
            // 返回地址
            'url_return' => '',
            // 回调地址
            'url_notify' => '',
            // 商户私钥
            'url_pri_key' => null,
            // 支付宝公钥
            'url_pub_key' => null,
            // SDK地址
            'url_sdk' => null,
        ],
    ],
    // 工具配置
    'tool' => [
        // 二维码
        'qrcode' => [
            // SDK地址
            'url_sdk' => null,
        ],
    ],
    // 雅虎天气配置
    'yahwea' => [
        // 天气配置
        'weather' => [
            // 查询语句
            'query' => 'select%20*%20from%20weather.forecast%20where%20woeid%20in%20(select%20woeid%20from%20geo.places(1)%20where%20text=%22cityname,%20ak%22)',
            // 请求格式
            'format' => 'json',
            // 环境
            'env' => 'store://datatables.org/alltableswithkeys',
            // 接口地址
            'domain' => 'https://query.yahooapis.com/v1/public/yql',
        ],
    ],
    // 阿里云配置
    'aliyun' => [
        // 动态域名服务
        'ddns' => [
            // 访问ID
            'access_key_id' => '',
            // 访问密钥
            'access_key_secret' => '',
        ],
        // 对象存储
        'oss' => [
            // 访问ID
            'access_key_id' => '',
            // 访问密钥
            'access_key_secret' => '',
            // 存储空间
            'bucket' => '',
            // 文件域
            'domain' => '',
        ],
    ],
    // 七牛云配置
    'qiniu' => [
        // 上传服务
        'upload' => [
            // 访问钥匙
            'access_key' => '',
            // 访问密钥
            'secret_key' => '',
            // 存储空间
            'bucket' => '',
            // 文件域
            'domain' => '',
        ],
    ],
    // 钉钉配置
    'dingtalk' => [
        // 基础配置
        'base' => [
            // 代理ID
            'agent_id' => '',
            // 应用钥匙
            'app_key' => '',
            // 应用密钥
            'app_secret' => '',
            // 企业ID
            'corp_id' => '',
            // 接口地址
            'domain' => 'https://oapi.dingtalk.com/sns/getuserinfo_bycode',
            // SDK地址
            'url_sdk' => \fxapp\Base::config('env.base.root') . 'extend' . DIRECTORY_SEPARATOR . 'alibaba' . DIRECTORY_SEPARATOR . 'dingtalk' . DIRECTORY_SEPARATOR . 'TopSdk.php',
        ],
    ],
    // 政务钉钉配置
    'dingtalkind' => [
        // 基础
        'base' => [
            // 应用钥匙
            'app_key' => '',
            // 应用密钥
            'app_secret' => '',
            // SDK地址
            'url_sdk' => \fxapp\Base::config('env.base.root') . 'extend' . DIRECTORY_SEPARATOR . 'alibaba' . DIRECTORY_SEPARATOR . 'dingtalkind' . DIRECTORY_SEPARATOR . 'zwdd-sdk-php.php',
            // 接口地址
            'domain' => 'https://openplatform.dg-work.cn',
        ],
        // 鉴权
        'token' => [
            // 接口名称
            'api_name' => '/gettoken.json',
        ],
        // 用户信息
        'user_info' => [
            // 接口名称
            'api_name' => '/rpc/oauth2/dingtalk_app_user.json',
        ],
    ],
];
