<?php
/**
 * 模块定义文件
 * create time: 2011-12-13 下午02:36:08
 * @version $Id: define.inc.php 154 2012-10-26 05:58:40Z liqt $
 * @author LiQintao
 */

$GLOBALS['scap']['module']['module_touchview_basic'] = array(
    'version' => '1.0.2',
    'property' => PROP_MODULE_BACK,
    'comment' => 'TouchView基础模块',
    'tables' => array(
    					'touchview_book',
    					'touchview_page', 
                ),
);
?>