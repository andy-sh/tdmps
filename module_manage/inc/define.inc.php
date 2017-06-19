<?php
/**
 * description:
 * create time: 2006-11-25 11:45:00
 * @version $Id: define.inc.php 145 2013-08-22 05:43:43Z liqt $
 * @author LiQintao(leeqintao@gmail.com)
 */

/**
 * [本模块信息定义]
 */
$GLOBALS['scap']['module']['module_manage'] = array(
		'version' => '1.3.8',
		'property' => PROP_MODULE_FRONT,
		'comment' => '系统管理模块',
		'tables' => array(),
	);

/**
 * [应用配置数据定义]
 * 
 * (textarea example) array('config_key' => 'welcome', 'config_module' => 'module_manage', 'config_cat' => "站点属性", 'config_name' => "欢迎信息", 'default_value' => '欢迎进入SCAP系统', 'set_type' => 'textarea', 'parameter' => array('cols' => 82, 'rows' => 6, 'wrap' => 'soft')),
 */
$GLOBALS['scap']['custom_value']['module_manage'] = array(
		array('config_key' => 'site_name', 'config_module' => 'module_manage', 'config_cat' => "站点属性", 'config_name' => "站点标题名称", 'default_value' => 'SCAP系统', 'set_type' => TYPE_CONFIG_INPUT_TEXT, 'parameter' => array('size' => '80', 'maxlength' => '50')),
		array('config_key' => 'default_url', 'config_module' => 'module_manage', 'config_cat' => "站点属性", 'config_name' => "登录后默认站内url", 'default_value' => 'module_basic.ui.welcome', 'set_type' => TYPE_CONFIG_INPUT_TEXT, 'parameter' => array('size' => '100', 'maxlength' => '200')),
		array('config_key' => 'welcome', 'config_module' => 'module_manage', 'config_cat' => "公共信息", 'config_name' => "公共信息", 'default_value' => '欢迎进入SCAP系统。', 'set_type' => TYPE_CONFIG_TEXTAREA, 'parameter' => array('rows' => '30')),
		array('config_key' => 'auth_type', 'config_module' => 'module_manage', 'config_cat' => "认证属性", 'config_name' => "系统认证方式", 'default_value' => TYPE_AUTH_DB, 'set_type' => TYPE_CONFIG_SELECT, 'parameter' => array('options' => $GLOBALS['scap']['auth_type_list'])),
		array('config_key' => 'flag_record_log', 'config_module' => 'module_manage', 'config_cat' => "日志", 'config_name' => "开启日志", 'default_value' => TRUE, 'set_type' => TYPE_CONFIG_SELECT, 'parameter' => array('options' => array(TRUE => '开启', FALSE => '关闭'))),
		array('config_key' => 'ldap_host', 'config_module' => 'module_manage', 'config_cat' => "LDAP", 'config_name' => "LDAP主机地址", 'default_value' => 'localhost', 'set_type' => TYPE_CONFIG_INPUT_TEXT, 'parameter' => array('size' => '80', 'maxlength' => '50')),
		array('config_key' => 'ldap_port', 'config_module' => 'module_manage', 'config_cat' => "LDAP", 'config_name' => "LDAP端口", 'default_value' => '389', 'set_type' => TYPE_CONFIG_INPUT_TEXT, 'parameter' => array('size' => '80', 'maxlength' => '50')),
		array('config_key' => 'ldap_base_dn', 'config_module' => 'module_manage', 'config_cat' => "LDAP", 'config_name' => "LDAP基准DN", 'default_value' => 'ou=People,dc=youdomain,dc=com', 'set_type' => TYPE_CONFIG_INPUT_TEXT, 'parameter' => array('size' => '80', 'maxlength' => '50')),
	);
?>