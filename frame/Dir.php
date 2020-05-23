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
namespace fxyin;

//类定义开始
class Dir
{
    private $_values = array();
    public $error = "";

    /**
      +----------------------------------------------------------
     * 架构函数
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @param string $path  目录路径
      +----------------------------------------------------------
     */
    public function __construct($path, $pattern = '*')
    {
        if (substr($path, -1) != "/")
            $path .= "/";
        $this->listFile($path, $pattern);
    }

    /**
     * 生成目录
     * @param  string  $path 目录
     * @param  integer $mode 权限
     * @return boolean
     */
    public static function create($path, $mode = 0777)
    {
        if (is_dir($path)) return true;
        $path = str_replace("\\", "/", $path);
        if (substr($path, -1) != '/') $path = $path . '/';
        $temp = explode('/', $path);
        $cur_dir = '';
        $max = count($temp) - 1;
        for ($i = 0; $i < $max; $i++) {
            $cur_dir .= $temp[$i] . '/';
            if (@is_dir($cur_dir)) continue;
            @mkdir($cur_dir, 0777, true);
            @chmod($cur_dir, 0777);
        }
        return is_dir($path);
    }

    /**
      +----------------------------------------------------------
     * 取得目录下面的文件信息
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @param mixed $pathname 路径
      +----------------------------------------------------------
     */
    public function listFile($pathname, $pattern = '*')
    {
        static $_listDirs = array();
        $guid = md5($pathname . $pattern);
        if (!isset($_listDirs[$guid])) {
            $dir = array();
            $list = glob($pathname . $pattern);
            foreach ($list as $i => $file) {
                // $dir[$i]['filename']    = basename($file);
                // basename取中文名出问题.改用此方法
                // 编码转换.把中文的调整一下.
                $dir[$i]['filename'] = preg_replace('/^.+[\\\\\\/]/', '', $file);
                $dir[$i]['pathname'] = realpath($file);
                $dir[$i]['owner'] = fileowner($file);
                $dir[$i]['perms'] = fileperms($file);
                $dir[$i]['inode'] = fileinode($file);
                $dir[$i]['group'] = filegroup($file);
                $dir[$i]['path'] = dirname($file);
                $dir[$i]['atime'] = fileatime($file);
                $dir[$i]['ctime'] = filectime($file);
                $dir[$i]['size'] = filesize($file);
                $dir[$i]['type'] = filetype($file);
                $dir[$i]['ext'] = is_file($file) ? strtolower(substr(strrchr(basename($file), '.'), 1)) : '';
                $dir[$i]['mtime'] = filemtime($file);
                $dir[$i]['isDir'] = is_dir($file);
                $dir[$i]['isFile'] = is_file($file);
                $dir[$i]['isLink'] = is_link($file);
                // $dir[$i]['isExecutable']= function_exists('is_executable')?is_executable($file):'';
                $dir[$i]['isReadable'] = is_readable($file);
                $dir[$i]['isWritable'] = is_writable($file);
            }
            $cmp_func = function ($a, $b) {
                $k  =  "isDir";
                if ($a[$k]  ==  $b[$k]) return 0;
                return  $a[$k] > $b[$k] ? -1 : 1;
            };
            // 对结果排序 保证目录在前面
            usort($dir, $cmp_func);
            $this->_values = $dir;
            $_listDirs[$guid] = $dir;
        } else {
            $this->_values = $_listDirs[$guid];
        }
    }

    /**
      +----------------------------------------------------------
     * 返回数组中的当前元素（单元）
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return array
      +----------------------------------------------------------
     */
    public function current($arr)
    {
        if (!is_array($arr)) {
            return false;
        }
        return current($arr);
    }

    /**
      +----------------------------------------------------------
     * 文件上次访问时间
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return integer
      +----------------------------------------------------------
     */
    public function getATime()
    {
        $current = $this->current($this->_values);
        return $current['atime'];
    }

    /**
      +----------------------------------------------------------
     * 取得文件的 inode 修改时间
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return integer
      +----------------------------------------------------------
     */
    public function getCTime()
    {
        $current = $this->current($this->_values);
        return $current['ctime'];
    }

    /**
      +----------------------------------------------------------
     * 遍历子目录文件信息
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return DirectoryIterator
      +----------------------------------------------------------
     */
    public function getChildren()
    {
        $current = $this->current($this->_values);
        if ($current['isDir']) {
            return new Dir($current['pathname']);
        }
        return false;
    }

    /**
      +----------------------------------------------------------
     * 取得文件名
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     */
    public function getFilename()
    {
        $current = $this->current($this->_values);
        return $current['filename'];
    }

    /**
      +----------------------------------------------------------
     * 取得文件的组
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return integer
      +----------------------------------------------------------
     */
    public function getGroup()
    {
        $current = $this->current($this->_values);
        return $current['group'];
    }

    /**
      +----------------------------------------------------------
     * 取得文件的 inode
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return integer
      +----------------------------------------------------------
     */
    public function getInode()
    {
        $current = $this->current($this->_values);
        return $current['inode'];
    }

    /**
      +----------------------------------------------------------
     * 取得文件的上次修改时间
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return integer
      +----------------------------------------------------------
     */
    public function getMTime()
    {
        $current = $this->current($this->_values);
        return $current['mtime'];
    }

