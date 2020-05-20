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
namespace fxyin;

class Env
{
    //框架版本
    const VERSION = '1.1.0';

    //框架版本时间
    const VERSION_TIME = '2020-05-20';

    //关键字声明
    const KEYWORD = [
        //系统
        'fxy' => 'Fxyin',
        //数据查询组装
        'dqa' => 'Data Query Assemble',
        //数据查询检查
        'dqc' => 'Data Query Check',
        //数据查询操作
        'dqo' => 'Data Query Operate',
        //数据结构检查
        'dsc' => 'Data Structure Check',
        //数据结构操作
        'dso' => 'Data Structure Operate',
        //框架公共检查
        'fcc' => 'Frame Common Check',
        //框架公共操作
        'fco' => 'Frame Common Operate',
        //框架公共服务
        'fcs' => 'Frame Common Service',
        //框架公共函数
        'fcf' => 'Frame Common Function',
        //框架模块检查
        'fmc' => 'Frame Module Check',
        //框架模块函数
        'fmf' => 'Frame Module Function',
        //框架模块操作
        'fmo' => 'Frame Module Operate',
        //框架服务核心
        'fsc' => 'Frame Service Core',
        //框架服务初始化
        'fsi' => 'Frame Service Init',
        //框架服务发送
        'fss' => 'Frame Service Send',
        //框架服务第三方
        'fst' => 'Frame Service Third',
        //框架终端客户端
        'ftc' => 'Frame Terminal Client',
        //框架终端服务器
        'fts' => 'Frame Terminal Server',
    ];

    /**
     * 环境-初始化
     * @param string $module 模块
     * @return boolean
     */
    public static function init($module = '', $app_path = null)
    {
        //目录结构
        //--根目录
        $_ENV['base']['doc_root'] = dirname(__DIR__) . DIRECTORY_SEPARATOR;
        //网络协议
        if (isset($_SERVER['HTTP_HOST'])) {
            //--主机名称
            $_ENV['base']['web_path'] = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
        } else {
            //--主机名称
            $_ENV['base']['web_path'] = '';
        }
        //程序路由
        //--应用目录
        $_ENV['base']['app_path'] = $_ENV['base']['doc_root'] . 'app' . DIRECTORY_SEPARATOR;



        //结构配置
        $_ENV['fxy']['doc_root'] = __DIR__ . DIRECTORY_SEPARATOR;                               //根目录
        $_ENV['fxy']['app_path'] = $_ENV['fxy']['doc_root'] . 'app' . DIRECTORY_SEPARATOR;      //应用目录
        $_ENV['fxy']['lib_path'] = $_ENV['fxy']['doc_root'] . 'frame' . DIRECTORY_SEPARATOR;    //包目录
        $_ENV['fxy']['core_path'] = $_ENV['fxy']['lib_path'] . 'fxyin' . DIRECTORY_SEPARATOR;   //核心程序目录
    }
}
