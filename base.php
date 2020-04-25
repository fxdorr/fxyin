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
//环境配置
$_ENV['fxy']['version'] = '1.0.0';                      //框架版本
$_ENV['fxy']['start_time'] = microtime(true);           //时间戳和微秒数
$_ENV['fxy']['start_mem'] = memory_get_usage();         //分配内存量
//关键字声明
$_ENV['fxy']['note']['dqa'] = 'Data Query Assemble';    //数据查询组装
$_ENV['fxy']['note']['dqc'] = 'Data Query Check';       //数据查询检查
$_ENV['fxy']['note']['dqo'] = 'Data Query Operate';     //数据查询操作
$_ENV['fxy']['note']['dsc'] = 'Data Structure Check';   //数据结构检查
$_ENV['fxy']['note']['dso'] = 'Data Structure Operate'; //数据结构操作
$_ENV['fxy']['note']['fcc'] = 'Frame Common Check';     //框架公共检查
$_ENV['fxy']['note']['fco'] = 'Frame Common Operate';   //框架公共操作
$_ENV['fxy']['note']['fcs'] = 'Frame Common Service';   //框架公共服务
$_ENV['fxy']['note']['fcf'] = 'Frame Common Function';  //框架公共函数
$_ENV['fxy']['note']['fmc'] = 'Frame Module Check';     //框架模块检查
$_ENV['fxy']['note']['fmf'] = 'Frame Module Function';  //框架模块函数
$_ENV['fxy']['note']['fmo'] = 'Frame Module Operate';   //框架模块操作
$_ENV['fxy']['note']['fsc'] = 'Frame Service Core';     //框架服务核心
$_ENV['fxy']['note']['fsi'] = 'Frame Service Init';     //框架服务初始化
$_ENV['fxy']['note']['fss'] = 'Frame Service Send';     //框架服务发送
$_ENV['fxy']['note']['fst'] = 'Frame Service Third';    //框架服务第三方
$_ENV['fxy']['note']['ftc'] = 'Frame Terminal Client';  //框架终端客户端
$_ENV['fxy']['note']['fts'] = 'Frame Terminal Server';  //框架终端服务器
$_ENV['fxy']['note']['fxy'] = 'Fxyin';                  //系统
//结构配置
$_ENV['fxy']['doc_root'] = __DIR__ . DIRECTORY_SEPARATOR;                               //根目录
$_ENV['fxy']['app_path'] = $_ENV['fxy']['doc_root'] . 'app' . DIRECTORY_SEPARATOR;      //应用目录
$_ENV['fxy']['lib_path'] = $_ENV['fxy']['doc_root'] . 'frame' . DIRECTORY_SEPARATOR;    //包目录
$_ENV['fxy']['core_path'] = $_ENV['fxy']['lib_path'] . 'fxyin' . DIRECTORY_SEPARATOR;   //核心程序目录
//加载文件
require $_ENV['fxy']['core_path'] . 'Loader.php';
//注册文件
\fxyin\Loader::register();
//加载惯例配置文件
\fxyin\Config::set(require $_ENV['fxy']['core_path'] . 'vendor' . DIRECTORY_SEPARATOR . 'convention.php');
//加载额外文件
require $_ENV['fxy']['core_path'] . 'vendor' . DIRECTORY_SEPARATOR . 'helper.php';
