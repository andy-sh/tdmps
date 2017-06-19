<?php
/**
 * description: tables info
 * create time: 2006-11-14 15:15:56
 * @version $Id: db.tables.inc.php 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao(leeqintao@gmail.com)
 */

// [数据表][系统帐户表]
$scap_db_tables['scap_accounts'] = array(
	'a_s_id' => array('a_s_id', 'C', '40', 'KEY'), // 内部唯一id
	'a_s_create_time' => array('a_s_create_time', 'T'),
	'a_s_create_id' => array('a_s_create_id', 'C', '40'),
	'a_s_lastedit_time' => array('a_s_lastedit_time', 'T'),
	'a_s_lastedit_id' => array('a_s_lastedit_id', 'C', '40'),
	'a_c_login_id' => array('a_c_login_id', 'C', '100'), // 用户登录用的唯一id
	'a_s_password' => array('a_s_password', 'C', '50'), // 登录密码
	'a_c_display_name' => array('a_c_display_name', 'C', '100'), // 用户显示的名称
	'a_s_status' => array('a_s_status', 'I1', 'UNSIGNED', 'DEFAULT' => 0), // 帐户当前状态
	'a_c_note' => array('a_c_note', 'X'), // 备注信息
);
$scap_db_table_options['scap_accounts'] = array('mysql' => 'TYPE=MyISAM', 'mysqlt' => 'TYPE=InnoDB');

// [数据表][配置数据表]
$scap_db_tables['scap_config'] = array(
	'c_s_module' => array('c_s_module', 'C', '50', 'KEY'), // 对应的模块代码
	'c_s_key' => array('c_s_key', 'C', '50', 'KEY'), // 配置数据的键名代码
	'c_c_value' => array('c_c_value', 'X'), // 配置的数据内容
);
$scap_db_table_options['scap_config'] = array('mysql' => 'TYPE=MyISAM', 'mysqlt' => 'TYPE=InnoDB');

// [数据表][权限数据表]
$scap_db_tables['scap_acl'] = array(
	'acl_s_module' => array('acl_s_module', 'C', '50', 'KEY'), // 对应的模块id
	'acl_s_account_id' => array('acl_s_account_id', 'C', '40', 'KEY'), // 帐户内部id
	'acl_c_acl_code' => array('acl_c_acl_code', 'I8', 'UNSIGNED', 'DEFAULT' => 0), // 权限数据
);
$scap_db_table_options['scap_acl'] = array('mysql' => 'TYPE=MyISAM', 'mysqlt' => 'TYPE=InnoDB');

// [数据表][模块数据表]
$scap_db_tables['scap_module_list'] = array(
	'ml_s_id' => array('ml_s_id', 'C', '50', 'KEY'), // 对应的模块id
	'ml_s_status' => array('ml_s_status', 'I1', 'UNSIGNED', 'DEFAULT' => 0), // 模块当前状态
	'ml_c_order' => array('ml_c_order', 'I1', 'UNSIGNED', 'DEFAULT' => 0), // 模块显示顺序
	'ml_c_version' => array('ml_c_version', 'C', '50'), // 模块版本
);
$scap_db_table_options['scap_module_list'] = array('mysql' => 'TYPE=MyISAM', 'mysqlt' => 'TYPE=InnoDB');

// [数据表][系统日志表]
$scap_db_tables['scap_log'] = array(
	'l_id' => array('l_id', 'I', 'AUTO', 'KEY'), // 日志序列号
	'l_time' => array('l_time', 'T'), // 记录时间
	'l_module' => array('l_module', 'C', '50'), // 对应的模块id
	'l_operator_type' => array('l_operator_type', 'I1', 'UNSIGNED', 'DEFAULT' => 0), // 操作者类型
	'l_operator_info' => array('l_operator_info', 'C', '50'), // 操作者信息
	'l_from' => array('l_from', 'C', '50'), // 操作来源（一般是IP地址）
	'l_act_type' => array('l_act_type', 'I1', 'UNSIGNED', 'DEFAULT' => 0), // 动作类型
	'l_act_object_type' => array('l_act_object_type', 'I1', 'UNSIGNED', 'DEFAULT' => 0), // 动作对象（每个模块单独定义）类型
	'l_act_object_info' => array('l_act_object_info', 'C', '50'), // 对象信息
	'l_act_result' => array('l_act_result', 'I1', 'UNSIGNED', 'DEFAULT' => 0), // 动作结果
	'l_note' => array('l_note', 'C', '50'), // 附加说明
);
$scap_db_table_options['scap_log'] = array('mysql' => 'TYPE=MyISAM', 'mysqlt' => 'TYPE=InnoDB');

// [数据表][应用日志表]
$scap_db_tables['scap_app_log'] = array(
	'al_object_id' => array('al_object_id', 'C', '40', 'KEY'),	//应用日志内部ID
	'al_type' => array('al_type', 'I2', 'UNSIGNED', 'DEFAULT' => 0, 'KEY'),	//所代表的类型,如是录入时间/最近编辑时间等.
	'al_sn' => array('al_sn', 'I2', 'UNSIGNED','DEFAULT' => 0, 'KEY'),	//序号
	'al_datetime' => array('al_datetime', 'T'),	//时间值
	'al_operator_id' => array('al_operator_id', 'C', '40'),	//执行操作的系统帐户ID
);
$scap_db_table_options['scap_app_log'] = array('mysql' => 'TYPE=MyISAM', 'mysqlt' => 'TYPE=InnoDB');
?>