<?php
/**
 * 系统header赋值文件
 * create time: 2006-10-30 11:31:19
 * @version $Id: scap.header.inc.php 100 2013-05-09 02:50:00Z liqt $
 * @author LiQintao(leeqintao@gmail.com)
 */
use scap\module\g_tool\config;

global $data_out;

$data_out['enable_chrome_in_ie'] = config::get('scap', 'enable_chrome_in_ie');

// 设置系统站点标题
$data_out['head_site_title'] = $GLOBALS['scap']['handle_current_class']->get_current_method_title();

$data_out['link_favicon'] = $GLOBALS['scap']['info']['site_url'].'/module_basic/templates/default/images/favicon.ico';

// 设置meta关键字
$data_out['head_keywords'] = $GLOBALS['scap']['handle_current_class']->get_current_head_keywords();

// 设置meta描述
$data_out['head_description'] = $GLOBALS['scap']['handle_current_class']->get_current_head_description();

$data_out['flag_refresh'] = isset($_GET['refresh']);
if($data_out['flag_refresh'])
{
    $data_out['refresh_seconds'] = $_GET['refresh'];
}

// 加载头部的css文件
if (!is_array($data_out['head_css_list']))
{
    $data_out['head_css_list'] = array();
}

// 加载头部的js文件
if(!is_array($data_out['head_js_list']))
{
    $data_out['head_js_list'] = array();
}

// 是否加载默认各种css/js库及代码
if ($data_out['flag_load_default_lib'])
{
    array_unshift(  $data_out['head_css_list'],
                    scap_get_css_url('module_basic', 'home.css', 'default'),
                    SCAP_RELATIVE_PATH_LIBRARY."jquery/plugins/slidermenu/jquery.slidermenu.css",
                    SCAP_RELATIVE_PATH_LIBRARY."jquery/plugins/jnotify/jquery.jnotify-alt.css"
    );

    // 默认加载jquery文件,必须先加载
    array_unshift(  $data_out['head_js_list'], 
                    \scap\module\g_jquery\jquery::get_js_url(),
                    \scap\module\g_jquery\jquery::get_scap_common_url(),
                    \scap\module\g_jquery\jquery::get_scap_plugin_url(),
                    scap_get_js_url('module_basic', 'home.js', 'default'),
                    SCAP_RELATIVE_PATH_LIBRARY."jquery/plugins/slidermenu/jquery.slidermenu.js",
                    SCAP_RELATIVE_PATH_LIBRARY."jquery/plugins/jnotify/jquery.jnotify.js"
    );

    // 默认加载greybox
    $dir = SCAP_RELATIVE_PATH_LIBRARY."greybox/";
    scap_module_ui::load_code_in_head(scap_html::js_file_tag("{$dir}AJS.js"));
    scap_module_ui::load_code_in_head(scap_html::js_file_tag("{$dir}AJS_fx.js"));
    scap_module_ui::load_code_in_head(scap_html::js_file_tag("{$dir}gb_scripts.js"));
    scap_module_ui::load_css_file("{$dir}gb_styles.css");

    // 加载自定义code
    $data_out['head_customer_code_list'][] = <<<EOT
<script type="text/javascript">
	$(function(){
        gen_side_menu();
    });
</script>
EOT;
}
$data_out['flag_show_menu'] = !$_GET['nomenu'];
$data_out['flag_has_navbar'] = false;
?>