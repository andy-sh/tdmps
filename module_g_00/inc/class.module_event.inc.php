<?php
/**
 * description: g00模块事件类
 * create time: 2009-4-7-下午05:31:26
 * @version $Id: class.module_event.inc.php 100 2013-05-09 02:50:00Z liqt $
 * @author LiQintao
 */

class module_event extends scap_module_event
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function process_ui_event()
	{
		switch($this->current_method_name)
		{
			case 'edit_file':
				if ($_POST['button']['btn_save'] && empty($_GET['obl_sn']))
				{
					$this->excute_event('file_add');
				}
				elseif ($_POST['button']['btn_save'] && !empty($_GET['obl_sn']))
				{
					$this->excute_event('file_edit');
				}
				elseif ($_POST['button']['btn_remove'])
				{
					$this->excute_event('file_remove');
				}
				break;
			case 'edit_personal_log':
				if ($_POST['button']['btn_save'])
				{
					$this->excute_event('personal_log_save');
				}
				elseif ($_POST['button']['btn_remove'])
				{
					$this->excute_event('personal_log_remove');
				}
				break;
			case 'edit_comment_item':
				if ($_POST['button']['btn_save'])
				{
					$this->excute_event('comment_item_save');
				}
				elseif ($_POST['button']['btn_remove'])
				{
					$this->excute_event('comment_item_remove');
				}
				break;
		}
	}
	
	/**
	 * 添加对象文件
	 * 
	 * @author Liqt
	 * 
	 * @return bool
	 */
	protected function event_file_add()
	{
		//--------变量定义及声明[start]--------
		$data_save = array();
		$data_flag = array();
		$flag_save = true;

		$data_in['get'] = array();// 保存表单获取到的get信息
		$data_in['post'] = array();// 保存表单post信息
		
		$data_save['content'] = array();// 主表单内容保存数据
		$data_flag['type_log'] = NULL;// 当前操作日志类型
		
		$result = false;// 事件返回结果
		//--------变量定义及声明[end]--------
		
		//--------获取表单上传数据[start]--------
		$data_in['post'] = trimarray($_POST['content']);
		$data_in['get']['object_id'] = $_GET['object_id'];
		$data_in['get']['entity_id'] = $_GET['entity_id'];
		
		//--------获取表单上传数据[end]--------
		
		//--------输入合法性检查[start]--------
		if(!verify_content_legal($data_in['post']['obl_name'], VCL_TYPE_NOT_EMPTY))
		{
			scap_insert_sys_info('warn', '【名称】请填写。');
			$flag_save = false;
		}
		
		if (!$flag_save)// 合法性检查为失败则返回false
		{
			return $result; // 【注意】返回
		}
		//--------输入合法性检查[end]--------
		
		//--------数据存储前的预处理[start]--------
		$data_save['content']['obl_object_id'] = $data_in['get']['object_id'];
		$data_save['content']['obl_entity_id'] = $data_save['content']['bd_entity_id'] = $data_in['get']['entity_id'];
		$data_save['content']['obl_name'] = $data_in['post']['obl_name'];
		$data_save['content']['obl_category'] = $data_in['post']['obl_category'];
		$data_save['content']['obl_comment'] = $data_in['post']['obl_comment'];
		
		//--------数据存储前的预处理[end]--------
		
		//--------数据库事物处理[start]--------
		try
		{
			$bd = binary_data::upload($_FILES['upload_file'], $data_save['content'], G_TYPE_BINARY_STORAGE_FS);
			
			$data_save['content']['bd_id'] = $bd->get_current_bd_id();
			
			binary_data::insert_object_binary_link($data_in['get']['object_id'], $data_save['content']);
			
			$log = new g_app_log($data_in['get']['object_id'], $data_in['get']['entity_id']);
			
			$log->update_log(G_TYPE_AL_EDIT, "上传文件。");
		}catch(Exception $e)
		{
			scap_insert_sys_info('error', $e->getMessage());
			return $result;// 【注意】返回
		}
		
		$str_info = sprintf("上传文件已成功。");
		scap_insert_sys_info('tip', $str_info);
		
		$result = true;// 执行成功标志
		return $result; // 【注意】返回
	}
	
	/**
	 * 编辑对象文件
	 * 
	 * @author Liqt
	 * 
	 * @return bool
	 */
	protected function event_file_edit()
	{
		//--------变量定义及声明[start]--------
		$data_save = array();
		$data_flag = array();
		$flag_save = true;

		$data_in['get'] = array();// 保存表单获取到的get信息
		$data_in['post'] = array();// 保存表单post信息
		
		$data_save['content'] = array();// 主表单内容保存数据
		
		$result = false;// 事件返回结果
		//--------变量定义及声明[end]--------
		
		//--------获取表单上传数据[start]--------
		$data_in['post'] = trimarray($_POST['content']);
		$data_in['get']['object_id'] = $_GET['object_id'];
		$data_in['get']['obl_sn'] = $_GET['obl_sn'];
		
		//--------获取表单上传数据[end]--------
		
		//--------输入合法性检查[start]--------
		if(!verify_content_legal($data_in['post']['obl_name'], VCL_TYPE_NOT_EMPTY))
		{
			scap_insert_sys_info('warn', '【名称】请填写。');
			$flag_save = false;
		}
		
		if (!$flag_save)// 合法性检查为失败则返回false
		{
			return $result; // 【注意】返回
		}
		//--------输入合法性检查[end]--------
		
		//--------数据存储前的预处理[start]--------
		$temp = binary_data::get_object_binary_list($data_in['get']['object_id'], "AND obl_sn={$data_in['get']['obl_sn']}");
		$data_db['content'] = $temp[$data_in['get']['obl_sn']];
		
		if (!empty($_FILES['upload_file']['size']))
		{
			$data_save['content'] = $data_db['content'];
		}
		
		$data_save['content']['obl_object_id'] = $data_in['get']['object_id'];
		$data_save['content']['obl_name'] = $data_in['post']['obl_name'];
		$data_save['content']['obl_category'] = $data_in['post']['obl_category'];
		$data_save['content']['obl_comment'] = $data_in['post']['obl_comment'];
		//--------数据存储前的预处理[end]--------
		
		//--------数据库事物处理[start]--------
		try
		{
			if (!empty($_FILES['upload_file']['size']))
			{
				$bd = new binary_data($data_save['content']['bd_id']);
				$bd->update_index($_FILES['upload_file']);
				$bd->update_binary($_FILES['upload_file']);
			}
			
			binary_data::insert_object_binary_link($data_in['get']['object_id'], $data_save['content'], $data_in['get']['obl_sn']);
			
			$log = new g_app_log($data_in['get']['object_id'], $data_db['content']['obl_entity_id']);
			
			$log->update_log(G_TYPE_AL_EDIT, "更新上传文件。");
		}catch(Exception $e)
		{
			scap_insert_sys_info('error', $e->getMessage());
			return $result;// 【注意】返回
		}
		
		$str_info = sprintf("更新上传文件已成功。");
		scap_insert_sys_info('tip', $str_info);
		
		$result = true;// 执行成功标志
		return $result; // 【注意】返回
	}
	
	/**
	 * 删除指定关联文件
	 * 
	 * @author Liqt
	 * 
	 * @return bool
	 */
	protected function event_file_remove()
	{
		//--------变量定义及声明[start]--------
		$data_save = array();
		$data_flag = array();
		$flag_save = true;

		$data_in['get'] = array();// 保存表单获取到的get信息
		$data_in['post'] = array();// 保存表单post信息
		
		$data_save['content'] = array();// 主表单内容保存数据
		
		$result = false;// 事件返回结果
		//--------变量定义及声明[end]--------
		
		//--------获取表单上传数据[start]--------
		$data_in['post'] = trimarray($_POST['content']);
		$data_in['get']['object_id'] = $_GET['object_id'];
		$data_in['get']['obl_sn'] = $_GET['obl_sn'];
		
		//--------获取表单上传数据[end]--------
		
		//--------输入合法性检查[start]--------
		if (!$flag_save)// 合法性检查为失败则返回false
		{
			return $result; // 【注意】返回
		}
		//--------输入合法性检查[end]--------
		
		//--------数据存储前的预处理[start]--------
		$temp = binary_data::get_object_binary_list($data_in['get']['object_id'], "AND obl_sn={$data_in['get']['obl_sn']}");
		$data_db['content'] = $temp[$data_in['get']['obl_sn']];
		
		//--------数据存储前的预处理[end]--------
		
		//--------数据库事物处理[start]--------
		try
		{
			$bd = new binary_data(binary_data::get_bdid_from_object($data_in['get']['object_id'], $data_in['get']['obl_sn']));
			
			$bd->remove();
			
			binary_data::remove_object_binary_link($data_in['get']['object_id'], $data_in['get']['obl_sn']);
			
			$log = new g_app_log($data_in['get']['object_id'], $data_db['content']['obl_entity_id']);
			
			$log->update_log(G_TYPE_AL_EDIT, "删除上传文件。");
		}catch(Exception $e)
		{
			scap_insert_sys_info('error', $e->getMessage());
			return $result;// 【注意】返回
		}
		
		$str_info = sprintf("删除上传文件已成功。");
		scap_insert_sys_info('tip', $str_info);
		
		$result = true;// 执行成功标志
		return $result; // 【注意】返回
	}
	

