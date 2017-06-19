<?php
/**
 * description: 导航菜单定义
 * create time: 2007-7-12 18:44:28
 * @version $Id: nav.inc.php 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao(leeqintao@gmail.com)
 */

$menu = array();
		
$i = 0;
$menu[$i++] = scap_create_module_menu_item('帐户管理', scap_get_url(array('module' => 'module_manage', 'class' => 'ui', 'method' => 'index_account')));
$menu[$i++] = scap_create_module_menu_item('参数管理', scap_get_url(array('module' => 'module_manage', 'class' => 'ui', 'method' => 'index_config')));
$menu[$i++] = scap_create_module_menu_item('权限管理', scap_get_url(array('module' => 'module_manage', 'class' => 'ui', 'method' => 'index_acl')));
$menu[$i++] = scap_create_module_menu_item('角色分配', scap_get_url(array('module' => 'module_manage', 'class' => 'ui', 'method' => 'assign_acl')));
$menu[$i++] = scap_create_module_menu_item('模块管理', scap_get_url(array('module' => 'module_manage', 'class' => 'ui', 'method' => 'index_module')));
$menu[$i++] = scap_create_module_menu_item('系统日志', scap_get_url(array('module' => 'module_manage', 'class' => 'ui', 'method' => 'index_log')));
$menu[$i++] = scap_create_module_menu_item('导入SQL', scap_get_url(array('module' => 'module_manage', 'class' => 'ui', 'method' => 'import_sql')));
?>