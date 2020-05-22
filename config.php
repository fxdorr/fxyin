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

/**
 * 基础配置
 * @return array
 */
return [
    // 环境配置
    'env' => [
        // 应用配置
        'app' => [
            // 基础应用
            0 => __DIR__ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR,
        ],
        // 基础配置
        'base' => [
            // 应用目录
            'app' => '',
            // 主机名称
            'web' => '',
        ],
    ],
    // 加载器配置
    'loader' => [
        // 框架配置
        'fxyin' => [
            // 基础框架
            0 => __DIR__ . DIRECTORY_SEPARATOR . 'frame' . DIRECTORY_SEPARATOR,
        ],
        // 应用配置
        'fxapp' => [
            // 基础应用
            0 => __DIR__ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR,
        ],
    ],
];
