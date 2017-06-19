<?php
/**
 * 导航菜单定义文件
 * create time: 2011-12-15 06:34:24
 * @version $Id: nav.inc.php 99 2012-03-25 15:43:34Z liqt $
 * @author zhangzhengqi
 */

$menu = array();
$i = 0;

$menu[$i++] = scap_create_module_menu_item('书籍', scap_get_url(array('module' => 'module_touchview_book', 'class' => 'ui_book', 'method' => 'index_book')));
$menu[$i++] = scap_create_module_menu_item('创建', scap_get_url(array('module' => 'module_touchview_book', 'class' => 'ui_book', 'method' => 'edit_book'), array('act' => STAT_ACT_CREATE)));
$menu[$i++] = scap_create_module_menu_item('导入', scap_get_url(array('module' => 'module_touchview_book', 'class' => 'ui_book', 'method' => 'import_book_step_1'), array()));
$menu[$i++] = scap_create_module_menu_item('配置', scap_get_url(array('module' => 'module_touchview_book', 'class' => 'ui_book', 'method' => 'edit_book_config'), array('act' => STAT_ACT_EDIT)));
?>