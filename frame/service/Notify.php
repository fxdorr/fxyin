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
namespace fxyin\service;

use fxyin\Service;
use fxyin\service\notify\Email as EmailService;
use fxyin\service\notify\Flashsms as FlashsmsService;
use fxyin\service\notify\Push as PushService;
use fxyin\service\notify\Sms as SmsService;

class Notify extends Service
{
    /**
     * 创建Notify对象
     * @param string $supplier 服务供应商
     * @return Notify|EmailService|FlashsmsService|PushService|SmsService
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
        $class = false !== strpos($supplier, '\\') ? $supplier : '\\fxyin\\service\\notify\\' . ucfirst($supplier);
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
        $result = fsi_result();
        $result[0] = false;
        $result[1] = 1002;
        $result[2] = \fxapp\Base::lang(['notify', 'service', '[', \fxapp\Base::config('app.lang.prefix') . $this->getSupplier(), ']', 'not2', 'find2']);
        return $result;
    }
}
