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
 * 雅虎中国
 * @return mixed
 */
class Yahoo extends Third
{
    /**
     * 服务
     * @param string $name 服务名称
     * @return void|Yahwea
     */
    public function service($name)
    {
        $data = $this->data;
        $supplier = $this->supplier;
        $name = strtolower($name);
        switch ($name) {
            case 'yahwea':
                return new Yahwea($data, $supplier);
        }
    }
}

/**
 * 雅虎天气
 * @return mixed
 */
class Yahwea extends Yahoo
{
    /**
     * 天气
     * @param string $entry['ctname'] 城市名称
     * @return mixed
     */
    public function weather()
    {
        // 初始化变量
        $entry = $this->data;
        $conf['param'] = '';
        $result = fsi_result();
        $predefined = [
            'ctname',
        ];
        $entry = fsi_param([$entry, $predefined], '1.2.2');
        $tray['ctname'] = $entry['ctname'];
        $pempty = \fxapp\Data::paramEmpty($tray);
        if (!$pempty[0]) return $pempty;
        // 环境
        $conf['env'] = \fxapp\Base::config('third.yahwea.weather.env');
        // 请求格式
        $conf['format'] = \fxapp\Base::config('third.yahwea.weather.format');
        // 查询语句
        $conf['query'] = \fxapp\Base::config('third.yahwea.weather.query');
        $conf['query'] = str_replace('cityname', $tray['ctname'], $conf['query']);
        // 接口域
        $conf['domain'] = \fxapp\Base::config('third.yahwea.weather.domain');
        $conf['data']['q'] = $conf['query'];
        $conf['data']['format'] = $conf['format'];
        $conf['data']['env'] = $conf['env'];
        // 拼接请求域
        foreach ($conf['data'] as $key => $value) {
            $conf['param'] = \fxapp\Text::splice($conf['param'], $key . '=' . $value, '&');
        }
        $conf['domain'] = \fxapp\Text::splice($conf['domain'], $conf['param'], '?');
        $response = \fxapp\Service::http($conf['domain'], '', [], 'get');
        $response = json_decode($response, true);
        if (isset($response['query'])) {
            $result[2] = \fxapp\Base::lang(['request', 'success']);
            $result[3] = $response;
            return $result;
        } else {
            $result[0] = false;
            $result[1] = 1002;
            $result[2] = \fxapp\Base::lang(['request', 'fail']);
            $result[3] = $response;
            return $result;
        }
    }
}
