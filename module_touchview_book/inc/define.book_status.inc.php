<?php
/**
 * book状态定义文件
 * create time: 2012-3-14 下午03:37:58
 * @version $Id: define.book_status.inc.php 89 2012-03-14 08:07:06Z liqt $
 * @author LiQintao
 */

define('TYPE_STATUS_BOOK_SHOW', 1);// 页面展示状态

define('STATUS_BOOK_SHOW_ON', 1);// 上线
define('STATUS_BOOK_SHOW_OFF', 2);// 下线

$GLOBALS['scap']['text']['module_touchview_book']['status_book_show'] = array(
    ''=> '-',
    STATUS_BOOK_SHOW_ON => '上线',
    STATUS_BOOK_SHOW_OFF => '下线',
);
?>