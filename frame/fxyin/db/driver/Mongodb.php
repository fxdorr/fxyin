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
namespace fxyin\db\driver;

use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Command;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Query as MongoQuery;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use fxyin\db\Connection;
use fxyin\Db;
use Exception;

class Mongodb extends Connection
{
    //数据库名
    protected $dbName = '';
    //查询数据类型
    protected $typeMap = 'array';
    //数据库对象
    protected $mongo;
    //游标对象
    protected $cursor;
    
    //当前数据库连接
    protected $linkID;
    protected $linkRead;
    protected $linkWrite;
    //数据库选项
    protected $options = [
        //数据库名&表名
        'table' => '',
        //条件
        'where' => [],
        //限制
        'projection' => [],
        //排序
        'order' => [],
        //数量
        'limit' => '',
    ];
    
    //返回或者影响记录数
    protected $numRows = 0;

    /**
     * 架构函数
     * @param array $config 数据库配置数组
     * @return void
     */
    public function __construct(array $config = [])
    {
        if (!class_exists('\MongoDB\Driver\Manager')) {
            throw new Exception('require mongodb > 1.0');
        }

        if (!empty($config)) {
            $this->config = array_merge($this->config, $config);
        }
    }

    /**
     * 连接-初始化
     * @return void
     */
    protected function initConnect()
    {
        if (!$this->mongo) {
            $this->mongo = $this->connect();
        }
    }

    /**
     * 连接-建立
     * @return mixed
     */
    public function connect($config = [], $linkNum = 0)
    {
        if (!isset($this->links[$linkNum])) {
            if (empty($config)) {
                $config = $this->config;
            } else {
                $config = array_merge($this->config, $config);
            }

            $this->dbName = $config['database'];
            $this->typeMap = $config['type_map'];

            $dsn = 'mongodb://' . $config['hostname'] . ':' . $config['hostport'] . '/' . $config['database'];
            $conf = [
    //             'username'=>$config['username'],
    //             'password'=>$config['password'],
    //             'db'=>$config['database'],
            ];
            $this->links[$linkNum] = new Manager($dsn, $conf);
        }
        return $this->links[$linkNum];
    }

    /**
     * 记录-查询
     * @return mixed
     */
    public function select()
    {
        $this->initConnect();
        Db::$queryTimes++;

        $option = $this->options;

        $filter = $option['where'];
        $options = [
            'projection' => $option['projection'],
            'sort' => $option['order'],
            'limit' => $option['limit'],
        ];
        
        //查询对象
        $query = new MongoQuery($filter, $options);
        //查询数据
        $record = $this->query($option['table'], $query);

        return $record;
    }

    /**
     * 记录-插入
     * @param array $data 数据
     * @return mixed
     */
    public function insert($data = [])
    {
        $this->initConnect();
        Db::$queryTimes++;

        if (empty($data)) {
            return false;
        }

        $option = $this->options;

        //创建对象
        $bulk = new BulkWrite;
        $bulk->insert($data);
        //写入数据
        $record = $this->execute($option['table'], $bulk);

        return $record;
    }

    /**
     * 记录-更新
     * @return mixed
     * @deprecated
     */
    public function update()
    {
        $this->initConnect();
        Db::$queryTimes++;
    }

    /**
     * 记录-删除
     * @return mixed
     * @deprecated
     */
    public function delete()
    {
        $this->initConnect();
        Db::$queryTimes++;


    }

    /**
     * 原生-查询
     * @access public
     * @param string            $namespace 当前查询的collection
     * @param MongoQuery        $query 查询对象
     * @param ReadPreference    $readPreference readPreference
     * @param string|bool       $class 返回的数据集类型
     * @param array|string      $typeMap 指定返回的typeMap
     * @return mixed
     */
    public function query($namespace, MongoQuery $query, ReadPreference $readPreference = null, $class = false, $typeMap = null)
    {
        $this->initConnect();
        Db::$queryTimes++;

        if (false === strpos($namespace, '.')) {
            $namespace = $this->dbName . '.' . $namespace;
        }

        $this->cursor = $this->mongo->executeQuery($namespace, $query, $readPreference);

        return $this->getResult($class, $typeMap);
    }

    /**
     * 原生-指令
     * @access public
     * @param Command           $command 指令
     * @param string            $dbName 当前数据库名
     * @param ReadPreference    $readPreference readPreference
     * @param string|bool       $class 返回的数据集类型
     * @param array|string      $typeMap 指定返回的typeMap
     * @return mixed
     */
    public function command(Command $command, $dbName = '', ReadPreference $readPreference = null, $class = false, $typeMap = null)
    {
        $this->initConnect();
        Db::$queryTimes++;

        $dbName = $dbName ? : $this->dbName;

        $this->cursor = $this->mongo->executeCommand($dbName, $command, $readPreference);

        return $this->getResult($class, $typeMap);
    }

    /**
     * 原生-写操作
     * @access public
     * @param string        $namespace
     * @param BulkWrite     $bulk
     * @param WriteConcern  $writeConcern
     * @return WriteResult
     */
    public function execute($namespace, BulkWrite $bulk, WriteConcern $writeConcern = null)
    {
        $this->initConnect();
        Db::$executeTimes++;

        if (false === strpos($namespace, '.')) {
            $namespace = $this->dbName . '.' . $namespace;
        }

        $writeResult = $this->mongo->executeBulkWrite($namespace, $bulk, $writeConcern);

        $this->numRows = $writeResult->getMatchedCount();

        return $writeResult;
    }

    /**
     * 获得数据集
     * @access protected
     * @param bool|string       $class true 返回Mongo cursor对象 字符串用于指定返回的类名
     * @param array|string      $typeMap 指定返回的typeMap
     * @return mixed
     */
    protected function getResult($class = '', $typeMap = null)
    {
        if (true === $class) {
            return $this->cursor;
        }

        // 设置结果数据类型
        if (is_null($typeMap)) {
            $typeMap = $this->typeMap;
        }

        $typeMap = is_string($typeMap) ? ['root' => $typeMap] : $typeMap;

        $this->cursor->setTypeMap($typeMap);

        // 获取数据集
        $result = $this->cursor->toArray();

        $this->numRows = count($result);

        return $result;
    }

    /**
     * 预定义-限制
     * $param string $projection 限制
     * @return mixed
     */
    public function projection($projection)
    {
        $this->options['projection'] = $projection;
        return $this;
    }
}
