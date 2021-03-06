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

namespace fxyin\service;

use fxyin\Service;
use fxyin\service\third\Alibaba as AlibabaService;
use fxyin\service\third\Baidu as BaiduService;
use fxyin\service\third\Cma as CmaService;
use fxyin\service\third\Cup as CupService;
use fxyin\service\third\Sina as SinaService;
use fxyin\service\third\Tencent as TencentService;
use fxyin\service\third\Tool as ToolService;
use fxyin\service\third\Yahoo as YahooService;

/**
 * 第三方服务
 * @return mixed
 */
class Third extends Service
{
    /**
     * 创建Third对象
     * @param string $supplier 服务供应商
     * @return Third|AlibabaService|BaiduService|CmaService|CupService|TencentService|SinaService|ToolService|YahooService
     */
    public function model($supplier = '')
    {
        // 参数设置
        $data = $this->data;
        if ($supplier) {
            $this->setSupplier($supplier);
        } else {
            $supplier = $this->supplier;
        }
        // 检查类是否存在
        $class = false !== strpos($supplier, '\\') ? $supplier : '\\fxyin\\service\\third\\' . ucfirst($supplier);
        if (class_exists($class)) {
            $service = new $class($data, $supplier);
        } else {
            $service = new static($data, $supplier);
        }
        return $service;
    }

    /**
     * 重载方法
     * @param string $name 名称
     * @param string $data 数据
     * @return mixed
     */
    public function __call($name, $data)
    {
        $echo = \fxapp\Server::echo();
        $echo[0] = false;
        $echo[1] = 1002;
        $echo[2] = \fxapp\Base::lang(['third', 'service', '[', \fxapp\Base::config('app.lang.prefix') . $this->getSupplier(), ']', 'not2', 'find2']);
        return $echo;
    }
}
