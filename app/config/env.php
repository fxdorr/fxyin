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

$param = \fxapp\Client::param();
$param['view'] = \fxapp\Base::json($param['view'] ?? null, 'decode');
\fxyin\Lang::setLangCookieName(\fxapp\Base::config('app.lang.name'));
// 视图配置
$tray['lang'] = $param['view']['lang'] ?? ($_SERVER['HTTP_LANG'] ?? \fxyin\Lang::detect());
// 识别语言
$config = \fxapp\Base::config('app.lang');
if (!in_array($tray['lang'], $config['list'])) {
    $tray['lang'] = \fxapp\Base::config('app.lang.default');
}

/**
 * 环境配置
 * @return array
 */
return [
    // 视图配置
    'view' => [
        // 语言
        'lang' => $tray['lang'],
    ],
];
