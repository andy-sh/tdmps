<?php
/**
 * 系统导航栏输出
 * create time: 2006-10-31 22:47:41
 * @version $Id: scap.navbar.inc.php 164 2014-02-17 04:40:21Z liqt $
 * @author LiQintao
 */

global $data_out;

// 显示navbar
$data_out['flag_has_navbar'] = true;

// 设置导航栏目
$module_list = scap_get_module_access_list();
$i = 0;
foreach ($module_list as $k => $v)
{
    // 获取模块配置信息，仅属性为PROP_MODULE_FRONT才显示
    $info = scap_get_module_local_info($k);
    if($info['property'] != PROP_MODULE_FRONT)
    {
        continue;
    }
    
	$data_out['nav_app_list'][$i]['module_id'] = $k;
	$data_out['nav_app_list'][$i]['class'] = 'noactive';
	$data_out['nav_app_list'][$i]['show'] = scap_html::anchor(array('href' => scap_get_url(array('module' => $k))), '<span>'.scap_lang_module_name($k).'</span>');
	
	// 获取模块菜单
    $nav_menu_data[$i] = scap_load_new_nav_menu($k);
    if(!is_array($nav_menu_data[$i]))
    {
        $nav_menu_data[$i] = array();
    }
    $j = 0;
    foreach($nav_menu_data[$i] as $k => $v)
    {
    	$data_out['nav_app_menu'][$i][$j]['text'] = $v['text'];
    	$data_out['nav_app_menu'][$i][$j]['url'] = $v['url'];
    	$j ++;
    }
	
	$i ++;
}

$data_out['logo_image'] = scap_html::image(array('src' => scap_get_image_url('module_basic', 'touchview.png'), 'style' => "height:42px; vertical-align:top; margin-top:3px; margin-left:10px;"), true);
$data_out['logo_title'] = scap_html::image(array('src' => scap_get_image_url('module_basic', 'logo_title.png'), 'style' => "height:48px; vertical-align:top;"), true);

// 修改密码链接
$data_out['link_change_password'] = scap_get_url(array('module' => 'module_basic', 'class' => 'ui', 'method' => 'change_password'));

// 注销链接
$data_out['link_logout'] = scap_get_url(array('module' => 'module_basic', 'class' => 'ui', 'method' => 'logout'));

// 设置当前帐户信息
$data_out['nav_user_info'] = sprintf("%s <a href='".$data_out['link_change_password']."' title='修改密码' style='text-decoration:none;'>[修改密码]</a> <a href='".$data_out['link_logout']."' style='text-decoration:none;' title='注销'>[注销]</a> <span id=\"show_im_info\"></span> 今天是%s",
										$GLOBALS['scap']['auth']['account_id'],
										date("Y年m月d日 D")
							);
							
// 设置portal链接
$data_out['nav_portal_link'] = scap_get_url(array('module' => 'module_sitsm_portal', 'class' => 'ui', 'method' => 'index_default'), array('nomenu' => 1));

// 设置"模块"菜单
$data_out['nav_flag_show_module_menu'] = true;
$data_out['nav_module_menu_title'] = scap_lang_module_name($GLOBALS['scap']['info']['current_module_id'])." ".TEXT_MENU;

// 加载当前方法标题
$data_out['nav_current_method_title'] = $GLOBALS['scap']['handle_current_class']->get_current_method_title();
?>