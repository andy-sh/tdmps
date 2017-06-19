<?php
/**
 * template服务类文件
 * 
 * @package module_g_template
 * @subpackage model
 * @version $Id: class.template.inc.php 225 2013-02-06 07:20:00Z liqt $
 * @creator liqt @ 2013-02-05 17:43:56 by caster0.0.2
 */
namespace scap\module\g_template;

/**
 * template服务类
 */
class template
{
    /**
     * 添加tpl目录到smarty实例中
     * 
     * @param smarty object $smarty smarty实例
     */
    public static function add_tpl_dir_for_smarty(& $smarty)
    {
        $smarty->addTemplateDir(__DIR__.'/tpl');
    }
    
    /**
	 * app.default.tpl模板机制的渲染函数
	 * 
	 * @param string $title 显示标题
	 * @param bool $show_nav 是否显示默认导航，默认为true
	 * @param bool $load_default_lib 是否加载默认的head文件，比如css js和一些代码，默认为true
	 * 
	 * @return null
	 */
	public static function render_default_tpl($title = '', $menu_text = '', $show_nav = true, $load_default_lib = true)
    {
        $data_render = array();
        
		if ($load_default_lib)
		{
		    // 加载默认css
		    \scap_ui::insert_head_css_file(scap_get_css_url('module_basic', 'home.css', 'default'), '', 1);
		    \scap_ui::insert_head_css_file(SCAP_RELATIVE_PATH_LIBRARY."jquery/plugins/slidermenu/jquery.slidermenu.css", '', 2);
		    \scap_ui::insert_head_css_file(SCAP_RELATIVE_PATH_LIBRARY."jquery/plugins/jnotify/jquery.jnotify-alt.css", '', 3);
		    // 加载默认js
		    \scap_ui::insert_head_js_file(\scap\module\g_jquery\jquery::get_js_url(), 1);
		    \scap_ui::insert_head_js_file(\scap\module\g_jquery\jquery::get_scap_common_url(), 2);
		    \scap_ui::insert_head_js_file(\scap\module\g_jquery\jquery::get_scap_plugin_url(), 3);
		    \scap_ui::insert_head_js_file(scap_get_js_url('module_basic', 'home.js', 'default'), 4);
		    \scap_ui::insert_head_js_file(SCAP_RELATIVE_PATH_LIBRARY."jquery/plugins/slidermenu/jquery.slidermenu.js", 5);
		    \scap_ui::insert_head_js_file(SCAP_RELATIVE_PATH_LIBRARY."jquery/plugins/jnotify/jquery.jnotify.js", 6);
		    // 默认加载greybox
		    $dir = SCAP_RELATIVE_PATH_LIBRARY."greybox/";
		    \scap_ui::insert_head_js_file("{$dir}AJS.js");
		    \scap_ui::insert_head_js_file("{$dir}AJS_fx.js");
		    \scap_ui::insert_head_js_file("{$dir}gb_scripts.js");
		    \scap_ui::insert_head_css_file("{$dir}gb_styles.css");

		    \scap_ui::insert_head_js_code("$(function(){gen_side_menu();});", 1);
		    \scap\module\g_form\form::load_show_system_info_base_file();// 加载显示系统消息
		}
        
        $data_render['flag_show_menu'] = !$_GET['nomenu'];
        $data_render['flag_has_navbar'] = $show_nav;
        
        if ($data_render['flag_has_navbar'])
        {
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

                $data_render['nav_app_list'][$i]['module_id'] = $k;
                $data_render['nav_app_list'][$i]['class'] = 'noactive';
                $data_render['nav_app_list'][$i]['show'] = \scap_html::anchor(array('href' => scap_get_url(array('module' => $k))), '<span>'.scap_lang_module_name($k).'</span>');

                // 获取模块菜单
                $nav_menu_data[$i] = scap_load_new_nav_menu($k);
                if(!is_array($nav_menu_data[$i]))
                {
                    $nav_menu_data[$i] = array();
                }
                $j = 0;
                foreach($nav_menu_data[$i] as $k => $v)
                {
                    $data_render['nav_app_menu'][$i][$j]['text'] = $v['text'];
                    $data_render['nav_app_menu'][$i][$j]['url'] = $v['url'];
                    $j ++;
                }
                
                $i ++;
            }

            // 设置导航栏上logo
            $data_render['nav_logo'] = \scap_html::image(array('src' => scap_get_image_url('module_basic', 'logo-scap.png'), 'height' => 48));

            // 修改密码链接
            $data_render['link_change_password'] = scap_get_url(array('module' => 'module_basic', 'class' => 'ui', 'method' => 'change_password'));

            // 注销链接
            $data_render['link_logout'] = scap_get_url(array('module' => 'module_basic', 'class' => 'ui', 'method' => 'logout'));

            // 设置当前帐户信息
            $data_render['nav_user_info'] = sprintf("%s <a href='".$data_render['link_change_password']."' title='修改密码' style='text-decoration:none;'>[修改密码]</a> <a href='".$data_render['link_logout']."' style='text-decoration:none;' title='注销'>[注销]</a> <span id=\"show_im_info\"></span> 今天是%s",
            $GLOBALS['scap']['auth']['account_id'],
            date("Y年m月d日 D")
            );
            
            // 设置"模块"菜单
            $data_render['nav_flag_show_module_menu'] = true;
            $data_render['nav_module_menu_title'] = scap_lang_module_name($GLOBALS['scap']['info']['current_module_id'])." ".TEXT_MENU;

            // 加载当前方法标题
            $data_render['nav_current_method_title'] = $title;
            
	        // 设置当前激活菜单
            if (empty($open_module_id))
            {
                $open_module_id = $GLOBALS['scap']['info']['current_module_id'];
            }
            
            $pos = \scap\module\g_tool\matrix::musearch($open_module_id, $data_render['nav_app_list']);

            if (!empty($pos))
            {
                $data_render['nav_app_list'][$pos[0]]['class'] = 'active';
            }
            
            if (!empty($menu_text))
            {
                $pos1 = \scap\module\g_tool\matrix::musearch($menu_text, $data_render['nav_app_menu'][$pos[0]]);
            }

            if (!empty($pos1))
            {
                $data_render['nav_app_menu'][$pos[0]][$pos1[0]]['class'] = 'current';
            }
        }
        
        $data_render['powered_by'] = "<span style=''>&copy;2009 - ".date("Y",time())."</span>";
        
        \scap_ui::merge_data_render($data_render);
    }
}
?>