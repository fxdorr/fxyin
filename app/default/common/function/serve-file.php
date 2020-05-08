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

use fxyin\Image;

/**
 * 框架-公共-操作-缩略图 <p>
 * fco thumb
 * </p>
 * @param string $src
 * @param int $width
 * @param int $height
 * @param boolean $replace
 * @return string
 */
function fco_thumb($src = '', $width = 500, $height = 500, $replace = false)
{
    if (is_file($src) && file_exists($src)) {
        $ext = pathinfo($src, PATHINFO_EXTENSION);
        $name = basename($src, '.' . $ext);
        $dir = dirname($src);
        if (in_array($ext, array('gif', 'jpg', 'jpeg', 'bmp', 'png'))) {
            $name = $name . '_thumb_' . $width . '_' . $height . '.' . $ext;
            $file = $dir . '/' . $name;
            if (!file_exists($file) || $replace == true) {
                $image = new \fxyin\file\driver\Image($src);
                $image->thumb($width, $height, 1);
                $image->save($file);
            }
            return $file;
        }
    }
    return $src;
}

/**
 * 框架-公共-操作-格式化文件大小 <p>
 * fco format file size
 * </p>
 * @param int $size 文件大小
 * @return string
 */
function fco_ffsize($size)
{
    $units = array(' B', ' KB', ' MB', ' GB', ' TB');
    for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
    return round($size, 2) . $units[$i];
}

/**
 * 框架-公共-函数-获取文件集合 <p>
 * fcf get files
 * </p>
 * @param mixed $var 变量
 * @param string $type 类型
 * @param string $limit 次数
 * @return mixed
 */
function fcf_getFiles($var, $ext = null, $limit = -1)
{
    //初始化变量
    $cace['list'] = [];
    $ext_list = explode('|', $ext);
    $loop = true;
    if ($limit == 0) {
        $loop = false;
    } else if ($limit > 0) {
        --$limit;
    }
    $var = realpath($var);
    if (!is_dir($var)) {
        return $cace['list'];
    }
    $files = scandir($var);
    foreach ($files as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        $cace['info'] = $var . DIRECTORY_SEPARATOR . $file;
        if (is_file($cace['info']) && (in_array(pathinfo($file, PATHINFO_EXTENSION), $ext_list) || is_null($ext))) {
            $cace['list'][] = $cace['info'];
        } else if (is_dir($cace['info']) && $loop) {
            $cace['child'] = fcf_getFiles($cace['info'], $ext, $limit);
            $cace['list'] = array_merge($cace['list'], $cace['child']);
        }
    }
    return $cace['list'];
}

/**
 * 框架-公共-函数-删除文件集合 <p>
 * fcf del files
 * </p>
 * @param mixed $var 变量
 * @return mixed
 */
function fcf_delFiles($var)
{
    //初始化变量
    $cace = [];
    $var = realpath($var);
    if (!is_dir($var)) {
        return false;
    }
    $files = scandir($var);
    foreach ($files as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        $cace['info'] = $var . DIRECTORY_SEPARATOR . $file;
        if (is_file($cace['info'])) {
            @unlink($cace['info']);
        } else if (is_dir($cace['info'])) {
            fcf_delFiles($cace['info']);
        }
    }
    @rmdir($var);
    return true;
}

/**
 * 框架-公共-函数-初始化媒体环境 <p>
 * fcf init media environment
 * </p>
 * @param mixed $var 变量
 * @param string $type 类型
 * @return mixed
 */
function fcf_initMediaEnv()
{
    //配置参数
    $config = fxy_config('file_media');
    $system = fts_system(1);
    foreach ($config as $key => $value) {
        switch ($system) {
            default:
                //未知系统
                //服务初始化
                $envoy = null;
                break;
            case 'linux':
                //Linux系统
            case 'windows nt':
                //Windows系统
                if (isset($value['path'][$system])) {
                    $config[$key]['bin'] = $value['path'][$system];
                }
                break;
        }
    }
    fxy_config('file_media', $config);
}

/**
 * 框架-公共-函数-获取媒体信息 <p>
 * fcf get media info
 * </p>
 * @param mixed $var 变量
 * @param string $type 类型
 * @return mixed
 */
function fcf_getMediaInfo($var, $type)
{
    switch ($type) {
        case 'video':
            //媒体-视频
            try {
                //配置参数
                $config = fxy_config('file_media.video');
                if (is_null($config['bin'])) return;
                $command = sprintf($config['bin'], $var);
                ob_start();
                passthru($command);
                $info = ob_get_contents();
                ob_end_clean();
                $data = [];
                if (preg_match("/Duration: (.*?), start: (.*?), bitrate: (\d*) kb\/s/", $info, $match)) {
                    //检测数据
                    $predefined = [
                        1, 2, 3,
                    ];
                    $match = fsi_param([$match, $predefined], '1.2.2');
                    //播放时间
                    $data['duration'] = $match[1];
                    $arr_duration = explode(':', $match[1]);
                    //检测数据
                    $predefined = [
                        0, 1, 2,
                    ];
                    $arr_duration = fsi_param([$arr_duration, $predefined], '1.2.2');
                    //转换播放时间为秒数
                    $data['seconds'] = $arr_duration[0] * 3600 + $arr_duration[1] * 60 + $arr_duration[2];
                    //开始时间
                    $data['start'] = $match[2];
                    //码率(kb)
                    $data['bitrate'] = $match[3];
                }
                if (preg_match("/Video: (.*?), (.*?), (.*?)[,\s]/", $info, $match)) {
                    //检测数据
                    $predefined = [
                        1, 2, 3,
                    ];
                    $match = fsi_param([$match, $predefined], '1.2.2');
                    //视频编码格式
                    $data['vcodec'] = $match[1];
                    //视频格式
                    $data['vformat'] = $match[2];
                    //视频分辨率
                    $data['resolution'] = $match[3];
                    $arr_resolution = explode('x', $match[3]);
                    //检测数据
                    $predefined = [
                        0, 1,
                    ];
                    $arr_resolution = fsi_param([$arr_resolution, $predefined], '1.2.2');
                    $data['width'] = $arr_resolution[0];
                    $data['height'] = $arr_resolution[1];
                }
                if (preg_match("/Audio: (\w*), (\d*) Hz/", $info, $match)) {
                    //检测数据
                    $predefined = [
                        1, 2,
                    ];
                    $match = fsi_param([$match, $predefined], '1.2.2');
                    //音频编码
                    $data['acodec'] = $match[1];
                    //音频采样频率
                    $data['asamplerate'] = $match[2];
                }
                if (isset($data['seconds']) && isset($data['start'])) {
                    //实际播放时间
                    $data['play_time'] = $data['seconds'] + $data['start'];
                }
                //文件大小
                $data['size'] = filesize($var);
                return $data;
            } catch (\Throwable $e) {
                fcf_dump(fcf_exception($e), 1);
            }
            break;
    }
}
