<?php
/**
 * scap默认配置文件
 * 
 * @category scap
 * @package config
 * @version $Id: default.php 164 2014-02-17 06:07:57Z liqt $
 * @creator liqt @ 2013-02-05 09:46:00
 */
use scap\module\g_tool\config;

// 数据库连接参数
config::set('db_connect', 'default', array('type' => 'mysql', 'host' => 'localhost', 'database' => 'tdmps', 'user' => 'root', 'password' => 'root'), 1);// 默认db连接配置

// 系统参数
config::set('scap', 'flag_enable_custom_url', false, 1);// 是否启用自定义url功能
// config::set('scap', 'custom_url_method', 'scap\module\g_cms_portal\portal::custom_url', 1);// 自定义url方法
config::set('scap', 'project_url', "http://{$_SERVER['SERVER_NAME']}/tdmps", 1);// 项目根url
config::set('scap', 'session_name', 'tdmps', 1);// session名称
config::set('scap', 'default_goto_url', scap_get_url(array('module' => 'module_touchview_book', 'class' => 'ui_book_front', 'method' => 'book'), array()), 1);// 默认跳转
config::set('scap', 'enable_auth_public', true, 1);// 启用公共身份认证机制(自动对用户赋予public身份)
config::set('scap', 'current_lang', 'zh-cn', 1);// 系统当前语言

// 系统模板可配参数
config::set('tpl', 'head.lang', 'zh-CN', 1);// 页面lang
config::set('tpl', 'head.keywords', 'scap', 1);// 页面keywords
config::set('tpl', 'head.description', '基于scap构建的站点', 1);// 页面description
config::set('tpl', 'head.author', '上海热信信息技术有限公司(http://hotide.cn)', 1);// 页面author
config::set('tpl', 'head.title', 'TDMPS', 1);// 页面title
config::set('tpl', 'head.icon', '/favicon.ico', 1);// 页面icon
config::set('tpl', 'head.enable_chrome_in_ie', true, 1);// 在IE中自动使用chrome frame功能（如果chrome frame安装的话）
?>