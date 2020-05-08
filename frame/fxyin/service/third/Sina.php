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
 * 新浪
 * @return mixed
 */
class Sina extends Third
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
     * @param string $tran['ip'] 目标IP
     * @return mixed
     */
    public function location()
    {
        //初始化变量
        $tran = $this->data;
        $conf['param'] = '';
        $result = fsi_result();
        $predefined = [
            'ip',
        ];
        $tran = fsi_param([$tran, $predefined], '1.2.2');
        $parm['ip'] = $tran['ip'];
        $pempty = dsc_pempty($parm);
        if (!$pempty[0]) return $pempty;
        //返回格式
        $conf['format'] = fxy_config('third.sina.location.format');
        //接口域
        $conf['domain'] = fxy_config('third.sina.location.domain');
        $conf['data']['format'] = $conf['format'];
        $conf['data']['ip'] = $parm['ip'];
        $conf['param'] = dso_splice($conf['param'], 'format=' . $conf['data']['format'], '&');
        $conf['param'] = dso_splice($conf['param'], 'ip=' . $conf['data']['ip'], '&');
        $conf['domain'] = dso_splice($conf['domain'], $conf['param'], '?');
        $response = fss_http($conf['domain'], '', [], 'post');
        preg_match('/{[\s\S]+}/i', $response, $response);
        if ($response) {
            $response = json_decode($response[0], true);
        }
        if ($response) {
            $result[2] = fxy_lang(['request', 'success']);
            $result[3] = $response;
            return $result;
        } else {
            $result[0] = false;
            $result[1] = 1002;
            $result[2] = fxy_lang(['location', 'fail']);
            $result[3] = $response;
            return $result;
        }
    }
}
