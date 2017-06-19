<?php
/**
 * description: 系统通用函数
 * create time: 2006-10-18 17:47:53
 * @version $Id: core.function.inc.php 162 2014-02-10 05:58:40Z liqt $
 * @author LiQintao
 */

use scap\module\g_tool\config;

/**
 * 系统应用解析器
 * 
 * @param string $module 模块名称
 * @param string $class UI类名称
 * @param string $method 方法名称
 * 
 * @return int 执行结果
 */
function scap_application_parser($module, $class, $method)
{
    // 返回值
    $result = SCAP_MSG_SUCCESS;
    
    // 获取待访问模块的注册信息
    $module_info = scap_get_module_register_list("ml_s_id = '{$module}'");
    
    // 访问模块的安全性检查：是否被注册
    if (count($module_info) == 0)
    {
    	$result = SCAP_MSG_ACCESS_UNREGISTER_MODULE;
    	return $result;
    }
    elseif ($module_info[$module]['ml_s_status'] != STAT_MODULE_NORMAL && strcasecmp($module, 'module_basic') != 0) // 访问模块的安全性检查：是否被启用
    {
    	$result = SCAP_MSG_ACCESS_NO_NORMAL_MODULE;
    	return $result;
    }
    
    
    // [$GLOBALS['scap']['info']['current_module_id']]
    $GLOBALS['scap']['info']['current_module_id'] = $module; // 当前模块id
    $GLOBALS['scap']['info']['current_class'] = $class; // 当前类名
    $GLOBALS['scap']['info']['current_method'] = $method; // 当前方法名
    
    scap_append_module_include_path($module);
    
    // 创建调用类的实例
    $GLOBALS['scap']['handle_current_class'] = scap_create_object(sprintf('%s.%s', $module, $class));
    
    // 访问模块类的安全性检查：不存在的模块类的访问
    if (empty($GLOBALS['scap']['handle_current_class']))
    {
    	$result = SCAP_MSG_ACCESS_NO_EXIST_CLASS;
    	return $result;
    }
    
    // 检查方法是否存在
    if (!method_exists($GLOBALS['scap']['handle_current_class'], $method))
    {
        $result = SCAP_MSG_ACCESS_NO_ACCESS_METHOD;
    	return $result;
    }
    
    // 加载访问控制检查
    scap_load_module_class($module, 'module_access_controller');
    $ac = new module_access_controller();
    
    if (!$ac->validate_access_point())
    {
        $result = SCAP_MSG_ACCESS_NO_ACCESS_METHOD;
    	return $result;
    }
    
    // 加载事件处理
    scap_load_module_class($module, 'module_event');
    $event = new module_event();
    
    $event->process_ui_event();
    
    $GLOBALS['scap']['handle_current_class']->set_current_event_info();
    
    // 执行操作
    if (eval('$GLOBALS[\'scap\'][\'handle_current_class\']->'.$method.'();') === FALSE)
    {
        $result = SCAP_MSG_APP_ERROR;// 应用自身错误
    }
    
    return $result;
}

/**
 * 界面输出系统错误信息(不改变当前URL)
 * @param int $error_type 错误类型
 */
function scap_show_system_error($error_type)
{
    $GLOBALS['scap']['info']['current_module_id'] = 'module_basic'; // 当前模块id
    $GLOBALS['scap']['info']['current_class'] = 'ui_system_info'; // 当前类名
    $GLOBALS['scap']['info']['current_method'] = 'no_access'; // 当前方法名
    
    scap_load_module_class('module_basic', 'ui_system_info');
    $GLOBALS['scap']['handle_current_class'] = new ui_system_info();
    
    $GLOBALS['scap']['handle_current_class']->no_access($error_type);
}

/**
 * 创建一个系统中的对象的实例
 * 
 * @param string $class [module name].[class name]
 * @param string $p1-$p5 对象的参数,预设5个
 * 
 * @return object object or null
 */
function scap_create_object($class, $p1='_UNDEF_', $p2='_UNDEF_', $p3='_UNDEF_', $p4='_UNDEF_', $p5='_UNDEF_')
{
	$flag_include = false;
	$obj = NULL;
	
	list($appname, $classname) = explode('.', $class);
	
	$filename = SCAP_PATH_ROOT.'/'.$appname.'/inc/class.'.$classname.'.inc.php';
	
	$included_files = get_included_files();
	
	if (array_key_exists($filename, $included_files))
	{
		$flag_include = true;
	}
	else
	{
		if (@file_exists($filename))
		{
			include_once($filename);
			$flag_include = true;
		}
	}
	
	if ($flag_include)
	{
		if ($p1 == '_UNDEF_' && $p1 != 1)
		{
			$obj = new $classname;
		}
		else
		{
			$para = array($p1,$p2,$p3,$p4,$p5);
			
			$i = 1;
			$code = '$obj = new '.$classname.'(';
			
			foreach($para as $v)
			{
				if ($v == '_UNDEF' && $v != 1)
				{
					break;
				}
				else
				{
					$code .= '$p'.$i.',';
				}
				$i ++;
			}
			
			$code = substr($code , 0, -1).');';
			eval($code);
		}
		
		return $obj;
	}
}

/**
 * 验证用户登录口令
 * 
 * @param string $userid
 * @param string $password
 * 
 * @return SCAP_MSG
 */
