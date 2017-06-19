<?php
/**
 * 图片通用服务类文件
 * 
 * @package module_g_image
 * @subpackage model
 * @version $Id: class.image.inc.php 739 2013-09-27 09:21:42Z liqt $
 * @creator liqt @ 2011-11-22 17:05:12
 */
namespace scap\module\g_image;

/**
 * 图片通用服务类
 */
class image
{
    /**
     * 获取lib库的url路径
     * 
     * @return string url路径，末尾带/
     */
    public static function get_lib_path_url()
    {
        return $GLOBALS['scap']['info']['site_url'].'/module_g_image/inc/lib/';
    }
    
    /**
     * 加载scap/preview库所需基础文件
     * 
     * @return void
     */
    public static function load_upload_preview_base_file()
    {
        \scap_ui::insert_head_js_file(self::get_lib_path_url().'scap/image_upload_preview.js');
    }
    
	/**
     * 加载colorbox所必需的js/css等文件
     * 
     */
    public static function load_nivoslider_base_file()
    {
        $url_lib = self::get_lib_path_url().'nivo-slider/3.2/';
        $url_lib_scap = self::get_lib_path_url().'scap/';
        
        \scap_ui::insert_head_css_file($url_lib."theme/default/default.css");
        \scap_ui::insert_head_css_file($url_lib."theme/bar/bar.css");
        \scap_ui::insert_head_css_file($url_lib."theme/dark/dark.css");
        \scap_ui::insert_head_css_file($url_lib."theme/light/light.css");
        \scap_ui::insert_head_css_file($url_lib."nivo-slider.css");
        \scap_ui::insert_head_js_file($url_lib."jquery.nivo.slider.pack.js");
        \scap_ui::insert_head_js_file($url_lib_scap."nivo_slider.js");
    }
    
    /**
     * 根据g_config中图片轮询定义自动生成轮询图的dom结构
     * - 需要配合使用module_g_config 1.0.3+的图片定义机制
     * - 调用scap\module\g_config\define_item_image的所支持的结构
     * 
     * @param string $defined_category_id 定义的类别id
     * @param string $class_root 根部div的类名称
     * 
     * @return string html
     */
    public static function build_dom_for_image_slider($defined_category_id, $class_root)
    {
        $html = sprintf('<div class="%s">', $class_root);
        
        foreach(\scap\module\g_config\define_category::category($defined_category_id)->items as $k => $v)
        {
            if (!$v->check_exist())
            {
                continue;// 图片不存在则忽略
            }
            
            $current_url = $v->get_value('url');// 图片链接
            $current_src = $v->get_full_url();// 图片路径
            $current_description = $v->get_value('description');// 描述
            
            $current_dom_img = sprintf('<img class="img-%s" src="%s" alt="%s" title="%s" />', $k, $current_src, $k, $current_description);
            
            if (empty($current_url))
            {
                $html .= $current_dom_img;// 如果链接为空，则不添加a
            }
            else
            {
                $html .= sprintf('<a href="%s" class="img-%s">%s</a>', $current_url, $k, $current_dom_img);
            }
        }
        
        $html .= '</div>';
        
        return $html;
    }
}
?>