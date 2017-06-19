<?php
/**
 * 图片目录类实现文件
 * create time: 2011-11-22 上午11:41:12
 * @version $Id: class.image_dir.inc.php 4 2012-07-21 07:04:47Z liqt $
 * @author LiQintao
 */

/**
 * 图片目录类
 *
 */
class image_dir
{
    /**
     * 图片本地路径
     * @var string
     */
    protected $path_image_dir = '';
    
    /**
     * URL路径
     * @var string
     */
    protected $url_image_dir = '';
    
    /**
     * 构造函数
     * @param string $path_image_dir 图片目录本地路径
     * @param string $url_image_dir URL路径，默认为空(无外网入口)
     */
    public function __construct($path_image_dir, $url_image_dir = '')
    {
        $this->set_path_image_dir($path_image_dir);
        $this->set_url_image_dir($url_image_dir);
    }
    
    /**
     * 设置图片路径
     * @param string $path_image_dir 图片目录本地路径
     * @throws Exception 无效路径
     */
    protected function set_path_image_dir($path_image_dir)
    {
        // 为路径结尾增加斜杠
        $this->path_image_dir = image_dir::add_dir_ending_slash($path_image_dir);
        
        if (is_dir($this->path_image_dir) == false)
        {
            throw new Exception("无效的图片目录路径：".$this->path_image_dir);
        }
    }
    
    /**
     * 设置图片目录URL路径
     * @param string $url_image_dir 图片目录URL路径
     */
    protected function set_url_image_dir($url_image_dir)
    {
        if (empty($url_image_dir)) return;
        
        // 为路径结尾增加斜杠
        $this->url_image_dir = image_dir::add_url_ending_slash($url_image_dir);
    }
    
    /**
     * 获取目录下的图片列表信息
     * 
     * @param string $pattern_name 获取的文件名称模式（正则）
     * 
     * @return array 图片列表信息，示例如下：
     * Array
(
    [01.jpg] => Array
        (
            [path] => /var/www/rs/module_rs_portal/setup/slider_image/01.jpg
            [url] => http://localhost/rs/module_rs_portal/setup/slider_image/01.jpg
            [name] => 01
            [extension] => jpg
            [width] => 618
            [height] => 246
            [mime] => image/jpeg
            [size] => 18.3KB
        )
 
    [02.jpg] => Array
        (
            ...
        )
)
     */
    public function get_image_list($pattern_name = "*.{jpg,jpeg,png,gif}")
    {
        $result = array();
        
        $images = glob($this->path_image_dir.$pattern_name, GLOB_BRACE);
        
        foreach($images as $v)
        {
            $temp_info = image_dir::path_info($v);
            $result[$temp_info['basename']]['path'] = $v;// 文件完整路径
            if (!empty($this->url_image_dir))
            {
                $result[$temp_info['basename']]['url'] = $this->url_image_dir.$temp_info['basename'];
            }
            $result[$temp_info['basename']]['name'] = basename($temp_info['basename'], '.'.$temp_info['extension']);// 文件名（不含扩展名）
            $result[$temp_info['basename']]['extension'] = $temp_info['extension'];// 扩展名
            $result[$temp_info['basename']]['size'] = image_dir::format_bytes(filesize($v));// 文件大小
            
            $temp_image_info = getimagesize($v);
            $result[$temp_info['basename']]['width'] = $temp_image_info[0];// 图像宽度
            $result[$temp_info['basename']]['height'] = $temp_image_info[1];// 图像高度
            $result[$temp_info['basename']]['mime'] = $temp_image_info['mime'];// 图像类型
        }
        
        return $result;
    }
    
    /**
     * 为路径最后自动加上斜杠
     * 
     * @param string $path 目录路径
     * 
     * @return string 处理后的路径
     */
    public static function add_dir_ending_slash($path)
    {
        $slash_type = (strpos($path, '\\') === 0) ? 'win' : 'unix';
        $last_char = substr($path, strlen($path)-1, 1);
        if ($last_char != '/' && $last_char != '\\')
        {
            // no slash:
            $path .= ($slash_type == 'win') ? '\\' : '/';
        }
        
        return $path;
    }
    
    /**
     * 为URL最后自动加上斜杠
     * 
     * @param string $url URL路径
     * 
     * @return string 处理后的url路径
     */
    public static function add_url_ending_slash($url)
    {
        $last_char = substr($url, strlen($url)-1, 1);
        if ($last_char != '/')
        {
            // no slash:
            $url .= '/';
        }
        
        return $url;
    }
    
	/**
     * 格式化文件大小，以方便阅读
     * 
     * @param int $bytes 字节大小
     * 
     * @return string 格式好的大小
     */
    public static function format_bytes($bytes)
    {
        if ($bytes < 1024) return $bytes.' B';
        elseif ($bytes < 1048576) return round($bytes / 1024, 2).' KB';
        elseif ($bytes < 1073741824) return round($bytes / 1048576, 2).' MB';
        elseif ($bytes < 1099511627776) return round($bytes / 1073741824, 2).' GB';
        else return round($bytes / 1099511627776, 2).' TB';
    }
    
    /**
     * 替换pathinfo，支持中文路径的返回文件路径的信息的方法
     * 
     * @param string $filepath 文件路径
     * 
     * @return array
     */
    public static function path_info($filepath)
    {
        $path_parts = array ();
        $path_parts ['dirname'] = substr ( $filepath, 0, strrpos ( $filepath, '/' ) );
        $path_parts ['basename'] = substr ( $filepath, strrpos ( $filepath, '/' ) + 1 );
        $path_parts ['extension'] = substr ( strrchr ( $filepath, '.' ), 1 );
        $path_parts ['filename'] = substr ( $path_parts ['basename'], 0, strrpos ( $path_parts ['basename'], '.' ) );
        return $path_parts;
    }
}
?>