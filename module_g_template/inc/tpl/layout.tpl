<!doctype html>
<html lang="{<$head.lang>}">
{<*
页面布局模板
使用html5，并向上兼容
@version $Id: layout.tpl 794 2013-10-30 06:57:09Z liqt $
@creator LiQintao@2013-02-05 上午11:26:16
*>}
<head>
	<meta charset="utf-8">
	<title>{<$head.title>}</title>
{<if $head.enable_chrome_in_ie>}
    <meta http-equiv="X-UA-Compatible" content="chrome=1">{<*页面在ie中使用chrome frame*>}
{</if>}
	<meta name="description" content="{<$head.description>}">
	<meta name="author" content="{<$head.author>}">
	<meta name="keywords" content="{<$head.keywords>}">
    <link rel="shortcut icon" href="{<$head.icon>}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">{<* mobile viewport optimisation *>}
{<foreach $head.css_list as $v>}{<*头部css文件加载*>}
{<if $v.ie_condition>}
	<!--[if {<$v.ie_condition>}]>
{</if>}
	<link rel="stylesheet" type="text/css" href="{<$v.url>}"{<if $v.media>} media="{<$v.media>}"{</if>} weight="{<$v.weight>}">
{<if $v.ie_condition>}
	<![endif]-->
{</if>}
{</foreach>}
{<foreach $head.js_list as $v>}{<*头部js文件加载*>}
{<if $v.ie_condition>}
	<!--[if {<$v.ie_condition>}]>
{</if>}
	<script src="{<$v.url>}" weight="{<$v.weight>}"></script>
{<if $v.ie_condition>}
	<![endif]-->
{</if>}
{</foreach>}
    <style>
{<foreach $head.css_code as $v>}{<*头部css代码加载*>}
    {<$v.code>}
{</foreach>}
    </style>
    <script>
{<foreach $head.js_code as $v>}{<*头部js代码加载*>}
    {<$v.code>}
{</foreach>}
    </script>
	{<block page_head_content>}{<$head.content>}{</block>}
</head>
<body {<block body_property>}{</block>}>
{<block page_body>}请替换页面内容。{</block>}
</body>
</html>