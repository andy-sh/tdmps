<?php
/**
 * description: 基础模块定义文件
 * create time: 2006-8-7 11:32:49
 * @version $Id: define.inc.php 164 2014-02-17 04:40:21Z liqt $
 * @author LiQintao(leeqintao@gmail.com)
 */
// [定义系统消息]
define('SCAP_MSG_UNKNOWN_ERROR', 0); // 未知错误
define('SCAP_MSG_SUCCESS', 1); // 成功消息

$i = 2;
define('SCAP_MSG_ACCOUNT_NOEXIST', $i++); // 帐户不存在
define('SCAP_MSG_PWD_ERROR', $i++); // 密码错误
define('SCAP_MSG_ACCOUNT_STOP', $i++); // 帐户被停用
define('SCAP_MSG_LDAP_AUTH_ERROR', $i++); // LDAP认证错误
define('SCAP_MSG_LDAP_CONNECT_ERROR', $i++); // LDAP连接错误

define('SCAP_MSG_ACCESS_NO_PUBLIC_METHOD', $i++); // 非授权方法访问
define('SCAP_MSG_ACCESS_UNREGISTER_MODULE', $i++); // 非注册模块访问
define('SCAP_MSG_ACCESS_NO_NORMAL_MODULE', $i++); // 停用模块访问
define('SCAP_MSG_ACCESS_NO_EXIST_CLASS', $i++); // 不存在的模块类的访问
define('SCAP_MSG_ACCESS_ILLEGAL_CLASS', $i++); // 不合法模块类的访问
define('SCAP_MSG_ACCESS_NO_ACCESS_METHOD', $i++); // 未授权方法的访问
define('SCAP_MSG_ACCESS_ILLEGAL', $i++); // 不合法的访问

define('SCAP_MSG_DATA_NO_EXIST', $i++); // 所访问的数据不存在
define('SCAP_MSG_APP_ERROR', $i++); // 应用本身错误

// [系统模块状态定义]
define('STAT_MODULE_NULL', 0); // 模块状态:无效
define('STAT_MODULE_STOP', 1); // 模块状态:停用
define('STAT_MODULE_NORMAL', 2); // 模块状态:正常
$GLOBALS['scap']['text']['module_basic']['stat_module'] = array(
		STAT_MODULE_NULL => '-',
		STAT_MODULE_STOP => '停用',
		STAT_MODULE_NORMAL => '正常',
	);

// [系统模块属性定义]
define('PROP_MODULE_BACK', 1); // 模块属性:后台模块
define('PROP_MODULE_FRONT', 2); // 模块属性:前台模块
$GLOBALS['scap']['text']['module_basic']['prop_module'] = array(
		PROP_MODULE_BACK => '后台',
		PROP_MODULE_FRONT => '前台',
	);

// [模块所需db表名称定义]
define('NAME_T_SYS_CONFIG', 'scap_config'); // 系统配置数据表名称
define('NAME_T_SYS_ACCOUNTS', 'scap_accounts'); // 系统帐户数据表名称
define('NAME_T_SYS_ACL', 'scap_acl'); // 系统权限数据表名称
define('NAME_T_SYS_ML', 'scap_module_list'); // 系统模块数据表名称
define('NAME_T_SYS_LOG', 'scap_log'); // 系统日志数据表名称

// [系统帐户状态定义]
define('STAT_ACCOUNT_STOP', 1); // 帐户状态:停用
define('STAT_ACCOUNT_NORMAL', 2); // 帐户状态:正常

// [常用操作状态定义]
define('STAT_ACT_CREATE', 1); // 创建工作状态
define('STAT_ACT_EDIT', 2); // 编辑工作状态
define('STAT_ACT_VIEW', 3); // 查看工作状态
define('STAT_ACT_REMOVE', 4); // 删除工作状态
define('STAT_ACT_PRINT', 5); //打印数据状态

// [参数设置界面类型定义]
define('TYPE_CONFIG_INPUT_TEXT', 1); // 输入框类型
define('TYPE_CONFIG_TEXTAREA', 2); // 文本框类型
define('TYPE_CONFIG_RICH_TEXT', 3); // 输入框类型
define('TYPE_CONFIG_SELECT', 4); // 选择栏类型