function scap_check_login_user($userid, $password)
{
	$rtn = SCAP_MSG_UNKNOWN_ERROR;
	
	// 获取当前帐号的认证方式
	$flag_auth_type = scap_get_config_value('module_manage', 'auth_type');
	
	// public/admin总是采用数据库认证
	if (empty($flag_auth_type) || strcasecmp ($userid, 'public') == 0 || strcasecmp ($userid, 'admin') == 0)
	{
		$flag_auth_type = TYPE_AUTH_DB;
	}
	
	$sql = "SELECT a_s_id, a_s_password, a_s_status FROM scap_accounts WHERE a_c_login_id = '$userid'";
	$rs = $GLOBALS['scap']['db']->db_connect->Execute($sql);
	
	if ($rs)
	{
		if ($rs->RecordCount() == 1)
		{
			if ($flag_auth_type == TYPE_AUTH_DB)// 数据库表认证
			{
				if (strcasecmp ($userid, 'public') == 0 || scap_check_password($password, $rs->fields['a_s_password']) === true)
				{
					if ($rs->fields['a_s_status'] == STAT_ACCOUNT_NORMAL)
					{
						$rtn = SCAP_MSG_SUCCESS;
						$GLOBALS['scap']['auth']['account_s_id'] = $rs->fields['a_s_id'];
					}
					else
					{
						$rtn = SCAP_MSG_ACCOUNT_STOP;
					}
				}
				else
				{
					$rtn = SCAP_MSG_PWD_ERROR;
				}
			}
			elseif($flag_auth_type == TYPE_AUTH_LDAP)// LDAP认证
			{
				$ds = ldap_connect(scap_get_config_value('module_manage', 'ldap_host'), scap_get_config_value('module_manage', 'ldap_port'));
				if ($ds)
				{
					ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
					if (!@ldap_bind($ds, "uid=$userid,".scap_get_config_value('module_manage', 'ldap_base_dn'), $password))
					{
						$rtn = SCAP_MSG_LDAP_AUTH_ERROR;
					}
					else
					{
						if ($rs->fields['a_s_status'] == STAT_ACCOUNT_NORMAL)
						{
							$rtn = SCAP_MSG_SUCCESS;
							$GLOBALS['scap']['auth']['account_s_id'] = $rs->fields['a_s_id'];
						}
						else
						{
							$rtn = SCAP_MSG_ACCOUNT_STOP;
						}
					}
					ldap_close($ds);
				}
				else
				{
					$rtn = SCAP_MSG_LDAP_CONNECT_ERROR;
				}
			}
		}
		elseif($rs->RecordCount() == 0)
		{
			$rtn = SCAP_MSG_ACCOUNT_NOEXIST;
		}
		
		$rs->Close();
	}
	
	return $rtn;
}

/**
 * 创建系统用户session
 * 
 * @param string $userid 账号登陆ID，默认为'public'
 * @param string $systemid 帐号系统id，默认为空
 */
function scap_create_session($userid='public', $systemid='')
{
	header("ETag: PUB" . time());
	header("Last-Modified: " . gmdate("D, d M Y H:i:s", time()-10) . " GMT");
	header("Expires: " . gmdate("D, d M Y H:i:s", time() + 5) . " GMT");
	header("Pragma: no-cache");
	header("Cache-Control: max-age=1, s-maxage=1, no-cache, must-revalidate");
	session_cache_limiter("nocache");
	
	session_name(config::get('scap', 'session_name'));
	session_start();
	session_regenerate_id(true);
	
	$_SESSION['sid'] = $GLOBALS['scap']['auth']['sid'] = session_id();
	$_SESSION['account_id'] = $GLOBALS['scap']['auth']['account_id'] = $userid;
	
	if (!empty($systemid))
	{
	    $_SESSION['account_s_id'] = $GLOBALS['scap']['auth']['account_s_id'] = $systemid;
	}
	else 
	{
	    $_SESSION['account_s_id'] = $GLOBALS['scap']['auth']['account_s_id'];
	}
	$_SESSION['ip'] = $GLOBALS['scap']['auth']['ip'] = $_SERVER['REMOTE_ADDR'];
	$_SESSION['sys_info'] = array();// 系统反馈信息 
}

/**
 * 检查是否拥有经过认证的session
 * 
 * 2009-1-21 16:07:去除对ip的一致性判断（因为某些宽带ip会随机变化）
 * 
 * @return bool true|false
 */
function scap_check_session()
{
	$rtn = false;
	$sid = $_COOKIE[config::get('scap', 'session_name')];
	
	if (empty($sid))
	{
		return $rtn;
	}

	session_name(config::get('scap', 'session_name'));
	session_id($sid);
	session_start();
	
	if($_SESSION['sid'] == $sid)
	{
		$GLOBALS['scap']['auth']['sid'] = $sid;
		$GLOBALS['scap']['auth']['account_id'] = $_SESSION['account_id'];
		$GLOBALS['scap']['auth']['account_s_id'] = $_SESSION['account_s_id'];
		$GLOBALS['scap']['auth']['ip'] = $_SERVER['REMOTE_ADDR'];
		$rtn = true;
	}
	return $rtn;
}

/**
 * 用户注销
 * 
 */
function scap_destory_session()
{
    $sid = $_COOKIE[config::get('scap', 'session_name')];
    
	if (empty($sid))
	{
		return;
	}
	
	session_id($sid);
	session_start();
	
	// Unset all of the session variables.
	$_SESSION = array();
	
	if (isset($_COOKIE[config::get('scap', 'session_name')]))
	{
		setcookie(config::get('scap', 'session_name'), '', time()-42000, '/');
	}	
	// Finally, destroy the session.
	session_destroy();	
}

/**
 * 从db中获取配置值
 * 
 * @param string $module_id 模块id
 * @param string $key_name 键名称
 * 
 * @return string 返回指定值
 */
function scap_get_config_value($module_id, $key_name)
{
 	$rtn = '';
 	$module_id = strtolower($module_id);
 	$key_name = strtolower($key_name);
 	
 	$sql = "SELECT c_c_value FROM scap_config WHERE c_s_module = '$module_id' AND c_s_key = '$key_name'";
	$rs = $GLOBALS['scap']['db']->db_connect->Execute($sql);
	
	if ($rs)
	{
		if ($rs->RecordCount() > 0)
		{
			$rs->MoveFirst();
			$rtn = $rs->fields['c_c_value'];
		}
		$rs->Close();
	}
	
	return $rtn;
}

/**
 * 设置模块配置值
 * 
 * @param string $module_id 模块id
 * @param string $key_name 键名称
 * @param string $key_value 值
 * 
 * @return bool
 */
function scap_set_config_value($module_id, $key_name, $key_value)
{
    $rtn = false;
	$where = '';
	$type = ''; // 操作类型: UPDATE / INSERT
	
	// 读取对应acl数据
	$sql = "SELECT c_s_key FROM scap_config WHERE c_s_module = '$module_id' AND c_s_key = '$key_name'";

	$rs = $GLOBALS['scap']['db']->db_connect->Execute($sql);
	
	if ($rs)
	{
		if ($rs->RecordCount() > 0)
		{
			$type = 'UPDATE';
			$where = "c_s_module = '$module_id' AND c_s_key = '$key_name'";
		}
		else
		{
			$type = 'INSERT';
		}
		$rs->Close();
	}
	else
	{
		return $rtn;
	}
	
	$rtn = $GLOBALS['scap']['db']->db_connect->AutoExecute('scap_config', array('c_s_module' => $module_id, 'c_s_key' => $key_name, 'c_c_value' => $key_value), $type, empty($where) ? false : $where);
	return $rtn;
}

