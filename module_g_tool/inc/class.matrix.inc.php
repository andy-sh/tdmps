<?php
/**
 * 矩阵处理类
 * 
 * @package module_g_tool
 * @subpackage model
 * @version $Id: class.matrix.inc.php 716 2013-08-22 09:39:13Z liqt $
 * @creator liqt @ 2013-01-15 11:17:25 by caster0.0.2
 */
namespace scap\module\g_tool;

/**
 * 矩阵处理类
 */
class matrix
{
    /**
     * 对多维数组进行指定值的排序操作
     * 
     * @param &array $data 进行排序的多维数组(eg.
      		array(
    		   array('id' => 1, 'name' => 'apple'),
    		   array('id' => 2, 'name' => 'orange'),
    		   array('id' => 8, 'name' => 'banana'),
    		))
     * @param string $sortby 需要排序的数组键值,多列以逗号分开(eg. 'name,id')
     * @param string $order value maybe: ASC|DESC
     * @param bool $flag_keep_index 是否保留原索引关系,默认为false
     * 
     * @return void
     */
    public static function musort(&$data, $sortby, $order='ASC', $flag_keep_index = false)
    {
    	static $sort_funcs = array();
    	
    	if (empty($sort_funcs[$sortby]))
    	{
    		$code = "\$c=0;";
    		
    		foreach (explode(',', $sortby) as $key)
    		{
    			$array = array_pop($data);
    			array_push($data, $array);
    			
    			if(is_numeric($array[$key]))
    			{
    				if (strcasecmp($order, 'ASC') == 0)// 升序
    				{
    					$code .= "if ( \$c = ((\$a['$key'] == \$b['$key']) ? 0:((\$a['$key'] < \$b['$key']) ? -1 : 1 )) ) return \$c;";
    				}
    				else// 降序
    				{
    					$code .= "if ( \$c = ((\$a['$key'] == \$b['$key']) ? 0:((\$a['$key'] > \$b['$key']) ? -1 : 1 )) ) return \$c;";
    				}
    			}
    			else
    			{
    				if (strcasecmp($order, 'ASC') == 0)
    				{
    					$code .= "if ( (\$c = strcasecmp(\$a['$key'],\$b['$key'])) != 0 ) return \$c;\n";
    				}
    				else
    				{
    					$code .= "if ( (\$c = strcasecmp(\$b['$key'],\$a['$key'])) != 0 ) return \$c;\n";
    				}
    			}
    		}
    		
    		$code .= 'return $c;';
    		$sort_func = $sort_funcs[$sortby] = create_function('$a, $b', $code);
    	}
    	else
    	{
    		$sort_func = $sort_funcs[$sortby];
    	}
    	
    	$sort_func = $sort_funcs[$sortby];
    	if ($flag_keep_index)
    	{
    		uasort($data, $sort_func);
    	}
    	else
    	{
    		usort($data, $sort_func);
    	}
    }
    
    /**
     * 多维数组数值查找函数
     * 
     * @param string $needle 待搜索的字符串
     * @param array $haystack 被搜索的多维数组
     * @param string $key_lookin 所要查找的键值名称，默认为""
     * 
     * @return array array(0=> '对应的第1层键值', 1 => '对应的第2层键值', ...)
     */
    public static function musearch($needle, $haystack, $key_lookin="")
    {
    	$path = NULL;
    	
    	if (!is_array($haystack))
    	{
    		return NULL;
    	}
    	
    	if (!empty($key_lookin) && array_key_exists($key_lookin, $haystack) && $needle === $haystack[$key_lookin])
    	{
    		$path[] = $key_lookin;
    	}
    	else
    	{
    		foreach($haystack as $key => $val)
    		{
    			if (is_scalar($val) && $val === $needle && empty($key_lookin))
    			{
    				$path[] = $key;
    				break;
    			}
    			elseif (is_array($val) && $path = self::musearch($needle, $val, $key_lookin))
    			{
    				array_unshift($path, $key);
    				break;
    			}
    		}
    	}
    	
    	return $path;
    }
    
    /**
     * 清除给定数据(字符串或多维数组)值的前后空白
     * 
     * @uses 有效的使用示例
     * $arr = array(
            'Key1' => ' Value 1 ',
            'Key2' => '      Value 2      ',
            'Key3' => array(
                '   Child Array Item 1 ', 
                '   Child Array Item 2'
            )
        );
        var_dump($arr);
        var_dump(\scap\module\g_tool\matrix::trim('  abc '));
        var_dump(\scap\module\g_tool\matrix::trim($arr));
     * 
     * @param mixed(string|array) $input 待处理数据
     * 
     * @return mixed(string|array)
     */
    public static function trim($input)
    {
        if (!is_array($input))
        {
            return trim($input);
        }
        
        return array_map('self::trim', $input);
    }
    
    /**
     * 将给定数据(字符串或多维数组)值进行url编码
     * 
     * @param mixed(string|array) $input 待处理数据
     * 
     * @return mixed(string|array)
     */
    public static function urlencode($input)
    {
        if (!is_array($input))
        {
            return urlencode($input);
        }
        
        return array_map('self::urlencode', $input);
    }
    
    /**
     * 检查数组中指定值是否存在
     * - 支持多维
     *
     * @param string $needle 待搜索的字符串
     * @param array $haystack 被搜索的多维数组
     * @param string $key_lookin 所要查找的键值名称，默认为""
     *
     * @return bool
     */
    public static function check_value_exist($needle, $haystack, $key_lookin="")
    {
        $result = false;
        
        $temp = self::musearch($needle, $haystack, $key_lookin);
        
        $result = !empty($temp);
        
        return $result;
    }
    
    /**
     * 对需要进入sql查询的参数进行安全处理
     * - 支持多维
     * 
     * @param mixed(string|array) $input 待处理数据
     * 
     * @return mixed(string|array)
     */
    public static function safe_input_for_sql($input)
    {
        if (!is_array($input))
        {
            return string::safe_input_for_sql($input);
        }
        
        return array_map('self::safe_input_for_sql', $input);
    }
}
?>