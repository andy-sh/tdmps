<?php
/**
 * description: 模块UI抽象类
 * create time: 2009-4-4-17:18:16
 * @version $Id: core.class.scap_module_ui.inc.php 94 2013-04-10 02:17:50Z liqt $
 * @author LiQintao
 */

/**
 * 模块UI抽象类
 * 
 * @deprecated 被scap_ui类替换
 *
 */
abstract class scap_module_ui
{
	/**
	 * 指定打开的的模块ID
	 * 用于跨模块调用菜单用
	 * @var string
	 */
	protected $open_module_id = NULL;
	
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
	protected $current_event_result = false;
	
	/**
	 * html元素操作类
	 * object class.html_output.php
	 */
	protected $html = NULL;
	
	/**
	 * 模板操作类
	 * object core.class.scap_tpl.inc.php
	 */
	public $tpl = NULL;
	
	/**
	 * 当前菜单
	 * @var string
	 */
	private $current_menu_text = NULL;
	
	/**
	 * 当前方法标题
	 * @var string
	 */
	private $current_method_title = NULL;
	
	/**
	 * 当前应用head meta关键字
	 * @var string
	 */
	private $current_head_keywords = '';
	
	/**
	 * 当前应用head meta描述
	 * @var string
	 */
	private $current_head_description = '';
	
	function __construct()
	{
	    $this->current_module_id = $GLOBALS['scap']['info']['current_module_id'];
	    $this->current_class_name = $GLOBALS['scap']['info']['current_class'];
	    $this->current_method_name = $GLOBALS['scap']['info']['current_method'];
	    
		$this->tpl = new scap_tpl();
		$this->html = new scap_html();
		
		set_include_path(get_include_path().PATH_SEPARATOR.SCAP_PATH_LIBRARY.'html_element');
		
		scap_load_module_language($this->current_module_id, 'zh-cn');
		
		// 该脚本最长执行时间
		@set_time_limit(300);
	}
	
	function __destruct()
	{
		
	}
	
	/**
	 * 设置当前方法标题
	 * @param $text
	 */
	protected function set_current_method_title($text)
	{
		$this->current_method_title = $text;
	}
	
	/**
	 * 获取当前方法标题
	 * @return string
	 */
	protected function get_current_method_title()
	{
		return $this->current_method_title;
	}
	
	/**
	 * 设置当前应用head meta关键字
	 * @param $text
	 */
	protected function set_current_head_keywords($text)
	{
		$this->current_head_keywords = $text;
	}
	
	/**
	 * 获取当前应用head meta关键字
	 * @return string
	 */
	protected function get_current_head_keywords()
	{
		return $this->current_head_keywords;
	}
	
	/**
	 * 获取应用head meta描述
	 * @return string
	 */
	protected function get_current_head_description()
	{
		return $this->current_head_description;
	}
	
	/**
	 * 设置当前菜单
	 * @param string $menu_text
	 * @param string $open_module_id 要指向的菜单的所属模块，默认为空（应用所属的模块）
	 */
	protected function set_current_menu_text($menu_text)
	{
		$this->current_menu_text = $menu_text;
		$this->open_module_id = $_GET['open_module_id'];
	}
	
	/**
	 * 包含系统的header模版数据
	 * 
	 * @param bool $flag_load_navbar 是否Load导航栏,默认为true
	 * @param bool $flag_load_default_lib 是否加载系统默认的head文件，比如css js和一些代码，默认为true
	 */
	static protected function load_scap_header($flag_load_navbar = true, $flag_load_default_lib = true)
	{
	    global $data_out;
	    $data_out['flag_load_default_lib'] = $flag_load_default_lib;
	    
		require(SCAP_PATH_BASIC.'inc/scap.header.inc.php');
		if ($flag_load_navbar)
		{
	 		require(SCAP_PATH_BASIC.'inc/scap.navbar.inc.php');
		}
	}
	
