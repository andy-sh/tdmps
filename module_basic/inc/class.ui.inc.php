<?php
/**
 * 系统基础UI方法类
 * create time: 2006-11-4 23:43:58
 * @version $Id: class.ui.inc.php 164 2014-02-17 04:40:21Z liqt $
 * @author LiQintao
 */

class ui extends scap_module_ui
{
	/**
	 *  构造函数
	 *  @access private
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 系统注销
	 */
	public function logout()
	{
		//--------消息/事件处理[start]--------
        switch ($this->current_event_name)
        {
            case 'logout':
                if ($this->current_event_result === false)
                {
                    
                }
                elseif ($this->current_event_result === true)
                {
                    scap_redirect_url(array('module' => 'module_basic', 'class' => 'ui', 'method' => 'login'));
                }
                break;
        }
        //--------消息/事件处理[end]--------
	}
	
	/**
	 * 系统登录界面
	 */
	public function login()
	{
        //--------变量定义及声明[start]--------
		$data_in	= array();	// 表单输入相关数据
		$data_db	= array();	// 数据库相关数据
		$data_def	= array();	// 相关定义数据
		$data_flag	= array();	// 相关标志数据
		
		$data_in['get'] = array();// 保存表单获取到的get信息
		$data_in['post'] = array();// 保存表单post信息
		$data_in['content'] = array();// 保存主表单数据
		$data_in['sys_info'] = array();// 保存系统信息
		$data_def['text_menu'] = '';// 当前模块菜单名称
		
		$data_def['title'] = TEXT_LOGIN_TITLE;// 当前界面标题设置
		//--------变量定义及声明[end]--------		
		
		//--------GET参数处理[start]--------
		$data_in['get']['account'] = trim($_GET['account']); // 登录用户id
		$data_in['get']['pwd'] = $_GET['pwd']; // 登录密码
		$data_in['get']['r'] = $_GET['r'];// 登录后的转向链接
		//--------GET参数处理[end]--------
		
		//--------操作类型分类处理[start]--------
		//--------操作类型分类处理[end]--------
		
		//--------消息/事件处理[start]--------
		switch ($this->current_event_name)
		{
			case 'login':
				if ($this->current_event_result === false)
				{
					
				}
				elseif ($this->current_event_result === true)
				{
					$data_def['default_url'] = scap_get_config_value('module_manage', 'default_url');
					
				    if (!empty($data_in['get']['r']))
					{
						scap_redirect_url($_GET['r']);
					}
					elseif (!empty($data_def['default_url']))
					{
						$act_para = array();
						list($act_para['module'], $act_para['class'], $act_para['method']) = explode('.', $data_def['default_url']);
						scap_redirect_url($act_para);
					}
					else
					{
						scap_redirect_url(array('module' => 'module_basic', 'class' => 'ui', 'method' => 'welcome'));
					}
				}
				break;
		}
		//--------消息/事件处理[end]--------
		
		//--------数据表查询操作[start]--------
		//--------数据表查询操作[end]--------
		
		//--------影响界面输出的$data_in数据预处理[start]--------
	    // 系统反馈信息处理
		$data_in['scap_sys_info'] = scap_get_sys_info();
		
		if (!empty($data_in['scap_sys_info']))
		{
			$data_in['sys_info'] += $data_in['scap_sys_info'];// 获取系统反馈信息
			scap_clear_sys_info();// 清空系统反馈信息
		}
		//--------影响界面输出的$data_in数据预处理[end]--------		
		
		//--------html元素只读/必填/显示等逻辑设定[start]--------
		
		//--------html元素只读/必填/显示等逻辑设定[end]--------
		
		//--------模版赋值[start]--------
		$data_out['text_login_title'] = TEXT_LOGIN_TITLE;
		$data_out['input_username'] = scap_html::input_text(array('id' => 'input_username', 'name' => 'content[account]', 'size' => '15', 'value' => $data_in['get']['account'], 'maxlength' => 50));
		$data_out['input_password'] = scap_html::input_password(array('id' => 'input_password', 'name' => 'content[passwd]', 'size' => '15', 'maxlength' => 40));
		
		$data_out['image_touchview'] = scap_html::image(array('src' => scap_get_image_url('module_basic', 'touchview.png')));
		$data_out['image_scap'] = scap_html::image(array('src' => scap_get_image_url('module_basic', 'ball1.gif'), 'align' => 'absmiddle', 'width' => '27', 'height' => '27'));
		$data_out['image_login'] = scap_html::image(array('src' => scap_get_image_url('module_basic', 'person-1.gif'), 'align' => 'absmiddle', 'width' => '27', 'height' => '27'));
		$data_out['image_key'] = scap_html::image(array('src' => scap_get_image_url('module_basic', 'keychain1.gif')));
		$data_out['btn_login'] = scap_html::input_submit(array('id' => 'input_submit', 'name' => 'button[login]', 'value' => "登录"));
		
	    // 系统信息输出
		foreach($data_in['sys_info'] as $k => $v)
		{
			$data_out['sys_info'][$k]['icon'] = scap_html::image(array('src' => scap_html::scap_get_icon_tip_url($v['type'])));
			$data_out['sys_info'][$k]['text'] = $v['text'];
		}

        $data_out['flag_no_alert'] = (count($data_out['sys_info']) == 0);
		//--------模版赋值[end]----------
		
		//--------构造界面输出[start]--------
		$this->load_css_file(scap_get_css_url('module_basic', 'touchview.login.css'));
        $this->load_blueprint_file();		
		$this->output_html($data_def['title'], 'touchview.login.tpl', $data_out, false);
		//--------构造界面输出[end]----------
	}
	
