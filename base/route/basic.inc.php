<?php
/**
 * description: 系统调用基本定义文件
 * create time: 2006-10-18 19:03:33
 * @version $Id: basic.inc.php 94 2013-04-10 02:17:50Z liqt $
 * @author LiQintao(leeqintao@gmail.com)
 */
use scap\module\g_tool\config;

//[绝对路径](PATH)
define('SCAP_PATH_ROOT', dirname(dirname(dirname(__FILE__))).'/');	// 根路径
define('SCAP_PATH_CONFIG', SCAP_PATH_ROOT.'config/');
define('SCAP_PATH_BASE', SCAP_PATH_ROOT.'base/');	//
define('SCAP_PATH_LIBRARY', SCAP_PATH_BASE.'library/');	//
define('SCAP_PATH_CORE', SCAP_PATH_BASE.'core/');	//
define('SCAP_PATH_TPL', SCAP_PATH_ROOT.'module_basic/templates/');// 系统模板路径
define('SCAP_PATH_BASIC', SCAP_PATH_ROOT.'module_basic/');// 系统基础模板路径
define('SCAP_RELATIVE_PATH_LIBRARY', 'base/library/'); // 系统库的相对路径

chdir(SCAP_PATH_ROOT);// 当前路径切换到根下

require(SCAP_PATH_ROOT.'module_basic/inc/define.inc.php');

// 设置系统包含路径
set_include_path(get_include_path().PATH_SEPARATOR.SCAP_PATH_CORE);

include_once(SCAP_PATH_CORE.'core.function.inc.php');		// scap相关函数库

// 自动加载机制
spl_autoload_register('scap_auto_load_core_class');
spl_autoload_register('scap_auto_load_module_class');

set_exception_handler('scap_exception_handler');

//[global variable]
/**
 * @var object 当前调用的类实例
 */
$GLOBALS['scap']['handle_current_class'] = NULL;

/**
 * @var array 认证相关信息
 */
$GLOBALS['scap']['auth'] = array(
								'sid' => '',				// 当前session ID
								'account_id' => '',			// 当前帐户的登录id
								'account_s_id' => '',		// 当前帐户的系统id
								'ip' => ''					// 当前帐户的登录ip
							);

/**
 * @var array 系统相关信息
 */
$GLOBALS['scap']['info'] = array(
								'site_url' => '',			// 系统url位置
								'current_lang' => '',		// 系统当前使用的语言代码
								'current_module_id' => '',	// 当前调用的模块id
								'current_class' => '',		// 当前调用的类名称
								'current_method' => '',		// 当前调用的方法名称
							);

config::autoload_files(SCAP_PATH_CONFIG);// 加载系统配置

$GLOBALS['scap']['info']['site_url'] = config::get('scap', 'project_url');// 保持向前兼容

/**
 * @var object 系统数据库实例
 */
$GLOBALS['scap']['db_connect'] = config::get('db_connect', 'default');
$GLOBALS['scap']['db']= new scap_db($GLOBALS['scap']['db_connect']['host'], $GLOBALS['scap']['db_connect']['user'], $GLOBALS['scap']['db_connect']['password'], $GLOBALS['scap']['db_connect']['database'], $GLOBALS['scap']['db_connect']['type']);

/**
 * @var array 系统中的自定义配置数据信息
 */
$GLOBALS['scap']['custom_value'] = array();

/**
 * @var array 系统中的自定义权限数据信息
 */
$GLOBALS['scap']['custom_acl'] = array();

/**
 * @var array 系统中的模块信息
 */
$GLOBALS['scap']['module'] = array();

/**
 * @var array 系统当前所执行事件信息
 */
$GLOBALS['scap']['event'] = array('name' => '', 'result' => NULL);

?>