/**
	 * 保存comment_item(包含add)
	 * 
	 * @author Hufh
	 * 
	 * @return bool
	 */
	protected function event_comment_item_save()
	{
		
		//--------变量定义及声明[start]--------
		$data_save = array();
		$data_flag = array();
		$flag_save = true;

		$data_in['get'] = array();// 保存表单获取到的get信息
		$data_in['post'] = array();// 保存表单post信息
		
		$data_save['content'] = array();// 主表单内容保存数据
		
		$result = false;// 事件返回结果
		//--------变量定义及声明[end]--------
		
		//--------获取表单上传数据[start]--------
		$data_in['post'] = trimarray($_POST['content']);
		$data_in['get']['ci_id'] = $_GET['ci_id'];
		$data_in['get']['entity_id'] = $_GET['entity_id'];
		$data_in['get']['link_entity_id'] = $_GET['link_entity_id'];
		$data_in['get']['object_id'] = $_GET['object_id'];
		//--------获取表单上传数据[end]--------
		
		//--------输入合法性检查[start]--------
		if(!verify_content_legal($data_in['post']['ci_name'], VCL_TYPE_NOT_EMPTY))
		{
			scap_insert_sys_info('warn', '【名称】请填写。');
			$flag_save = false;
		}
	
		if(!verify_content_legal($data_in['post']['ci_comment'], VCL_TYPE_NOT_EMPTY))
		{
			scap_insert_sys_info('warn', '【评论内容】请填写。');
			$flag_save = false;
		}
		
		if (!$flag_save)// 合法性检查为失败则返回false
		{
			return $result; // 【注意】返回
		}
		//--------输入合法性检查[end]--------
		
		//--------数据存储前的预处理[start]--------
		
		$data_save['content']['ci_name'] = $data_in['post']['ci_name'];
		$data_save['content']['ci_content'] = $data_in['post']['ci_comment'];
		
		$data_save['content']['link_object_id'] = $data_in['get']['object_id'];
		
		//--------数据存储前的预处理[end]--------
		
		//--------数据库事物处理[start]--------
		if(empty($data_in['get']['ci_id']))
		{
			try
			{
				$ci = g_comment::create($data_save['content'], $data_in['get']['entity_id']);
				
				//插入简单关联
				$ol = new g_object_link($ci->get_current_ci_id(), $data_in['get']['entity_id'], $data_in['get']['link_entity_id']);
				$ol->set_current_link_entity_id($data_in['get']['link_entity_id']);
				$ol->update_link($data_in['get']['object_id'], 1);
			}catch(Exception $e)
			{
				scap_insert_sys_info('error', $e->getMessage());
				return $result;// 【注意】返回
			}
		}
		else
		{
			try
			{
				$ci = new g_comment($data_in['get']['ci_id'], $data_in['get']['entity_id']);
				$ci->update($data_save['content']);
			}catch(Exception $e)
			{
				scap_insert_sys_info('error', $e->getMessage());
				return $result;// 【注意】返回
			}
		}
		
				
		$str_info = sprintf("编辑评论已成功。");
		scap_insert_sys_info('tip', $str_info);
		
		$result = true;// 执行成功标志
		return $result; // 【注意】返回

	}
	
	/**
	 * 删除评论事件
	 * 
	 * @return bool true|false
	 * 
	 * @author Hufh
	 */
	protected function event_comment_item_remove()
	{
		//--------变量定义及声明[start]--------
		$data_save = array();
		$data_flag = array();
		
		$data_def['text_act'] = "删除";
		
		$data_in['get'] = array();// 保存表单获取到的get信息
		$data_in['post'] = array();// 保存表单post信息
		
		$data_save['content'] = array();// 主表单内容保存数据
		
		$result = false;// 事件返回结果
		//--------变量定义及声明[end]--------
		
		//--------获取表单上传数据[start]--------
		$data_in['get']['ci_id'] = $_GET['ci_id'];
		$data_in['get']['entity_id'] = $_GET['entity_id'];
		$data_in['get']['link_entity_id'] = $_GET['link_entity_id'];
		//--------获取表单上传数据[end]--------

		//--------数据库事物处理[start]--------
		try
		{
			// 实例化g_personal_log类
			$ci = new g_comment($data_in['get']['ci_id'], $data_in['get']['entity_id']);
			
			// 更新ci主体
			$ci->remove();
			
			// 更新简单关联表
			$ol = new g_object_link($data_in['get']['ci_id'], $data_in['get']['entity_id'], $data_in['get']['link_entity_id']);
			$ol->remove_all_link();
			
		}catch(Exception $e)
		{
			scap_insert_sys_info('error', $e->getMessage());
			return $result;// 【注意】返回
		}
		
		//--------数据库事物处理[end]--------
		
		$str_info = sprintf("删除评论已成功。");
		scap_insert_sys_info('tip', $str_info);
		
		$result = true;// 执行成功标志
		
		return $result; // 【注意】返回
	}
	
}
?>