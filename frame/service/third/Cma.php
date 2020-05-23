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
 * 中国气象局
 * @return mixed
 */
class Cma extends Third
{
    /**
     * 服务
     * @param string $name 服务名称
     * @return void|Cnwea
     */
    public function service($name)
    {
        $data = $this->data;
        $supplier = $this->supplier;
        $name = strtolower($name);
        switch ($name) {
            case 'cnwea':
                return new Cnwea($data, $supplier);
        }
    }
}

/**
 * 中国天气网
 * @return mixed
 */
class Cnwea extends Cma
{
    /**
     * 天气
     * @param string $entry['ctid'] 城市ID
     * @return mixed
     */
    public function weather()
    {
        // 初始化变量
        $entry = $this->data;
        $echo = \fxapp\Server::echo();
        $predefined = [
            'ctid',
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.2.2');
        $tray['ctid'] = $entry['ctid'];
        $pempty = \fxapp\Data::paramEmpty($tray);
        if (!$pempty[0]) return $pempty;
        // 接口域
        $conf['domain'] = \fxapp\Base::config('third.cnwea.weather.domain');
        $conf['domain'] = $conf['domain'] . $tray['ctid'] . '.html';
        $response = \fxapp\Service::http($conf['domain'], '', [], 'get');
        $response = json_decode($response, true);
        if (isset($response['weatherinfo'])) {
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

    /**
     * 天气2
     * @param string $entry['ctid'] 城市ID
     * @return mixed
     */
    public function weather2()
    {
        // 初始化变量
        $entry = $this->data;
        $echo = \fxapp\Server::echo();
        $predefined = [
            'ctid',
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.2.2');
        $tray['ctid'] = $entry['ctid'];
        $pempty = \fxapp\Data::paramEmpty($tray);
        if (!$pempty[0]) return $pempty;
        // 接口域
        $conf['domain'] = \fxapp\Base::config('third.cnwea.weather2.domain');
        $conf['domain'] = $conf['domain'] . $tray['ctid'] . '.html';
        $response = \fxapp\Service::http($conf['domain'], '', [], 'get');
        $response = json_decode($response, true);
        if (isset($response['weatherinfo'])) {
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
