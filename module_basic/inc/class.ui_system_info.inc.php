<?php
/**
 * 系统信息UI文件
 * create time: 2011-6-21 上午10:28:37
 * @version $Id: class.ui_system_info.inc.php 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao
 */

class ui_system_info extends scap_module_ui
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
	 * 非授权/无法访问提示界面
	 */
	public function no_access($msg = 0)
	{
	    //--------变量定义及声明[start]--------
		$data_in	= array();	// 输入到表单的数据
		$data_db	= array();	// 数据库中读取到的数据
		$data_def	= array();	// 定义的一些信息
		$data_in['get'] = array(); // 保存表单获取到的get信息

		$data_def['title'] = '访问受限';// 当前界面标题设置
		//--------变量定义及声明[end]--------
		
		//--------GET参数处理[start]--------
		if (isset($_GET['msg']))
		{
		    $data_in['get']['msg'] = intval($_GET['msg']); // 操作类型
		}
		else
		{
		    $data_in['get']['msg'] = $msg;
		}
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
		if (scap_check_excute_from_cli())// cli调用
		{
		    echo "访问受限：{$data_in['text_no_access']}\n";
		}
		else
		{
		    $this->output_html($data_def['title'], 'no-access.tpl', $data_out, false, true, false);
		}
		//--------构造界面输出[end]----------
	}
	
}
?>