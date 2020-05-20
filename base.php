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
//结构配置
$_ENV['fxy']['doc_root'] = __DIR__ . DIRECTORY_SEPARATOR;                               //根目录
$_ENV['fxy']['app_path'] = $_ENV['fxy']['doc_root'] . 'app' . DIRECTORY_SEPARATOR;      //应用目录
$_ENV['fxy']['lib_path'] = $_ENV['fxy']['doc_root'] . 'frame' . DIRECTORY_SEPARATOR;    //包目录
$_ENV['fxy']['core_path'] = $_ENV['fxy']['lib_path'] . 'fxyin' . DIRECTORY_SEPARATOR;   //核心程序目录
//加载文件
require $_ENV['fxy']['core_path'] . 'Loader.php';
//注册文件
\fxyin\Loader::register();
