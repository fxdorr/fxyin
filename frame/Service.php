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

namespace fxyin;

use fxyin\service\Notify as NotifyService;
use fxyin\service\Third as ThirdService;

/**
 * 服务类
 */
class Service
{
    /**
     * 原始数据
     * @var array
     */
    protected $data;

    /**
     * 服务类型
     * @var string
     */
    protected $type;

    /**
     * 服务供应商
     * @var string
     */
    protected $supplier;

    /**
     * 服务模型
     * @var mixed
     */
    protected $model;

    /**
     * 架构函数
     * @param array $data 请求参数
     * @param string $supplier 服务供应商
     * @return void
     */
    public function __construct($data = '', $supplier = '')
    {
        $this->setData($data);
        $this->setSupplier($supplier);
    }

    /**
     * 创建Service对象
     * @param array $data 请求参数
     * @param string $type 服务类型
     * @param string $supplier 服务供应商
     * @return Service|NotifyService|ThirdService
     */
    public static function create($data = '', $type = '', $supplier = '')
    {
        $type = empty($type) ? 'null' : strtolower($type);

        $class = false !== strpos($type, '\\') ? $type : '\\fxyin\\service\\' . ucfirst($type);
        if (class_exists($class)) {
            $service = new $class($data, $supplier);
        } else {
            $service = new static($data, $supplier);
        }

        return $service;
    }

    /**
     * 请求参数设置
     * @param mixed $data 输出数据
     * @return mixed
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * 请求参数获取
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * 服务类型设置
     * @param mixed $type 服务类型
     * @return mixed
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * 服务类型获取
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * 服务供应商设置
     * @param mixed $supplier 服务供应商
     * @return mixed
     */
    public function setSupplier($supplier)
    {
        $this->supplier = $supplier;
        return $this;
    }

    /**
     * 服务供应商获取
     * @return mixed
     */
    public function getSupplier()
    {
        return $this->supplier;
    }

    /**
     * 服务模型设置
     * @param mixed $model 服务模型
     * @return mixed
     */
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * 服务模型获取
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }
}
