<?php
/**
 * description: scap事件抽象类
 * create time: 2009-4-4 10:56:15
 * @version $Id: core.class.scap_module_event.inc.php 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao
 */

/**
 * scap事件抽象类
 * 继承类应实现相应事件函数
 * 
 * @author Liqt
 *
 */
abstract class scap_module_event
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
	 * 当前调用的事件名称
	 * @var string
	 */
	protected $current_event_name = NULL;
	
	/**
	 * 当前调用的事件执行结果
	 * @var bool
	 */
	protected $current_event_result = NULL;
	
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
	}
	
	function __destruct()
	{
		
	}
	
	/**
	 * 处理当前UI事件
	 * @return null
	 */
	abstract public function process_ui_event();
	
	/**
	 * 执行指定的事件
	 * 
	 * @param $event_name
	 * @return null
	 */
	protected function excute_event($event_name)
	{
		if (!method_exists($this, "event_{$event_name}"))
		{
			throw new Exception("事件方法{$event_name}未注册！");
		}
		
		$GLOBALS['scap']['event']['name'] = $this->current_event_name = $event_name;
		$GLOBALS['scap']['event']['result'] = $this->current_event_result = call_user_func(array($this, "event_{$event_name}"));
	}
}
?>