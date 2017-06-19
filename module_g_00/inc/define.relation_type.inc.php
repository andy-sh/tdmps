<?php
/**
 * description: 通用关系类型定义
 * create time: 2010-8-12 01:44:13
 * @version $Id: define.relation_type.inc.php 4 2012-07-18 06:40:23Z liqt $
 * @author gaox
 */
define('G_TYPE_RELATION_SIMPLE', 1);// 简单关系 无主次顺序
define('G_TYPE_RELATION_FILIATION', 2);// 父子关系 有主次顺序
define('G_TYPE_RELATION_REPEAT', 3);// 重复关系 无主次顺序
define('G_TYPE_RELATION_INCLUDE', 4);// 包含关系 有主次顺序
define('G_TYPE_RELATION_USE', 5);// 使用关系 有主次顺序
?>