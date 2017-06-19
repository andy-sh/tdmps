<?php
/**
 * 模块数据库定义文件
 * create time: 2011-12-13 下午01:54:38
 * @version $Id: db.tables.inc.php 154 2012-10-26 05:58:40Z liqt $
 * @author LiQintao
 */

// 书籍表
$scap_db_tables['touchview_book'] = array(
    'b_sn' => array('b_sn', 'I', 'AUTO', 'KEY'), // 序号(自动递增)
	'b_id' => array('b_id', 'C', '40'), // 内部唯一id
    'b_status' => array('b_status', 'I1', 'UNSIGNED', 'DEFAULT' => 0), // 展示状态
    'b_sort_sn' => array('b_sort_sn', 'I2', 'DEFAULT' => 0), // 排序序号
	'b_name' => array('b_name', 'X'), // 名称
	'b_description' => array('b_description', 'X'), // 简介
    'b_config' => array('b_config', 'X'),// 配置
);
$scap_db_table_options['touchview_book'] = array('mysql' => 'TYPE=MyISAM', 'mysqlt' => 'TYPE=InnoDB');

// 书页表
$scap_db_tables['touchview_page'] = array(
    'p_sn' => array('p_sn', 'I', 'AUTO', 'KEY'), // 序号(自动递增)
	'p_id' => array('p_id', 'C', '40'), // 内部唯一id
	'b_id' => array('b_id', 'C', '40'), // 所属书籍id
    'p_type' => array('p_type', 'I2', 'UNSIGNED','DEFAULT' => 0), //页面类型
	'p_parent_id' => array('p_parent_id', 'C', '40'), // 所属父节点id
	'p_sort_sn' => array('p_sort_sn', 'I2', 'DEFAULT' => 0), // 排序序号
	'p_name' => array('p_name', 'X'), // 名称
	'p_content' => array('p_content', 'X'), // 内容
    'p_config' => array('p_config', 'X'),// 配置
);
$scap_db_table_options['touchview_page'] = array('mysql' => 'TYPE=MyISAM', 'mysqlt' => 'TYPE=InnoDB');
?>