<?php
// +----------------------------------------------------------------------
// | Name fxyin
// +----------------------------------------------------------------------
// | Author wztqy <wztqy@139.com>
// +----------------------------------------------------------------------
// | Copyright Copyright © 2016-2099 fxri. All rights reserved.
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
     * @return mixed
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
     * @param string $tran['content'] 内容
     * @param string $tran['file_name'] 文件名称
     * @param string $tran['file_path'] 文件路径
     * @param string $tran['file_url'] 文件URL
     * @param string $tran['level'] 等级
     * @param string $tran['size'] 大小
     * @param string $tran['margin'] 边框
     * @param string $tran['print'] 打印
     * @return mixed
     */
    public function qrcodeMake()
    {
        //初始化变量
        $tran = $this->data;
        $result = fsi_result();
        $predefined = [
            'content', 'file_name', 'file_path',
        ];
        $tran = fsi_param([$tran, $predefined], '1.2.2');
        $predefined = [
            'file_url' => $tran['file_path'] . $tran['file_name'], 'level' => 'L', 'size' => 3,
            'margin' => 4, 'print' => false,
        ];
        $tran = fsi_param([$tran, $predefined], '1.1.2');
        $parm['content'] = $tran['content'];
        $parm['file_name'] = $tran['file_name'];
        $parm['file_path'] = $tran['file_path'];
        $pempty = dsc_pempty($parm);
        if (!$pempty[0]) return $pempty;
        $parm['file_url'] = $tran['file_url'];
        $parm['level'] = $tran['level'];
        $parm['size'] = $tran['size'];
        $parm['margin'] = $tran['margin'];
        $parm['print'] = $tran['print'];
        //SDK地址
        $conf['url_sdk'] = fxy_config('third_tool')['qrcode']['url_sdk'];
        //引入核心库文件
        fxy_load($conf['url_sdk']);
        //调用QRcode类的静态方法png生成二维码图片
        if (!is_dir(dirname($parm['file_url']))) {
            \fxyin\Dir::create(dirname($parm['file_url']));
        }
        \QRcode::png($parm['content'], $parm['file_url'], $parm['level'], $parm['size'], $parm['margin'], $parm['print']);
        $result[1] = fxy_lang(['request', 'success']);
        $result[2]['data'] = $parm;
        return $result;
    }

    /**
     * Excel导出
     * @param string $tran['title'] 标题
     * @param string $tran['file_name'] 文件名称
     * @param string $tran['data'] 数据
     * @return mixed
     */
    public function excelExport()
    {
        //初始化变量
        $tran = $this->data;
        $result = fsi_result();
        $predefined = [
            'title', 'data',
        ];
        $tran = fsi_param([$tran, $predefined], '1.2.2');
        $predefined = [
            'file_name' => 'report',
        ];
        $tran = fsi_param([$tran, $predefined], '1.1.2');
        $parm['title'] = $tran['title'];
        $parm['data'] = $tran['data'];
        $pempty = dsc_pempty($parm);
        if (!$pempty[0]) return $pempty;
        $parm['file_name'] = $tran['file_name'];
        //服务处理
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-type:application/vnd.ms-excel.numberformat:@");
        header("Content-Disposition:attachment;filename=" . $parm['file_name'] . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        //导出xls开始
        if (!empty($parm['title'])) {
            foreach ($parm['title'] as $key => $value) {
                $parm['title'][$key] = iconv("UTF-8", "GB2312", $value);
            }
            $parm['title'] = implode("\t", $parm['title']);
            echo $parm['title'] . "\n";
        }
        if (!empty($parm['data'])) {
            foreach ($parm['data'] as $key => $value) {
                foreach ($value as $ckey => $cvalue) {
                    $parm['data'][$key][$ckey] = iconv("UTF-8", "GB2312", $cvalue);
                }
                $parm['data'][$key] = implode("\t", $parm['data'][$key]);
            }
            echo implode("\n", $parm['data']);
        }
    }

    /**
     * Excel导入
     * @param string $tran['file_path'] 文件路径
     * @return mixed
     */
    public function excelImport()
    {
        //初始化变量
        $tran = $this->data;
        $result = fsi_result();
    }
}
