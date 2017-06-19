<?php
/**
 * 模块访问控制类实现文件
 * 
 * @package module_g_template
 * @subpackage controller
 * @version $Id: class.module_access_controller.inc.php 216 2013-02-05 05:45:25Z liqt $
 * @creator liqt @ 2013-02-05 13:43:05 by caster0.0.2
 */

/**
 * module_g_template访问控制类
 * 
 */
class module_access_controller extends scap_module_access_controller
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 访问控制路由函数
     * @see scap_module_access_controller::validate_access_point()
     * 
     * @return bool
     */
    public function validate_access_point()
    {
        $result = true;
        
        return $result;
    }
    
}
?>