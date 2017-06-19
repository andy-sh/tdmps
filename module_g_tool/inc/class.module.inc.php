<?php
/**
 * 系统模块处理类
 * 
 * @package module_g_tool
 * @subpackage model
 * @version $Id: class.module.inc.php 733 2013-09-23 07:09:57Z liqt $
 * @creator liqt @ 2013-08-26 15:39:09 by caster0.0.7
 */
namespace scap\module\g_tool;

/**
 * scap系统模块处理类
 * - 模块前缀：module_
 * - 模块id：模块id均不包含模块统一前缀
 */
class module
{
    /**
     * 模块统一前缀
     * @var string
     */
    const PREFIX = 'module_';
    
    /**
     * 模块信息定义：版本
     * @var string
     */
    const INFO_VERSION = 'version';
    
    /**
     * 模块信息定义：属性
     * @var string
     */
    const INFO_PROPERTY = 'property';
    
    /**
     * 模块信息定义：名称
     * @var string
     */
    const INFO_NAME = 'name';
    
    /**
     * 模块信息定义：备注
     * @var string
     */
    const INFO_COMMENT = 'comment';
    
    /**
     * 模块信息定义：注册的数据表集合
     * @var string
     */
    const INFO_TABLE = 'tables';
    
    /**
     * 模块属性:后台
     * - 与module_basic中定义的PROP_MODULE_BACK兼容
     * 
     * @var int
     */
    const PROPERTY_BACK = 1;
    
    /**
     * 模块属性:前台
     * - 与module_basic中定义的PROP_MODULE_FRONT兼容
     * 
     * @var int
     */
    const PROPERTY_FRONT = 2;
    
    /**
     * 获取模块根路径
     * 
     * @param string $project_path 项目路径，默认为NULL(则为当前调用模块的项目路径)
     * 
     * @return string
     */
    public static function get_module_path_root($project_path = NULL)
    {
        if (empty($project_path))
        {
            $project_path = SCAP_PATH_ROOT;
        }
        return $project_path;
    }
    
    /**
     * 对指定输入获取规范的模块id
     * - 模块id：模块id均不包含模块统一前缀
     * - 将类似 module_test 或者 test的输入均转换为标准id test
     * 
     * @param string $input 待规范的模块id字符串
     * 
     * @return string
     */
    public static function format_id($input)
    {
        $result = '';
        $input = strtolower($input);
        
        if (substr_compare($input, self::PREFIX, 0, strlen(self::PREFIX)) === 0)
        {
            $result = substr($input, strlen(self::PREFIX));
        }
        else
        {
            $result = $input;
        }
        
        return $result;
    }
    
    /**
     * 获取给定的模块id的文件夹根路径
     * 
     * @param string $id 模块id
     * @param string $project_path 项目路径，默认为NULL(则为当前模块所属的项目路径)
     * 
     * @return string
     */
    public static function get_path($id, $project_path = NULL)
    {
        $result = '';
        
        $result = self::get_module_path_root($project_path).'/'.self::PREFIX.self::format_id($id);
        
        return $result;
    }
    
    /**
     * 获取给定的模块id的changelog完整文件路径
     * 
     * @param string $id 模块id
     * @param string $project_path 项目路径，默认为NULL(则为当前模块所属的项目路径)
     * 
     * @return string
     */
    public static function get_path_file_changelog($id, $project_path = NULL)
    {
        $result = '';
        
        $result = self::get_module_path_root($project_path).'/'.self::PREFIX.self::format_id($id).'/changelog.txt';
        
        return $result;
    }
    
    /**
     * 获取给定的模块id的readme完整文件路径
     * 
     * @param string $id 模块id
     * @param string $project_path 项目路径，默认为NULL(则为当前模块所属的项目路径)
     * 
     * @return string
     */
    public static function get_path_file_readme($id, $project_path = NULL)
    {
        $result = '';
        
        $result = self::get_module_path_root($project_path).'/'.self::PREFIX.self::format_id($id).'/readme.txt';
        
        return $result;
    }
    