/**
 * 获取指定模版的对应css文件url
 * 
 * @param string $module_id 模块id
 * @param string $file_name 文件名称
 * @param string $tpl_name 模版名称
 * 
 * @return string css文件的链接
 */
function scap_get_css_url($module_id, $file_name, $tpl_name = 'default')
{
	$file_dir = SCAP_PATH_BASIC.'templates/'.$tpl_name.'/css/'.$file_name;
	
	if (!file_exists($file_dir))
	{
		$tpl_name = 'default';
	}
	
	$rtn = $GLOBALS['scap']['info']['site_url'].'/'.$module_id.'/templates/'.$tpl_name.'/css/'.$file_name;
	return $rtn;
}

/**
 * 获取指定模版的对应js文件url
 * 
 * @param string $module_id 模块id
 * @param string $file_name 文件名称
 * @param string $tpl_name 模版名称
 * 
 * @return string js文件的链接
 */
function scap_get_js_url($module_id, $file_name, $tpl_name = 'default')
{
	$file_dir = SCAP_PATH_BASIC.'templates/'.$tpl_name.'/js/'.$file_name;
	
	if (!file_exists($file_dir))
	{
		$tpl_name = 'default';
	}
	
	$rtn = $GLOBALS['scap']['info']['site_url'].'/'.$module_id.'/templates/'.$tpl_name.'/js/'.$file_name;
	return $rtn;
}

/**
 * 获取指定模版的对应image文件url
 * 
 * @param string $module_id 模块id
 * @param string $file_name 文件名称
 * @param string $tpl_name 模版名称
 * 
 * @return string image文件的链接
 */
function scap_get_image_url($module_id, $file_name, $tpl_name = 'default')
{
	$file_dir = SCAP_PATH_BASIC.'templates/'.$tpl_name.'/images/'.$file_name;
	
	if (!file_exists($file_dir))
	{
		$tpl_name = 'default';
	}
	
	$rtn = $GLOBALS['scap']['info']['site_url'].'/'.$module_id.'/templates/'.$tpl_name.'/images/'.$file_name;
	return $rtn;
}

/**
 * 检查系统acl指定核准权限的结果
 * 2007-6-19:增加其它帐户对匿名帐户public的权限继承
 * 
 * @param string $module_id 模块id
 * @param unsigned int $acl_bit 指定核准权限的对应位(右至左,0-31)
 * @param string $account_id 系统用户的id
 * @param bool $flag_sid $account_id是否是系统帐户内部id,如果否则为登录id.默认为false
 * 
 * @return bool true|false
 */
function scap_check_acl($module_id, $acl_bit, $account_id='', $flag_is_sid = false)
{
	$rtn = false;
	$acl_code = 0;
	$acl_string = '';
	$acl_result = 0;
	
	if (empty($account_id) && !$flag_is_sid)
	{
		$account_id = $GLOBALS['scap']['auth']['account_id'];
	}
	
	if ($flag_is_sid)
	{
		$sql = "SELECT acl_c_acl_code FROM scap_acl WHERE acl_s_module = '$module_id' AND acl_s_account_id = '$account_id'";
	}
	else
	{
		$sql = "SELECT acl_c_acl_code FROM scap_acl " .
				"LEFT JOIN scap_accounts ON(acl_s_account_id = a_s_id)" .
				"WHERE acl_s_module = '$module_id' AND a_c_login_id = '$account_id'";
	}
	$rs = $GLOBALS['scap']['db']->db_connect->Execute($sql);
	
	if ($rs)
	{
		if ($rs->RecordCount() > 0)
		{
			$rs->MoveFirst();
			$acl_code = $rs->fields['acl_c_acl_code'];
			$rs->Close();
		}
		else
		{
			$rs->Close();
			return false;
		}
	}
	else
	{
		return false;
	}
	
	$acl_string = str_pad(base_convert($acl_code, 10, 2), ACL_BITS_LENGTH, '0', STR_PAD_LEFT);
	
	$acl_result = intval(substr($acl_string, (ACL_BITS_LENGTH - $acl_bit - 1), 1));
	if ($acl_result == 1)
	{
		$rtn = true;
	}
	
	if (!$rtn && strcasecmp($account_id, 'public') != 0)
	{
		$rtn = scap_check_acl($module_id, $acl_bit, 'public');
	}
	
	return $rtn;
}

/**
 * 设置acl权限位
 * 
 * @param string $module_id 模块id
 * @param array $arr_acl 设置的acl位数组,形式如: array([acl位置] => 1 or 0, ...)
 * @param string $account_id 系统用户的内部id(sid)
 * 
 * @return bool true | false
 */
function scap_set_acl($module_id, $arr_acl, $account_id)
{
	$rtn = false;
	$acl_code = 0;
	$acl_string = '';
	$new_acl_string = '';
	$where = '';
	$type = ''; // 操作类型: UPDATE / INSERT
	
	if (!is_array($arr_acl))
	{
		$arr_acl = array();
	}
	
	// 读取对应acl数据
	$sql = "SELECT acl_c_acl_code FROM scap_acl WHERE acl_s_module = '$module_id' AND acl_s_account_id = '$account_id'";

	$rs = $GLOBALS['scap']['db']->db_connect->Execute($sql);
	
	if ($rs)
	{
		if ($rs->RecordCount() > 0)// 有acl数据
		{
			$rs->MoveFirst();
			$acl_code = $rs->fields['acl_c_acl_code'];
			
			$type = 'UPDATE';
			$where = "acl_s_module = '$module_id' AND acl_s_account_id = '$account_id'";
		}
		else// 无acl数据
		{
			$type = 'INSERT';
		}
		$rs->Close();
	}
	else
	{
		return $rtn;
	}
	
	// 原acl对应的32位字符串
	$acl_string = str_pad(base_convert($acl_code, 10, 2), ACL_BITS_LENGTH, '0', STR_PAD_LEFT);
	
	// 更新acl字符串为设置的值
	foreach($arr_acl as $k => $v)
	{
		$acl_string{ACL_BITS_LENGTH - $k -1} = $v;
	}
	
	// 更新新的acl数值
	$acl_code = base_convert($acl_string, 2, 10);
	
	$rtn = $GLOBALS['scap']['db']->db_connect->AutoExecute(NAME_T_SYS_ACL, array('acl_s_module' => $module_id, 'acl_s_account_id' => $account_id, 'acl_c_acl_code' => $acl_code), $type, empty($where) ? false : $where);
	return $rtn;
}

/**
 * 获取系统的模块列表
 * 
 * @param string $where 查询条件表述
 * 
 * @return array 模块属性列表:[模块id]=>(...)
 */
