<?php
// +----------------------------------------------------------------------
// | Name fxyin
// +----------------------------------------------------------------------
// | Author wztqy <tqy@fxri.net>
// +----------------------------------------------------------------------
// | Copyright Copyright © 2016-2099 fxri. All rights reserved.
// +----------------------------------------------------------------------
// | Link http://www.fxri.net
// +----------------------------------------------------------------------
namespace fxyin\service\notify;

use fxyin\service\Notify;
use fxyin\service\driver\notify\email\common\Email as EmailDriver;

/**
 * 邮件
 * @return mixed
 */
class Email extends Notify
{
    /**
     * 通用
     * @param string $tran['account'] 邮箱账号
     * @param string $tran['title'] 信息标题
     * @param string $tran['content'] 信息内容
     * @return mixed
     */
    public function common()
    {
        //初始化变量
        $tran = $this->data;
        $result = fsi_result();
        $predefined = [
            'account', 'title', 'content',
        ];
        $tran = fsi_param([$tran, $predefined], '1.2.1');
        $parm['account'] = $tran['account'];
        $parm['title'] = $tran['title'];
        $parm['content'] = $tran['content'];
        $pempty = dsc_pempty($parm);
        if (!$pempty[0]) return $pempty;
        //初始化环境变量
        //SMTP服务器
        $conf['smtpserver'] = fxy_config('notify_email')['common']['smtpserver'];
        //SMTP服务器端口
        $conf['smtpserverport'] = fxy_config('notify_email')['common']['smtpport'];
        //SMTP服务器的用户邮箱
        $conf['smtpusermail'] = fxy_config('notify_email')['common']['formmail'];
        //SMTP服务器的用户帐号
        $conf['smtpuser'] = fxy_config('notify_email')['common']['mailuser'];
        //SMTP服务器的用户密码
        $conf['smtppass'] = fxy_config('notify_email')['common']['mailpass'];
        $pempty = dsc_pempty($conf);
        if (!$pempty[0]) {
            $pempty[2] = fxy_lang(['lack', 'api', 'config']);
            return $pempty;
        }
        $conf['smtpemailto'] = $parm['account'];//发送给谁
        $conf['mailtitle'] = $parm['title'];//邮件主题
        $conf['mailcontent'] = $parm['content'];//邮件内容
        $conf['mailtype'] = 'HTML';//邮件格式（HTML/TXT）,TXT为文本邮件
        //开始执行
        //************************ 配置信息 ****************************
        //这里面的一个true是表示使用身份验证,否则不使用身份验证.
        $email = new EmailDriver($conf['smtpserver'], $conf['smtpserverport'], true, $conf['smtpuser'], $conf['smtppass']);
        //是否显示发送的调试信息
        $email->debug = false;
        $state = $email->sendmail($conf['smtpemailto'], $conf['smtpusermail'], $conf['mailtitle'], $conf['mailcontent'], $conf['mailtype']);
        if ($state) {
            $result[2] = fxy_lang(['send', 'success']);
            return $result;
        } else {
            $result[0] = false;
            $result[1] = 1002;
            $result[2] = fxy_lang(['send', 'fail']);
            return $result;
        }
    }
}
