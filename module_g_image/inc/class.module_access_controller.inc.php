<?php
/**
 * 模块访问控制类
 * create time: 2011-11-21 上午10:16:49
 * @version $Id: class.module_access_controller.inc.php 4 2012-07-21 07:04:47Z liqt $
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
        
        switch($this->current_method_name)
        {
        }
        
        return $result;
    }
}
?>