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
use fxyin\service\driver\notify\email\common\Email as EmailDriver;

/**
 * 邮件
 * @return mixed
 */
class Email extends Notify
{
    /**
     * 通用
     * @param string $param['account'] 邮箱账号
     * @param string $param['title'] 信息标题
     * @param string $param['content'] 信息内容
     * @return mixed
     */
    public function common()
    {
        // 初始化变量
        $param = $this->data;
        $echo = \fxapp\Server::echo();
        $predefined = [
            'account', 'title', 'content',
        ];
        $param = \fxapp\Param::define([$param, $predefined], '1.2.1');
        $tray['account'] = $param['account'];
        $tray['title'] = $param['title'];
        $tray['content'] = $param['content'];
        $tray['check'] = \fxapp\Data::paramEmpty($tray, 1);
        if (!$tray['check'][0]) return $tray['check'];
        // 初始化环境变量
        // SMTP服务器
        $conf['smtpserver'] = \fxapp\Base::config('notify.email.common.smtpserver');
        // SMTP服务器端口
        $conf['smtpserverport'] = \fxapp\Base::config('notify.email.common.smtpport');
        // SMTP服务器的用户邮箱
        $conf['smtpusermail'] = \fxapp\Base::config('notify.email.common.formmail');
        // SMTP服务器的用户帐号
        $conf['smtpuser'] = \fxapp\Base::config('notify.email.common.mailuser');
        // SMTP服务器的用户密码
        $conf['smtppass'] = \fxapp\Base::config('notify.email.common.mailpass');
        $tray['check'] = \fxapp\Data::paramEmpty($conf, 1);
        if (!$tray['check'][0]) {
            $tray['check'][2] = \fxapp\Base::lang(['lack', 'api', 'config']);
            return $tray['check'];
        }
        // 发送给谁
        $conf['smtpemailto'] = $tray['account'];
        // 邮件主题
        $conf['mailtitle'] = $tray['title'];
        // 邮件内容
        $conf['mailcontent'] = $tray['content'];
        // 邮件格式（HTML/TXT）,TXT为文本邮件
        $conf['mailtype'] = 'HTML';
        // 开始执行
        // ************************ 配置信息 ****************************
        // 这里面的一个true是表示使用身份验证,否则不使用身份验证.
        $email = new EmailDriver($conf['smtpserver'], $conf['smtpserverport'], true, $conf['smtpuser'], $conf['smtppass']);
        // 是否显示发送的调试信息
        $email->debug = false;
        $state = $email->sendmail($conf['smtpemailto'], $conf['smtpusermail'], $conf['mailtitle'], $conf['mailcontent'], $conf['mailtype']);
        if ($state) {
            $echo[2] = \fxapp\Base::lang(['send', 'success']);
            return $echo;
        } else {
            $echo[0] = false;
            $echo[1] = 1002;
            $echo[2] = \fxapp\Base::lang(['send', 'fail']);
            return $echo;
        }
    }
}
