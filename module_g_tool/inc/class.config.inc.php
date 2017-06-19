<?php
/**
 * scap配置服务类
 * 
 * @package module_g_tool
 * @subpackage model
 * @version $Id: class.config.inc.php 727 2013-09-13 06:26:30Z liqt $
 * @creator liqt @ 2013-02-05 11:17:25 by caster0.0.2
 */
namespace scap\module\g_tool;

/**
 * scap配置类
 * - 支持对同一配置项的多个配置值，每个配置值可设定调用优先级，优先级数值越大，越被优先调用
 * @abstract
 */
abstract class config
{
    /**
     * 可配置数据数组
     * 
     * @var array
     */
    public static $config = array();
    
    /**
     * 设置配置项
     * 
     * @param string $domain_name 配置项所属域(一般是模块名等)
     * @param string $item_name 配置项名称
     * @param mixed $item_value 配置项值
     * @param int $priority 该配置优先级，默认为5，值越高则同等配置项优先取值
     */
    public static function set($domain_name, $item_name, $item_value, $priority = 5)
    {
        self::$config[$domain_name][$item_name][$priority] = $item_value;
    }
    
    /**
     * 获取配置项的对应值
     * 多个值获取优先级最高的
     * 
     * @param string $domain_name 配置项所属域
     * @param string $item_name 配置项名称
     * @param mixed $default_value 默认值(如果配置值不存在则使用)
     * 
     * @return mixed
     */
    public static function get($domain_name, $item_name, $default_value = NULL)
    {
        $result = NULL;
        
        if (isset(self::$config[$domain_name][$item_name]))
        {
            krsort(self::$config[$domain_name][$item_name]);// 按优先级排序
            $result = current(self::$config[$domain_name][$item_name]);
        }
        else 
        {
            $result = $default_value;
        }
        
        return $result;
    }
    
    /**
     * 获取所有配置值
     * 
     * @return array
     */
    public static function get_all()
    {
        return self::$config;
    }
    
    /**
     * 自动加载指定配置目录下的所有配置文件
     * -默认最先加载default.php
     * -只要是后缀为.php的文件都被当作config文件include。
     * -不支持子目录。
     * 
     * @param string $dir 配置文件目录(需加/)
     */
    public static function autoload_files($dir)
    {
        $files = glob($dir."*.php");
        $default_file = $dir.'default.php';
        
        if (!is_array($files))
        {
            return;
        }
        
        // 默认最先加载default.php
        $postion = array_search($default_file, $files);
        if ($postion !== FALSE)
        {
            include_once $default_file;
            unset($files[$postion]);
        }
        
        foreach($files as $v)
        {
            include_once $v;
        }
    }
}
?>