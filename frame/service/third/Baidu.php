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

namespace fxyin\service\third;

use fxyin\service\Third;

/**
 * 百度
 * @return mixed
 */
class Baidu extends Third
{
    /**
     * 服务
     * @param string $name 服务名称
     * @return void|Lbsyun|BaiduTranslate
     */
    public function service($name)
    {
        $data = $this->data;
        $supplier = $this->supplier;
        $name = strtolower($name);
        switch ($name) {
            case 'lbsyun':
                return new Lbsyun($data, $supplier);
            case 'translate':
                return new BaiduTranslate($data, $supplier);
        }
    }
}

/**
 * LBS云
 * @return mixed
 */
class Lbsyun extends Baidu
{
    /**
     * 定位
     * @param string $entry['ip'] 目标IP
     * @return mixed
     */
    public function location()
    {
        // 初始化变量
        $entry = $this->data;
        $echo = \fxapp\Server::echo();
        $predefined = [
            'ip',
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.2.2');
        $tray['ip'] = $entry['ip'];
        $tray['check'] = \fxapp\Data::paramEmpty($tray, 1);
        if (!$tray['check'][0]) return $tray['check'];
        // 应用钥匙
        $conf['app_key'] = \fxapp\Base::config('third.baidu.location.app_key');
        // 坐标
        $conf['coor'] = \fxapp\Base::config('third.baidu.location.coor');
        // 接口域
        $conf['domain'] = \fxapp\Base::config('third.baidu.location.domain');
        $conf['data']['ak'] = $conf['app_key'];
        $conf['data']['ip'] = $tray['ip'];
        $conf['data']['coor'] = $conf['coor'];
        $response = \fxapp\Service::http($conf['domain'], $conf['data'], [], 'post');
        $response = json_decode($response, true);
        if (!$response['status']) {
            $echo[2] = \fxapp\Base::lang(['request', 'success']);
            $echo[3] = $response;
            return $echo;
        } else {
            $echo[0] = false;
            $echo[1] = 1002;
            $echo[2] = $response['message'];
            $echo[3] = $response;
            return $echo;
        }
    }
}

// +----------------------------------------------------------------------
// | PHP MVC FrameWork v1.0 在线翻译类 使用百度翻译接口 无需申请Api Key
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2099 http://qiling.org All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: qiling <70419470@qq.com> 2015年4月13日 下午2:22:15
// +----------------------------------------------------------------------
/**
 * 在线翻译类
 * @author qiling <70419470@qq.com>
 */
class BaiduTranslate extends Baidu
{
    /**
     * 支持的语种
     * @var ArrayAccess
     */
    static $Lang = [
        'auto' => '自动检测',
        'ara' => '阿拉伯语',
        'de' => '德语',
        'ru' => '俄语',
        'fra' => '法语',
        'kor' => '韩语',
        'nl' => '荷兰语',
        'pt' => '葡萄牙语',
        'jp' => '日语',
        'th' => '泰语',
        'wyw' => '文言文',
        'spa' => '西班牙语',
        'el' => '希腊语',
        'it' => '意大利语',
        'en' => '英语',
        'yue' => '粤语',
        'zh' => '中文',
    ];

    /**
     * 获取支持的语种
     * @return array 返回支持的语种
     */
    public function getLang()
    {
        return self::$Lang;
    }

    /**
     * 执行文本翻译
     * @param string $text 要翻译的文本
     * @param string $from 原语言语种 默认:英文
     * @param string $to 目标语种 默认:中文
     * @return boolean string 翻译失败:false 翻译成功:翻译结果
     */
    public function translate($text, $from = 'en', $to = 'zh')
    {
        // http://fanyi.baidu.com/v2transapi?from=zh&query=%E7%94%A8%E8%BD%A6%E8%B5%84%E8%AE%AF&to=fra
        $url = "http://fanyi.baidu.com/v2transapi";
        $data = [
            'from' => $from,
            'to' => $to,
            'query' => $text,
        ];
        $data = http_build_query($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, "http://fanyi.baidu.com");
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:37.0) Gecko/20100101 Firefox/37.0');
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $echo = curl_exec($ch);
        curl_close($ch);

        $echo = json_decode($echo, true);

        if (!isset($echo['trans_result']['data']['0']['dst'])) {
            return false;
        }
        return $echo['trans_result']['data']['0']['dst'];
    }
}
