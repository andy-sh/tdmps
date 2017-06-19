<?php
/**
 * description: 对象状态相关定义（通用部分）
 * create time: 2009-2-16-上午09:43:45
 * @version $Id: define.object_status.inc.php 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao
 */

// 通用对象状态类型定义：范围1-99，各模块自定义从100开始
define('G_TYPE_STATUS_VALID', 1);// 对象有效状态
// 类型为"有效状态"(TSV=TYPE_STATUS_VALID)下的状态定义：
define('G_STATUS_TSV_VALID', 1);// 有效状态
define('G_STATUS_TSV_INVALID', 2);// 无效状态


?>