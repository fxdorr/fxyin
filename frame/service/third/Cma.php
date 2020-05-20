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
 * 中国气象局
 * @return mixed
 */
class Cma extends Third
{
    /**
     * 服务
     * @param string $name 服务名称
     * @return mixed
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
     * @param string $tran['ctid'] 城市ID
     * @return mixed
     */
    public function weather()
    {
        //初始化变量
        $tran = $this->data;
        $result = fsi_result();
        $predefined = [
            'ctid',
        ];
        $tran = fsi_param([$tran, $predefined], '1.2.2');
        $parm['ctid'] = $tran['ctid'];
        $pempty = dsc_pempty($parm);
        if (!$pempty[0]) return $pempty;
        //接口域
        $conf['domain'] = fxy_config('third.cnwea.weather.domain');
        $conf['domain'] = $conf['domain'] . $parm['ctid'] . '.html';
        $response = fss_http($conf['domain'], '', [], 'get');
        $response = json_decode($response, true);
        if (isset($response['weatherinfo'])) {
            $result[2] = fxy_lang(['request', 'success']);
            $result[3] = $response;
            return $result;
        } else {
            $result[0] = false;
            $result[1] = 1002;
            $result[2] = fxy_lang(['request', 'fail']);
            $result[3] = $response;
            return $result;
        }
    }

    /**
     * 天气2
     * @param string $tran['ctid'] 城市ID
     * @return mixed
     */
    public function weather2()
    {
        //初始化变量
        $tran = $this->data;
        $result = fsi_result();
        $predefined = [
            'ctid',
        ];
        $tran = fsi_param([$tran, $predefined], '1.2.2');
        $parm['ctid'] = $tran['ctid'];
        $pempty = dsc_pempty($parm);
        if (!$pempty[0]) return $pempty;
        //接口域
        $conf['domain'] = fxy_config('third.cnwea.weather2.domain');
        $conf['domain'] = $conf['domain'] . $parm['ctid'] . '.html';
        $response = fss_http($conf['domain'], '', [], 'get');
        $response = json_decode($response, true);
        if (isset($response['weatherinfo'])) {
            $result[2] = fxy_lang(['request', 'success']);
            $result[3] = $response;
            return $result;
        } else {
            $result[0] = false;
            $result[1] = 1002;
            $result[2] = fxy_lang(['request', 'fail']);
            $result[3] = $response;
            return $result;
        }
    }
}
