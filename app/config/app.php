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
 * 应用配置
 * @return array
 */
return [
    // 调试配置
    'debug' => [
        // 开关
        'switch' => true,
        // 等级
        'level' => null,
        // 数据
        'data' => [
            // 入参
            'param' => ftc_param(),
            'get' => $_GET ?? null,
            'post' => $_POST ?? null,
            'input' => file_get_contents('php://input'),
            // 文件
            'files' => $_FILES ?? null,
            // 环境
            'server' => $_SERVER ?? null,
            'cookie' => $_COOKIE ?? null,
            'session' => $_SESSION ?? null,
            'env' => $_ENV ?? null,
        ],
    ],
    // 日志配置
    'log' => [
        // 开关
        'switch' => false,
        // 等级
        'level' => '1,2,3,4,5,6',
    ],
    // 响应配置
    'echo' => [
        // 模板
        'template' => [
            // 逻辑状态
            0 => true,
            // 错误代码
            1 => 0,
            // 提示信息
            2 => '',
            // 响应数据
            3 => [],
            // 扩展数据
            4 => ['' => null],
        ],
        // 格式
        'format' => [
            // 错误代码
            1 => 'state',
            // 提示信息
            2 => 'message',
            // 响应数据
            3 => 'data',
            // 扩展数据
            4 => 'extend',
        ]
    ],
    // 语言配置
    'lang' => [
        // 开关-多语言
        'switch' => true,
        // 前缀-忽略转换
        'prefix' => '!@',
        // 前缀-忽略文本
        'ignore' => '!#',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ],
    // 媒体配置
    'media' => [
        // 视频
        'video' => [
            // 启动
            'bin' => null,
            // 目录
            'path' => [
                // Linux系统
                'linux' => null,
                // Windows系统
                'windows nt' => null,
            ],
        ],
    ],
    // 缓存配置
    'cache' => [
        // 文件缓存
        'file' => [
            // 类型
            'type' => 'file',
            // 设置不同的缓存保存目录
            'path' => '',
        ],
        // Redis缓存
        'redis' => [
            // 类型
            'type' => 'redis',
            // 地址
            'host' => '127.0.0.1',
            // 密码
            'password' => '',
        ],
    ],
];