	/**
	 * 系统默认欢迎界面
	 */
	public function welcome()
	{
		//--------变量定义及声明[start]--------
		$data_def	= array();	// 定义的一些信息
		// 当前界面标题设置
		$data_def['title'] = '公共信息';
		//--------变量定义及声明[end]--------
		
		//--------GET参数处理[start]--------
		$data_in['get']['nonav'] = intval($_GET['nonav']); // 是否显示系统导航栏,空为显示,否则为不显示
		//--------GET参数处理[end]--------
		
		//--------模版赋值[start]--------
		$data_out['text_welcome'] = scap_get_config_value('module_manage', 'welcome');
		//--------模版赋值[end]----------
		
		//--------构造界面输出[start]--------
		$this->output_html($data_def['title'], 'welcome.tpl', $data_out, !$data_in['get']['nonav']);
		//--------构造界面输出[end]----------
	}
	
	/**
	 * 非授权/无法访问提示界面
	 */
	public function no_access()
	{
	    //--------变量定义及声明[start]--------
		$data_in	= array();	// 输入到表单的数据
		$data_db	= array();	// 数据库中读取到的数据
		$data_def	= array();	// 定义的一些信息
		$data_in['get'] = array(); // 保存表单获取到的get信息

		$data_def['title'] = '访问受限';// 当前界面标题设置
		//--------变量定义及声明[end]--------
		
		//--------GET参数处理[start]--------
		$data_in['get']['msg'] = intval($_GET['msg']); // 操作类型
		//--------GET参数处理[end]--------
		
		//--------影响界面输出的$data_in数据预处理[start]--------
	    switch($data_in['get']['msg'])
		{
			case SCAP_MSG_ACCESS_NO_PUBLIC_METHOD:
				$data_in['text_no_access'] = '试图访问一个未经注册的系统方法';
				break;
			case SCAP_MSG_ACCESS_UNREGISTER_MODULE:
				$data_in['text_no_access'] = '试图访问一个未经注册的系统模块';
				break;
			case SCAP_MSG_ACCESS_NO_NORMAL_MODULE:
				$data_in['text_no_access'] = '试图访问一个未启用的系统模块';
				break;
			case SCAP_MSG_ACCESS_NO_EXIST_CLASS:
				$data_in['text_no_access'] = '试图访问一个不存在的系统模块类';
				break;
			case SCAP_MSG_ACCESS_ILLEGAL_CLASS:
				$data_in['text_no_access'] = '试图访问一个不合法的系统模块类';
				break;
			case SCAP_MSG_ACCESS_NO_ACCESS_METHOD:
				$data_in['text_no_access'] = '试图访问一个未授权的系统方法';
				break;
			case SCAP_MSG_ACCESS_ILLEGAL:
				$data_in['text_no_access'] = '不合法的访问';
				break;
			case SCAP_MSG_DATA_NO_EXIST:
				$data_in['text_no_access'] = '所访问的数据不存在';
				break;
			default:
				$data_in['text_no_access'] = '未知';
		}
		//--------影响界面输出的$data_in数据预处理[end]--------
		
		//--------模版赋值[start]--------
		$data_out['text_no_access'] = scap_html::image(array('src' => scap_get_image_url('module_basic', 'no-access-32-32.gif')));
		$data_out['text_no_access'] .= sprintf("您当前的访问受到系统限制！原因是【%s】。", $data_in['text_no_access']); 
		//--------模版赋值[end]----------
		
		//--------构造界面输出[start]--------
		$this->output_html($data_def['title'], 'no-access.tpl', $data_out, !$data_in['get']['nonav']);
		//--------构造界面输出[end]----------
	}
	
