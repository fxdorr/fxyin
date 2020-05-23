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
 * 中国银联
 * @return mixed
 */
class Cup extends Third
{
    /**
     * 服务
     * @param string $name 服务名称
     * @return void|Unionpay
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
     * @param string $entry['sn'] 订单SN
     * @param string $entry['money'] 支付金额
     * @return mixed
     */
    public function payApply()
    {
        // 初始化变量
        $echo = \fxapp\Server::echo();
        $echo[0] = false;
        $echo[1] = 1002;
        $echo[2] = \fxapp\Base::lang(['pay', 'not2', 'open3']);
        return $echo;
    }

    /**
     * 支付回调
     * @return mixed
     */
    public function payCallback()
    {
        // 初始化变量
        $echo = \fxapp\Server::echo();
        $echo[0] = false;
        $echo[1] = 1002;
        $echo[2] = \fxapp\Base::lang(['pay', 'not2', 'open3']);
        return $echo;
    }
}
