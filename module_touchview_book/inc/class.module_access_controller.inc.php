<?php
/**
 * 模块访问控制类
 * create time: 2011-12-15 06:36:55
 * @version $Id: class.module_access_controller.inc.php 119 2012-04-23 08:04:20Z liqt $
 * @author zhangzhengqi
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
        
        if ($this->current_class_name == 'ui_book_front')
        {
            switch($this->current_method_name)
            {
                case 'book':
                    $result = $this->access_front_book();
                    break;
            }
        }
        elseif ($this->current_class_name == 'ui_book')
        {
            $result = $this->access_book_manage();
        }
        
        return $result;
    }
    
    /**
     * 访问书籍
     * 
     * @return bool
     */
    private function access_front_book()
    {
        //--------变量定义及声明[start]--------
        $data_def = array();
        $result = true;
        //--------变量定义及声明[end]--------
        
        if (empty($_GET['b_id']))
        {
            $_GET['b_id'] = book::get_default_book_id();// 如果没有指定b id，使用默认书籍
            $_GET['view'] = 'home';
        }
        
        return $result;
    }
    
	/**
     * 书籍管理
     * 
     * @return bool
     */
    private function access_book_manage()
    {
        //--------变量定义及声明[start]--------
        $result = false;
        //--------变量定义及声明[end]--------
        
        $result = scap_check_acl($this->current_module_id, ACL_BIT_MODULE);
        if ($result) return $result;
        
        return $result;
    }
}
?>