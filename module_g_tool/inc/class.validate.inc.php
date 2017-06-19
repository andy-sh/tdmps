<?php
/**
 * (字符串)验证类文件
 * 
 * @package module_g_tool
 * @subpackage model
 * @version $Id: class.validate.inc.php 169 2013-01-30 04:58:14Z liqt $
 * @creator liqt @ 2013-01-30 12:04:02 by caster0.0.2
 */
namespace scap\module\g_tool;

/**
 * (字符串)验证类
 */
class validate
{
    /**
     * 是否非空(输入字符串长度大于0)
     * 
     * @param string $content 待验证内容
     * 
     * @return bool
     */
    public static function is_notempty($content)
    {
        $result = (strlen($content) > 0);
        
        return $result;
    }
    
    /**
     * 是否为email
     * 
     * @param string $content 待验证内容
     * @param bool $flag_allow_empty 允许为空标志，默认false
     * 
     * @return bool
     */
    public static function is_email($content, $flag_allow_empty = false)
    {
        $result = false;
        
        if ($flag_allow_empty && strlen($content) == 0)
        {
            $result = true;
        }
        else
        {
            $result = preg_match("/^[A-Za-z0-9._%+-]+@\S+$/", $content);
        }
        
        return $result;
    }
    
    /**
     * 是否为mobile
     * - 中国手机
     * 
     * @param string $content 待验证内容
     * @param bool $flag_allow_empty 允许为空标志，默认false
     * 
     * @return bool
     */
    public static function is_mobile($content, $flag_allow_empty = false)
    {
        $result = false;
        
        if ($flag_allow_empty && strlen($content) == 0)
        {
            $result = true;
        }
        else
        {
            $result = preg_match("/^(1)\d{10}$/", $content);
        }
        
        return $result;
    }
    
    /**
     * 是否为ip
     * 
     * @param string $content 待验证内容
     * @param bool $flag_allow_empty 允许为空标志，默认false
     * 
     * @return bool
     */
    public static function is_ip($content, $flag_allow_empty = false)
    {
        $result = false;
        
        if ($flag_allow_empty && strlen($content) == 0)
        {
            $result = true;
        }
        else
        {
            $result = preg_match("/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/", $content);
        }
        
        return $result;
    }
}
?>