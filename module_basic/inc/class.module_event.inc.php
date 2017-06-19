<?php
/**
 * 模块事件处理类
 * create time: 2011-2-24 下午04:19:13
 * @version $Id: class.module_event.inc.php 73 2013-01-24 09:40:42Z liqt $
 * @author LiQintao
 */

class module_event extends scap_module_event
{
	public function __construct()
	{
		parent::__construct();
	}

	public function process_ui_event()
	{
		switch($this->current_method_name)
		{
		    case 'logout':
		        $this->excute_event('logout');
		        break;
		    case 'login':
		        if ($_POST['button']['login'] || (isset($_GET['account']) && isset($_GET['pwd'])) || strcasecmp($_GET['account'], 'public') == 0)
		        {
		            $this->excute_event('login');
		        }
		        break;
		    case 'change_password':
		        if ($_POST['button']['btn_save'])
		        {
		            $this->excute_event('change_password');
		        }
		        break;
		}
	}
	
	/**
	 * 系统注销事件
	 * 
	 * @return bool
	 */
	protected function event_logout()
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
        
        //--------获取表单上传数据[end]--------
        
        //--------输入合法性检查[start]--------
        if (!$flag_save)// 合法性检查为失败则返回false
        {
            return $result; // 【注意】返回
        }
        //--------输入合法性检查[end]--------
        // [log]登出
		scap_insert_log($this->current_module_id, // 模块id
						TYPE_LOG_OP_ACCOUNT, // 操作者类型
						scap_html::scap_show_account($GLOBALS['scap']['auth']['account_id'], 'a_c_login_id'), // 操作者信息
						$GLOBALS['scap']['auth']['ip'], // 来源描述
						TYPE_LOG_ACT_LOGOUT, // 动作类型
						TYPE_LOG_ACT_OBJECT_SYS, // 动作对象
						'', // 对象信息
						TYPE_LOG_RESULT_SUCCESS, // 动作结果类型
						'' // 附加说明
					);

		scap_destory_session();
                
        $result = true;// 执行成功标志
        
