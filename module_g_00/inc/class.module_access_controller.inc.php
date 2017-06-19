<?php
/**
 * description: 模块访问控制器类
 * create time: 2009-4-7-下午05:32:13
 * @version $Id: class.module_access_controller.inc.php 4 2012-07-18 06:40:23Z liqt $
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