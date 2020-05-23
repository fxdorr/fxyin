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
 * 新浪
 * @return mixed
 */
class Sina extends Third
{
    /**
     * 服务
     * @param string $name 服务名称
     * @return void|SinaOpen
     */
    public function service($name)
    {
        $data = $this->data;
        $supplier = $this->supplier;
        $name = strtolower($name);
        switch ($name) {
            case 'open':
                return new SinaOpen($data, $supplier);
        }
    }
}

/**
 * 新浪开发平台
 * @return mixed
 */
class SinaOpen extends Sina
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
        $conf['param'] = '';
        $echo = \fxapp\Server::echo();
        $predefined = [
            'ip',
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.2.2');
        $tray['ip'] = $entry['ip'];
        $pempty = \fxapp\Data::paramEmpty($tray);
        if (!$pempty[0]) return $pempty;
        // 返回格式
        $conf['format'] = \fxapp\Base::config('third.sina.location.format');
        // 接口域
        $conf['domain'] = \fxapp\Base::config('third.sina.location.domain');
        $conf['data']['format'] = $conf['format'];
        $conf['data']['ip'] = $tray['ip'];
        $conf['param'] = \fxapp\Text::splice($conf['param'], 'format=' . $conf['data']['format'], '&');
        $conf['param'] = \fxapp\Text::splice($conf['param'], 'ip=' . $conf['data']['ip'], '&');
        $conf['domain'] = \fxapp\Text::splice($conf['domain'], $conf['param'], '?');
        $response = \fxapp\Service::http($conf['domain'], '', [], 'post');
        preg_match('/{[\s\S]+}/i', $response, $response);
        if ($response) {
            $response = json_decode($response[0], true);
        }
        if ($response) {
            $echo[2] = \fxapp\Base::lang(['request', 'success']);
            $echo[3] = $response;
            return $echo;
        } else {
            $echo[0] = false;
            $echo[1] = 1002;
            $echo[2] = \fxapp\Base::lang(['location', 'fail']);
            $echo[3] = $response;
            return $echo;
        }
    }
}
