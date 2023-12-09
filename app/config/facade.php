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

/**
 * 门面配置
 * @return array
 */
return [
    // 数据配置
    'data' => [
        // 条件配置
        'where' => [
            // 表达式
            -1 => null,
            // 键名
            0 => null,
            // 键值
            1 => null,
            // 方法
            2 => null,
            // 逻辑
            3 => 'and',
            // 分组
            4 => '1',
            // 取反
            5 => 0,
            // 大小写敏感
            6 => 1,
        ],
    ],
    // 服务器配置
    'server' => [
        // 响应配置
        'echo' => [
            // 模板
            'template' => [
                // 逻辑状态
                0 => true,
                // 状态代码
                1 => 1,
                // 提示信息
                2 => '',
                // 响应数据
                3 => [],
                // 扩展数据
                4 => ['' => null],
                // 请求时间
                5 => strval(time()),
            ],
            // 格式
            'format' => [
                // 状态代码
                1 => 'code',
                // 提示信息
                2 => 'message',
                // 响应数据
                3 => 'data',
                // 扩展数据
                4 => 'extend',
                // 请求时间
                5 => 'time',
            ],
        ],
    ],
];
