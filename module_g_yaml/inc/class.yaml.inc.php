<?php
/**
 * yaml类实现文件
 * 
 * @package module_g_yaml
 * @subpackage model
 * @version $Id: class.yaml.inc.php 499 2013-05-10 10:02:52Z liqt $
 * @creator liqt @ 2012-12-11 17:43:56 by caster0.0.2
 */
namespace scap\module\g_yaml;

/**
 * yaml服务类
 */
class yaml
{
    /**
     * 加载yaml所需的必要css/js等文件
     * 
     * @param string $default_style_name 默认应用样式名称，默认'default'
     */
    public static function load_yaml_base_file($default_style_name = 'default')
    {
        $url_yaml = $GLOBALS['scap']['info']['site_url'].'/module_g_yaml/inc/lib/yaml/';
        $url_html5shiv = $GLOBALS['scap']['info']['site_url'].'/module_g_yaml/inc/lib/html5shiv/';
        $url_scap = $GLOBALS['scap']['info']['site_url'].'/module_g_yaml/inc/lib/scap/';
        
        \scap_ui::insert_head_css_file($url_yaml.'core/base.css', '', 1);
        \scap_ui::insert_head_css_file($url_scap."{$default_style_name}.css", '', 2);// 默认应用样式
        \scap_ui::insert_head_css_file($url_yaml.'print/print.css', 'print', 3);
        \scap_ui::insert_head_css_file($url_yaml.'core/iehacks.css', '', 1000, 'lte IE 7');// ie hack,最后加载
        
        \scap_ui::insert_head_js_file($url_html5shiv.'html5shiv.js', NULL, 'lt IE 9');
    }
    
}
?>