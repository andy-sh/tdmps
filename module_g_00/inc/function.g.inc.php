<?php
/**
 * description: g模块函数库
 * create time: 2008-12-15-下午03:54:41
 * @version $Id: function.g.inc.php 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao
 */

/**
 * 插入应用日志
 *
 * @author Liqt
 *
 * @param string $entity_id 所属实体id
 * @param string $object_id 对象ID
 * @param int $log_type 操作类型
 * @param string $comment 备注说明
 * @param int $sn 操作的序号,如果为0,则默认在该类型操作的最大序号上自动+1作为新操作的序号
 * @param string|int $op_time 日志记录时间,默认为当前系统时间
 * @param string $op_id 记录操作系统的ID,默认为当前登录帐号ID
 *
 * @return bool true|false
 */
function g_insert_app_log($entity_id, $object_id, $log_type, $comment='', $sn = NULL, $op_time='', $op_id = '')
{
	//--------变量定义及声明[start]--------
	$data_save = array();
	//--------变量定义及声明[end]--------

	$data_save['content']['al_object_id'] = $object_id;
	$data_save['content']['al_type'] = $log_type;
	$data_save['content']['al_entity_id'] = $entity_id;
	$data_save['content']['al_comment'] = $comment;
	$data_save['content']['al_client_ip'] = $_SERVER['REMOTE_ADDR'];
	$data_save['content']['al_user_agent'] = $_SERVER['HTTP_USER_AGENT'];

	if (is_null($sn)) // 获取sn当前最大值
	{
		$max_sn = scap_db_get_max_value('g_app_log', 'al_sn', "al_object_id = '{$object_id}' AND al_type = {$log_type}");

		if ($max_sn === false)
		{
			$str_info = sprintf('日志操作失败.错误信息：%s', scap_db_error_msg());
			scap_insert_sys_info('tip', $str_info);
			return false;
		}
		$data_save['content']['al_sn'] = $max_sn + 1;
	}
	else
	{
		$data_save['content']['al_sn'] = $sn;
	}

	$data_save['content']['al_time'] = empty($op_time) ? time() : $op_time;
	$data_save['content']['al_operator_id'] = empty($op_id) ? $GLOBALS['scap']['auth']['account_s_id'] : $op_id;

	$data_save['where'] = "al_object_id = '{$data_save['content']['al_object_id']}' AND al_type = {$data_save['content']['al_type']} AND al_sn = {$data_save['content']['al_sn']}";

	if (!is_null($sn))// 如果指定了sn则先删除之
	{
		$GLOBALS['scap']['db']->db_connect->Execute("DELETE FROM g_app_log WHERE ({$data_save['where']})");
	}

	if($GLOBALS['scap']['db']->db_connect->AutoExecute('g_app_log', $data_save['content'], 'INSERT') === false)
	{
		$str_info = sprintf('日志操作失败.错误信息：%s', scap_db_error_msg());
		scap_insert_sys_info('tip', $str_info);
		return false;
	}

	return true;
}

/**
 * 读取应用日志
 *
 * @author Liqt
 *
 * @param string $object_id 对象ID
 * @param int $log_type 操作类型,默认为NULL（获取所有日志）
 * @param int $log_sn 日志序号,默认为NULL（获取所有序号日志）
 * @param DESC|ASC $sn_sort 返回数据的sn顺序,默认为降序排列
 *
 * @return array 日志数组
 */
