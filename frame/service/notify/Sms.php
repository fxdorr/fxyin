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
     * @param string $tran['account'] 邮箱账号
     * @param string $tran['_param'] 信息参数
     * @return mixed
     */
    public function alidayu()
    {
        //初始化变量
        $tran = $this->data;
        $result = fsi_result();
        $predefined = [
            'account', '_param',
        ];
        $tran = fsi_param([$tran, $predefined], '1.2.1');
        $predefined = [
            '_sms_type', '_sms_sign', '_sms_param',
            '_sms_template',
        ];
        $tran['_param'] = fsi_param([$tran['_param'], $predefined], '1.2.1');
        $parm['account'] = $tran['account'];
        $pempty = dsc_pempty($parm);
        if (!$pempty[0]) return $pempty;
        $parm['_param'] = $tran['_param'];
        //初始化环境变量
        //应用钥匙
        $conf['appkey'] = fxy_config('notify.sms.alidayu.app_key');
        //应用密钥
        $conf['secretKey'] = fxy_config('notify.sms.alidayu.app_secret');
        //SDK地址
        $conf['url_sdk'] = fxy_config('notify.sms.alidayu.url_sdk');
        $pempty = dsc_pempty($conf);
        if (!$pempty[0]) {
            $pempty[2] = fxy_lang(['lack', 'api', 'config']);
            return $pempty;
        }
        fxy_load($conf['url_sdk']);
        //开始执行
        $c = new \TopClient;
        $c->appkey = $conf['appkey'];
        $c->secretKey = $conf['secretKey'];
        $req = new \AlibabaAliqinFcSmsNumSendRequest;
        // $req->setExtend('123456');
        $req->setSmsType($parm['_param']['_sms_type']);
        $req->setSmsFreeSignName($parm['_param']['_sms_sign']);
        $req->setSmsParam($parm['_param']['_sms_param']);
        $req->setRecNum($parm['account']);
        $req->setSmsTemplateCode($parm['_param']['_sms_template']);
        $resp = $c->execute($req);
        $record['status'] = $resp->result->success;
        $record['msg'] = $resp->msg;
        $record['sub_msg'] = $resp->sub_msg;
        if ($record['status']) {
            $result[2] = fxy_lang(['send', 'success']);
            return $result;
        } else {
            $result[0] = false;
            $result[1] = 1002;
            $result[2] = $record['sub_msg'] ?: $record['msg'];
            if ($result[2] == '触发业务流控') {
                $result[2] = fxy_lang(['send', 'fail', ',', 'sms', 'interval', '1', 'minute']);
            } else {
                $result[2] = fxy_lang(['send', 'fail', ',', $result[2]]);
            }
            return $result;
        }
    }

    /**
     * Webservice
     * @param string $tran['account'] 手机账号
     * @param string $tran['content'] 信息内容
     * @return mixed
     */
    public function webservice()
    {
        //初始化变量
        $tran = $this->data;
        $result = fsi_result();
        $predefined = [
            'account', 'content',
        ];
        $tran = fsi_param([$tran, $predefined], '1.2.1');
        $parm['account'] = $tran['account'];
        $parm['content'] = $tran['content'];
        $pempty = dsc_pempty($parm);
        if (!$pempty[0]) return $pempty;
        //初始化环境变量
        //企业账号
        $conf['corporation'] = fxy_config('notify.sms.webservice.corporation');
        //接入号即服务代码
        $conf['src_tele_num'] = fxy_config('notify.sms.webservice.src_tele_num');
        //接口域
        $conf['domain'] = fxy_config('notify.sms.webservice.domain');
        $pempty = dsc_pempty($conf);
        if (!$pempty[0]) {
            $pempty[2] = fxy_lang(['lack', 'api', 'config']);
            return $pempty;
        }
        //设置配置
        $conf['message_id'] = -1;
        $conf['password'] = (int) substr($parm['account'], 7, 4) * 3 + 6016;
        $conf['src_tele_num'] = $conf['src_tele_num'];
        $conf['dest_tele_num'] = $parm['account'];
        $conf['message'] = $parm['content'];
        //开始执行
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
        $parm_2 = ['in0' => $corporation, 'in1' => $message];
        $record = $client->sendmsg($parm_2);
        $doc = new \SimpleXMLElement($record->out);
        $status = $doc->info->state;
        if ($status == 0) {
            //发送成功
            $result[2] = fxy_lang(['send', 'success']);
            return $result;
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
            //返回发送失败的提示
            $result[0] = false;
            $result[1] = 1002;
            $result[2] = $errinfo;
            return $result;
        }
    }
}
