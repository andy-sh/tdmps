<?php
/**
 * form相关组件服务类
 * 
 * @package module_g_form
 * @subpackage model
 * @version $Id: class.form.inc.php 931 2013-12-10 08:00:02Z liqt $
 * @creator liqt @ 2013-02-04 11:20:23 by caster0.0.2
 */
namespace scap\module\g_form;

/**
 * form相关组件服务类
 * 
 */
class form
{
    /**
     * 加载xheditor所必需的js/css等文件
     * 
     * @param string $language 语言类型，默认LANGUAGE_ZH_CN
     */
    public static function load_xheditor_base_file($language = xheditor::LANGUAGE_ZH_CN)
    {
        xheditor::load_base_file($language);
    }
    
    /**
     * 加载所必需的js/css等文件
     * 
     */
    public static function load_pnotify_base_file()
    {
        $url_lib = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/pnotify/';
        $url_lib_scap = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/scap/';
        
        \scap_ui::insert_head_js_file($url_lib."jquery.pnotify.min.js");
        \scap_ui::insert_head_js_file($url_lib_scap."pnotify.js");
        \scap_ui::insert_head_css_file($url_lib."jquery.pnotify.default.css");
        \scap_ui::insert_head_css_file($url_lib."jquery.pnotify.default.icons.css");
        \scap\module\g_jquery\jquery::load_jquery_ui_theme();
    }
    
	/**
     * 加载mcdropdown所必需的js/css等文件
     * - 能够让用户在一个复杂分级的树形下拉选项中进行选择
     */
    public static function load_mcdropdown_base_file()
    {
        $url_lib = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/mcdropdown/';
        $url_lib_scap = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/scap/';
        
        \scap_ui::insert_head_js_file($url_lib."jquery.mcdropdown.js");
        \scap_ui::insert_head_js_file($url_lib."jquery.bgiframe.js");
        \scap_ui::insert_head_js_file($url_lib_scap."mcdropdown.js");// 加载scap下的js封装
        \scap_ui::insert_head_css_file($url_lib."css/jquery.mcdropdown.min.css");
    }
    
    /**
     * 加载显示系统信息组件的必须文件
     * - 依赖pnotify
     */
    public static function load_show_system_info_base_file()
    {
        $url_lib_scap = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/scap/';
        
        self::load_pnotify_base_file();
        \scap_ui::insert_head_js_file($url_lib_scap.'show_system_info.js');
    }
    
    /**
     * 加载autocomplete组件的必须文件
     * - 依赖jquery-ui
     */
    public static function load_autocomplete_base_file()
    {
        $url_lib_scap = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/scap/';
        
        \scap\module\g_jquery\jquery::load_jquery_ui_js();
        \scap_ui::insert_head_js_file($url_lib_scap.'autocomplete.js');
    }
    
	/**
     * 加载jstree所必需的js/css等文件
     */
    public static function load_jstree_base_file()
    {
        $url_lib = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/jstree/';
        $url_lib_scap = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/scap/';
        
        \scap_ui::insert_head_js_file($url_lib."jquery.jstree.js");
        \scap_ui::insert_head_js_file($url_lib."jquery.cookie.js");
        \scap_ui::insert_head_js_file($url_lib."jquery.hotkeys.js");
    }
    
	/**
     * 加载fg.menu所必需的js/css等文件
     * - 依赖jquery_ui_theme
     */
    public static function load_fgmenu_base_file()
    {
        $url_lib = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/fg.menu/';
        $url_lib_scap = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/scap/';
        
        \scap_ui::insert_head_js_file($url_lib."fg.menu.js");
        \scap_ui::insert_head_css_file($url_lib."fg.menu.css");
        \scap\module\g_jquery\jquery::load_jquery_ui_theme();
    }
    
	/**
     * 加载colorbox所必需的js/css等文件
     * 
     * @param string $theme_name 主题名称，默认为2
     */
    public static function load_colorbox_base_file($theme_name = '2')
    {
        $url_lib = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/colorbox/';
        $url_lib_scap = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/scap/';
        
        \scap_ui::insert_head_css_file($url_lib."theme/{$theme_name}/colorbox.css");
        \scap_ui::insert_head_js_file($url_lib."jquery.colorbox-min.js");
        \scap_ui::insert_head_js_file($url_lib_scap."colorbox.js");
    }
    
	/**
     * 加载jquery.form所必需的js/css等文件
     * - 依赖jquery
     */
    public static function load_jqueryform_base_file()
    {
        $url_lib = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/jquery.form/';
        $url_lib_scap = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/scap/';
        
        \scap_ui::insert_head_js_file($url_lib."jquery.form.js");
    }
    
	/**
     * 加载rateit所必需的js/css等文件
     * - 依赖jquery 1.6+
     */
    public static function load_rateit_base_file()
    {
        $url_lib = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/rateit/';
        $url_lib_scap = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/scap/';
        
        \scap_ui::insert_head_css_file($url_lib."rateit.css");
        \scap_ui::insert_head_js_file($url_lib."jquery.rateit.min.js");
    }
    