    /**
     * 获取给定的模块id的文件夹url路径
     * 
     * @param string $id 模块id
     * @param string $project_path 项目路径，默认为NULL(则为当前模块所属的项目路径)
     * 
     * @return string
     */
    public static function get_url($id, $project_path = NULL)
    {
        $result = '';
        
        $result = project::get_url($project_path).'/'.self::PREFIX.self::format_id($id);
        
        return $result;
    }
    
    /**
     * 根据指定模块属性值获取属性名称
     * 
     * @param int $property 模块属性值
     * 
     * @return string 属性名称
     */
    public static function get_property_name($property)
    {
        $result = '';
        
        switch($property)
        {
            case self::PROPERTY_BACK:
                $result = '后台';
                break;
            case self::PROPERTY_FRONT:
                $result = '前台';
                break;
            default:
                $result = '未知';
        }
        
        return $result;
    }
    
    /**
     * 获取本地模块列表
     * 
     * @param bool $flag_detail 是否返回模块详情，默认为false
     * @param string $project_path 项目路径，默认为NULL(则为当前模块所属的项目路径)
     * 
     * @return array $flag_detail为false:array('module_xx', 'module_yy', ...);
     * $flag_detail为true:array('module_xx' => array('version' => '1.0.0', ...), ...)
     */
    public static function get_local_list($flag_detail = false, $project_path = NULL)
    {
        $result = array();
        
        $path = self::get_module_path_root($project_path);
        
        if ($handle = opendir($path))
    	{
    		while(false !== ($file = readdir($handle)))
    		{
    			if (is_dir($path.'/'.$file) && strncmp(self::PREFIX, $file, strlen(self::PREFIX)) == 0)
    			{
    			    $current_id = self::format_id($file);
    			    if ($flag_detail)
    			    {
    			        $result[$current_id] = self::get_local_info($file, NULL, $project_path);
    			    }
    			    else
    			    {
    			        $result[] = $current_id;
    			    }
    			}
    		}
    		closedir($handle);
    	}
    	
    	return $result;
    }
    
    /**
     * 获取本地指定模块的信息
     * 
     * @param string $id 模块id
     * @param string $item 待获取的信息条目，默认为NULL(如果没有指定，则返回所有信息)
     * @param string $project_path 项目路径，默认为NULL(则为当前模块所属的项目路径)
     * 
     * @return mixed 如果没有指定$item，则返回所有信息，指定$item则仅返回其对应值
     */
    public static function get_local_info($id, $item = NULL, $project_path = NULL)
    {
        $result = array();
        
        $id = self::PREFIX.self::format_id($id);
        
        $file_def = self::get_module_path_root($project_path).'/'.$id."/inc/define.inc.php";
        
        if (file_exists($file_def))
        {
            @include($file_def);
            $result = $GLOBALS['scap']['module'][$id];
        }
        
        // 获取module名称
        @include (self::get_module_path_root($project_path).'/'.$id."/language/lang.zh-cn.inc.php");
        $result[self::INFO_NAME] = constant('TEXT_'.strtoupper($id));
        
        if (!empty($item))
        {
            $result = $result[$item];
        }
        
        return $result;
    }
    
    /**
     * 检查给定的模块id是否在本地存在
     * 
     * @param string $id 模块id
     * @param string $project_path 项目路径，默认为NULL(则为当前模块所属的项目路径)
     * 
     * @return bool
     */
    public static function check_local_exist($id, $project_path = NULL)
    {
        $result = false;
        
        $result = is_dir(self::get_path($id));
        
        return $result;
    }
    
    /**
     * 从给定的路径获取对应的模块id
     * 
     * @param string $path 文件或者文件夹完整路径,如/var/www/tool/module_basic/changelog.txt
     * 
     * @return string module id，如basic
     */
    public static function get_id_from_path($path)
    {
        $result = '';
        
        $array = preg_split("/[\/\\\]/", $path);// 识别\或者/作为分隔符
        foreach($array as $v)
        {
            if (@substr_compare($v, self::PREFIX, 0, strlen(self::PREFIX)) === 0)
            {
                $result = self::format_id($v);
                break;
            }
        }
        
        return $result;
    }
}
?>