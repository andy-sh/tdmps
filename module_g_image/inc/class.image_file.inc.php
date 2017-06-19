<?php
/**
 * 图像文件类
 * 
 * @package module_g_image
 * @subpackage model
 * @version $Id: class.image_file.inc.php 739 2013-09-27 09:21:42Z liqt $
 * @creator liqt @ 2013-06-25 16:19:44 by caster0.0.3
 */
namespace scap\module\g_image;

/**
 * 图像文件类
 * - 用于处理单个图像文件
 * - 待逐步完善
 */
class image_file 
{
    /**
     * 文件完整路径
     * @var string
     */
    public $src = '';
    
    /**
     * 图片名称(如xxx.jpg)
     * @var string
     */
    public $name = '';
    
    /**
     * 图片名称(不含文件后缀)
     * @var string
     */
    public $name_body = '';
    
    /**
     * 图片名称后缀
     * @var string
     */
    public $name_ext = '';
    
    /**
     * 图片MIME类型
     * @var string
     */
    public $mime = '';
    
    /**
     * 图片宽度
     * @var int
     */
    public $width = 0;
    
    /**
     * 图片高度
     * @var int
     */
    public $height = 0;
    
    /**
     * 图片大小(字节)
     * @var int
     */
    public $size = 0;
    
    /**
     * 图片大小(方便阅读模式)
     * @var string
     */
    public $size_readable = 0;
    
    /**
     * 构造函数
     * 
     * @param string $src 图像文件完整路径
     */
    public function __construct($src)
    {
        $this->src = $src;
        if (!file_exists($src))
        {
            trigger_error("图片($src)不存在。", E_USER_WARNING);
        }
        else
        {
            $info = \scap\module\g_tool\file::path_info($src);
            $this->name = $info['basename'];
            $this->name_body = $info['filename'];
            $this->name_ext = $info['extension'];
            
            $info = getimagesize($src);
            $this->width = $info[0];
            $this->height = $info[1];
            $this->mime = $info['mime'];
            
            $this->size = filesize($src);
            $this->size_readable = \scap\module\g_tool\string::get_readable_from_bytes($this->size);
        }
    }
}
?>