        return $result; // 【注意】返回
	}
	
	/**
	 * 系统登录事件
	 * 
	 * @return bool
	 */
	protected function event_login()
	{
	    //--------变量定义及声明[start]--------
        $data_save = array();
        $data_flag = array();
        $data_temp = array();
        $flag_save = true;
        
        $data_in['get'] = array();// 保存表单获取到的get信息
        $data_in['post'] = array();// 保存表单post信息    
        
        $data_save['content'] = array();// 主表单内容保存数据
        
        $data_flag['type_log'] = NULL;// 当前操作日志类型

        $result = false;// 事件返回结果
        //--------变量定义及声明[end]--------
        
        //--------获取表单上传数据[start]--------
        $data_in['post'] = $_POST['content'];
        
        $data_in['get']['account'] = trim($_GET['account']); // 登录用户id
		$data_in['get']['pwd'] = $_GET['pwd']; // 登录密码
		
		// 支持从get(url)方式登陆
		if (empty($_POST['button']['login']) && isset($_GET['account']) && isset($_GET['pwd']))
		{
		    $data_in['post']['account'] = $data_in['get']['account'];
		    $data_in['post']['passwd'] = $data_in['get']['pwd'];
		}
		
		// 匿名访问
		if (strcasecmp($data_in['get']['account'], 'public') == 0)
		{
		    $data_in['post']['account'] = $data_in['get']['account'];
		}
		
		$data_in['post']['account'] = trim($data_in['post']['account']);// 去除帐号前后空格
        //--------获取表单上传数据[end]--------

        //--------输入合法性检查[start]--------
        if (!verify_content_legal($data_in['post']['account'], VCL_TYPE_NOT_EMPTY))
        {
            scap_insert_sys_info('tip', '用户名不能为空。');
            $flag_save = false;
        }
        
	    if (strcasecmp($data_in['post']['account'], 'public') == 0)
		{
		    scap_insert_sys_info('tip', '无效的用户。');
            $flag_save = false;
		}
        
        if ($flag_save)
        {
            $auth_result = scap_check_login_user(trim($data_in['post']['account']), $data_in['post']['passwd']);
            
            if ($auth_result != SCAP_MSG_SUCCESS)
            {
                switch($auth_result)
                {
                    case SCAP_MSG_ACCOUNT_NOEXIST:
                        scap_insert_sys_info('error', '帐户不存在。');
                        $data_in['msg_feedback'] = "帐户不存在";
                        break;
                    case SCAP_MSG_PWD_ERROR:
                        scap_insert_sys_info('error', '口令错误。');
                        $data_in['msg_feedback'] = "口令错误";
                        break;
                    case SCAP_MSG_ACCOUNT_STOP:
                        scap_insert_sys_info('error', '帐户被停用。');
                        $data_in['msg_feedback'] = "帐户被停用";
                        break;
                    case SCAP_MSG_LDAP_AUTH_ERROR:
                        scap_insert_sys_info('error', 'LDAP认证错误。');
                        $data_in['msg_feedback'] = "LDAP认证错误";
                        break;
                    case SCAP_MSG_LDAP_CONNECT_ERROR:
                        scap_insert_sys_info('error', 'LDAP连接错误。');
                        $data_in['msg_feedback'] = "LDAP连接错误";
                        break;
                    default:
                        scap_insert_sys_info('error', '未知错误。');
                        $data_in['msg_feedback'] = "未知错误";
                }
                	
                // [log]登录失败
                $temp_info = scap_html::scap_show_account($data_in['post']['account'], 'a_c_login_id');
                $temp_info = (strlen($temp_info) < 5) ? $data_in['post']['account'] : $temp_info;
                scap_insert_log($this->current_module_id, // 模块id
                                TYPE_LOG_OP_ACCOUNT, // 操作者类型
                                $temp_info, // 操作者信息
                                $_SERVER['REMOTE_ADDR'], // 来源描述
                                TYPE_LOG_ACT_LOGIN, // 动作类型
                                TYPE_LOG_ACT_OBJECT_SYS, // 动作对象
                									'', // 对象信息
                                TYPE_LOG_RESULT_FAIL, // 动作结果类型
                                $data_in['msg_feedback']// 附加说明
                );
                
                $flag_save = false;
            }
        }
        
        if (!$flag_save)// 合法性检查为失败则返回false
        {
            return $result; // 【注意】返回
        }
        //--------输入合法性检查[end]--------
            
        //--------数据库事物处理[start]--------
        $data_save['content'] = $data_in['post'];
        try
        {
            scap_create_session($data_in['post']['account']);
            
            // [log]登录成功
            scap_insert_log($this->current_module_id, // 模块id
                            TYPE_LOG_OP_ACCOUNT, // 操作者类型
                            scap_html::scap_show_account($GLOBALS['scap']['auth']['account_id'], 'a_c_login_id'), // 操作者信息
                            $GLOBALS['scap']['auth']['ip'], // 来源描述
                            TYPE_LOG_ACT_LOGIN, // 动作类型
                            TYPE_LOG_ACT_OBJECT_SYS, // 动作对象
                									'', // 对象信息
                            TYPE_LOG_RESULT_SUCCESS, // 动作结果类型
                									'' // 附加说明
            );
        }catch(Exception $e)
        {
            scap_insert_sys_info('error', $e->getMessage());
            return $result;// 【注意】返回
        }
        
        //--------数据库事物处理[end]--------
        
        $result = true;// 执行成功标志
        
        return $result; // 【注意】返回
	}
	
    /**
     * 修改当前账户密码事件
     */
    protected function event_change_password()
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
        $data_in['pw_now'] = scap_encrypt_password($data_in['post']['pw_now']);
        //--------获取表单上传数据[end]--------
        
        //--------输入合法性检查[start]--------
        if (scap_db_get_row_count('scap_accounts', "a_s_id = '{$this->current_account_s_id}' AND a_s_password = '{$data_in['pw_now']}'") < 1)
        {
            scap_insert_sys_info('warn', '【现在口令】错误!');
            $flag_save = false;
        }
        	
        if ($data_in['post']['pw_new'] != $data_in['post']['pw_new_confirm'])
        {
            scap_insert_sys_info('warn', '【确认新口令】与【新口令】不相同!');
            $flag_save = false;
        }
        
        if (!$flag_save)// 合法性检查为失败则返回false
        {
            return $result; // 【注意】返回
        }
        //--------输入合法性检查[end]--------
        
        //--------数据库事物处理[start]--------
        $data_save['content'] = $data_in['post'];
        $data_save['content']['a_s_password'] = scap_encrypt_password($data_in['post']['pw_new']);
        
        if (scap_entity::edit_row('scap_accounts', $data_save['content'], 'update', "a_s_id = '{$this->current_account_s_id}'", 'module_basic') == false)
        {
            scap_insert_sys_info('error', sprintf("口令更改失败！错误信息：%s", scap_db_error_msg()));
            return $result; // 【注意】返回
        }
        
        //--------数据库事物处理[end]--------
        
        $str_info = sprintf("口令更改成功。");
        scap_insert_sys_info('tip', $str_info);
        
        $result = true;// 执行成功标志
        
        return $result; // 【注意】返回
    }
}
?>