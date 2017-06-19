<?php
/**
 * 页面类型定义
 * create time: 2012-1-9 上午11:16:21
 * @version $Id: define.page_type.inc.php 20 2012-01-11 13:41:18Z liqt $
 * @author LiQintao
 */

define('TYPE_PAGE_NORMAL', 1);// 常规页面
define('TYPE_PAGE_SECTION', 2);// 章节页面

$GLOBALS['scap']['text']['module_touchview_page']['type_page'] = array(
    ''=> '-',
    TYPE_PAGE_NORMAL => '常规页面',
    TYPE_PAGE_SECTION => '章节页面',
);
?>