<?php
/**
 * description: 模块访问控制器抽象类
 * create time: 2009-4-3-上午11:25:22
 * @version $Id: core.class.scap_module_access_controller.inc.php 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao
 */

/**
 * 模块访问控制器抽象类
 * 
 * @uses 对模块的访问进行控制，包括：1.URL的访问控制 2.UI事件的访问控制
 * 
 * @author Liqt
 *
 */
abstract class scap_module_access_controller
{
	/**
	 * 当前调用的GET参数字符串
	 * @var string
	 */
	protected $current_get_query = NULL;
	
	/**
	 * 当前解析GET参数字符串后的参数数组
	 * @var array
	 */
	protected $current_get_array = array();
	
	/**
	 * 当前调用的模块ID
	 * @var string
	 */
	protected $current_module_id = NULL;
	
	/**
	 * 当前调用的类名称
	 * @var string
	 */
	protected $current_class_name = NULL;
	
	/**
	 * 当前调用的方法名称
	 * @var string
	 */
	protected $current_method_name = NULL;
	
	/**
	 * 当前帐户登录id
	 * @var string
	 */
	protected $current_account_id = '';
	
	/**
	 * 当前帐户uuid
	 * @var string
	 */
	protected $current_account_s_id = '';
	
	function __construct()
	{
		$this->current_get_query = $_SERVER['QUERY_STRING'];// 获取当前访问的URL参数字符串
		parse_str($this->current_get_query, $this->current_get_array);// 解析GET参数
		list($this->current_module_id, $this->current_class_name, $this->current_method_name) = explode('.', $this->current_get_array['m']);
		
		$this->current_account_s_id = $GLOBALS['scap']['auth']['account_s_id'];
		$this->current_account_id = $GLOBALS['scap']['auth']['account_id'];
		
		// 加载模块下的权限定义文件:define.module_acl.inc.php
		@scap_load_module_define($this->current_module_id, 'module_acl');
	}
	
	function __destruct()
	{
		
	}
	
	/**
	 * 验证当前调用是否合法
	 * 
	 * @uses 供SCAP在index.php中调用，对当前URL请求进行访问控制检查
	 * 
	 * @tutorial 该方法需要子类继承实现，通过对$_REQUEST数据的分析实现UI中可调用方法的接入点检查。
	 * 
	 * @return bool
	 */
	abstract public function validate_access_point();
}
?>