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

class Third extends Service
{
    /**
     * 创建Third对象
     * @param string $supplier 服务供应商
     * @return Third|AlibabaService|BaiduService|CmaService|CupService
     * @return TencentService|SinaService|ToolService|YahooService
     */
    public function model($supplier = '')
    {
        //参数设置
        $data = $this->data;
        if ($supplier) {
            $this->setSupplier($supplier);
        } else {
            $supplier = $this->supplier;
        }
        //检查类是否存在
        $class = false !== strpos($supplier, '\\') ? $supplier : '\\fxyin\\service\\third\\' . ucfirst($supplier);
        if (class_exists($class)) {
            $service = new $class($data, $supplier);
        } else {
            $service = new static($data, $supplier);
        }
        return $service;
    }

    /**
     * 默认提示
     * @return mixed
     */
    public function __call($name, $data)
    {
        $result = fsi_result(2);
        $result[0] = false;
        $result[1] = fxy_lang(['third', 'service', '[', fxy_config('lang')['prefix'] . $this->getSupplier(), ']', 'not2', 'find2']);
        $result[3] = 1002;
        return $result;
    }
}
