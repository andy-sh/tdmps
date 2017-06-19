<!doctype html>
<html>
<!--{<*
 * description: 系统的head模版
 * create time: 2006-10-30 11:18:47
 * @version $Id: scap.header.tpl 100 2013-05-09 02:50:00Z liqt $
 * @author LiQintao
*>}-->
	<head>
		<link rel="shortcut icon" href="{<$link_favicon>}" />
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		
{<if $enable_chrome_in_ie === true>}		
		<meta http-equiv="X-UA-Compatible" content="chrome=1">{<*页面在ie中使用chrome frame*>}
{</if>}

{<if $flag_refresh>}
		<meta http-equiv="refresh" content="{<$refresh_seconds>}" >
{</if>}
		<title>{<$head_site_title>}</title>
{<if $head_keywords>}
		<meta name="keywords" content="{<$head_keywords>}" />
{</if>}
{<if $head_description>}
		<meta name="description" content="{<$head_description>}" />
{</if>}
		<meta name="author" content="">
		
{<if $flag_load_blueprint>}
		<link rel="stylesheet" href="{<$url_blueprint>}screen.css" type="text/css" media="screen, projection">
  		<link rel="stylesheet" href="{<$url_blueprint>}print.css" type="text/css" media="print"> 
  		<!--[if lt IE 8]>
    		<link rel="stylesheet" href="{<$url_blueprint>}ie.css" type="text/css" media="screen, projection">
 		<![endif]-->
{</if>}

		<!-- Load css file(start)-->
{<section name=i loop=$head_css_list  start=0>}
		<link rel="stylesheet" type="text/css" href="{<$head_css_list[i]>}" />
{</section>}
		<!-- Load css file(end)-->
		
		<!-- Load js file(start)-->
{<section name=i loop=$head_js_list  start=0>}
		<script type="text/javascript" src="{<$head_js_list[i]>}"></script>
{</section>}
		<!-- Load js file(end)-->
		
		<!-- Load customer code(start)-->
{<section name=i loop=$head_customer_code_list  start=0>}
		{<$head_customer_code_list[i]>}
{</section>}
		<!-- Load customer code(end)-->

	</head>
		
	<body>
		<!-- Load body code(start)-->
{<section name=i loop=$body_customer_code_list  start=0>}
		{<$body_customer_code_list[i]>}
{</section>}
		<!-- Load body code(end)-->
