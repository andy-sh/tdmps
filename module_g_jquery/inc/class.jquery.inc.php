<?php
/**
 * jquery服务类
 * 
 * @package module_g_jquery
 * @subpackage model
 * @version $Id: class.jquery.inc.php 723 2013-09-06 07:01:46Z liqt $
 * @creator liqt @ 2013-02-03 14:31:15 by caster0.0.2
 */
namespace scap\module\g_jquery;

/**
 * jquery服务类
 */
class jquery
{
    /**
     * 获取jquery的js文件url
     * 
     * @param string $version 指定的版本号，默认为空(使用默认版本)
     * 
     * @return string
     */
    public static function get_js_url($version = '1.7.1')
    {
        $url_lib = $GLOBALS['scap']['info']['site_url'].'/module_g_jquery/inc/lib/jquery/';
        
        return $url_lib."{$version}.min.js";
    }
    
    /**
     * 获取scap中js服务方法的框架文件
     * 
     * @return string
     */
    public static function get_scap_plugin_url()
    {
        $url_lib = $GLOBALS['scap']['info']['site_url'].'/module_g_jquery/inc/lib/scap/';
        
        return $url_lib."jquery.scap.js";
    }
    
    /**
     * 获取scap常用基础函数库文件url
     * 
     * @return string
     */
    public static function get_scap_common_url()
    {
        $url_lib = $GLOBALS['scap']['info']['site_url'].'/module_g_jquery/inc/lib/scap/';
        
        return $url_lib."common.js";
    }
    
    /**
     * 加载scap系统最小化的所需js及css文件
     * - 文件包括jquery,scap/common.js,jquery.scap.js,jquery.cookie
     */
    public static function load_min_base_file()
    {
        \scap_ui::insert_head_js_file(self::get_js_url(), 1);
        \scap_ui::insert_head_js_file($GLOBALS['scap']['info']['site_url'].'/module_g_jquery/inc/lib/jquery/jquery.cookie.js', 2);
        \scap_ui::insert_head_js_file(self::get_scap_common_url(), 3);
        \scap_ui::insert_head_js_file(self::get_scap_plugin_url(), 4);
    }
    
    /**
     * 加载jquery ui theme样式文件
     * 
     * @param string $theme_name 样式名称，默认'smoothness'
     */
    public static function load_jquery_ui_theme($theme_name = 'smoothness')
    {
        $url_base = $GLOBALS['scap']['info']['site_url'].'/module_g_jquery/inc/lib/jquery-ui/themes/';
        
        \scap_ui::insert_head_css_file($url_base.$theme_name.'/jquery-ui.css');
    }
    
    /**
     * 加载jquery ui theme样式文件
     * 
     * @param string $version 指定的版本号，默认为空(使用默认版本)
     * @param string $theme_name 样式名称，默认'smoothness'
     */
    public static function load_jquery_ui_js($version = '1.10.0', $theme_name = 'smoothness')
    {
        $url_base = $GLOBALS['scap']['info']['site_url'].'/module_g_jquery/inc/lib/jquery-ui/';
        
        self::load_jquery_ui_theme($theme_name);
        \scap_ui::insert_head_js_file($url_base."{$version}.min.js", 2);
        \scap_ui::insert_head_js_file($url_base."datepicker.zh.js");// 日历组件中文配置
    }
    
    /**
     * 加载jquery mobile所需js及css文件
     * 
     */
    public static function load_jquery_mobile_base_file()
    {
        $url_base = $GLOBALS['scap']['info']['site_url'].'/module_g_jquery/inc/lib/jquery-mobile/1.3.0/';
        
        \scap_ui::insert_head_js_file($url_base."jquery.mobile.min.js");
        \scap_ui::insert_head_css_file($url_base."jquery.mobile.min.css", '', 1);
    }
    
    /**
     * 加载jquery ui theme样式文件
     * 
     * @param string $theme_name 样式名称，默认'smoothness'
     */
    public static function load_jquery_cookie_base_file()
    {
        $url_base = $GLOBALS['scap']['info']['site_url'].'/module_g_jquery/inc/lib/jquery/';
        
        \scap_ui::insert_head_js_file($url_base.'jquery.cookie.js');
    }
}
?>