<?php
/**
 * scap项目相关信息处理类
 * 
 * @package module_g_tool
 * @subpackage model
 * @version $Id: class.project.inc.php 780 2013-10-09 04:39:22Z liqt $
 * @creator liqt @ 2013-09-22 15:39:09 by caster0.0.7
 */
namespace scap\module\g_tool;

/**
 * scap项目相关信息处理类
 */
class project
{
    /**
     * 获取项目根路径
     * 
     * @param string $path 项目路径，默认为NULL(则为当前项目路径)
     * 
     * @return string
     */
    public static function get_path_root($path = NULL)
    {
        if (empty($path))
        {
            $path = SCAP_PATH_ROOT;
        }
        return $path;
    }
    
    /**
     * 获取指定项目的url根路径
     * 
     * @param string $path 指定的项目路径，默认为NULL(则为当前模块所属的项目路径)
     * 
     * @return string 该项目的url路径
     */
    public static function get_url($path = NULL)
    {
        $result = '';
        
        if (!empty($path))
        {
            $current_url = $GLOBALS['scap']['info']['site_url'];
            $dir_config = $path.'/config/';
            if (is_dir($dir_config))// for scap2.1+
            {
                $current_config = config::$config;
                
                @config::autoload_files($dir_config);
                $result = config::get('scap', 'project_url');
                
                if (file_exists($dir_config.'/config.inc.php'))// for 部分scap中间版本项目
                {
                    @include $dir_config.'/config.inc.php';
                    $result = $GLOBALS['scap']['info']['site_url'];
                }
                
                config::$config = $current_config;// 避免项目数据污染当前项目
            }
            else// for scap2.0-
            {
                @include $path.'/config.inc.php';
                $result = $GLOBALS['scap']['info']['site_url'];
            }
            
            $GLOBALS['scap']['info']['site_url'] = $current_url;// 避免项目数据污染当前项目
        }
        else
        {
            $result = config::get('scap', 'project_url');
        }
        
        return $result;
    }
    
    /**
     * 获取给定项目的changelog完整文件路径
     * 
     * @param string $path 项目路径，默认为NULL(则为当前模块所属的项目路径)
     * 
     * @return string
     */
    public static function get_path_file_changelog($path = NULL)
    {
        $result = '';
        
        $result = self::get_path_root($path).'/changelog.txt';
        
        return $result;
    }
    
    /**
     * 获取给定项目的readme完整文件路径
     * 
     * @param string $path 项目路径，默认为NULL(则为当前模块所属的项目路径)
     * 
     * @return string
     */
    public static function get_path_file_readme($path = NULL)
    {
        $result = '';
        
        $result = self::get_path_root($path).'/readme.txt';
        
        return $result;
    }
    
    /**
     * 获取给定项目的htaccess完整文件路径
     * 
     * @param string $path 项目路径，默认为NULL(则为当前模块所属的项目路径)
     * 
     * @return string
     */
    public static function get_path_file_htaccess($path = NULL)
    {
        $result = '';
        
        $result = self::get_path_root($path).'/.htaccess';
        
        return $result;
    }
}
?>