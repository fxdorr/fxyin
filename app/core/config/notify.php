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

/**
 * 通知配置
 * @return array
 */
return [
    //短信配置
    'sms' => [
        //温州移动供应接口
        'webservice' => [
            //企业账号
            'corporation' => '',
            //接入号即服务代码
            'src_tele_num' => '',
            //接口域
            'domain' => '',
        ],
        //阿里大于
        'alidayu' => [
            //应用钥匙
            'app_key' => '',
            //应用密钥
            'app_secret' => '',
            //SDK地址
            'url_sdk' => $_ENV['fxy']['core_path'] . 'service' . DIRECTORY_SEPARATOR . 'driver' . DIRECTORY_SEPARATOR . 'notify' . DIRECTORY_SEPARATOR . 'sms' . DIRECTORY_SEPARATOR . 'alidayu' . DIRECTORY_SEPARATOR . 'TopSdk.php',
        ],
    ],
    //邮箱配置
    'email' => [
        //公共配置
        'common' => [
            //SMTP服务器
            'smtpserver' => '',
            //SMTP服务器端口
            'smtpport' => '',
            //SMTP服务器的用户邮箱
            'formmail' => '',
            //SMTP服务器的用户帐号
            'mailuser' => '',
            //SMTP服务器的用户密码
            'mailpass' => '',
        ],
    ],
    //推送配置
    'push' => [
        //极光推送
        'jpush' => [
            //应用钥匙
            'app_key' => '',
            //应用密钥
            'app_secret' => '',
            //SDK地址
            'url_sdk' => $_ENV['fxy']['core_path'] . 'service' . DIRECTORY_SEPARATOR . 'driver' . DIRECTORY_SEPARATOR . 'notify' . DIRECTORY_SEPARATOR . 'push' . DIRECTORY_SEPARATOR . 'jpush' . DIRECTORY_SEPARATOR . 'autoload.php',
        ],
    ],
    //闪信配置
    'flashsms' => [
        //公共配置
        'common' => [
            //用户ID
            'uid' => '',
            //应用钥匙
            'app_key' => '',
            //应用密钥
            'app_secret' => '',
            //消息的源地址，即开发者的接入码。[示例] 1065795555
            'from' => '',
            //因PHP的加密结果不匹配，密码摘要采用JAVA加密
            'digest' => '',
            //接口域
            'domain' => '',
        ],
    ],
];
