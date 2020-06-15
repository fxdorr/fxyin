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
 * 文件类
 */
class File
{
    /**
     * 处理文件-格式化大小
     * @param int $size 文件大小
     * @return string
     */
    public function formatSize($size)
    {
        $units = array(' B', ' KB', ' MB', ' GB', ' TB');
        for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
        return round($size, 2) . $units[$i];
    }

    /**
     * 查询文件-获取列表
     * @param mixed $var 变量
     * @param string $type 类型
     * @param string $limit 次数
     * @return mixed
     */
    public function getList($var, $ext = null, $limit = -1)
    {
        // 初始化变量
        $tray['list'] = [];
        $ext_list = explode('|', $ext);
        $loop = true;
        if ($limit == 0) {
            $loop = false;
        } else if ($limit > 0) {
            --$limit;
        }
        $var = realpath($var);
        if (!is_dir($var)) {
            return $tray['list'];
        }
        $files = scandir($var);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $tray['info'] = $var . DIRECTORY_SEPARATOR . $file;
            if (is_file($tray['info']) && (in_array(pathinfo($file, PATHINFO_EXTENSION), $ext_list) || is_null($ext))) {
                $tray['list'][] = $tray['info'];
            } else if (is_dir($tray['info']) && $loop) {
                $tray['child'] = $this->getList($tray['info'], $ext, $limit);
                $tray['list'] = array_merge($tray['list'], $tray['child']);
            }
        }
        return $tray['list'];
    }

    /**
     * 处理文件-删除目录
     * @param mixed $var 变量
     * @return mixed
     */
    public function deleteDirectory($var)
    {
        // 初始化变量
        $tray = [];
        $var = realpath($var);
        if (!is_dir($var)) {
            return false;
        }
        $files = scandir($var);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $tray['info'] = $var . DIRECTORY_SEPARATOR . $file;
            if (is_file($tray['info'])) {
                @unlink($tray['info']);
            } else if (is_dir($tray['info'])) {
                $this->deleteDirectory($tray['info']);
            }
        }
        @rmdir($var);
        return true;
    }

    /**
     * 查询文件-获取媒体信息
     * @param mixed $var 变量
     * @param string $type 类型
     * @return mixed
     */
    public function getMediaInfo($var, $type)
    {
        switch ($type) {
            case 'video':
                // 媒体-视频
                try {
                    // 配置参数
                    $config = \fxapp\Base::config('app.media.video');
                    if (is_null($config)) return;
                    $command = sprintf($config, $var);
                    ob_start();
                    passthru($command);
                    $info = ob_get_contents();
                    ob_end_clean();
                    $data = [];
                    if (preg_match("/Duration: (.*?), start: (.*?), bitrate: (\d*) kb\/s/", $info, $match)) {
                        // 检测数据
                        $predefined = [
                            1, 2, 3,
                        ];
                        $match = \fxapp\Param::define([$match, $predefined], '1.2.2');
                        // 播放时间
                        $data['duration'] = $match[1];
                        $arr_duration = explode(':', $match[1]);
                        // 检测数据
                        $predefined = [
                            0, 1, 2,
                        ];
                        $arr_duration = \fxapp\Param::define([$arr_duration, $predefined], '1.2.2');
                        // 转换播放时间为秒数
                        $data['seconds'] = $arr_duration[0] * 3600 + $arr_duration[1] * 60 + $arr_duration[2];
                        // 开始时间
                        $data['start'] = $match[2];
                        // 码率(kb)
                        $data['bitrate'] = $match[3];
                    }
                    if (preg_match("/Video: (.*?), (.*?), (.*?)[,\s]/", $info, $match)) {
                        // 检测数据
                        $predefined = [
                            1, 2, 3,
                        ];
                        $match = \fxapp\Param::define([$match, $predefined], '1.2.2');
                        // 视频编码格式
                        $data['vcodec'] = $match[1];
                        // 视频格式
                        $data['vformat'] = $match[2];
                        // 视频分辨率
                        $data['resolution'] = $match[3];
                        $arr_resolution = explode('x', $match[3]);
                        // 检测数据
                        $predefined = [
                            0, 1,
                        ];
                        $arr_resolution = \fxapp\Param::define([$arr_resolution, $predefined], '1.2.2');
                        $data['width'] = $arr_resolution[0];
                        $data['height'] = $arr_resolution[1];
                    }
                    if (preg_match("/Audio: (\w*), (\d*) Hz/", $info, $match)) {
                        // 检测数据
                        $predefined = [
                            1, 2,
                        ];
                        $match = \fxapp\Param::define([$match, $predefined], '1.2.2');
                        // 音频编码
                        $data['acodec'] = $match[1];
                        // 音频采样频率
                        $data['asamplerate'] = $match[2];
                    }
                    if (isset($data['seconds']) && isset($data['start'])) {
                        // 实际播放时间
                        $data['play_time'] = $data['seconds'] + $data['start'];
                    }
                    // 文件大小
                    $data['size'] = filesize($var);
                    return $data;
                } catch (\Throwable $th) {
                    \fxapp\Base::dump(\fxapp\Text::timeMilli(\fxapp\Text::timeMilli()), \fxapp\Text::throwable($th));
                }
                break;
        }
    }
}