	/**
	 * 包含系统的footer模版数据
	 * 
	 */
	static protected function load_scap_footer()
	{
		require(SCAP_PATH_BASIC.'inc/scap.footer.inc.php');
		
		// 现实当前调用模块关联的模块系统信息
		global $data_out;
		$data_out['module_info'] = '<p>';
		foreach($GLOBALS['scap']['module'] as $k => $v)
		{
			$data_out['module_info'] .= "[$k:{$v['version']}]";
		}
		$data_out['module_info'] .= '</p>';
	}
	
	/**
	 * 显示系统的header模版
	 * 
	 * @param bool $flag_show_navbar 是否show导航栏,默认为true
	 */
	protected function show_scap_header($flag_show_navbar = true)
	{
		$this->tpl->set_module_tpl('module_basic');
		$this->tpl->display('scap.header.tpl');
		
		if ($flag_show_navbar)
		{
			$this->tpl->display('scap.navbar.tpl');
		}
	}
	
	/**
	 * 显示系统的footer模版
	 */
	protected function show_scap_footer()
	{
		$this->tpl->set_module_tpl('module_basic');
		$this->tpl->display('scap.footer.tpl');
	}
	
	/**
	 * 设置系统模块菜单是否显示
	 * 需在load_scap_header()后调用
	 * 
	 * @param bool $flag_show 是否显示系统模块菜单部分
	 */
	protected function show_scap_module_menu($flag_show = true)
	{
		global $data_out;
		$data_out['nav_flag_show_module_menu'] = $flag_show;
	}
	
	/**
	 * 加载当前模块所需的css文件
	 * 
	 * @param string $url css文件的链接
	 */
	static public function load_css_file($url)
	{
		global $data_out;
		$data_out['head_css_list'][] = $url;
	}
	
	/**
	 * 加载当前模块所需的js文件
	 * 
	 * @param string $url js文件的链接
	 */
	static public function load_js_file($url)
	{
		global $data_out;
		if (!is_array($data_out['head_js_list']))
		{
		    $data_out['head_js_list'] = array();
		}
		// 避免重复添加
		if (array_search($url, $data_out['head_js_list']) === FALSE)
		{
		  $data_out['head_js_list'][] = $url;
		}
	}
	
	/**
	 * 加载指定代码到<head>中
	 * 
	 * @param $code
	 */
	static public function load_code_in_head($code)
	{
		global $data_out;
		$data_out['head_customer_code_list'][] = $code;
	}
	
	/**
	 * 加载指定代码到<body>中
	 * 
	 * @param $code
	 */
	static public function load_code_in_body($code)
	{
		global $data_out;
		$data_out['body_customer_code_list'][] = $code;
	}
	
	/**
	 * 加载日历选择组件需的js文件
	 * @deprecated
	 */
	static public function load_calendar_file($lang = 'zh', $theme = 'calendar-win2k-1', $stripped = true)
	{
		$dir = SCAP_RELATIVE_PATH_LIBRARY."jscalendar/";
		// calendar stylesheet
		scap_module_ui::load_css_file($dir.$theme.".css");
		// main calendar program
		if ($stripped)
		{
			scap_module_ui::load_js_file($dir."calendar_stripped.js");
			scap_module_ui::load_js_file($dir."calendar-setup_stripped.js");
		}
		else
		{
			scap_module_ui::load_js_file($dir."calendar.js");
			scap_module_ui::load_js_file($dir."calendar-setup.js");
		}
		// language for the calendar
		scap_module_ui::load_js_file($dir."lang/calendar-$lang.js");
	}
	
	/**
	 * 加载jquery插件所需文件
	 *
	 * @param array $arr_files 只需说明jquery/plugins下的路径
	 */
	static public function load_jquery_plugin($arr_files)
	{
		$dir = SCAP_RELATIVE_PATH_LIBRARY."jquery/plugins/";
		foreach ($arr_files as $k => $v)
		{
			if (preg_match("/(\.js)$/", $v))
			{
				scap_module_ui::load_js_file($dir.$v);
			}
			elseif (preg_match("/(\.css)$/", $v))
			{
				scap_module_ui::load_css_file($dir.$v);
			}
		}
	}
	