// [系统认证方式定义]
define('TYPE_AUTH_DB', 1); // 数据库表认证方式
define('TYPE_AUTH_LDAP', 2); // LDAP认证方式

$GLOBALS['scap']['auth_type_list'] = array(
		TYPE_AUTH_DB => '数据库认证',
		TYPE_AUTH_LDAP => 'LDAP认证',
	);

/**
 * [本模块信息定义]
 */
$GLOBALS['scap']['module']['module_basic'] = array(
		'version' => '1.2.6.tdmps.1',
		'property' => PROP_MODULE_BACK,
		'comment' => 'SCAP系统基础后台模块',
		'tables' => array(
							'scap_accounts',
							'scap_config',
							'scap_acl',
							'scap_module_list',
							'scap_log',
							'scap_app_log', // 2008-03-28 add
				),
	);

/**
 * [权限定义]
 * 共32位可用,系统使用第1位[0](从右到左)作为模块的总权限标志.
 */ 
define('ACL_BITS_LENGTH', 64); // ACL位数长度[64]
define('ACL_BIT_MODULE', 0); // 系统保留的模块总权限标志位

/**
 * [日志基本信息定义]
 */
// [日志:操作者类型定义]
define('TYPE_LOG_OP_UNKNOWN', 0); // 操作者类型:未知
define('TYPE_LOG_OP_ACCOUNT', 1); // 操作者类型:系统帐号
define('TYPE_LOG_OP_SYS', 2); // 操作者类型:系统本身
$GLOBALS['scap']['text']['module_basic']['type_log_op'] = array(
		TYPE_LOG_OP_UNKNOWN => '未知',
		TYPE_LOG_OP_ACCOUNT => '系统帐号',
		TYPE_LOG_OP_SYS=> '系统本身',
	);
	
	
// [日志:动作结果定义]
define('TYPE_LOG_RESULT_UNKNOWN', 0); // 动作结果:未知
define('TYPE_LOG_RESULT_SUCCESS', 1); // 动作结果:成功
define('TYPE_LOG_RESULT_FAIL', 2); // 动作结果:失败
define('TYPE_LOG_RESULT_NO', 3); // 动作结果:无返回结果
$GLOBALS['scap']['text']['module_basic']['type_log_result'] = array(
		TYPE_LOG_RESULT_UNKNOWN => '未知',
		TYPE_LOG_RESULT_SUCCESS => '成功',
		TYPE_LOG_RESULT_FAIL => '失败',
		TYPE_LOG_RESULT_NO => '无返回结果',
	);
	
// [日志:动作类型定义]
define('TYPE_LOG_ACT_UNKNOWN', 0); // 动作类型:未知
define('TYPE_LOG_ACT_LOGIN', 1); // 动作类型:登录
define('TYPE_LOG_ACT_LOGOUT', 2); // 动作类型:登出
define('TYPE_LOG_ACT_CREATE', 3); // 动作类型:创建
define('TYPE_LOG_ACT_UPDATE', 4); // 动作类型:更新
define('TYPE_LOG_ACT_REMOVE', 5); // 动作类型:删除
define('TYPE_LOG_ACT_VIEW', 6); // 动作类型:查看
$GLOBALS['scap']['text']['module_basic']['type_log_act'] = array(
		TYPE_LOG_ACT_UNKNOWN => '未知',
		TYPE_LOG_ACT_LOGIN => '登录',
		TYPE_LOG_ACT_CREATE => '创建',
		TYPE_LOG_ACT_LOGIN => '登录',
		TYPE_LOG_ACT_LOGOUT => '登出',
		TYPE_LOG_ACT_UPDATE => '更新',
		TYPE_LOG_ACT_REMOVE => '删除',
		TYPE_LOG_ACT_VIEW => '查看',
	);




// [日志:动作对象定义]
define('TYPE_LOG_ACT_OBJECT_UNKNOWN', 0); // 动作对象:未知
define('TYPE_LOG_ACT_OBJECT_SYS', 1); // 动作对象:系统
$GLOBALS['scap']['text']['module_basic']['type_log_act_object'] = array(
		TYPE_LOG_ACT_OBJECT_UNKNOWN => '未知',
		TYPE_LOG_ACT_OBJECT_SYS => '系统',
	);


?>