function scap_get_module_list($where = '')
{
	$module_list = array();
	
	$sql = "SELECT * FROM scap_module_list";
	if (!empty($where))
	{
		$sql .= " WHERE ".$where;
	}
	$sql .= " ORDER BY ml_c_order ASC";
	
	$GLOBALS['scap']['db']->db_connect->SetFetchMode(ADODB_FETCH_ASSOC);
	$rs = $GLOBALS['scap']['db']->db_connect->Execute($sql);
	
	if ($rs)
	{
		$rs->MoveFirst();
		while(!$rs->EOF)
		{
			$module_list[$rs->fields['ml_s_id']] = $rs->fields;
			$rs->MoveNext();
		}
	}
	
	return $module_list;
}

/**
 * 获取指定用户的能够访问的模块列表
 * 如果是后台模块，则总能访问，因此只记录可用的前台模块
 * 
 * @param string $account_id 系统用户的登录id
 * 
 * @return array 模块属性列表:[模块id]=>(...)
 */
function scap_get_module_access_list($account_id='')
{
	if (empty($account_id))
	{
		$account_id = $GLOBALS['scap']['auth']['account_id'];
	}
	
	$module_list = scap_get_module_list("ml_s_status=".STAT_MODULE_NORMAL);

	foreach($module_list as $k => $v)
	{
		// 去除无访问权限的模块信息
		if (!scap_check_acl($k, ACL_BIT_MODULE, $account_id))
		{
			unset($module_list[$k]);
		}
	}
	
	return $module_list;
}

/**
 * 获取模块的对应文本名称
 * 
 * @param string $module_id 系统模块id
 * @param string $lang 要读取的语言
 * 
 * @return string 系统模块的显示名称
 */
function scap_lang_module_name($module_id, $lang = 'zh-cn')
{
	$rtn = '';
	@include (SCAP_PATH_ROOT.$module_id."/language/lang.$lang.inc.php");
	eval('$rtn = TEXT_'.strtoupper($module_id).';');
	return $rtn;
}

/**
 * 获取系统内部的链接地址
 * 
 * @param array $method 方法参数:array('module'=> '', 'class' => '', 'method' => '')
 * @param array $parameters 参数数组: key => $value
 * 
 * @return string 系统方法调用链接
 */
function scap_get_url($method, $parameters = array())
{
    // 避免project_url自定义配置未读到
    config::autoload_files(SCAP_PATH_CONFIG);
    
	$url = '';
	$project_url = config::get('scap', 'project_url');
	if (empty($method['class']) || empty($method['method']))
	{
		$url = $project_url;
	}
	else
	{
		$url = "{$project_url}/?m={$method['module']}.{$method['class']}.{$method['method']}";
		foreach($parameters as $k => $v)
		{
			$url .= "&$k=$v";
		}
		
		// 支持自定义url机制
		if (config::get('scap', 'flag_enable_custom_url'))
		{
		    $custom_url = @call_user_func(config::get('scap', 'custom_url_method'), $method, $parameters);
		    if (!is_null($custom_url))
		    {
		        $url = $custom_url;
		    }
		}
	}
	
	return $url;
}

/**
 * 重定向系统链接
 * 
 * @param array $method 方法参数:array('module'=> '', 'class' => '', 'method' => '')或者是 string
 * @param array $parameters 参数数组: key => $value
 */
function scap_redirect_url($method, $parameters = array())
{
    // 避免project_url自定义配置未读到
    config::autoload_files(SCAP_PATH_CONFIG);
    
	if (empty($method))
	{
		return;
	}
	
	if (is_array($method))
	{
		$url = "{$GLOBALS['scap']['info']['site_url']}/{$GLOBALS['scap']['route_file']}?m={$method['module']}.{$method['class']}.{$method['method']}";
		
		foreach($parameters as $k => $v)
		{
			$url .= "&$k=$v";
		}
		
	    // 支持自定义url机制
		if (config::get('scap', 'flag_enable_custom_url'))
		{
		    $custom_url = @call_user_func(config::get('scap', 'custom_url_method'), $method, $parameters);
		    if (!is_null($custom_url))
		    {
		        $url = $custom_url;
		    }
		}
	}
	else
	{
		$url = $method;
	}
    
	header("Location: $url");
	exit;
}

/**
 * 添加模块菜单数据项
 * 
 * @param string $menu_text 菜单的名称
 * @param string $menu_url 菜单的链接
 * 
 * @return array 封装好的一个菜单项数组结构
 */
function scap_create_module_menu_item($menu_text, $menu_url)
{
	$menu_item = array('text' => $menu_text, 'url' => $menu_url);
	return $menu_item;
}

/**
 * 获取指定系统帐户的信息
 * 
 * @param string $id_value 要查询帐户的id值
 * @param string $id_name id值对应的内部数据表项名称：只能是a_s_id或a_c_login_id，默认为 a_s_id
 * 
 * @return array 返回获取的帐户信息数组：a_s_id, a_c_login_id, a_c_display_name, a_s_status
 */
function scap_get_account_info($id_value, $id_name = 'a_s_id')
{
	$info = array();
	
	if (strcmp($id_name, 'a_s_id') != 0 && strcmp($id_name, 'a_c_login_id') != 0)
	{
		return $info;
	}
	
	$sql = "SELECT a_s_id, a_c_login_id, a_c_display_name, a_s_status FROM ".NAME_T_SYS_ACCOUNTS." WHERE $id_name = '$id_value'";
	$GLOBALS['scap']['db']->db_connect->SetFetchMode(ADODB_FETCH_ASSOC);
	$rs = $GLOBALS['scap']['db']->db_connect->Execute($sql);
	
	if ($rs)
	{
		$rs->MoveFirst();
		$info = $rs->fields;
		$rs->Close();
	}
	
	return $info;
}

/**
 * 检查系统中public帐号是否有效：存在，且开启
 * 
 * @return bool true | false
 */
function scap_check_account_public_valid()
{
    return scap_db_get_row_count(NAME_T_SYS_ACCOUNTS, "a_c_login_id = 'public' AND a_s_status = 2");
}

/**
 * 获取系统帐户列表
 * 
 * @param string $where 查询条件表述
 * 
 * @return array 返回系统帐户的信息列表
 */