function g_get_app_log($object_id, $log_type = NULL, $log_sn = NULL, $sn_sort = 'DESC')
{
	//--------变量定义及声明[start]--------
	$data_db = array();
	//--------变量定义及声明[end]--------

	$data_db['content'] = array();
	$data_db['sql'] = "SELECT * FROM g_app_log WHERE al_object_id = '$object_id'";
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
 * 插入实体对象的状态数据
 *
 * @author Liqt
 *
 * @param string $entity_id
 * @param string $status_type
 * @param string $object_id
 * @param int $status
 * @param string $comment
 * @param int $sn 默认为NULL，如果指定，则覆盖原sn的数据
 *
 * @return boolean
 */
function g_insert_object_status($entity_id, $status_type, $object_id, $status, $comment = '', $sn = NULL)
{
	//--------变量定义及声明[start]--------
	$data_save = array();
	//--------变量定义及声明[end]--------

	$data_save['content']['os_object_id'] = $object_id;
	$data_save['content']['os_status_type'] = $status_type;
	$data_save['content']['os_entity_id'] = $entity_id;
	$data_save['content']['os_status'] = $status;
	$data_save['content']['os_trigger_time'] = time();
	$data_save['content']['os_trigger_id'] = $GLOBALS['scap']['auth']['account_s_id'];
	$data_save['content']['os_comment'] = $comment;

	if (is_null($sn))
	{
		$max_sn = scap_db_get_max_value('g_object_status', 'os_sn', "os_object_id = '{$data_save['content']['os_object_id']}' AND os_status_type = {$data_save['content']['os_status_type']}");

		if ($max_sn === false)
		{
			$str_info = sprintf('插入对象状态条目失败。错误信息：%s', scap_db_error_msg());
			scap_insert_sys_info('tip', $str_info);
			return false;
		}
		$data_save['content']['os_sn'] = $max_sn + 1;
	}
	else // 如果指定了sn则先删除之
	{
		$data_save['content']['os_sn'] = $sn;
		$data_save['where'] = "os_object_id = '{$data_save['content']['os_object_id']}' AND os_status_type = {$data_save['content']['os_status_type']} AND os_sn = {$data_save['content']['os_sn']}";
		$GLOBALS['scap']['db']->db_connect->Execute("DELETE FROM g_object_status WHERE ({$data_save['where']})");
	}

	if($GLOBALS['scap']['db']->db_connect->AutoExecute('g_object_status', $data_save['content'], 'INSERT') === false)
	{
		$str_info = sprintf('插入对象状态条目失败。错误信息：%s', scap_db_error_msg());
		scap_insert_sys_info('tip', $str_info);
		return false;
	}

	return true;
}

/**
 * 获取对象最新的状态值
 *
 * @author Liqt
 *
 * @param string $object_id
 * @param string $status_type
 *
 * @return int 最近状态值
 */
function g_get_last_object_status_value($object_id, $status_type)
{
	//--------变量定义及声明[start]--------
	$data_db	= array();	// 数据库相关数据
	$rtn = NULL;
	//--------变量定义及声明[end]--------

	$data_db['sql'] = <<<SQL
SELECT os_status 
FROM g_object_status 
WHERE (
	os_object_id = '{$object_id}' 
	AND os_status_type = {$status_type} 
	AND os_sn = (SELECT MAX(os_sn) FROM g_object_status WHERE (os_object_id = '{$object_id}' AND os_status_type = {$status_type}))
)
SQL;

	$GLOBALS['scap']['db']->db_connect->SetFetchMode(ADODB_FETCH_ASSOC);
	$rs = $GLOBALS['scap']['db']->db_connect->Execute($data_db['sql']);
	if ($rs)
	{
		$rs->MoveFirst();
		$rtn = $rs->fields['os_status'];
	}

	return $rtn;
}

/**
 * 构造指定对象日志的显示链接
 *
 * @uses 需调用ui_basic->load_greybox_file()
 *
 * @author Liqt
 *
 * @param string $object_id 对象id
 * @param string $link_name 链接名称
 * @param string $height 窗口高度(用box方式)
 * @param string $width 窗口宽度(用box方式)
 * @param string $flag_box 是否用box方式，默认为true
 *
 * @return string
 */
function g_create_object_log_link($object_id, $link_name, $height = 400, $width = 800, $flag_box = true)
{
	$log_link = "<a href=\"";
	$log_link.= scap_get_url(array('module' => 'module_g_00', 'class' => 'ui', 'method' => 'view_object_log'), array('search[object_id]' => $object_id));
	$log_link.= "\"";
	$log_link.= "class='scap_button'";
	$log_link.= $flag_box ? " onclick=\"return GB_showCenter('日志查询', this.href, $height, $width)\"" : " ";
	$log_link.= ">$link_name</a>";

	return $log_link;
}

/**
 * 将当前页面重定向到反馈信息页面
 *
 * @author Liqt
 *
 * @param string $str_info 自定义反馈信息
 */
function g_redirect_feedback_info_page($str_info = '')
{
	scap_redirect_url(array('module' => 'module_g_00', 'class' => 'ui', 'method' => 'feedback_info'), array('info' => urlencode($str_info)));
}

/**
 * 查询g_object_status中每个对象的状态，本函数生成的是sql语句的一部分
 *
 *
 *
 * @param string $object_field 主表中的和g_object_status 表中关联的id 比如EM.em_i
 * @param string $entity_id 实体id
 * @param array $search_status 需要查询的状态类型,array('ST1' =>TYPE_STATUS_EVA_MAIN, 'ST2'=>TYPE_STATUS_EVA_CONCLUSION)
 * @param string $alias_left_join 	left join 查询结果的的别名
 */
function g_left_join_object_status($object_field_name,$entity_id,$search_status_type,$alias_left_join)
{
	//--------变量定义及声明[start]--------
	$data_db	= array();	// 数据库相关数据
	//--------变量定义及声明[end]--------
	$data_db['where'] = '';
	$data_db['where'] .= empty($entity_id) ? '' : " AND os_entity_id = '{$entity_id}'";

	foreach($search_status_type as $k => $v)
	{
		$data_db['select'] .= ",MAX(IF(os_status_type=$v,os_status,0)) ".$k;
	}
	$sql = "
			LEFT JOIN
			(
				SELECT  os_object_id".$data_db['select']."
					 FROM
					(	
						SELECT OS.os_object_id ,OS.os_status_type,OS.os_sn,OS.os_status FROM g_object_status OS 
						INNER JOIN 
						(	
							SELECT  os_object_id ,os_status_type, MAX(os_sn) os_sn from g_object_status 
							WHERE 1=1 {$data_db['where']}
							GROUP BY os_object_id,os_status_type
						)AS OS1
						ON OS1.os_object_id=OS.os_object_id AND OS1.os_status_type = OS.os_status_type  AND OS1.os_sn = OS.os_sn  
					) 	
					AS OBJECT_STATUS  GROUP BY os_object_id 
			)  AS  ".$alias_left_join." 
			ON ".$alias_left_join.".os_object_id = ".$object_field_name." 
			";

	return $sql;
}

function g_get_cloginid_from_asid($asid)
{
	$rtn = '';
	$data_db['sql'] = "SELECT a_c_login_id FROM scap_accounts WHERE (a_s_id = '{$asid}')";
	$GLOBALS['scap']['db']->db_connect->SetFetchMode(ADODB_FETCH_ASSOC);
	$rs = $GLOBALS['scap']['db']->db_connect->Execute($data_db['sql']);
	if ($rs)
	{
		$rs->MoveFirst();
		$rtn = $rs->fields['a_c_login_id'];
	}
	return $rtn;
}

function g_get_asid_from_cloginid($cloginid)
{
	$rtn = '';
	$data_db['sql'] = "SELECT a_s_id FROM scap_accounts WHERE (a_c_login_id = '{$cloginid}')";
	$GLOBALS['scap']['db']->db_connect->SetFetchMode(ADODB_FETCH_ASSOC);
	$rs = $GLOBALS['scap']['db']->db_connect->Execute($data_db['sql']);
	if ($rs)
	{
		$rs->MoveFirst();
		$rtn = $rs->fields['a_s_id'];
	}
	return $rtn;
}
/**
 * 获取实体对象关联的所有类别
 *
 * @author liqt
 *
 * @param string $object_id 对象id
 *
 * @return array 输出数组格式为array('ocl_sn' => 'ocl_category_id', ...)
 */
function g_get_object_category_list($object_id)
{
	//--------变量定义及声明[start]--------
	$data_db	= array();	// 数据库相关数据
	$list = array();
	//--------变量定义及声明[end]--------
	$data_db['where'] = "AND ocl_object_id = '{$object_id}'";

	$data_db['sql'] = <<<SQL
SELECT * FROM g_object_category_link
WHERE (1=1 {$data_db['where']})
SQL;

	$GLOBALS['scap']['db']->db_connect->SetFetchMode(ADODB_FETCH_ASSOC);
	$rs = $GLOBALS['scap']['db']->db_connect->Execute($data_db['sql']);

	if ($rs)
	{
		$rs->MoveFirst();
		while(!$rs->EOF)
		{
			$list[$rs->fields['ocl_sn']] = array('ocl_category_id' => $rs->fields['ocl_category_id'], 'ocl_name' => $rs->fields['ocl_name']);
			$rs->MoveNext();
		}
	}

	return $list;
}

/**
 * 插入实体对象的类别数据
 *
 * @param string $entity_id 实体id
 * @param string $object_id 实体对象id
 * @param string $category_id 类别id
 * @param string $category_name 类别名
 * @param int $sn 默认为NULL，如果指定，则覆盖原sn的数据
 *
 * @return boolean
 */
function g_insert_object_category($entity_id, $object_id, $category_id, $category_name = NULL, $sn = NULL)
{
	//--------变量定义及声明[start]--------
	$data_save = array();
	//--------变量定义及声明[end]--------

	$data_save['content']['ocl_object_id'] = $object_id;
	$data_save['content']['ocl_entity_id'] = $entity_id;
	$data_save['content']['ocl_category_id'] = $category_id;
	if (!is_null($category_name))
	{
		$data_save['content']['ocl_name'] = $category_name;
	}

	if (is_null($sn))
	{
		$data_save['type'] = 'INSERT';
		$max_sn = scap_db_get_max_value('g_object_category_link', 'ocl_sn', "ocl_object_id = '{$data_save['content']['ocl_object_id']}'");

		if ($max_sn === false)
		{
			$str_info = sprintf('插入对象类别关联失败。错误信息：%s', scap_db_error_msg());
			throw new Exception($str_info);
		}
		$data_save['content']['ocl_sn'] = $max_sn + 1;
		$data_save['content']['ocl_name'] = $category_name;

	}
	else
	{
		$data_save['content']['ocl_sn'] = $sn;
		$data_save['type'] = 'UPDATE';
		$data_save['where'] = "ocl_object_id = '{$data_save['content']['ocl_object_id']}' AND ocl_sn = {$data_save['content']['ocl_sn']}";
	}

	if (scap_entity::edit_row("g_object_category_link", $data_save['content'], $data_save['type'], $data_save['where'], 'module_g_00') === false)
	{
		$str_info = sprintf('插入对象类别关联失败。错误信息：%s', scap_db_error_msg());
		throw new Exception($str_info);
	}

	return true;
}

/**
 * 插入实体对象的时间节点
 *
 * @author Liqt
 *
 * @param string $entity_id 实体ID
 * @param string $object_id 对象id
 * @param string $time 日期
 * @param string $name 时间节点名称
 * @param string $category 时间节点类别
 * @param int $sn 默认为NULL，如果指定，则覆盖原sn的数据
 *
 * @return boolean
 */
function g_insert_object_time_node($entity_id, $object_id, $time, $name, $category, $sn = NULL)
{
	//--------变量定义及声明[start]--------
	$data_save = array();
	//--------变量定义及声明[end]--------

	$data_save['content']['otn_object_id'] = $object_id;
	$data_save['content']['otn_entity_id'] = $entity_id;
	$data_save['content']['otn_name'] = $name;
	$data_save['content']['otn_category'] = $category;
	$data_save['content']['otn_time'] = $time;

	if (is_null($sn))
	{
		$max_sn = scap_db_get_max_value('g_object_time_node', 'otn_sn', "otn_object_id = '{$data_save['content']['otn_object_id']}'");

		if ($max_sn === false)
		{
			$str_info = sprintf('插入对象时间条目失败。错误信息：%s', scap_db_error_msg());
			scap_insert_sys_info('tip', $str_info);
			return false;
		}
		$data_save['content']['otn_sn'] = $max_sn + 1;
	}
	else // 如果指定了sn则先删除之
	{
		$data_save['content']['otn_sn'] = $sn;
		$data_save['where'] = "otn_object_id = '{$data_save['content']['otn_object_id']}' AND otn_sn = {$data_save['content']['otn_sn']}";
		$GLOBALS['scap']['db']->db_connect->Execute("DELETE FROM g_object_time_node WHERE ({$data_save['where']})");
	}

	if($GLOBALS['scap']['db']->db_connect->AutoExecute('g_object_time_node', $data_save['content'], 'INSERT') === false)
	{
		$str_info = sprintf('插入对象时间条目失败。错误信息：%s', scap_db_error_msg());
		scap_insert_sys_info('tip', $str_info);
		return false;
	}

	return true;
}

/**
 * 获取实体对象关联的时间节点列表
 *
 * @author Liqt
 *
 * @param string $object_id 对象id
 * @param string $where 附加查询条件(不含关键字where)
 *
 * @return array 输出数组格式为array('otn_sn' => array(...), ...)
 */
function g_get_object_time_node_list($object_id, $where = '')
{
	//--------变量定义及声明[start]--------
	$data_db	= array();	// 数据库相关数据
	$list = array();
	//--------变量定义及声明[end]--------

	$sql = "SELECT * FROM g_object_time_node WHERE (otn_object_id = '{$object_id}' {$where}) ORDER BY otn_sn ASC";

	$GLOBALS['scap']['db']->db_connect->SetFetchMode(ADODB_FETCH_ASSOC);
	$rs = $GLOBALS['scap']['db']->db_connect->Execute($sql);

	if ($rs)
	{
		$rs->MoveFirst();
		while(!$rs->EOF)
		{
			$list[$rs->fields['otn_sn']] = $rs->fields;
			$rs->MoveNext();
		}
	}

	return $list;
}
?>