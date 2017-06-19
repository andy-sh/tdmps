<?php
/**
 * 文件/目录处理类
 * 
 * @package module_g_tool
 * @subpackage model
 * @version $Id: class.file.inc.php 733 2013-09-23 07:09:57Z liqt $
 * @creator liqt @ 2013-01-15 11:17:25 by caster0.0.2
 */
namespace scap\module\g_tool;

/**
 * 文件/目录处理类
 */
class file
{
    /**
	 * 解析文件中的后缀
	 * 
	 * @param $file_name 文件名称（可含路径）
	 * 
	 * @return string 文件后缀
	 */
	public static function get_file_postfix($file_name)
	{
		$type = pathinfo($file_name);
		$type = strtolower($type["extension"]);
		return $type;
	}
	
    /**
     * 替换pathinfo，支持中文路径的返回文件路径的信息的方法
     * 
     * @param string $filepath 文件路径，如'/www/htdocs/inc/lib.inc.php'
     * 
     * @return array('dirname'=>'/www/htdocs/inc', 'basename'=>'lib.inc.php', 'extension'=>'php', 'filename' => 'lib.inc')
     */
    public static function path_info($filepath)
    {
        preg_match('%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im',$filepath,$m);
        if($m[1]) $ret['dirname']=$m[1];
        if($m[2]) $ret['basename']=$m[2];
        if($m[5]) $ret['extension']=$m[5];
        if($m[3]) $ret['filename']=$m[3];
        return $ret;
    }
    
    /**
     * 获取文件名称(带后缀)
     * 
     * @param string $path 文件完整路径
     * 
     * @return string
     */
    public static function get_file_name($path)
    {
        $result = '';
        
        $info = self::path_info($path);
        $result = $info['basename'];
        
        return $result;
    }
    
    /**
     * 删除指定文件或文件夹（不为空也可删除）
     *
     * @param string $dir 文件夹或文件地址
     *
     * @return bool
     */
    public static function remove($dir)
    {
        if (!file_exists($dir))
        {
            return true;
        }

        if (!is_dir($dir) || is_link($dir))
        {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item)
        {
            if ($item == '.' || $item == '..')
            {
                continue;
            }

            if (!self::remove($dir . "/" . $item))
            {
                chmod($dir . "/" . $item, 0777);
                if (!self::remove($dir . "/" . $item))
                {
                    return false;
                }
            };
        }
        return rmdir($dir);
    }
    
    /**
     * 打包指定路径到zip文件
     * use:zip('/folder/to/compress/', './compressed.zip');
     *
     * @param string $source 待压缩路径
     * @param string $destination 压缩后存储路径
     *
     * @return bool
     */
    public static function zip($source, $destination)
    {
        if (!extension_loaded('zip') || !file_exists($source))
        {
            return false;
        }

        $zip = new \ZipArchive();
        if (!$zip->open($destination, \ZIPARCHIVE::CREATE))
        {
            return false;
        }

        $source = str_replace('\\', '/', realpath($source));

        if (is_dir($source) === true)
        {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source), \RecursiveIteratorIterator::SELF_FIRST);
            foreach ($files as $file)
            {
                if (basename($file) == '.' || basename($file) == '..')
                {
                    continue;
                }
                $file = str_replace('\\', '/', realpath($file));
                if (is_dir($file) === true)
                {
                    $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                }
                elseif (is_file($file) === true)
                {
                    $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                }
            }
        }
        elseif (is_file($source) === true)
        {
            $zip->addFromString(basename($source), file_get_contents($source));
        }

        return $zip->close();
    }
    
    /**
     * 创建一个指定内容的临时文件,并返回该文件完整路径
     * 
     * @param string $content 指定文件内容
     * 
     * @return string|FALSE 返回临时文件名，出错返回 FALSE。
     */
    public static function create_tmp_file($content)
    {
        $file = tempnam(sys_get_temp_dir(), 'scaptmp');
        file_put_contents($file, $content);
        return $file;
    }
    
    /**
     * 保存文件到磁盘
     * 
     * @throws
     * 
     * @param string $name 文件名称(包含完整路径)
     * @param string $content 文件内容
     * @param bool $fore_replace 如果已存在同名文件强制替换(默认为false)
     * 
     * @return void
     */
    public static function save_file($name, $content, $fore_replace = false)
    {
        if (file_exists($name) && !$fore_replace)
        {
            throw new \Exception(sprintf("文件写入失败：'%s'已存在。", $name));
        }
        
        $result = file_put_contents($name, $content);// 创建文件
        
        if ($result === FALSE)
        {
            throw new \Exception(sprintf("文件写入失败：'%s'。", $name));
        }
    }
}
?>