function scap_get_account_list($where = '')
{
	$list = array();
	
	$sql = "SELECT a_s_id, a_c_login_id, a_c_display_name, a_s_status FROM ".NAME_T_SYS_ACCOUNTS;
	if (!empty($where))
	{
		$sql .= " WHERE ".$where;
	}
	$GLOBALS['scap']['db']->db_connect->SetFetchMode(ADODB_FETCH_ASSOC);
	$rs = $GLOBALS['scap']['db']->db_connect->Execute($sql);
	
	if ($rs)
	{
		$i = 0;
		while(!$rs->EOF)
		{
			$list[$i ++] = $rs->fields;
			$rs->MoveNext();
		}
		$rs->Close();
	}
	return $list;
}

/**
 * Returns the database operate last status or error message
 */
function scap_db_error_msg()
{
	return $GLOBALS['scap']['db']->db_connect->ErrorMsg();
}

/**
 * Returns rows affected by UPDATE/DELETE
 */
function scap_db_affected_rows()
{
	return $GLOBALS['scap']['db']->db_connect->Affected_Rows();
}

/**
 * 获取36位的GUID
 * 
 * @return string 36位的GUID
 */
function scap_get_guid()
{
	require_once(SCAP_PATH_LIBRARY.'class.guid.inc.php');
	
	$guid = new Guid();
	
	$guid->genGuid();
	return $guid->getGuid();
}

/**
 * 获取指定查询条件的数据行数目
 * 
 * @param string $table 数据表名称
 * @param string $where 数据表查询条件
 * 
 * @return int 条目数目，如果查询失败返回false
 */
function scap_db_get_row_count($table, $where)
{
	$count = false;
	
	$sql = "SELECT COUNT(*) row_count FROM $table";
	if (!empty($where))
	{
		$sql .= " WHERE ".$where;
	}
	
	$GLOBALS['scap']['db']->db_connect->SetFetchMode(ADODB_FETCH_ASSOC);
	$rs = $GLOBALS['scap']['db']->db_connect->Execute($sql);
	
	if ($rs)
	{
		$rs->MoveFirst();
		$count = $rs->fields['row_count'];
		$rs->Close();
	}
	
	return $count;
}

/**
 * 获取数据表中指定表达式的最大的数值
 * 
 * @param string $table 数据表名称
 * @param string $max_item MAX中的表达式
 * @param string $where 数据表查询条件
 * 
 * @return int 条目数目，如果查询失败返回false
 */
function scap_db_get_max_value($table, $max_item, $where)
{
	$value = false;
	
	$sql = "SELECT MAX($max_item) max_value FROM $table";
	if (!empty($where))
	{
		$sql .= " WHERE ".$where;
	}
	
	$GLOBALS['scap']['db']->db_connect->SetFetchMode(ADODB_FETCH_ASSOC);
	$rs = $GLOBALS['scap']['db']->db_connect->Execute($sql);
	
	if ($rs)
	{
		$rs->MoveFirst();
		$value = $rs->fields['max_value'];
		$rs->Close();
	}
	
	return $value;
}

/**
 * 开启系统数据库的事务机制
 */
function scap_db_start_transaction()
{
	$GLOBALS['scap']['db']->db_connect->StartTrans();
}

/**
 * 完成系统数据库事务机制
 */
function scap_db_end_transaction()
{
	$GLOBALS['scap']['db']->db_connect->CompleteTrans();
}

/**
 * 加密系统口令
 * 
 * @param string $origin 原始口令
 * 
 * @return string 加密后的口令
 */
function scap_encrypt_password($origin)
{
	return md5($origin);
}

/**
 * 比对系统口令
 * 
 * @param string $origin 原始口令
 * @param string $encrypt 加密后的口令
 * 
 * @return bool {true, false}
 */
function scap_check_password($origin, $encrypt)
{
	$rtn = false;
 	
 	if (md5($origin) === $encrypt)
 	{
 		$rtn = true;
 	}
 	
 	return $rtn;
}

/**
 * 获取指定系统模块的本地信息
 * 
 * @param string $module_id 系统模块id
 * 
 * @return array 系统模块定义文件中的信息数组
 */
function scap_get_module_local_info($module_id)
{
	$info = array();
	
	$file_def = SCAP_PATH_ROOT.$module_id."/inc/define.inc.php";
	
	if (file_exists($file_def))
	{
		include($file_def);
		$info = $GLOBALS['scap']['module'][$module_id];
	}
	
	return $info;
}

/**
 * 获取服务器物理存在的系统模块列表，系统模块必须是以'module_'开头的文件夹
 * 
 * @return array 系统模块信息数组，如array('module_basic' => array('version' => '1.00', ...), ...)
 */
function scap_get_module_local_list()
{
	$list = array();
	
	if ($handle = opendir(SCAP_PATH_ROOT))
	{
		while(false !== ($file = readdir($handle)))
		{
			if (is_dir($file) && strncmp('module_', $file, strlen('module_')) == 0)
			{
				$info = scap_get_module_local_info($file);
				if (!empty($info))// 只列出已设置模块信息的模块
				{
					$list[$file] = $info;
				}
			}
		}
		closedir($handle);
	}
	
	return $list;
}

/**
 * 获取在db中注册的系统模块列表
 * 
 * @param string $where 查询条件表述
 * 
 * @return array 注册的模块信息数组
 */
function scap_get_module_register_list($where = '')
{
	$list = array();
	
	$sql = "SELECT * FROM ".NAME_T_SYS_ML;
	if (!empty($where))
	{
		$sql .= " WHERE ".$where;
	}
	$GLOBALS['scap']['db']->db_connect->SetFetchMode(ADODB_FETCH_ASSOC);
	$rs = $GLOBALS['scap']['db']->db_connect->Execute($sql);
	
	if ($rs)
	{
		while(!$rs->EOF)
		{
			$list[$rs->fields['ml_s_id']] = $rs->fields;
			$rs->MoveNext();
		}
		$rs->Close();
	}
	return $list;
}

/**
 * 建立一条系统日志
 * 
 * + 添加对"开启日志"项的支持 by liqt@2008-9-1
 * 
 * @param string $module_id 模块id
 * @param int $op_type 操作者类型
 * @param string $op_info 操作者信息
 * @param string $from 来源描述
 * @param int $act_type 动作类型
 * @param int $act_object_type 动作对象
 * @param string $act_object_info 对象信息
 * @param int $result_type 动作结果类型
 * @param string $note 附加说明
 */
