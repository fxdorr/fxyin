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
namespace fxyin\service\third;

use fxyin\service\Third;

/**
 * 工具
 * @return mixed
 */
class Tool extends Third
{
    /**
     * 服务
     * @param string $name 服务名称
     * @return void|ToolService
     */
    public function service($name)
    {
        $data = $this->data;
        $supplier = $this->supplier;
        $name = strtolower($name);
        switch ($name) {
            case 'service':
                return new ToolService($data, $supplier);
        }
    }
}

/**
 * 服务
 * @return mixed
 */
class ToolService extends Tool
{
    /**
     * 二维码生成
     * @param string $entry['content'] 内容
     * @param string $entry['file_name'] 文件名称
     * @param string $entry['file_path'] 文件路径
     * @param string $entry['file_url'] 文件URL
     * @param string $entry['level'] 等级
     * @param string $entry['size'] 大小
     * @param string $entry['margin'] 边框
     * @param string $entry['print'] 打印
     * @return mixed
     */
    public function qrcodeMake()
    {
        // 初始化变量
        $entry = $this->data;
        $echo = \fxapp\Server::echo();
        $predefined = [
            'content', 'file_name', 'file_path',
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.2.2');
        $predefined = [
            'file_url' => $entry['file_path'] . $entry['file_name'], 'level' => 'L', 'size' => 3,
            'margin' => 4, 'print' => false,
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.1.2');
        $tray['content'] = $entry['content'];
        $tray['file_name'] = $entry['file_name'];
        $tray['file_path'] = $entry['file_path'];
        $pempty = \fxapp\Data::paramEmpty($tray);
        if (!$pempty[0]) return $pempty;
        $tray['file_url'] = $entry['file_url'];
        $tray['level'] = $entry['level'];
        $tray['size'] = $entry['size'];
        $tray['margin'] = $entry['margin'];
        $tray['print'] = $entry['print'];
        // SDK地址
        $conf['url_sdk'] = \fxapp\Base::config('third.tool.qrcode.url_sdk');
        // 引入核心库文件
        \fxapp\Base::load($conf['url_sdk']);
        // 调用QRcode类的静态方法png生成二维码图片
        if (!is_dir(dirname($tray['file_url']))) {
            \fxyin\Dir::create(dirname($tray['file_url']));
        }
        \QRcode::png($tray['content'], $tray['file_url'], $tray['level'], $tray['size'], $tray['margin'], $tray['print']);
        $echo[2] = \fxapp\Base::lang(['request', 'success']);
        $echo[3] = $tray;
        return $echo;
    }

    /**
     * Excel导出
     * @param string $entry['title'] 标题
     * @param string $entry['file_name'] 文件名称
     * @param string $entry['data'] 数据
     * @return mixed
     */
    public function excelExport()
    {
        // 初始化变量
        $entry = $this->data;
        $echo = \fxapp\Server::echo();
        $predefined = [
            'title', 'data',
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.2.2');
        $predefined = [
            'file_name' => 'report',
        ];
        $entry = \fxapp\Param::define([$entry, $predefined], '1.1.2');
        $tray['title'] = $entry['title'];
        $tray['data'] = $entry['data'];
        $pempty = \fxapp\Data::paramEmpty($tray);
        if (!$pempty[0]) return $pempty;
        $tray['file_name'] = $entry['file_name'];
        // 服务处理
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-type:application/vnd.ms-excel.numberformat:@");
        header("Content-Disposition:attachment;filename=" . $tray['file_name'] . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        // 导出xls开始
        if (!empty($tray['title'])) {
            foreach ($tray['title'] as $key => $value) {
                $tray['title'][$key] = iconv("UTF-8", "GB2312", $value);
            }
            $tray['title'] = implode("\t", $tray['title']);
            echo $tray['title'] . "\n";
        }
        if (!empty($tray['data'])) {
            foreach ($tray['data'] as $key => $value) {
                foreach ($value as $ckey => $cvalue) {
                    $tray['data'][$key][$ckey] = iconv("UTF-8", "GB2312", $cvalue);
                }
                $tray['data'][$key] = implode("\t", $tray['data'][$key]);
            }
            echo implode("\n", $tray['data']);
        }
    }

    /**
     * Excel导入
     * @param string $entry['file_path'] 文件路径
     * @return mixed
     */
    public function excelImport()
    {
        // 初始化变量
        $entry = $this->data;
        $echo = \fxapp\Server::echo();
    }
}
