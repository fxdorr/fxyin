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
namespace fxyin\db;

use fxyin\Db;

class Connection
{
    //数据库连接
    protected $links = [];
    //数据库配置
    protected $config = [
        //类型
        'type' => '',
        //地址
        'hostname' => '',
        //数据库名
        'database' => '',
        //用户名
        'username' => '',
        //密码
        'password' => '',
        //端口
        'hostport' => '',
        //数据库表前缀
        'prefix' => '',
    ];
    //数据库选项
    protected $options = [
        //数据库名&表名
        'table' => '',
        //条件
        'where' => [],
        //排序
        'order' => [],
        //数量
        'limit' => '',
    ];

    /**
     * 构造函数
     * @return mixed
     */
    public function __construct(array $config = [])
    {
        if (!empty($config)) {
            $this->config = array_merge($this->config, $config);
        }
    }

    /**
     * 预定义-数据表
     * $param string $name 表名
     * @return mixed
     */
    public function table($name)
    {
        $this->options['table'] = $name;
        return $this;
    }

    /**
     * 预定义-条件
     * $param string $where 条件
     * @return mixed
     */
    public function where($where)
    {
        $this->options['where'] = $where;
        return $this;
    }

    /**
     * 预定义-排序
     * $param string $order 排序
     * @return mixed
     */
    public function order($order)
    {
        $this->options['order'] = $order;
        return $this;
    }

    /**
     * 预定义-数量
     * $param string $number 数量
     * @return mixed
     */
    public function limit($number)
    {
        $this->options['limit'] = $number;
        return $this;
    }
}
