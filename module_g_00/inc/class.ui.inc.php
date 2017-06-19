<?php
/**
 * description: 通用ui方法类
 * create time: 2008-12-21-下午04:07:24
 * @version $Id: class.ui.inc.php 100 2013-05-09 02:50:00Z liqt $
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
		scap_load_module_function('module_g_00', 'g');
	}
	
	/**
	 * 查看指定对象的日志
	 * 必须指定GET参数:search[object_id]
	 */
	public function view_object_log()
	{
		//--------加载相关文件[start]--------
		scap_load_module_define('module_g_00', 'log_type');// 加载通用日志类型定义
		//--------加载相关文件[end]--------
		
		//--------变量定义及声明[start]--------
		$data_in	= array();	// 表单输入相关数据
		$data_db	= array();	// 数据库相关数据
		$data_def	= array();	// 定义的一些信息
		
		$data_in['sys_info'] = array();// 保存系统信息
		$data_in['get'] = array();// 保存表单获取到的get信息
		//--------变量定义及声明[end]--------
		
		//--------GET参数处理[start]--------
		
	 	//--------GET参数处理[end]--------
	 	
		//--------查询参数处理[start]--------
		$data_in['search'] = array();// 处理后的查询参数数据
		$data_in['extra_vars'] = array();// 构造的额外需传递的参数数据
		$data_db['where'] = '';// 查询参数对应的查询条件语句(不含where关键字)
		
		// 处理传入的查询参数数组:$_REQUEST['search']
		if (!empty($_REQUEST['search']))
		{
			foreach($_REQUEST['search'] as $k => $v)
			{
				// 解码已编码的查询参数
				$data_in['search'][$k] = trim(urldecode($v));
			}
		}
		
		// 具体处理不同参数的传递
		if (!empty($data_in['search']['object_id']))// 对象id
		{
			$data_db['where'] .= " AND al_object_id = '{$data_in['search']['object_id']}'";
			$data_in['extra_vars']['search[object_id]'] = $data_in['search']['object_id'];
		}
		else
		{
			$data_db['where'] .= " AND FALSE";
			$data_in['extra_vars']['search[object_id]'] = $data_in['search']['object_id'];
		}
		//--------查询参数处理[end]----------
		
		//--------分页/步长处理[start]--------
		$data_def['steps_options'] = array(10, 20, 40, 80);// 设置分页步长选项
		$data_def['step_default'] = $data_def['steps_options'][1];// 默认步长
		$data_in['split_page'] = array();// 分页参数输入数据
		
		// 步长处理(步长参数名称为'steps')
		if (!is_null(scap_html::scap_index_steps_select_get()))// 获取步长下拉菜单选项值
		{
			$data_in['split_page']['steps'] = scap_html::scap_index_steps_select_get();
		}
		elseif(isset($_REQUEST['steps']))
		{
			$data_in['split_page']['steps'] = (int)$_REQUEST['steps'];
			// 如果获取步长数值不在步长选项之中,则将其置为默认步长
			if (!in_array($data_in['split_page']['steps'], $data_def['steps_options']))
			{
				$data_in['split_page']['steps'] = $data_def['step_default'];
			}
		}
		else
		{
			$data_in['split_page']['steps'] = $data_def['step_default'];
		}
		
		$data_in['extra_vars']['steps'] = $data_in['split_page']['steps'];
		
		// 分页位置处理(分页参数名称为'start')
		if (!is_null(scap_html::scap_index_pages_select_get()))
		{
			$data_in['split_page']['start'] = scap_html::scap_index_pages_select_get();
		}
		elseif(isset($_REQUEST['start']))
		{
			$data_in['split_page']['start'] = (int)$_REQUEST['start'];
		}
		else
		{
			$data_in['split_page']['start'] = 0;
		}
		
		$data_in['extra_vars']['start'] = $data_in['split_page']['start'];
		//--------分页/步长处理[end]--------
		
		//--------排序参数处理[start]--------
		$data_in['taxis'] = array();// 排序参数输入数据
		switch($_REQUEST['order'])// 定义合法的可排序列名
		{
			case 'al_type':
			case 'al_time':
			case 'al_operator_id':
			case 'al_client_ip':
			case 'al_user_agent':
				$data_in['taxis']['order'] = $_REQUEST['order'];
				break;
			default:
				$data_in['taxis']['order'] = 'al_time';// 默认排序的列名
		}
		$data_in['extra_vars']['order'] = $data_in['taxis']['order'];
		switch($_REQUEST['sort'])// 排序方式处理:升序/降序
		{
			case 'ASC':
			case 'DESC':
				$data_in['taxis']['sort'] = $_REQUEST['sort'];
				break;
			default:
				$data_in['taxis']['sort'] = 'DESC';// 默认排序方式
				break;
		}
		$data_in['extra_vars']['sort'] = $data_in['taxis']['sort'];
		//--------排序参数处理[end]--------
		
		// 将url参数数组进行编码处理
		$data_in['extra_vars'] = urlencodearray($data_in['extra_vars']);
		
		//--------避免在post数据后,浏览器点击"后退"或"前进"出现"页面无法显示错误"[start]--------
		if (!empty($_POST))
		{
			// 将post数据及时转化为get数据
			scap_redirect_url(array('module' => $this->current_module_id, 'class' => $this->current_class_name, 'method' => $this->current_method_name), $data_in['extra_vars']);
		}
		//--------避免在post数据后,浏览器点击"后退"或"前进"出现"页面无法显示错误"[end]--------
		
		// 构造查询所需参数
		$sql = <<<SQL
SELECT * FROM g_app_log 
WHERE (1=1 {$data_db['where']})
SQL;

		$data_db['query'] = array(
				'id' => '',
				'sql' => $sql,
				'order' => $data_in['taxis']['order'],
				'sort' => $data_in['taxis']['sort'],
				'start' => $data_in['split_page']['start'],
				'steps' => $data_in['split_page']['steps'],
			);
		// 执行查询,并返回查询集合到$data_db['content']
		$data_db['content'] = scap_entity::query($data_db['query']);
		//--------数据表查询操作[end]--------
		
		//--------影响界面输出的$data_in数据预处理[start]--------
		
		//--------影响界面输出的$data_in数据预处理[end]--------
		
		//--------模版赋值[start]--------
		if (count($data_db['content']) == 0)
		{
			$data_out['text_no_data'] = "没有相关数据。";
		}
		else
		{
			$i = 0;
			foreach($data_db['content'] as $k => $v)
			{
				$data_out['data_list'][$i] = $v;
				$data_out['data_list'][$i]['row_color'] = scap_html::scap_row_color($i);
				
				$data_out['data_list'][$i]['al_type'] = $GLOBALS['scap']['text']['module_g_00']['al_type'][$v['al_type']];
				
				// agent信息
				$temp = get_browser($v['al_user_agent'], true);
				$data_out['data_list'][$i]['al_user_agent'] = "{$temp['platform']}:{$temp['browser']}{$temp['version']}";
				
				// 派发人c_id
				$temp = scap_get_account_info($v['al_operator_id']);
				$data_out['data_list'][$i]['al_operator_id'] = "{$temp['a_c_display_name']}[{$temp['a_c_login_id']}]";
				
				$i ++;
			}
		}
		// [分页功能输出]
		$data_out['index_page_prev'] = scap_html::scap_index_page_prev($data_db['query']['start'], $data_in['extra_vars']);
		$data_out['index_page_next'] = scap_html::scap_index_page_next($data_db['query']['start'], $data_db['query']['pages'], $data_in['extra_vars']);
		$data_out['index_page_tip'] = scap_html::scap_index_page_tip($data_db['query']['start'], $data_db['query']['steps'], $data_db['query']['pages'], $data_db['query']['total']);
		$data_out['index_pages_select'] = scap_html::scap_index_pages_select($data_db['query']['start'], $data_db['query']['pages']);
		$data_out['index_steps_select'] = scap_html::scap_index_steps_select($data_db['query']['steps'], $data_def['steps_options']);
		
		// [索引头部信息输出]
		$data_out['head_time'] = scap_html::scap_index_order($data_in['taxis']['sort'], 'al_time', $data_in['taxis']['order'], "操作时间", $data_in['extra_vars']);
		$data_out['head_type'] = scap_html::scap_index_order($data_in['taxis']['sort'], 'al_type', $data_in['taxis']['order'], "操作", $data_in['extra_vars']);
		$data_out['head_operator_id'] = scap_html::scap_index_order($data_in['taxis']['sort'], 'al_operator_id', $data_in['taxis']['order'], "操作者", $data_in['extra_vars']);
		$data_out['head_client_ip'] = scap_html::scap_index_order($data_in['taxis']['sort'], 'al_client_ip', $data_in['taxis']['order'], "操作来源", $data_in['extra_vars']);
		$data_out['head_user_agent'] = scap_html::scap_index_order($data_in['taxis']['sort'], 'al_user_agent', $data_in['taxis']['order'], "客户端", $data_in['extra_vars']);
		$data_out['head_comment'] = "备注";
		
		//--------模版赋值[end]----------
		
		//--------构造界面输出[start]--------
		$this->output_html("日志查询", 'view.object_log.tpl', $data_out, false);
		
		//--------构造界面输出[end]----------
	}
	
	/**
	 * 显示系统信息(供其他界面ajax调用)
	 */
	public function view_system_info()
	{
		//--------变量定义及声明[start]--------
		$data_in	= array();	// 表单输入相关数据
		$data_db	= array();	// 数据库相关数据
		$data_def	= array();	// 相关定义数据
		$data_flag	= array();	// 相关标志数据
		
		$data_in['get'] = array();// 保存表单获取到的get信息
		$data_in['sys_info'] = array();// 保存系统信息
		$data_in['post'] = array();// 保存表单post信息
		$data_in['content'] = array();// 保存主表单数据
		
		$data_flag['event_result'] = NULL;// 事件执行结果
		$data_def['title'] = '';// 当前界面标题设置
		$data_def['text_menu'] = '';// 当前对应的模块菜单名称
		$data_def['text_act'] = '';// 当前操作描述
		//--------变量定义及声明[end]--------
		
		//--------GET参数处理[start]--------
		//--------GET参数处理[end]--------
		
		//--------影响界面输出的$data_in数据预处理[start]--------
		// 系统反馈信息处理
		$data_in['scap_sys_info'] = scap_get_sys_info();
		
		if (!empty($data_in['scap_sys_info']))
		{
			$data_in['sys_info'] +=  $data_in['scap_sys_info'];// 获取系统反馈信息
			scap_clear_sys_info();// 清空系统反馈信息
		}
		//--------影响界面输出的$data_in数据预处理[end]--------
		
		//--------模版赋值[start]--------
		// 系统信息输出
		foreach($data_in['sys_info'] as $k => $v)
		{
			$data_out['sys_info'][$k]['icon'] = scap_html::image(array('src' => scap_html::scap_get_icon_tip_url($v['type'])));
			$data_out['sys_info'][$k]['text'] = $v['text'];
		}
		//--------模版赋值[end]----------
		
		//--------构造界面输出[start]--------
		$this->output_html($data_def['title'], 'view.system_info.tpl', $data_out, false, false);
		//--------构造界面输出[end]----------
		
	}
	
	/**
	 * 显示反馈信息
	 * 
	 * @author Liqt
	 */
	public function feedback_info()
	{
		//--------变量定义及声明[start]--------
		$data_in	= array();	// 表单输入相关数据
		$data_db	= array();	// 数据库相关数据
		$data_def	= array();	// 相关定义数据
		$data_flag	= array();	// 相关标志数据
		
		$data_in['get'] = array();// 保存表单获取到的get信息
		$data_in['post'] = array();// 保存表单post信息
		$data_in['content'] = array();// 保存主表单数据
		
		//--------变量定义及声明[end]--------
		
		//--------GET参数处理[start]--------
		$data_in['get']['nonav'] = intval($_GET['nonav']);
		$data_in['get']['info'] = $_GET['info'];
		//--------GET参数处理[end]--------
		
		//--------影响界面输出的$data_in数据预处理[start]--------
		
		//--------影响界面输出的$data_in数据预处理[end]--------
		
		//--------模版赋值[start]--------
		
		//--------模版赋值[end]----------
		
		//--------构造界面输出[start]--------
		$data_out['feedback_info'] = $data_in['get']['info'];
		
		$this->load_js_file(scap_get_js_url('module_g_00', 'load_system_info.js'));
		$this->output_html("系统反馈信息", 'feedback_info.tpl', $data_out, false);
		//--------构造界面输出[end]----------
		
	}
	
	/**
	 * 查看指定对象的关联文件列表
	 * 
	 * @tutorial 需要在调用页面加载：
	 * $this->load_jquery_plugin(array('cluetip/jquery.cluetip.js', 'cluetip/jquery.cluetip.css'));
	 * 
	 * 同时在页面js调用完成后执行：
	 * $('a.file_detail').cluetip({local: true,showTitle: false,width: 250,arrows: true});// 支持详细信息的提示标签
	 * 
	 * @author Liqt
	 */
	public function view_files()
	{
		scap_load_module_class('module_g_00', 'binary_data');
		
		//--------变量定义及声明[start]--------
		$data_in	= array();	// 表单输入相关数据
		$data_db	= array();	// 数据库相关数据
		$data_def	= array();	// 相关定义数据
		$data_flag	= array();	// 相关标志数据
		
		$data_in['get'] = array();// 保存表单获取到的get信息
		$data_in['post'] = array();// 保存表单post信息
		$data_in['content'] = array();// 保存主表单数据
		
		$data_def['title'] = '文件列表';// 当前界面标题设置
		//--------变量定义及声明[end]--------
		
		//--------GET参数处理[start]--------
		$data_in['get']['object_id'] = $_GET['object_id']; // 对象ID
		$data_in['get']['encoding'] = $_GET['encoding'];// 输出编码格式：gb2312/utf8
		$data_in['get']['nonav'] = true;
		//--------GET参数处理[end]--------
		
		//--------数据表查询操作[start]--------
		$data_db['content']['file_list'] = binary_data::get_object_binary_list($data_in['get']['object_id']);
		//--------数据表查询操作[end]--------
		
		//--------影响界面输出的$data_in数据预处理[start]--------
		
		//--------影响界面输出的$data_in数据预处理[end]--------
		
		//--------html元素只读/必填/显示等逻辑设定[start]--------
		
		//--------html元素只读/必填/显示等逻辑设定[end]--------
		
		//--------模版赋值[start]--------
		$i = 0;
		if(!empty($data_db['content']['file_list']))
		{
			arsort($data_db['content']['file_list']);// 逆序排列
			
			foreach($data_db['content']['file_list'] as $k => $v)
			{
				$data_out['file_list'][$i]['sn'] = $i+1;
				$data_out['file_list'][$i]['row_color'] = scap_html::scap_row_color($i);
				$data_out['file_list'][$i]['obl_sn'] = $v['obl_sn'];
				$data_out['file_list'][$i]['obl_name'] = $v['obl_name']." ({$v['bd_file_postfix']})";
				$data_out['file_list'][$i]['obl_category'] = $v['obl_category'];
				
				if (intval($v['bd_file_size']/1024) < 1)
				{
					$file_size = intval($v['bd_file_size']).' Byte';
				}
				else
				{
					$file_size = intval($v['bd_file_size']/1024).' KB';
				}
				
				$upload_account = scap_html::scap_show_account($v['bd_upload_id']);
				
				$detail = <<<DETAIL
<div id="file_{$v['obl_sn']}" style="width:250px;display:none;">
	<table style="font-size:12px;width:250px;">
		<tr>
			<th>文件名称:</th><td>{$v['obl_name']}.{$v['bd_file_postfix']}</td>
		</tr>
		<tr>
			<th width="30%">文件大小:</th><td width="70%">{$file_size}</td>
		</tr>
		<tr>
			<th>文件类型:</th><td>{$v['bd_file_type']}</td>
		</tr>
		<tr>
			<th>更新时间:</th><td>{$v['bd_upload_time']}</td>
		</tr>
		<tr>
			<th>更新账号:</th><td>{$upload_account}</td>
		</tr>
		<tr>
			<th>文件备注:</th><td>{$v['obl_comment']}</td>
		</tr>
	</table>
</div>
DETAIL;
				$data_out['file_list'][$i]['info'] .= $upload_account;
				$data_out['file_list'][$i]['info'] .= " ".$v['bd_upload_time']." [";
				$data_out['file_list'][$i]['info'] .= scap_html::anchor(array('class' => 'file_detail', 'href' => "#file_{$v['obl_sn']}", 'rel' => "#file_{$v['obl_sn']}" ), '详细')."]";
				$data_out['file_list'][$i]['info'] .= $detail;
				
				$i++;
			}
		}
		
		//--------模版赋值[end]----------
		
		//--------构造界面输出[start]--------
		$this->load_jquery_plugin(array('cluetip/jquery.cluetip.js', 'cluetip/jquery.cluetip.css'));
		$this->output_html($data_def['title'], 'view.files.tpl', $data_out, false, false);
		//--------构造界面输出[end]----------
	}
	
	/**
	 * 编辑对象关联的文件
	 */
	public function edit_file()
	{
		//--------变量定义及声明[start]--------
		$data_in	= array();	// 表单输入相关数据
		$data_db	= array();	// 数据库相关数据
		$data_def	= array();	// 相关定义数据
		$data_flag	= array();	// 相关标志数据
		
		$data_in['get'] = array();// 保存表单获取到的get信息
		$data_in['post'] = array();// 保存表单post信息
		$data_in['content'] = array();// 保存主表单数据
		
		$data_def['title'] = '编辑文件';// 当前界面标题设置
		//--------变量定义及声明[end]--------
		
		//--------GET参数处理[start]--------
		$data_in['get']['nonav'] = true; // 是否显示系统导航栏,空为显示,否则为不显示
		$data_in['get']['obl_sn'] = $_GET['obl_sn'];
		$data_in['get']['object_id'] = $_GET['object_id'];
		$data_in['get']['use_tab'] = $_GET['use_tab'];
		
		//--------GET参数处理[end]--------
		if(empty($data_in['get']['obl_sn']))
		{
			$data_flag['is_create'] = true;
		}
		else
		{
			$data_flag['is_edit'] = true;
		}
		//--------消息/事件处理[start]--------
		switch ($this->current_event_name)
		{
			case 'file_add':
			case 'file_edit':
			case 'file_remove':
				if ($this->current_event_result === false)
				{
					$data_in['post'] = trimarray($_POST['content']);
				}
				elseif ($this->current_event_result === true)
				{
				    if($data_in['get']['use_tab'])
				    {
				        echo <<<RETURN
<script type="text/javascript">
	parent.parent.GB_hide();// 隐藏当前窗口
	parent.parent.load_system_info();// 加载系统信息
	parent.parent.reload_ajaxtab(); //重新
</script>
RETURN;
				    }
				    else
				    {
					    echo <<<RETURN
<script type="text/javascript">
	parent.parent.GB_hide();// 隐藏当前窗口
	parent.parent.load_system_info();// 加载系统信息
	parent.parent.load_file_list();
</script>
RETURN;
				    }
					exit;// 退出
				}
				break;
		}
		//--------消息/事件处理[end]--------
		
		//--------数据表查询操作[start]--------
		
		if ($data_flag['is_edit'])
		{
			$temp = binary_data::get_object_binary_list($data_in['get']['object_id'], "AND obl_sn={$data_in['get']['obl_sn']}");
			$data_in['content'] = $temp[$data_in['get']['obl_sn']];
		}
		
		//--------数据表查询操作[end]--------
		
		//--------影响界面输出的$data_in数据预处理[start]--------
		if (!empty($data_in['post']))// 处理post上来的数据与其它数据来源的合并及相关转化
		{
			$data_in['content'] = array_merge($data_in['content'], $data_in['post']);
		}
		
		//--------影响界面输出的$data_in数据预处理[end]--------
		
		//--------html元素只读/必填/显示等逻辑设定[start]--------
		$data_flag['obl_name']['readonly'] = false;
		$data_flag['obl_name']['required'] = true;
		
		$data_flag['obl_category']['readonly'] = false;
		$data_flag['obl_category']['required'] = false;
		
		$data_flag['obl_comment']['readonly'] = false;
		$data_flag['obl_comment']['show'] = true;
		
		$data_flag['btn_remove']['show'] = $data_flag['is_edit'];
		//--------html元素只读/必填/显示等逻辑设定[end]--------
		
		//--------模版赋值[start]--------
		$data_out['obl_name'] = scap_html::input_text( 	array('name' => 'content[obl_name]', 'id'=>'obl_name', 'value' => $data_in['content']['obl_name'],'size' => 50, 'maxlength' => 256),
															$data_flag['obl_name']['readonly'],
															false,
															true,
															$data_flag['obl_name']['required']
														);
		
		$data_out['upload_file']	= scap_html::input_file(array('name' => 'upload_file', 'id' => 'upload_file', 'size' => '50'));
		
		$data_out['obl_category'] = scap_html::input_text( 	array('name' => 'content[obl_category]', 'id'=>'obl_category', 'value' => $data_in['content']['obl_category'],'size' => 50, 'maxlength' => 256),
																$data_flag['obl_category']['readonly'],
																false,
																true,
																$data_flag['obl_category']['required']
														);
														
		$data_out['obl_comment'] = scap_html::textarea(	array(	'name' => "content[obl_comment]", 'rows' => 3), 
															$data_in['content']['obl_comment'],
															$data_flag['obl_comment']['readonly'],
															false,
															$data_flag['obl_comment']['show']
													);
														
		$data_out['btn_save'] = scap_html::input_submit(array('name' => 'button[btn_save]', 'value' => '保存'));
		$data_out['btn_remove'] = scap_html::input_submit(array('name' => 'button[btn_remove]', 'value' => '删除', 'onclick' => "return confirm('确认删除该数据么,此过程将不可恢复!');"), false, $data_flag['btn_remove']['show']);
		$data_out['btn_cancel'] = scap_html::input_button(array('name' => 'button[btn_cancel]', 'value' => '取消', 'onclick' => 'parent.parent.GB_hide();'));
		
		//--------模版赋值[end]----------
		
		//--------构造界面输出[start]--------
		$this->load_js_file(scap_get_js_url('module_g_00', 'load_system_info.js'));
		
		$this->output_html($data_def['title'], 'edit.file.tpl', $data_out, !$data_in['get']['nonav']);
		//--------构造界面输出[end]----------
	}
	
	/**
	 * 下载对象关联的文件
	 */
	public function download_file()
	{
		scap_load_module_class('module_g_00', 'binary_data');
		
		$data_in['get']['obl_sn'] = $_GET['obl_sn'];
		$data_in['get']['object_id'] = $_GET['object_id'];
		
		try
		{
			$data_db = binary_data::get_object_binary_list($data_in['get']['object_id'], "AND obl_sn={$data_in['get']['obl_sn']}");
			$data_out = $data_db[$data_in['get']['obl_sn']];
			
			$bd = new binary_data($data_out['bd_id']);
			
			$data_out['file_data'] = $bd->get_binary();
			
			// 文件名称UTF8转GB2312，以使可恶的IE可以正常下载中文文件名的文件
			if( strpos($_SERVER['HTTP_USER_AGENT'],'MSIE') )
			{
			    $data_out['obl_name'] = mb_convert_encoding($data_out['obl_name'], 'gb2312', 'utf-8');
			}
		}catch(Exception $e)
		{
			exit($e->getMessage());
		}
		
		header("Content-type:{$data_out['bd_file_type']}");
		header("Cache-control: cache, must-revalidate"); // cache产生时间不一致，用了cache-control就可以直接打开文件了
		header("Content-Disposition: attachment;filename=\"{$data_out['obl_name']}.{$data_out['bd_file_postfix']}\"");
		header("Pragma: public");// 解决SSL在IE下存在无法正常下载的问题
		echo $data_out['file_data'];
	}

}
?>