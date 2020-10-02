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
namespace fxapp\facade;

/**
 * 服务器类
 */
class Server
{
    /**
     * 获取IP
     * @return mixed
     */
    public function ip()
    {
        $ip = \fxapp\Service::http('http://httpbin.org/ip', '', [], 'get');
        if (!is_json($ip)) return;
        $ip = json_decode($ip, true);
        $ip['origin'] = explode(',', $ip['origin'])[0];
        return $ip['origin'];
    }

    /**
     * 获取系统
     * @param int $type 类型
     * @return mixed
     */
    public function system($type = -1)
    {
        $echo = php_uname();
        // 开始检测
        if (preg_match('/win/i', $echo) && preg_match('/95/i', $echo)) {
            $echo = 'Windows 95';
        } else if (preg_match('/win 9x/i', $echo) && preg_match('/4.90/i', $echo)) {
            $echo = 'Windows ME';
        } else if (preg_match('/win/i', $echo) && preg_match('/98/i', $echo)) {
            $echo = 'Windows 98';
        } else if (preg_match('/win/i', $echo) && preg_match('/nt 5.1/i', $echo)) {
            $echo = 'Windows XP';
        } else if (preg_match('/win/i', $echo) && preg_match('/nt 5.2/i', $echo)) {
            $echo = 'Windows 2003';
        } else if (preg_match('/win/i', $echo) && preg_match('/nt 5/i', $echo)) {
            $echo = 'Windows 2000';
        } else if (preg_match('/win/i', $echo) && preg_match('/nt/i', $echo)) {
            $echo = 'Windows NT';
        } else if (preg_match('/win/i', $echo) && preg_match('/32/i', $echo)) {
            $echo = 'Windows 32';
        } else if (preg_match('/linux/i', $echo)) {
            $echo = 'Linux';
        } else if (preg_match('/unix/i', $echo)) {
            $echo = 'Unix';
        } else if (preg_match('/sun/i', $echo) && preg_match('/os/i', $echo)) {
            $echo = 'SunOS';
        } else if (preg_match('/ibm/i', $echo) && preg_match('/os/i', $echo)) {
            $echo = 'IBM OS/2';
        } else if (preg_match('/Mac/i', $echo) && preg_match('/PC/i', $echo)) {
            $echo = 'Macintosh';
        } else if (preg_match('/PowerPC/i', $echo)) {
            $echo = 'PowerPC';
        } else if (preg_match('/AIX/i', $echo)) {
            $echo = 'AIX';
        } else if (preg_match('/HPUX/i', $echo)) {
            $echo = 'HPUX';
        } else if (preg_match('/NetBSD/i', $echo)) {
            $echo = 'NetBSD';
        } else if (preg_match('/BSD/i', $echo)) {
            $echo = 'BSD';
        } else if (preg_match('/OSF1/i', $echo)) {
            $echo = 'OSF1';
        } else if (preg_match('/IRIX/i', $echo)) {
            $echo = 'IRIX';
        } else if (preg_match('/FreeBSD/i', $echo)) {
            $echo = 'FreeBSD';
        } else if (preg_match('/teleport/i', $echo)) {
            $echo = 'teleport';
        } else if (preg_match('/flashget/i', $echo)) {
            $echo = 'flashget';
        } else if (preg_match('/webzip/i', $echo)) {
            $echo = 'webzip';
        } else if (preg_match('/offline/i', $echo)) {
            $echo = 'offline';
        } else {
            $echo = 'Others';
        }
        switch ($type) {
            case 1:
                // 格式化-小写
                $echo = strtolower($echo);
                break;
            case 2:
                // 格式化-大写
                $echo = strtoupper($echo);
                break;
        }
        return $echo;
    }

    /**
     * 处理数据-格式
     * @param array $data 数据
     * @param int $type 类型
     * @return array
     */
    public function format($data, $type = 1)
    {
        // 初始化变量
        $echo = [];
        $debug['switch'] = \fxapp\Base::config('app.debug.switch');
        $debug['trace'] = \fxapp\Base::config('app.debug.trace');
        $debug['param'] = \fxapp\Base::config('app.param');
        switch ($type) {
            default:
            case 1:
                // 默认
                if (is_json($data)) {
                    $data = \fxapp\Param::json($data, 'decode');
                } else if (is_object($data)) {
                    $data = (array) $data;
                }
                $echo = $data;
                break;
            case 2:
                // 通用
                $base = \fxapp\Base::config('app.echo.format');
                // 处理数据
                $data[2] = \fxapp\Base::lang($data[2] ?? '');
                foreach ($base as $key => $value) {
                    if (array_key_exists($key, $data)) {
                        $echo[$value] = $data[$key];
                    }
                }
                break;
        }
        if (!is_array($echo)) return $echo;
        // 调试模式
        if ($debug['switch'] && $debug['trace']) {
            $echo['debug'] = ['' => null];
            $debug['trace'] = \fxapp\Text::explode(',', $debug['trace']);
            $tray['name'] = [];
            foreach ($debug['trace'] as $value) {
                switch ($value) {
                    default:
                        // 匹配
                        if (array_key_exists($value, $debug['param'])) {
                            $tray['name'][] = [$value];
                        }
                        break;
                    case '1':
                        // 全部
                        $tray['name'][] = array_keys($debug['param']);
                        break;
                    case '2':
                        // 入参
                        $tray['name'][] = ['param', 'get', 'post', 'input', 'cli'];
                        break;
                    case '3':
                        // 文件
                        $tray['name'][] = ['files'];
                        break;
                    case '4':
                        // 环境
                        $tray['name'][] = ['server', 'cookie', 'session', 'env'];
                        break;
                }
            }
            $tray['name'] = array_unique(\fxapp\Param::append(...$tray['name']));
            foreach ($tray['name'] as $value) {
                $echo['debug'][$value] = $debug['param'][$value];
            }
        }
        // 空数组转对象
        $echo = \fxapp\Param::object($echo);
        return $echo;
    }

    /**
     * 初始化响应
     * @return array
     */
    public function echo()
    {
        // 初始化变量
        $echo = \fxapp\Base::config('app.echo.template');
        return $echo;
    }
}
