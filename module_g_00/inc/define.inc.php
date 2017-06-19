<?php
/**
 * 通用模块00定义
 * create time: 2008-12-15-下午03:18:25
 * @version $Id: define.inc.php 100 2013-05-09 02:50:00Z liqt $
 * @author LiQintao
 */

// 本模块信息定义
$GLOBALS['scap']['module']['module_g_00'] = array(
		'version' => '1.2.4',
		'property' => PROP_MODULE_BACK,
		'comment' => '通用模块基础',
		'tables' => array(
							'g_app_log',
							'g_binary_data',
							'g_binary_data_content',
							'g_object_attach_element',
							'g_object_binary_link',
							'g_object_category_link',
                            'g_object_relation',
							'g_object_status',
							'g_object_time_node',
					),
	);
?>