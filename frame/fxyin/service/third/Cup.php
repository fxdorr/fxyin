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
namespace fxyin\service\third;

use fxyin\service\Third;

/**
 * 中国银联
 * @return mixed
 */
class Cup extends Third
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
            case 'unionpay':
                return new Unionpay($data, $supplier);
        }
    }
}

/**
 * 银联支付
 * @return mixed
 */
class Unionpay extends Cup
{
    /**
     * 支付申请
     * @param string $tran['sn'] 订单SN
     * @param string $tran['money'] 支付金额
     * @return mixed
     */
    public function payApply()
    {
        //初始化变量
        $result = fsi_result();
        $result[0] = false;
        $result[1] = 1002;
        $result[2] = fxy_lang(['pay', 'not2', 'open3']);
        return $result;
    }

    /**
     * 支付回调
     * @return mixed
     */
    public function payCallback()
    {
        //初始化变量
        $result = fsi_result();
        $result[0] = false;
        $result[1] = 1002;
        $result[2] = fxy_lang(['pay', 'not2', 'open3']);
        return $result;
    }
}