	/**
     * 加载jquery.scrollTo所必需的js/css等文件
     * - 依赖jquery
     */
    public static function load_scrollto_base_file()
    {
        $url_lib = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/scrollto/';
        $url_lib_scap = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/scap/';
        
        \scap_ui::insert_head_js_file($url_lib."jquery.scrollTo.min.js");
        \scap_ui::insert_head_js_file($url_lib."jquery.localScroll.min.js");
    }
    
	/**
     * 加载superfish所必需的js/css等文件
     * - 依赖jquery
     */
    public static function load_superfish_base_file()
    {
        $url_lib = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/superfish/';
        $url_lib_scap = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/scap/';
        
//        \scap_ui::insert_head_css_file($url_lib."css/superfish.css");
        \scap_ui::insert_head_js_file($url_lib."hoverIntent.js");
        \scap_ui::insert_head_js_file($url_lib."superfish.js");
        \scap_ui::insert_head_js_file($url_lib_scap."superfish.js");
    }
    
	/**
     * 加载jquery.validate所必需的js/css等文件
     * - 依赖jquery
     */
    public static function load_jquery_validate_base_file()
    {
        $url_lib = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/jquery.validate/';
        $url_lib_scap = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/scap/';
        
        \scap_ui::insert_head_js_file($url_lib."jquery.validate.min.js");
        \scap_ui::insert_head_js_file($url_lib."additional-methods.min.js");
        \scap_ui::insert_head_js_file($url_lib."messages_zh.js");
    }
    
    /**
     * 加载autosize所需的基本文件
     * - 依赖jquery
     */
    public static function load_autosize_base_file()
    {
        $url_lib = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/autosize/';
        $url_lib_scap = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/scap/';
        
        \scap_ui::insert_head_js_file($url_lib."jquery.autosize-min.js");
    }
    
    /**
     * 加载日历选择组件需的js文件
     */
    public static function load_calendar_base_file($lang = 'zh', $theme = 'calendar-win2k-1', $stripped = true)
    {
        $url_lib = $GLOBALS['scap']['info']['site_url']."/module_g_form/inc/lib/jscalendar/";
        
        // calendar stylesheet
        \scap_ui::insert_head_css_file($url_lib.$theme.".css");
        
        // main calendar program
        if ($stripped)
        {
            \scap_ui::insert_head_js_file($url_lib."calendar_stripped.js");
            \scap_ui::insert_head_js_file($url_lib."calendar-setup_stripped.js");
        }
        else
        {
            \scap_ui::insert_head_js_file($url_lib."calendar.js");
            \scap_ui::insert_head_js_file($url_lib."calendar-setup.js");
        }

        // language for the calendar
        \scap_ui::insert_head_js_file($url_lib."lang/calendar-$lang.js");
    }
    
    /**
     * 在app中自动关闭colorbox窗口并刷新父窗口
     * 
     * @param bool $flag_reload 是否在关闭后自动刷新父窗口，默认为true
     * 
     */
    public static function auto_close_current_colorbox($flag_reload = true)
    {
        scap_redirect_url(array('module' => 'module_g_form', 'class' => 'ui_server', 'method' => 'close_colorbox'), array('reload' => $flag_reload));
    }
    
	/**
     * 加载jcarousellite所必需的js/css等文件
     */
    public static function load_jcarousellite_base_file()
    {
        $url_lib = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/jcarousellite/';
        $url_lib_scap = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/scap/';
        
        \scap_ui::insert_head_js_file($url_lib."jquery.jcarousellite.min.js");
        \scap_ui::insert_head_js_file($url_lib."jquery.mousewheel.min.js");
        \scap_ui::insert_head_js_file($url_lib_scap."jcarousellite.js");
    }
    
	/**
     * 加载stoc所必需的js/css等文件
     */
    public static function load_stoc_base_file()
    {
        $url_lib = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/stoc/';
        $url_lib_scap = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/scap/';
        
        \scap_ui::insert_head_js_file($url_lib."jquery.stoc.js");
    }
    
	/**
     * 加载dotdotdot所必需的js/css等文件
     */
    public static function load_dotdotdot_base_file()
    {
        $url_lib = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/dotdotdot/';
        $url_lib_scap = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/scap/';
        
        \scap_ui::insert_head_js_file($url_lib."jquery.dotdotdot.js");
    }
    
	/**
     * 加载columnizer所必需的js/css等文件
     */
    public static function load_columnizer_base_file()
    {
        $url_lib = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/columnizer/';
        $url_lib_scap = $GLOBALS['scap']['info']['site_url'].'/module_g_form/inc/lib/scap/';
        
        \scap_ui::insert_head_js_file($url_lib."jquery.columnizer.js");
    }
}
?>