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
        $ip = \fxapp\Service::http("http://httpbin.org/ip", '', [], 'get');
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
        if (preg_match("/win/i", $echo) && preg_match("/95/i", $echo)) {
            $echo = 'Windows 95';
        } else if (preg_match("/win 9x/i", $echo) && preg_match("/4.90/i", $echo)) {
            $echo = 'Windows ME';
        } else if (preg_match("/win/i", $echo) && preg_match("/98/i", $echo)) {
            $echo = 'Windows 98';
        } else if (preg_match("/win/i", $echo) && preg_match("/nt 5.1/i", $echo)) {
            $echo = 'Windows XP';
        } else if (preg_match("/win/i", $echo) && preg_match("/nt 5.2/i", $echo)) {
            $echo = 'Windows 2003';
        } else if (preg_match("/win/i", $echo) && preg_match("/nt 5/i", $echo)) {
            $echo = 'Windows 2000';
        } else if (preg_match("/win/i", $echo) && preg_match("/nt/i", $echo)) {
            $echo = 'Windows NT';
        } else if (preg_match("/win/i", $echo) && preg_match("/32/i", $echo)) {
            $echo = 'Windows 32';
        } else if (preg_match("/linux/i", $echo)) {
            $echo = 'Linux';
        } else if (preg_match("/unix/i", $echo)) {
            $echo = 'Unix';
        } else if (preg_match("/sun/i", $echo) && preg_match("/os/i", $echo)) {
            $echo = 'SunOS';
        } else if (preg_match("/ibm/i", $echo) && preg_match("/os/i", $echo)) {
            $echo = 'IBM OS/2';
        } else if (preg_match("/Mac/i", $echo) && preg_match("/PC/i", $echo)) {
            $echo = 'Macintosh';
        } else if (preg_match("/PowerPC/i", $echo)) {
            $echo = 'PowerPC';
        } else if (preg_match("/AIX/i", $echo)) {
            $echo = 'AIX';
        } else if (preg_match("/HPUX/i", $echo)) {
            $echo = 'HPUX';
        } else if (preg_match("/NetBSD/i", $echo)) {
            $echo = 'NetBSD';
        } else if (preg_match("/BSD/i", $echo)) {
            $echo = 'BSD';
        } else if (preg_match("/OSF1/i", $echo)) {
            $echo = 'OSF1';
        } else if (preg_match("/IRIX/i", $echo)) {
            $echo = 'IRIX';
        } else if (preg_match("/FreeBSD/i", $echo)) {
            $echo = 'FreeBSD';
        } else if (preg_match("/teleport/i", $echo)) {
            $echo = 'teleport';
        } else if (preg_match("/flashget/i", $echo)) {
            $echo = 'flashget';
        } else if (preg_match("/webzip/i", $echo)) {
            $echo = 'webzip';
        } else if (preg_match("/offline/i", $echo)) {
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
        $debug['level'] = \fxapp\Base::config('app.debug.level');
        $debug['data'] = \fxapp\Base::config('app.debug.data');
        switch ($type) {
            default:
            case 1:
                // 默认
                $echo = $data;
                break;
            case 2:
                // 通用
                $base = \fxapp\Base::config('app.echo.format');
                // 处理数据
                $data[2] = \fxapp\Base::lang($data[2]);
                foreach ($base as $key => $value) {
                    if (array_key_exists($key, $data)) {
                        $echo[$value] = $data[$key];
                    }
                }
                break;
        }
        // 调试模式
        if ($debug['switch'] && $debug['level']) {
            $echo['debug'] = ['' => null];
            $debug['level'] = \fxapp\Text::explode(',', $debug['level']);
            foreach ($debug['level'] as $value) {
                switch ($value) {
                    default:
                        // 匹配
                        if (array_key_exists($value, $debug['data'])) {
                            $echo['debug'][$value] = $debug['data'][$value];
                        }
                        break;
                    case '1':
                        // 全部
                        $echo['debug'] = \fxapp\Param::define([$echo['debug'], $debug['data']], '1.1.1');
                        break;
                    case '2':
                        // 入参
                        $echo['debug']['param'] = $debug['data']['param'];
                        $echo['debug']['get'] = $debug['data']['get'];
                        $echo['debug']['post'] = $debug['data']['post'];
                        $echo['debug']['input'] = $debug['data']['input'];
                        $echo['debug']['cli'] = $debug['data']['cli'];
                        break;
                    case '3':
                        // 文件
                        $echo['debug']['files'] = $debug['data']['files'];
                        break;
                    case '4':
                        // 环境
                        $echo['debug']['server'] = $debug['data']['server'];
                        $echo['debug']['cookie'] = $debug['data']['cookie'];
                        $echo['debug']['session'] = $debug['data']['session'];
                        $echo['debug']['env'] = $debug['data']['env'];
                        break;
                }
            }
        }
        // 空对象处理
        $echo = \fxapp\Param::object($echo);
        return $echo;
    }

    /**
     * 处理数据-返回
     * @param mixed $data 数据
     * @param string $type 类型
     * @return mixed
     */
    public function return($data, $type = '')
    {
        // 返回格式
        $type = !empty($type) ? $type : 'json';
        $type = strtolower($type);
        switch ($type) {
            default:
            case 'json':
                // 返回JSON数据格式到客户端，包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                exit(\fxapp\Base::json($data, 'encode'));
        }
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
