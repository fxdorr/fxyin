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
namespace fxyin\service\notify;

use fxyin\service\Notify;
use JPush\Client;
use JPush\Config;

/**
 * 推送
 * @return mixed
 */
class Push extends Notify
{
    /**
     * 极光推送
     * @param string $param['title'] 信息标题
     * @param string $param['content'] 信息内容
     * @param string $param['param'] 信息参数
     * @return mixed
     */
    public function jpush()
    {
        // 初始化变量
        $param = $this->data;
        $result = fsi_result();
        $predefined = [
            'title', 'content', 'param',
        ];
        $param = fsi_param([$param, $predefined], '1.2.1');
        $tray['title'] = $param['title'];
        $tray['content'] = $param['content'];
        $pempty = \fxapp\Data::paramEmpty($tray);
        if (!$pempty[0]) return $pempty;
        $tray['param'] = $param['param'];
        // 初始化信息参数
        if (is_array($tray['param'])) {
            $tray['_param'] = $tray['param'];
        } else {
            $tray['_param'] = json_decode($tray['param'], true);
        }
        // 扩展参数
        $predefined = [
            '_set_extras' => [], '_set_options' => [],
        ];
        $tray['_param'] = fsi_param([$tray['_param'], $predefined], '1.1.2');
        // 推送参数
        $predefined = [
            '_push_mode' => 1, '_push_target' => 1,
        ];
        $tray['_param'] = fsi_param([$tray['_param'], $predefined], '1.1.2');
        // 选项参数
        $predefined = [
            'apns_production' => true,
        ];
        $tray['_param']['_set_options'] = fsi_param([$tray['_param']['_set_options'], $predefined], '1.1.2');
        // 初始化环境变量
        // 应用钥匙
        $conf['app_key'] = \fxapp\Base::config('notify.push.jpush.app_key');
        // 应用密钥
        $conf['app_secret'] = \fxapp\Base::config('notify.push.jpush.app_secret');
        // SDK地址
        $conf['url_sdk'] = \fxapp\Base::config('notify.push.jpush.url_sdk');
        $pempty = \fxapp\Data::paramEmpty($conf);
        if (!$pempty[0]) {
            $pempty[2] = \fxapp\Base::lang(['lack', 'api', 'config']);
            return $pempty;
        }
        \fxapp\Base::load($conf['url_sdk']);
        try {
            // 初始化服务
            $client = new Client($conf['app_key'], $conf['app_secret']);
            $model = $client->push()
                ->setPlatform('all')
                ->options($tray['_param']['_set_options']);
            // 推送模式
            $tray['_param']['_push_mode'] = \fxapp\Text::explode(',', $tray['_param']['_push_mode']);
            foreach ($tray['_param']['_push_mode'] as $key => $value) {
                switch ($value) {
                    case 1:
                        // 默认通知
                        $model = $model
                            ->setNotificationAlert($tray['content']);
                        break;
                    case 2:
                        // 自定义消息
                        $model = $model
                            ->message($tray['content'], [
                                'title' => $tray['title'],
                                'content_type' => 'text',
                                'extras' => $tray['_param']['_set_extras'],
                            ]);
                        break;
                    case 3:
                        // IOS通知
                        $model = $model
                            ->iosNotification($tray['content'], [
                                'sound' => 'sound.caf',
                                'badge' => '+1',
    //                             'sound' => Config::DISABLE_SOUND,
    //                             'badge' => Config::DISABLE_BADGE,
                                // 'content-available' => true,
                                // 'mutable-content' => true,
                                'category' => 'jiguang',
                                'extras' => $tray['_param']['_set_extras'],
                            ]);
                        break;;
                    case 4:
                        // Android通知
                        $model = $model
                            ->androidNotification($tray['content'], [
                                'title' => $tray['title'],
                                'extras' => $tray['_param']['_set_extras'],
                                // 'build_id' => 2,
                            ]);
                        break;
                }
            }
            // 推送目标
            $tray['_param']['_push_target'] = \fxapp\Text::explode(',', $tray['_param']['_push_target']);
            foreach ($tray['_param']['_push_target'] as $key => $value) {
                switch ($value) {
                    case 1:
                        // 全部推送
                        $model = $model
                            ->addAllAudience();
                        break;
                    case 2:
                        // 标签推送
                        $predefined = [
                            '_push_tag',
                        ];
                        $_param = fsi_param([$tray['_param'], $predefined], '2.2.2');
                        // 检查推送目标格式
                        $pass = false;
                        foreach ($_param as $key => $value) {
                            if (is_null($value)) {
                                continue;
                            } else if (!is_array($value)) {
                                $value = [$value];
                                $pass = true;
                            } else if (is_array($value)) {
                                $pass = true;
                            }
                            foreach ($value as $key2 => $value2) {
                                if (is_numeric($value2)) {
                                    $value2 = strval($value2);
                                }
                                $value[$key2] = $value2;
                            }
                            $_param[$key] = $value;
                        }
                        if (!$pass) {
                            $result[0] = false;
                            $result[1] = 1002;
                            $result[2] = \fxapp\Base::lang(['lack', 'tag']);
                            return $result;
                        }
                        $model = $model
                            ->addTag($_param['_push_tag']);
                        break;
                    case 3:
                        // 别名推送
                        $predefined = [
                            '_push_alias',
                        ];
                        $_param = fsi_param([$tray['_param'], $predefined], '2.2.2');
                        // 检查推送目标格式
                        $pass = false;
                        foreach ($_param as $key => $value) {
                            if (is_null($value)) {
                                continue;
                            } else if (!is_array($value)) {
                                $value = [$value];
                                $pass = true;
                            } else if (is_array($value)) {
                                $pass = true;
                            }
                            foreach ($value as $key2 => $value2) {
                                if (is_numeric($value2)) {
                                    $value2 = strval($value2);
                                }
                                $value[$key2] = $value2;
                            }
                            $_param[$key] = $value;
                        }
                        if (!$pass) {
                            $result[0] = false;
                            $result[1] = 1002;
                            $result[2] = \fxapp\Base::lang(['lack', 'alias']);
                            return $result;
                        }
                        $model = $model
                            ->addAlias($_param['_push_alias']);
                        break;
                    case 4:
                        // ID推送
                        $predefined = [
                            '_push_id',
                        ];
                        $_param = fsi_param([$tray['_param'], $predefined], '2.2.2');
                        // 检查推送目标格式
                        $pass = false;
                        foreach ($_param as $key => $value) {
                            if (is_null($value)) {
                                continue;
                            } else if (!is_array($value)) {
                                $value = [$value];
                                $pass = true;
                            } else if (is_array($value)) {
                                $pass = true;
                            }
                            foreach ($value as $key2 => $value2) {
                                if (is_numeric($value2)) {
                                    $value2 = strval($value2);
                                }
                                $value[$key2] = $value2;
                            }
                            $_param[$key] = $value;
                        }
                        if (!$pass) {
                            $result[0] = false;
                            $result[1] = 1002;
                            $result[2] = \fxapp\Base::lang(['lack', 'jpush', 'id']);
                            return $result;
                        }
                        $model = $model
                            ->addRegistrationId($_param['_push_id']);
                        break;
                }
            }
            $record = $model->send();
        } catch (\Throwable $e) {
            $result[0] = false;
            $result[1] = 1002;
            $result[2] = $e->getMessage();
            return $result;
        }
        if ($record) {
            $result[2] = \fxapp\Base::lang(['send', 'success']);
            return $result;
        } else {
            $result[0] = false;
            $result[1] = 1002;
            $result[2] = \fxapp\Base::lang(['send', 'fail']);
            return $result;
        }
    }
}
