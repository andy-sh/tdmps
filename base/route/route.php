<?php
/**
 * 框架web访问路由文件
 * 
 * @version $Id: route.php 94 2013-04-10 02:17:50Z liqt $
 * @creator LiQintao @ 2012-7-16 下午10:06:34
 */
use scap\module\g_tool\config;

require_once('basic.inc.php');
include(SCAP_PATH_ROOT."module_basic/language/lang.".config::get('scap', 'current_lang').".inc.php");// 加载module_basic语言文件

/**
 * $_GET['m'] : 系统所要调用的方法名称, [moudle name].[class name].[method name]
 * $_GET['r'] : 系统重定位的参数
 */
$act_para = array('module' => '', 'class' => '', 'method' => '');
if (isset($_GET['m']))
{
	list($act_para['module'], $act_para['class'], $act_para['method']) = explode('.', $_GET['m']);
}
else
{
    scap_redirect_url(config::get('scap', 'default_goto_url'));
}

if (!scap_check_session() && strcasecmp($_GET['m'], "module_basic.ui.login") != 0)
{
    $result_custom_login = false;
    
    if (config::get('scap', 'custom_login_method'))// 如果未登录，则检查是否有定制的登录机制
    {
        $result_custom_login = call_user_func(config::get('scap', 'custom_login_method'));
    }
    
    if (!$result_custom_login)
    {
        if (scap_check_account_public_valid() && (config::get('scap', 'enable_auth_public') || strcasecmp($_GET['account'], 'public') == 0))
        {
            // 如果应用指定用public访问，并且public帐号有效，则自动使用public登录
            scap_create_session('public');
        }
        else
        {
            $url = "{$GLOBALS['scap']['info']['site_url']}/?m=module_basic.ui.login";
            
            if (!empty($_GET['m']))
            {
                $url .= "&r=".rawurlencode($_SERVER['REQUEST_URI']);
            }
            
            scap_redirect_url($url);
            exit;
        }
    }
}

// 应用解析执行
$result = scap_application_parser($act_para['module'], $act_para['class'], $act_para['method']);

// 访问受限信息显示
if ($result != SCAP_MSG_SUCCESS)
{
    scap_show_system_error($result);
}
?>