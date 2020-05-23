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
namespace fxyin\service\notify;

use fxyin\service\Notify;

/**
 * 闪信
 * @return mixed
 */
class Flashsms extends Notify
{
    /**
     * 通用
     * @param string $param['account'] 手机账号
     * @param string $param['content'] 信息内容
     * @return mixed
     */
    public function common()
    {
        // 初始化变量
        $param = $this->data;
        $echo = \fxapp\Server::echo();
        $predefined = [
            'account', 'content',
        ];
        $param = \fxapp\Param::define([$param, $predefined], '1.2.1');
        $tray['account'] = $param['account'];
        $tray['content'] = $param['content'];
        $pempty = \fxapp\Data::paramEmpty($tray);
        if (!$pempty[0]) return $pempty;
        // 初始化环境变量
        // 用户ID
        $conf['uid'] = \fxapp\Base::config('notify.flashsms.common.uid');
        // 用户安全子系统标识，即能力工具箱分配的Appkey
        // 应用钥匙
        $conf['app_key'] = \fxapp\Base::config('notify.flashsms.common.app_key');
        // 应用密钥
        $conf['app_secret'] = \fxapp\Base::config('notify.flashsms.common.app_secret');
        // 消息的源地址，即开发者的接入码。[示例] 1065795555
        $conf['from'] = \fxapp\Base::config('notify.flashsms.common.from');
        // 因PHP的加密结果不匹配，密码摘要采用JAVA加密
        $conf['digest'] = \fxapp\Base::config('notify.flashsms.common.digest');
        // 接口域
        $conf['domain'] = \fxapp\Base::config('notify.flashsms.common.domain');
        $pempty = \fxapp\Data::paramEmpty($conf);
        if (!$pempty[0]) {
            $pempty[2] = \fxapp\Base::lang(['lack', 'api', 'config']);
            return $pempty;
        }
        // 待发数据
        $tray['2_1'] = [
            // 消息的源地址，即开发者的接入码。[示例] 1065795555
            'From' => $conf['from'],
            // 消息的目的地址，终端用户的手机号码。[示例] ["8618625150488","8618625150489"]
            'To' => $tray['account'],
            // 短信内容。[示例] hello world!
            'Body' => $tray['content'],
        ];
        $tray['3_1']['data'] = \fxapp\Param::json($tray['2_1'], 'encode');
        $tray['3_1']['data_count'] = strlen($tray['3_1']['data']);
        // 请求头配置
        $tray['3_1']['app_key'] = $conf['app_key'];
        // 随机数。参见OASIS WS-Security standard。
        $tray['3_1']['nonce'] = \fxapp\Math::rand(24);
        $tray['3_1']['time'] = time() - 28800;
        // 创建时间（UTC 时间）。[格式] yyyy-MM-dd'T'HH:mm:ss'Z'
        $tray['3_1']['created'] = date('Y-m-d', $tray['3_1']['time']) . 'T' . date('H:i:s', $tray['3_1']['time']) . 'Z';
        $tray['3_1']['password'] = $conf['app_secret'];
        // $tray['3_1']['passworddigest'] = base64_encode(hash('sha256', $tray['3_1']['nonce'].$tray['3_1']['created'].$tray['3_1']['password']));
        $conf['digest'] = \fxapp\Text::splice($conf['digest'], 'digest=' . $tray['3_1']['nonce'] . $tray['3_1']['created'] . $tray['3_1']['password'], '?');
        $tray['3_1']['digest'] = json_decode(\fxapp\Service::http($conf['digest'], '', [], 'post'), true);
        // 密码摘要。摘要算法如下：PasswordDigest = Base64 (SHA256 (nonce + created + App Secret))。
        $tray['3_1']['passworddigest'] = $tray['3_1']['digest']['info'];
        $tray['3_1']['header'] = [
            // 认证鉴权方式。能力工具箱采用 WSSE  UsernameToken，该参数固定为 “WSSE”。
            // 认证鉴权方，能力工具箱中默认为“SDP”。
            // 能力工具箱采用WSSE 的UsernameToken，该参数应该填为 “UsernameToken”。
            // 认证类型，固定为”Appkey”。
            'Authorization:WSSE realm="SDP", profile="UsernameToken",type="Appkey"',
            // 标识WSSE的认证类型： 能力工具箱使用WSSE的 UsernameToken进行认证，本参数填写“UsernameToken”。
            // 用户安全子系统标识，即能力工具箱分配的Appkey。
            // 密码摘要。摘要算法如下：PasswordDigest = Base64 (SHA256 (nonce + created + App Secret))。
            // 随机数。参见OASIS WS-Security standard。
            // 创建时间（UTC 时间）。[格式] yyyy-MM-dd'T'HH:mm:ss'Z'
            'X-WSSE:UsernameToken Username="' . $tray['3_1']['app_key'] . '",PasswordDigest="' . $tray['3_1']['passworddigest'] . '",Nonce="' . $tray['3_1']['nonce'] . '",Created="' . $tray['3_1']['created'] . '"',
            'Accept:text/json',
            'Content-Type:application/json;charset=UTF-8',
            'Content-Length: ' . $tray['3_1']['data_count']
        ];
        // 发送请求
        $record = \fxapp\Service::http($conf['domain'], $tray['3_1']['data'], $tray['3_1']['header']);
        // 响应解析
        $record = json_decode($record, true);
        if (empty($record) || isset($record['Code'])) {
            $errinfo = '发送失败！';
            switch ($record['Code']) {
                case 'E000001':
                    $errinfo = '请求消息中缺少Authorization认证头域。';
                    break;
                case 'E000002':
                    $errinfo = '在Authorization认证头域中缺失Realm字段。';
                    break;
                case 'E000003':
                    $errinfo = '在Authorization认证头域中缺失Profile字段。';
                    break;
                case 'E000004':
                    $errinfo = 'Authorization认证头域中Realm字段不合法。';
                    break;
                case 'E000005':
                    $errinfo = 'Authorization认证头域中Profile字段不合法。';
                    break;
                case 'E000006':
                    $errinfo = '请求消息中缺少X-WSSE认证头域。';
                    break;
                case 'E000007':
                    $errinfo = 'API调用时，请求消息头中X-WSSE头域内没有携带UserName字段。';
                    break;
                case 'E000008':
                    $errinfo = 'API调用时，请求消息头中X-WSSE头域内没有携带Nonce字段。';
                    break;
                case 'E000009':
                    $errinfo = 'API调用时，请求消息头中X-WSSE头域内没有携带Created字段，或X-WSSE头域没有携带Created字段。';
                    break;
                case 'E000010':
                    $errinfo = 'API调用时，请求消息头中X-WSSE头域内没有携带PasswordDigest字段。';
                    break;
                case 'E000011':
                    $errinfo = '在X-WSSE认证头域中PasswordDigest字段值不合法。';
                    break;
                case 'E000012':
                    $errinfo = '开发者账号不存在。';
                    break;
                case 'E000013':
                    $errinfo = '开发者状态不正常。';
                    break;
                case 'E000014':
                    $errinfo = 'IP认证失败。';
                    break;
                case 'E000015':
                    $errinfo = '速率超限。';
                    break;
                case 'E000016':
                    $errinfo = 'API状态不正常。';
                    break;
                case 'E000017':
                    $errinfo = '目的号码无效，缺少国家码。';
                    break;
                case 'E000018':
                    $errinfo = 'From号码为空。';
                    break;
                case 'E000019':
                    $errinfo = 'From号码不存在或不合法。';
                    break;
                case 'E000099':
                    $errinfo = '未知的参数。';
                    break;
                case 'E100001':
                    $errinfo = '开发者没有订购短信能力。';
                    break;
                case 'E100002':
                    $errinfo = '发送方号码（From）不支持短信能力。';
                    break;
                case 'E100005':
                    $errinfo = '短信消息中接收状态报告的回调地址不合法。';
                    break;
                case 'E100006':
                    $errinfo = '消息体为空。';
                    break;
                case 'E999999':
                    $errinfo = '服务器发生未知的错误。';
                    break;
                case 'E050010':
                    $errinfo = '目的号码不能为空。';
                    break;
                case 'E050011':
                    $errinfo = '目的号码长度不能超过20。';
                    break;
                case 'E050013':
                    $errinfo = '发送短信的目的号码不能超过100个。';
                    break;
                case 'E100004':
                    $errinfo = '短信或彩信内容或主题的长度大于最大长度。';
                    break;
            }
            // 返回发送失败的提示
            $echo[0] = false;
            $echo[1] = 1002;
            $echo[2] = $errinfo;
            return $echo;
        } else {
            // 发送成功
            $echo[2] = \fxapp\Base::lang(['send', 'success']);
            return $echo;
        }
    }
}
