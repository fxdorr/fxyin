<?php
// +----------------------------------------------------------------------
// | Name fxyin
// +----------------------------------------------------------------------
// | Author wztqy <wztqy@139.com>
// +----------------------------------------------------------------------
// | Copyright Copyright © 2016-2099 fxri. All rights reserved.
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
        $result = fsi_result(2);
        $predefined = [
            'ctid',
        ];
        $tran = fsi_param([$tran, $predefined], '1.2.2');
        $parm['ctid'] = $tran['ctid'];
        $pempty = dsc_pempty($parm);
        if (!$pempty[0]) return $pempty;
        //接口域
        $conf['domain'] = fxy_config('third_cnwea')['weather']['domain'];
        $conf['domain'] = $conf['domain'] . $parm['ctid'] . '.html';
        $response = fss_http($conf['domain'], '', [], 'get');
        $response = json_decode($response, true);
        if (isset($response['weatherinfo'])) {
            $result[1] = fxy_lang(['request', 'success']);
            $result[2]['data'] = $response;
            return $result;
        } else {
            $result[0] = false;
            $result[1] = fxy_lang(['request', 'fail']);
            $result[2]['data'] = $response;
            $result[3] = 1002;
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
        $result = fsi_result(2);
        $predefined = [
            'ctid',
        ];
        $tran = fsi_param([$tran, $predefined], '1.2.2');
        $parm['ctid'] = $tran['ctid'];
        $pempty = dsc_pempty($parm);
        if (!$pempty[0]) return $pempty;
        //接口域
        $conf['domain'] = fxy_config('third_cnwea')['weather2']['domain'];
        $conf['domain'] = $conf['domain'] . $parm['ctid'] . '.html';
        $response = fss_http($conf['domain'], '', [], 'get');
        $response = json_decode($response, true);
        if (isset($response['weatherinfo'])) {
            $result[1] = fxy_lang(['request', 'success']);
            $result[2]['data'] = $response;
            return $result;
        } else {
            $result[0] = false;
            $result[1] = fxy_lang(['request', 'fail']);
            $result[2]['data'] = $response;
            $result[3] = 1002;
            return $result;
        }
    }
}
