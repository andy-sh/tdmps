<?php
/**
 * 门户模块访问控制类
 * create time: 2011-12-13 下午04:34:19
 * @version $Id: class.module_access_controller.inc.php 119 2012-04-23 08:04:20Z liqt $
 * @author LiQintao
 */

class module_access_controller extends scap_module_access_controller
{
    function __construct()
    {
        parent::__construct();
    }
    
    public function validate_access_point()
    {
        $result = true;
        
        if ($this->current_class_name == 'ui_page')
        {
            $result = $this->access_page_manage();
        }
        
        return $result;
    }
    
	/**
     * 页面管理
     * 
     * @return bool
     */
    private function access_page_manage()
    {
        //--------变量定义及声明[start]--------
        $result = false;
        //--------变量定义及声明[end]--------
        
        $result = scap_check_acl('module_touchview_book', ACL_BIT_MODULE);
        if ($result) return $result;
        
        return $result;
    }
}
?>