	/**
	 * 修改当前帐户口令
	 */
	public function change_password()
	{
         //--------变量定义及声明[start]--------
        $data_in    = array();  // 表单输入相关数据
        $data_db    = array();  // 数据库相关数据
        $data_def   = array();  // 相关定义数据
        $data_flag  = array();  // 相关标志数据
        
        $data_in['get'] = array();// 保存表单获取到的get信息
        $data_in['post'] = array();// 保存表单post信息
        $data_in['content'] = array();// 保存主表单数据
        
        $data_def['title'] = '修改口令';// 当前界面标题设置
        $data_def['text_act'] = '';// 当前操作描述
        //--------变量定义及声明[end]--------
        
        //--------GET参数处理[start]--------
        $data_in['get']['nonav'] = intval($_GET['nonav']); // 是否显示系统导航栏,空为显示,否则为不显示
        //--------GET参数处理[end]--------
        
        //--------操作类型分类处理[start]--------
        //--------操作类型分类处理[end]--------
        
        //--------消息/事件处理[start]--------
        switch ($this->current_event_name)
        {
            case 'change_password':
                if ($this->current_event_result === false)
                {
                    $data_in['post'] = trimarray($_POST['content']);
                }
                elseif ($this->current_event_result === true)
                {
                   scap_redirect_url(array('module' => $this->current_module_id, 'class' => $this->current_class_name, 'method' => $this->current_method_name), array('nonav' => $data_in['get']['nonav']));
                }
                break;
        }
        //--------消息/事件处理[end]--------
        
        //--------数据表查询操作[start]--------
        
        //--------数据表查询操作[end]--------
        
        //--------影响界面输出的$data_in数据预处理[start]--------
        if (!empty($data_db['content']))
        {
            $data_in['content'] = array_merge($data_in['content'], $data_db['content']);
        }
        
        if (!empty($data_in['post']))// 处理post上来的数据与其它数据来源的合并及相关转化
        {
            $data_in['content'] = array_merge($data_in['content'], $data_in['post']);
        }
        //--------影响界面输出的$data_in数据预处理[end]--------
        
        //--------html元素只读/必填/显示等逻辑设定[start]--------
        
        //--------html元素只读/必填/显示等逻辑设定[end]--------
        
        //--------模版赋值[start]--------
        $data_out['pw_now'] = scap_html::input_password(array('name' => 'content[pw_now]', 'size' => 15, 'maxlength' => 40));
		$data_out['pw_new'] = scap_html::input_password(array('name' => 'content[pw_new]', 'size' => 15, 'maxlength' => 40));
		$data_out['pw_new_confirm'] = scap_html::input_password(array('name' => 'content[pw_new_confirm]', 'size' => 15, 'maxlength' => 40));
		
		$data_out['btn_save'] = scap_html::input_submit(array('name' => 'button[btn_save]', 'value' => '保存'));
        //--------模版赋值[end]----------
        
        //--------构造界面输出[start]--------
        $this->load_js_file(scap_get_js_url('module_g_00', 'load_system_info.js'));
        
        $this->set_current_menu_text($data_def['text_menu']);
        $this->output_html($data_def['title'], 'change_password.tpl', $data_out, !$data_in['get']['nonav']);
        //--------构造界面输出[end]---------- 
	}
}
?>