	/**
	 * 加载greybox所需文件
	 */
	static public function load_greybox_file()
	{
		$dir = SCAP_RELATIVE_PATH_LIBRARY."greybox/";
		scap_module_ui::load_code_in_head(scap_html::js_tag("var GB_ROOT_DIR = \"{$dir}\";"));// 该句应该放在文件之前
		scap_module_ui::load_code_in_head(scap_html::js_file_tag("{$dir}AJS.js"));
		scap_module_ui::load_code_in_head(scap_html::js_file_tag("{$dir}AJS_fx.js"));
		scap_module_ui::load_code_in_head(scap_html::js_file_tag("{$dir}gb_scripts.js"));
		scap_module_ui::load_css_file("{$dir}gb_styles.css");
	}
	
	/**
	 * 加载blueprint框架文件
	 * 
	 */
	static public function load_blueprint_file()
	{
		global $data_out;
		$data_out['flag_load_blueprint'] = true;
		$data_out['url_blueprint'] = SCAP_RELATIVE_PATH_LIBRARY."blueprint/";
	}
    
	/**
	 * 加载blueprint插件文件
	 * 
	 * @param string $name 插件名称
	 */
	static public function load_blueprint_plugin($name)
	{
		$dir = SCAP_RELATIVE_PATH_LIBRARY."blueprint/plugins/";
		scap_module_ui::load_css_file($dir.$name."/screen.css");
	}
    
	/**
	 * 设置新版界面的当前激活菜单
	 * 
	 * @param string $menu_text 激活菜单的文本
	 */
	protected function set_current_menu_item()
	{
	    global $data_out;
	    
	    if (empty($this->open_module_id))
	    {
	        $this->open_module_id = $this->current_module_id;
	    }
		
		$pos = \scap\module\g_tool\matrix::musearch($this->open_module_id, $data_out['nav_app_list']);
		
		if (!empty($pos))
		{
			$data_out['nav_app_list'][$pos[0]]['class'] = 'active';
		}
		
		$pos1 = \scap\module\g_tool\matrix::musearch($this->current_menu_text, $data_out['nav_app_menu'][$pos[0]]);
        
		if (!empty($pos1))
		{
			$data_out['nav_app_menu'][$pos[0]][$pos1[0]]['class'] = 'current';
		}
	}
    
	/**
	 * 输出html
	 * 
	 * @param string $title 显示标题
	 * @param string $tpl_name 模板名称
	 * @param array $data_out_from_app 模板赋值变量
	 * @param bool $show_nav 是否显示系统导航，默认为true
	 * @param bool $load_system_frame 是否加载系统布局框架，默认为true
	 * @param bool $load_default_lib 是否加载系统默认的head文件，比如css js和一些代码，默认为true
	 * @param bool $flag_debug 是否调试模板，默认false
	 * 
	 * @return null
	 */
	protected function output_html($title, $tpl_name, $data_out_from_app = array(), $show_nav = true, $load_system_frame = true, $load_default_lib = true, $flag_debug = false)
	{
		global $data_out;
		
		if (!is_array($data_out))
		{
			$data_out = array();
		}
		
		if (!is_array($data_out_from_app))
		{
			$data_out_from_app = array();
		}
		
		$data_out = array_merge($data_out, $data_out_from_app);
		
		if ($load_system_frame)
		{
			$this->set_current_method_title($title);// 设置显示标题
			scap_module_ui::load_scap_header($show_nav, $load_default_lib);// 加载系统header
			scap_module_ui::load_scap_footer();
			$this->set_current_menu_item();
		}
		$this->tpl->assign($data_out);
		
		if ($load_system_frame)
		{
			$this->show_scap_header($show_nav);
		}
		
		// 是否调试模板
		$this->tpl->debug_tpl = SCAP_PATH_LIBRARY."smarty/debug.tpl";
        $this->tpl->debugging = $flag_debug;
        
		$this->tpl->set_module_tpl($this->current_module_id);
		$this->tpl->display($tpl_name);
		
		if ($load_system_frame)
		{
			$this->show_scap_footer();
		}
	}
	
	/**
	 * 设置当前处理事件的信息
	 */
	public function set_current_event_info()
	{
		$this->current_event_name = $GLOBALS['scap']['event']['name'];
		$this->current_event_result = $GLOBALS['scap']['event']['result'];
	}
}
?>