<?php
/**
 * description: 通用日志类型定义
 * create time: 2008-12-18-上午10:54:29
 * @version $Id: define.log_type.inc.php 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao
 */

// 通用日志类型定义:范围1-99，各模块自定义从100开始
// [注]定义内容不能更改，只能增加
define('G_TYPE_AL_CREATE', 1);// 创建
define('G_TYPE_AL_LASTEDIT', 2);// 最后编辑
define('G_TYPE_AL_SUBMIT', 3);// 提交
define('G_TYPE_AL_ASSIGN', 4);// 派发
define('G_TYPE_AL_EXECUTE', 5);// 执行
define('G_TYPE_AL_END', 6);// 结束
define('G_TYPE_AL_CHECK', 7);// 审核
define('G_TYPE_AL_ARCHIVE', 8);// 归档
define('G_TYPE_AL_UNARCHIVE', 9);// 解档
define('G_TYPE_AL_EDIT', 10);// 编辑
define('G_TYPE_AL_READ', 11);// 读取
define('G_TYPE_AL_REMOVE', 12);// 移除(非物理)
define('G_TYPE_AL_DELETE', 13);// 删除(物理)
define('G_TYPE_AL_DOWNLOAD', 14);// 下载
define('G_TYPE_AL_ACCEPT', 15);// 受理
define('G_TYPE_AL_RESOLVE', 16);// 解决
define('G_TYPE_AL_CLOSE', 17);// 关闭
define('G_TYPE_AL_UPGRADE', 18);// 升级


$GLOBALS['scap']['text']['module_g_00']['al_type'] = array(
	'' => '-',
	G_TYPE_AL_CREATE => '创建',
	G_TYPE_AL_LASTEDIT => '最后修改',
	G_TYPE_AL_SUBMIT => '提交',
	G_TYPE_AL_ASSIGN => '派发',
	G_TYPE_AL_EXECUTE => '执行',
	G_TYPE_AL_END => '结束',
	G_TYPE_AL_CHECK => '审核',
	G_TYPE_AL_ARCHIVE => '归档',
	G_TYPE_AL_UNARCHIVE => '解档',
	G_TYPE_AL_EDIT => '编辑',
	G_TYPE_AL_READ => '读取',
	G_TYPE_AL_REMOVE => '移除',
	G_TYPE_AL_DELETE => '删除',
	G_TYPE_AL_DOWNLOAD => '下载',
	G_TYPE_AL_ACCEPT => '受理',
	G_TYPE_AL_RESOLVE => '解决',
	G_TYPE_AL_CLOSE => '关闭',
	G_TYPE_AL_UPGRADE => '升级',
);
?>