function scap_insert_log($module_id, $op_type, $op_info, $from, $act_type, $act_object_type, $act_object_info, $result_type, $note ='')
{
	// 判断"开启日志"项是否开启，否则不记录系统日志
	if (!scap_get_config_value('module_manage', 'flag_record_log'))
	{
		return;
	}
	
	$record = array();
	$record['l_time'] = time();
	$record['l_module'] = $module_id;
	$record['l_operator_type'] = $op_type;
	$record['l_operator_info'] = $op_info;
	$record['l_from'] = $from;
	$record['l_act_type'] = $act_type;
	$record['l_act_object_type'] = $act_object_type;
	$record['l_act_object_info'] = $act_object_info;
	$record['l_act_result'] = $result_type;
	$record['l_note'] = $note;
	
	$GLOBALS['scap']['db']->db_connect->AutoExecute(NAME_T_SYS_LOG, $record, 'INSERT');
}

/**
 * 获取界面日期时间戳
 * 
 * @param string $str_time 日期字符串
 */
function scap_get_ui_time($str_time)
{
	$rtn = 0;
	
	if (!empty($str_time))
	{
		$rtn = strtotime($str_time);
	}
	
	return $rtn;
}

/**
 * 获取指定模块的custom value数组信息
 * 
 * @param string $module_id 系统模块id
 * 
 * @return array 返回对应模块的define.inc.php中定义的custom_value数组
 */
function scap_get_module_custom_values_define($module_id)
{
	$rtn = array();
	
	@include_once (SCAP_PATH_ROOT."{$module_id}/inc/define.inc.php");
	$rtn = $GLOBALS['scap']['custom_value'][$module_id];
	
	return $rtn;
}

/**
 * 获取指定custom value的属性定义信息
 * 
 * @param string $module_id 系统模块id
 * @param string $key custom value的键值名称
 * 
 * @return array 返回对应的define.inc.php中定义的custom_value属性信息
 */
function scap_get_special_custom_value_property($module_id, $key)
{
	$rtn = array();
	
	$arr_cs = scap_get_module_custom_values_define($module_id);
	$temp = \scap\module\g_tool\matrix::musearch($key, $arr_cs, 'config_key');
	$rtn = $arr_cs[$temp[0]];
	return $rtn;
}

/**
 * 获取指定模块的acl定义数组信息
 * 
 * @todo 老版本的acl定义在define.inc.php文件中待废弃，应转移至define.module_acl.inc.php
 * 
 * @param string $module_id 系统模块id
 * 
 * @return array 返回对应模块的define.inc.php中定义的acl数组
 */
function scap_get_module_acl_define($module_id)
{
	$rtn = array();
	
	@include_once (SCAP_PATH_ROOT."{$module_id}/inc/define.inc.php");//待废弃，仅兼容老版本
	@include_once (SCAP_PATH_ROOT."{$module_id}/inc/define.module_acl.inc.php");
	
	$rtn = $GLOBALS['scap']['custom_acl'][$module_id];
	
	return $rtn;
}

/**
 * 添加系统反馈信息(使用session)
 * 
 * @param string $type 信息类型,目前支持可以是: tip/error
 * @param string $text 信息内容
 */
function scap_insert_sys_info($type, $text)
{
	$_SESSION['sys_info'][] = array('type' => $type, 'text' => $text);
}

/**
 * 清空系统反馈信息
 * 
 */
function scap_clear_sys_info()
{
	unset($_SESSION['sys_info']);
}

/**
 * 获取系统反馈信息
 * 
 * @param string $type 信息类型,目前支持可以是: tip/error
 * @param string $text 信息内容
 */
function scap_get_sys_info()
{
	$rtn = !empty($_SESSION['sys_info']) ? $_SESSION['sys_info'] : array();
	return $rtn;
}

/**
 * 加载模块的快速导航配置文件
 * 
 * @param string $module_id 模块id
 */
function scap_load_nav($moudle_id)
{
	$out = '';
	@include_once(SCAP_PATH_ROOT.$moudle_id."/inc/nav.inc.php");
	
	if (empty($menu))
	{
		return;
	}
	
	$out .= "<ul style=\"display:none;\" id='nav_{$moudle_id}'>";
	
	foreach($menu as $k => $v)
	{
		$out .= "<li><a href='{$v['url']}'>{$v['text']}</a></li>";
	}
	
	$out .= "</ul>";
	
	global $data_out;
	$data_out['body_customer_code_list'][] = $out;
}

/**
 * 新版界面加载模块菜单
 * 
 * @param string $module_id 模块id
 */
function scap_load_new_nav_menu($module_id)
{
	@include_once(SCAP_PATH_ROOT.$module_id."/inc/nav.inc.php");
	ob_clean();// 去除output缓存
	if (empty($menu))
	{
		return;
	}
	
	return $menu;
}

/**
 * 插入系统应用日志
 * 
 * @param string $object_id 被操作对象ID
 * @param int $log_type 操作类型
 * @param int $sn 操作的序号,如果为0,则默认在该类型操作的最大序号上自动+1作为新操作的序号
 * @param string|int $op_time 日志记录时间,默认为当前系统时间
 * @param string $op_id 记录操作系统的ID,默认为当前登录帐号ID
 * 
 * @return bool true|false
 */
function scap_insert_app_log($object_id, $log_type, $sn = 0, $op_time='', $op_id = '')
{
	$data_save = array();
	$data_in = array();
	
	$data_save['al_object_id'] = $object_id;
	$data_save['al_type'] = $log_type;
	
	if (empty($sn)) // 获取sn当前最大值
	{
		$max_sn = scap_db_get_max_value('scap_app_log', 'al_sn', "al_object_id = '{$object_id}' AND al_type = {$log_type}");
		
		if ($max_sn === false)
		{
			$str_info = sprintf('日志操作失败.错误信息：%s', scap_db_error_msg());
			scap_insert_sys_info('tip', $str_info);
			return false;
		}
		$data_save['al_sn'] = $max_sn + 1;
	}
	else
	{
		$data_save['al_sn'] = $sn;
	}
	
	$data_save['al_datetime'] = empty($op_time) ? time() : $op_time;
	$data_save['al_operator_id'] = empty($op_id) ? $GLOBALS['scap']['auth']['account_s_id'] : $op_id;
	
	$data_in['where'] = "al_object_id = '{$data_save['al_object_id']}' AND al_type = {$data_save['al_type']} AND al_sn = {$data_save['al_sn']}";
	
	if (!empty($sn))
	{
		$GLOBALS['scap']['db']->db_connect->Execute("DELETE FROM scap_app_log WHERE ({$data_in['where']})");
	}

	if($GLOBALS['scap']['db']->db_connect->AutoExecute('scap_app_log', $data_save, 'INSERT') === false)
	{
		$str_info = sprintf('日志操作失败.错误信息：%s', scap_db_error_msg());
		scap_insert_sys_info('tip', $str_info);
		return false;
	}
	
	return true;	
}

