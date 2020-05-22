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
namespace fxyin\service\notify;

use fxyin\service\Notify;

/**
 * 短信
 * @return mixed
 */
class Sms extends Notify
{
    /**
     * 阿里大于
     * @param string $param['account'] 邮箱账号
     * @param string $param['_param'] 信息参数
     * @return mixed
     */
    public function alidayu()
    {
        // 初始化变量
        $param = $this->data;
        $echo = \fxapp\Server::echo();
        $predefined = [
            'account', '_param',
        ];
        $param = \fxapp\Param::define([$param, $predefined], '1.2.1');
        $predefined = [
            '_sms_type', '_sms_sign', '_sms_param',
            '_sms_template',
        ];
        $param['_param'] = \fxapp\Param::define([$param['_param'], $predefined], '1.2.1');
        $tray['account'] = $param['account'];
        $pempty = \fxapp\Data::paramEmpty($tray);
        if (!$pempty[0]) return $pempty;
        $tray['_param'] = $param['_param'];
        // 初始化环境变量
        // 应用钥匙
        $conf['appkey'] = \fxapp\Base::config('notify.sms.alidayu.app_key');
        // 应用密钥
        $conf['secretKey'] = \fxapp\Base::config('notify.sms.alidayu.app_secret');
        // SDK地址
        $conf['url_sdk'] = \fxapp\Base::config('notify.sms.alidayu.url_sdk');
        $pempty = \fxapp\Data::paramEmpty($conf);
        if (!$pempty[0]) {
            $pempty[2] = \fxapp\Base::lang(['lack', 'api', 'config']);
            return $pempty;
        }
        \fxapp\Base::load($conf['url_sdk']);
        // 开始执行
        $c = new \TopClient;
        $c->appkey = $conf['appkey'];
        $c->secretKey = $conf['secretKey'];
        $req = new \AlibabaAliqinFcSmsNumSendRequest;
        // $req->setExtend('123456');
        $req->setSmsType($tray['_param']['_sms_type']);
        $req->setSmsFreeSignName($tray['_param']['_sms_sign']);
        $req->setSmsParam($tray['_param']['_sms_param']);
        $req->setRecNum($tray['account']);
        $req->setSmsTemplateCode($tray['_param']['_sms_template']);
        $resp = $c->execute($req);
        $record['status'] = $resp->result->success;
        $record['msg'] = $resp->msg;
        $record['sub_msg'] = $resp->sub_msg;
        if ($record['status']) {
            $echo[2] = \fxapp\Base::lang(['send', 'success']);
            return $echo;
        } else {
            $echo[0] = false;
            $echo[1] = 1002;
            $echo[2] = $record['sub_msg'] ?: $record['msg'];
            if ($echo[2] == '触发业务流控') {
                $echo[2] = \fxapp\Base::lang(['send', 'fail', ',', 'sms', 'interval', '1', 'minute']);
            } else {
                $echo[2] = \fxapp\Base::lang(['send', 'fail', ',', $echo[2]]);
            }
            return $echo;
        }
    }

    /**
     * Webservice
     * @param string $param['account'] 手机账号
     * @param string $param['content'] 信息内容
     * @return mixed
     */
    public function webservice()
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
        // 企业账号
        $conf['corporation'] = \fxapp\Base::config('notify.sms.webservice.corporation');
        // 接入号即服务代码
        $conf['src_tele_num'] = \fxapp\Base::config('notify.sms.webservice.src_tele_num');
        // 接口域
        $conf['domain'] = \fxapp\Base::config('notify.sms.webservice.domain');
        $pempty = \fxapp\Data::paramEmpty($conf);
        if (!$pempty[0]) {
            $pempty[2] = \fxapp\Base::lang(['lack', 'api', 'config']);
            return $pempty;
        }
        // 设置配置
        $conf['message_id'] = -1;
        $conf['password'] = (int) substr($tray['account'], 7, 4) * 3 + 6016;
        $conf['src_tele_num'] = $conf['src_tele_num'];
        $conf['dest_tele_num'] = $tray['account'];
        $conf['message'] = $tray['content'];
        // 开始执行
        $wsdl = $conf['domain'];
        $client = new \SoapClient($wsdl);
        $corporation = $conf['corporation'];
        $message = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<infos>'
            . '<info>'
            . '<msg_id><![CDATA[' . $conf['message_id'] . ']]></msg_id>'
            . '<password><![CDATA[' . $conf['password'] . ']]></password>'
            . '<src_tele_num><![CDATA[' . $conf['src_tele_num'] . ']]></src_tele_num>'
            . '<dest_tele_num><![CDATA[' . $conf['dest_tele_num'] . ']]></dest_tele_num>'
            . '<msg><![CDATA[' . $conf['message'] . ']]></msg>'
            . '</info>'
            . '</infos>';
        $tray['2_1'] = ['in0' => $corporation, 'in1' => $message];
        $record = $client->sendmsg($tray['2_1']);
        $doc = new \SimpleXMLElement($record->out);
        $status = $doc->info->state;
        if ($status == 0) {
            // 发送成功
            $echo[2] = \fxapp\Base::lang(['send', 'success']);
            return $echo;
        } else {
            $errinfo = '发送失败！';
            switch ($status) {
                case -1:
                    $errinfo = '企业帐号错误！';
                    break;
                case -2:
                    $errinfo = '验证码格式错误！';
                    break;
                case -3:
                    $errinfo = '接入号即服务代码错误！';
                    break;
                case -4:
                    $errinfo = '手机号码错误！';
                    break;
                case -5:
                    $errinfo = '消息为空！';
                    break;
                case -6:
                    $errinfo = '消息太长！';
                    break;
                case -7:
                    $errinfo = '验证码不匹配！';
                    break;
            }
            // 返回发送失败的提示
            $echo[0] = false;
            $echo[1] = 1002;
            $echo[2] = $errinfo;
            return $echo;
        }
    }
}