    /**
      +----------------------------------------------------------
     * 取得文件的所有者
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     */
    public function getOwner()
    {
        $current = $this->current($this->_values);
        return $current['owner'];
    }

    /**
      +----------------------------------------------------------
     * 取得文件路径，不包括文件名
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     */
    public function getPath()
    {
        $current = $this->current($this->_values);
        return $current['path'];
    }

    /**
      +----------------------------------------------------------
     * 取得文件的完整路径，包括文件名
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     */
    public function getPathname()
    {
        $current = $this->current($this->_values);
        return $current['pathname'];
    }

    /**
      +----------------------------------------------------------
     * 取得文件的权限
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return integer
      +----------------------------------------------------------
     */
    public function getPerms()
    {
        $current = $this->current($this->_values);
        return $current['perms'];
    }

    /**
      +----------------------------------------------------------
     * 取得文件的大小
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return integer
      +----------------------------------------------------------
     */
    public function getSize()
    {
        $current = $this->current($this->_values);
        return $current['size'];
    }

    /**
      +----------------------------------------------------------
     * 取得文件类型
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     */
    public function getType()
    {
        $current = $this->current($this->_values);
        return $current['type'];
    }

    /**
      +----------------------------------------------------------
     * 是否为目录
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return boolen
      +----------------------------------------------------------
     */
    public function isDir()
    {
        $current = $this->current($this->_values);
        return $current['isDir'];
    }

    /**
      +----------------------------------------------------------
     * 是否为文件
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return boolen
      +----------------------------------------------------------
     */
    public function isFile()
    {
        $current = $this->current($this->_values);
        return $current['isFile'];
    }

    /**
      +----------------------------------------------------------
     * 文件是否为一个符号连接
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return boolen
      +----------------------------------------------------------
     */
    public function isLink()
    {
        $current = $this->current($this->_values);
        return $current['isLink'];
    }

    /**
      +----------------------------------------------------------
     * 文件是否可以执行
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return boolen
      +----------------------------------------------------------
     */
    public function isExecutable()
    {
        $current = $this->current($this->_values);
        return $current['isExecutable'];
    }

    /**
      +----------------------------------------------------------
     * 文件是否可读
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return boolen
      +----------------------------------------------------------
     */
    public function isReadable()
    {
        $current = $this->current($this->_values);
        return $current['isReadable'];
    }

    /**
      +----------------------------------------------------------
     * 获取foreach的遍历方式
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     */
    public function getIterator()
    {
        return new \ArrayObject($this->_values);
    }

    // 返回目录的数组信息
    public function toArray()
    {
        return $this->_values;
    }

    // 静态方法

    /**
      +----------------------------------------------------------
     * 判断目录是否为空
      +----------------------------------------------------------
     * @access static
      +----------------------------------------------------------
     * @return void
      +----------------------------------------------------------
     */
    public function isEmpty($directory)
    {
        $handle = opendir($directory);
        while (($file = readdir($handle)) !== false) {
            if ($file != "." && $file != "..") {
                closedir($handle);
                return false;
            }
        }
        closedir($handle);
        return true;
    }

    /**
      +----------------------------------------------------------
     * 取得目录中的结构信息
      +----------------------------------------------------------
     * @access static
      +----------------------------------------------------------
     * @return void
      +----------------------------------------------------------
     */
    public function getList($directory)
    {
        return scandir($directory);
    }

    /**
      +----------------------------------------------------------
     * 删除目录（包括下面的文件）
      +----------------------------------------------------------
     * @access static
      +----------------------------------------------------------
     * @return void
      +----------------------------------------------------------
     */
    public function delDir($directory, $subdir = true)
    {
        if (is_dir($directory) == false) {
            $this->error = "该目录是不存在！";
            return false;
        }
        $handle = opendir($directory);
        while (($file = readdir($handle)) !== false) {
            if ($file != "." && $file != "..") {
                is_dir("$directory/$file") ?
                    Dir::delDir("$directory/$file") :
                    unlink("$directory/$file");
            }
        }
        if (readdir($handle) == false) {
            closedir($handle);
            rmdir($directory);
        }
    }

    /**
      +----------------------------------------------------------
     * 删除目录下面的所有文件，但不删除目录
      +----------------------------------------------------------
     * @access static
      +----------------------------------------------------------
     * @return void
      +----------------------------------------------------------
     */
    public function del($directory)
    {
        if (is_dir($directory) == false) {
            $this->error = "该目录是不存在！";
            return false;
        }
        $handle = opendir($directory);
        while (($file = readdir($handle)) !== false) {
            if ($file != "." && $file != ".." && is_file("$directory/$file")) {
                unlink("$directory/$file");
            }
        }
        closedir($handle);
    }

    /**
      +----------------------------------------------------------
     * 复制目录
      +----------------------------------------------------------
     * @access static
      +----------------------------------------------------------
     * @return void
      +----------------------------------------------------------
     */
    public function copyDir($source, $destination)
    {
        if (is_dir($source) == false) {
            $this->error = "源目录不存在！";
            return false;
        }
        if (is_dir($destination) == false) {
            mkdir($destination, 0700);
        }
        $handle = opendir($source);
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                is_dir("$source/$file") ?
                    Dir::copyDir("$source/$file", "$destination/$file") :
                    copy("$source/$file", "$destination/$file");
            }
        }
        closedir($handle);
    }
}