/**
 * 读取系统应用日志
 * 
 * @param string $object_id 被操作对象ID
 * @param int $log_type 操作类型,默认为NULL（获取所有日志）
 * @param int $log_sn 日志序号,默认为NULL（获取所有序号日志）
 * @param DESC|ASC $sn_sort 返回数据的sn顺序,默认为降序排列
 */
function scap_get_app_log($object_id, $log_type = NULL, $log_sn = NULL, $sn_sort = 'DESC')
{
	$data_db = array();
	
	$data_db['content'] = array();
	$data_db['sql'] = "SELECT * FROM scap_app_log WHERE al_object_id = '$object_id'";
	$data_db['sql'] .=  is_null($log_type) ? '' : " AND al_type = $log_type";
	$data_db['sql'] .=  is_null($log_sn) ? '' : " AND al_sn = $log_sn";
	$data_db['sql'] .=  " ORDER BY al_sn $sn_sort";
	
	$GLOBALS['scap']['db']->db_connect->SetFetchMode(ADODB_FETCH_ASSOC);
	$rs = $GLOBALS['scap']['db']->db_connect->Execute($data_db['sql']);
	
	if ($rs)
	{
		$rs->MoveFirst();
		while(!$rs->EOF)
		{
			$data_db['content'][] = $rs->fields;
			$rs->MoveNext();
		}
	}
	
	return $data_db['content'];
}

/**
 * 判断当前帐号是否是admin
 * 
 * @return true|false 返回判断结果
 */
function scap_check_current_account_is_admin()
{
	$rtn = false;
	if (strcasecmp($GLOBALS['scap']['auth']['account_id'], 'admin') == 0)
	{
		$rtn = true;
	}
	
	return $rtn;	
}

/**
 * 加载模块指定定义文件
 * 
 * @param string $module_id
 * @param string $define_name
 */
function scap_load_module_define($module_id, $define_name)
{
	if (empty($define_name))
	{
		return include_once(SCAP_PATH_ROOT."{$module_id}/inc/define.inc.php");
	}
	else
	{
		return include_once(SCAP_PATH_ROOT."{$module_id}/inc/define.{$define_name}.inc.php");
	}
}

/**
 * 加载模块指定函数文件
 * 
 * @param string $module_id
 * @param string $function_name
 */
function scap_load_module_function($module_id, $function_name)
{
	if (empty($function_name))
	{
		return include_once(SCAP_PATH_ROOT."{$module_id}/inc/function.inc.php");
	}
	else
	{
		return include_once(SCAP_PATH_ROOT."{$module_id}/inc/function.{$function_name}.inc.php");
	}
}

/**
 * 加载模块指定类文件
 * 
 * @param string $module_id
 * @param string $class_name
 */
function scap_load_module_class($module_id, $class_name)
{
	if (empty($class_name))
	{
		return include_once(SCAP_PATH_ROOT."{$module_id}/inc/class.inc.php");
	}
	else
	{
		return include_once(SCAP_PATH_ROOT."{$module_id}/inc/class.{$class_name}.inc.php");
	}
}

/**
 * 加载模块指定语言文件
 * 
 * @param string $module_id
 * @param string $lang_name
 */
function scap_load_module_language($module_id, $lang_name)
{
	return include_once(SCAP_PATH_ROOT."{$module_id}/language/lang.{$lang_name}.inc.php");
}


/**
 * 将指定模块的inc路径加到当前环境中
 * @param string $module_id
 */
function scap_append_module_include_path($module_id)
{
	set_include_path(get_include_path().PATH_SEPARATOR.SCAP_PATH_ROOT.$module_id."/inc/");
}

/**
 * 判断当前脚本是否从命令行(cli)执行
 * 
 * @return bool true|false
 */
function scap_check_excute_from_cli()
{
    return (PHP_SAPI == 'cli');
}

/**
 * 创建系统账号
 *
 * @param string $login_id 登录id
 * @param string $display_name 显示名称
 * @param string $password 初始密码
 * 
 * @return bool
 */
function scap_create_account($login_id, $display_name, $password = '654321')
{
    $data_in = array();// 输入数据
    $data_save = array();// 入库数据
    $result = false;// 返回结果

    // 入库数据赋值
    $data_save['content']['a_c_login_id'] = $login_id;
    $data_save['content']['a_c_display_name'] = $display_name;
    $data_save['content']['a_s_id'] = scap_get_guid();// id内部生成
    $data_save['content']['a_s_password'] = scap_encrypt_password($password);
    $data_save['content']['a_s_create_time'] = time();
    $data_save['content']['a_s_create_id'] = $GLOBALS['scap']['auth']['account_s_id'];
    $data_save['content']['a_s_status'] = STAT_ACCOUNT_NORMAL;

    if (scap_db_get_row_count(NAME_T_SYS_ACCOUNTS, "a_c_login_id = '{$login_id}'") > 0)
    {
        throw new Exception("要创建的系统账号已存在。");
    }

    $ok = scap_entity::edit_row(NAME_T_SYS_ACCOUNTS, $data_save['content'], 'insert', '', 'module_basic');

    if ($ok === true)
    {
        $result = true;
    }
    else
    {
        throw new Exception("创建系统账号失败。");
    }

    return $result;
}

/**
 * 自动加载模块相关的类
 * - 支持命名空间 scap\module\[模块id]\[类名称]
 *
 * e.g \scap\module\g_account\entity_account
 *
 * @param string $class 类名称
 */
function scap_auto_load_module_class($class)
{
    $file = '';
    $class = ltrim($class, '\\');// scap\module\g_account\entity_account
    $structure = explode('\\', $class);// Array ( [0] => scap [1] => module [2] => g_account [3] => entity_account )
    
    if (count($structure) == 1)// 未使用namespace(向上兼容)
    {
        return @include_once("class.$class.inc.php");
    }
    elseif ($structure[0] != 'scap' || $structure[1] != 'module')
    {
        return;
    }

    return scap_load_module_class('module_'.$structure[2], $structure[3]);
}

/**
 * 自动加载核心类
 * 
 * @param string $class 类名称
 */
function scap_auto_load_core_class($class)
{
    if (preg_match("/^scap_/", $class) > 0)// 是系统核心类
	{
		@include_once(SCAP_PATH_CORE."core.class.$class.inc.php");
	}
}

/**
 * 为系统异常建立处理函数
 */
