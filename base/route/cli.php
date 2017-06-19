<?php
/**
 * 框架cli访问入口
 * 
 * create time: 2009-12-25-上午09:58:57
 * @version $Id: cli.php 4 2012-07-18 06:40:23Z liqt $
 */

error_reporting(E_ALL ^ E_NOTICE);

require_once('basic.inc.php');

if (!scap_check_excute_from_cli())
{
    exit('禁止从非CLI访问。');
}

/**
 * 调用方法：
 * php cli.php module_test.ui.test para1=a para2=b para3=c
 */

// 构造$_SERVER['QUERY_STRING']
$_SERVER['QUERY_STRING'] = '';

// 将CLI参数赋值给$_GET
$_GET['m'] = $_SERVER["argv"][1];
$_SERVER['QUERY_STRING'] .= "m={$_SERVER["argv"][1]}"; 

foreach($_SERVER["argv"] as $k => $v)
{
    if ($k < 2)// 0,1都是指定参数,2之后是应用自定义参数
    {
        continue;
    }
    
    $_SERVER['QUERY_STRING'] .= "&{$v}";
    $temp_split = explode('=', $v);// 用=号分割参数
    if (count($temp_split) != 2)
    {
        echo "Check your input!\nUse: php cli.php module_test.ui.test para1=a para2=b para3=c \n";
        exit;
    }
    $_GET[$temp_split[0]] = $temp_split[1];
}

/**
 * $_GET['m'] : 系统所要调用的方法名称, [moudle name].[class name].[method name]
 */

// 获取要执行的参数
// index.php?m=[moudle name].[class name].[method name]
$act_para = array('module' => '', 'class' => '', 'method' => '');
if (isset($_GET['m']))
{
    list($act_para['module'], $act_para['class'], $act_para['method']) = explode('.', $_GET['m']);
}
else
{
    echo "welcome to CLI!\nUse: php cli.php module_test.ui.test para1=a para2=b para3=c \n";
    exit;
}

// 应用解析执行
$result = scap_application_parser($act_para['module'], $act_para['class'], $act_para['method']);

// 访问受限信息显示
if ($result != SCAP_MSG_SUCCESS)
{
    scap_show_system_error($result);
}


?>