function scap_exception_handler($exception)
{
    $message = $exception->getMessage();

    if (scap_check_excute_from_cli())// cli调用
    {
        $output = <<<HTML
系统异常信息:{$message}\n
HTML;
    }
    else
    {
        $output = <<<HTML
<!doctype html> 
<html>
    <head> 
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <title>系统异常信息</title>
    </head>
    <body>
        <div style="text-align:left;"><span style="font-weight:bold;">系统信息：</span>{$message}</div>
    </body>
</html>
HTML;
    }
    echo $output;
}

/**
 * 根据指定权限位获取相关被授权的帐号列表
 * 
 * @param string $module_id 模块id
 * @param unsigned int $acl_bit 指定核准权限的对应位(右至左,0-31)
 * 
 * @return array 帐号的id列表 array('id1', 'id2', ...)
 */
function scap_get_account_list_by_acl($module_id, $acl_bit)
{
    $result = array();
    
    // 待检查的权限位数字(十进制)
    $acl_to_check = base_convert(str_pad('1', $acl_bit+1, '0'), 2, 10);
    
	$sql = <<<SQL
SELECT acl_s_account_id FROM scap_acl WHERE (
    acl_s_module = '{$module_id}' AND
    (acl_c_acl_code & {$acl_to_check}) > 0
)
SQL;
	$GLOBALS['scap']['db']->db_connect->SetFetchMode(ADODB_FETCH_ASSOC);
	$rs = $GLOBALS['scap']['db']->db_connect->Execute($sql);
	
	if ($rs)
	{
		$rs->MoveFirst();
		while(!$rs->EOF)
		{
			$result[] = $rs->fields['acl_s_account_id'];
			$rs->MoveNext();
		}
	}
	
	return $result;
}

/**
 * 将一个数组的所有值都去除前后空格
 * 
 * @deprecated
 * 
 * @param array $arr 要处理的一维数组
 * 
 * @return array 处理后的数组
 */
function trimarray($arr)
{
	$rtn = array();
	
	if (!is_array($arr))
	{
		return $rtn;
	}
	
	foreach($arr as $k => $v)
	{
		$rtn[$k] = trim($v);
	}
	
	return $rtn;
}

/**
 * 将一个url参数数组的值进行url编码
 * @deprecated
 * 
 * @param array $arr 要处理的参数数组
 * 
 * @return array 处理后的数组
 */
function urlencodearray($arr)
{
	$rtn = array();
	
	if (!is_array($arr))
	{
		return $rtn;
	}
	
	foreach($arr as $k => $v)
	{
		$rtn[$k] = urlencode($v);
	}
	
	return $rtn;
}

$vcl_no = 1;
define('VCL_TYPE_CUSTOM', $vcl_no++);// 定制
define('VCL_TYPE_NOT_EMPTY', $vcl_no++);// 内容非空(字符长度为0)
define('VCL_TYPE_NUMBER_F',  $vcl_no++);// 浮点型数字
define('VCL_TYPE_NUMBER_I',  $vcl_no++);// 整型数字
define('VCL_TYPE_TIME_D',  $vcl_no++);// 仅日期
define('VCL_TYPE_TIME_T',  $vcl_no++);// 仅时间(不含秒)
define('VCL_TYPE_TIME_DT',  $vcl_no++);// 日期+时间
define('VCL_TYPE_IP',  $vcl_no++);// IP地址
define('VCL_TYPE_EMAIL',  $vcl_no++);// email地址
define('VCL_TYPE_MOBILE',  $vcl_no++);// 手机号码
/**
 * 文本合法性检查函数
 * @deprecated
 * 
 * + 2008-9-8 by liqt
 * 
 * @param $content 待检查的文本内容
 * @param $type 合法性类型
 * @param $flag_allow_empty 是否允许文本为空(如果为true，则文本为空时恒返回true)
 * @param $regex 自定义的合法性正则表达式，仅在$type为 VCL_TYPE_CUSTOM 时有效
 * 
 * @return bool 返回为true|false
 */
function verify_content_legal($content, $type, $flag_allow_empty = false, $regex = "")
{
	$rtn = false;
	$data_in['regex'] = "";
	
	switch($type)
	{
		case VCL_TYPE_CUSTOM:
			$data_in['regex'] = $regex;
			break;
		case VCL_TYPE_NOT_EMPTY:
//			$data_in['regex'] = "/^.+$/";
			$rtn = (strlen($content) > 0);
			break;
		case VCL_TYPE_NUMBER_F://legal: .5 1.4 -10 120
			$data_in['regex'] = "/^[-+]?[0-9]*\.?[0-9]+$/";
			break;
		case VCL_TYPE_NUMBER_I://legal: -18 9 +9
			$data_in['regex'] = "/^[-+]?[0-9]+$/";
			break;
		case VCL_TYPE_TIME_D://legal: 2008-02-29
			$data_in['regex'] = "/^20\d{2}(-|\/)((0[1-9])|(1[0-2]))(-|\/)((0[1-9])|([1-2][0-9])|(3[0-1]))$/";
			break;
		case VCL_TYPE_TIME_T://legal: 09:29 illegal: 9:29
			$data_in['regex'] = "/^(([0-1][0-9])|(2[0-3])):([0-5][0-9])$/";
			break;
		case VCL_TYPE_TIME_DT://legal: 2008-02-29 19:20 illegal: 2008-2-29 19:20 2008-02-29 9:20
			$data_in['regex'] = "/^20\d{2}(-|\/)((0[1-9])|(1[0-2]))(-|\/)((0[1-9])|([1-2][0-9])|(3[0-1]))(\s)(([0-1][0-9])|(2[0-3])):([0-5][0-9])$/";
			break;
		case VCL_TYPE_IP:
			$data_in['regex'] = "/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/";
			break;
		case VCL_TYPE_EMAIL:
			$data_in['regex'] = "/^[A-Za-z0-9._%+-]+@\S+$/";
			break;
		case VCL_TYPE_MOBILE:
//			$data_in['regex'] = "/^(13|15|18)\d{9}$/";
			$data_in['regex'] = "/^(1)\d{10}$/";
			break;
	}
	
	// 是否允许文本为空(如果为true，则文本为空时恒返回true)
	if ($flag_allow_empty && $type != VCL_TYPE_NOT_EMPTY && strlen($content) == 0)
	{
		$rtn = true;
	}
	elseif (!empty($data_in['regex']))
	{
		$rtn = preg_match($data_in['regex'], $content);// 用正则进行文本格式检查
	}
	
	return $rtn;
}
?>