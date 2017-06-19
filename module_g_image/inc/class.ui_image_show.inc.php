<?php
/**
 * description: 图片展示UI文件
 * create time: 2012-2-17 10:22:37
 * @version $Id: class.ui_image_show.inc.php 10 2012-07-23 03:35:21Z liqt $
 * @author Gao Xiang
 */

/**
 * 图片展示UI类
 */
class ui_image_show extends scap_module_ui
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 图片展示UI
     * 
     * 支持的get参数：
     * steps: int 每次翻过的图片数
     * size: float 每次展示的图片数，支持小数
     * width: li的宽度
     * height: li的高度
     * vertical: 0 or 1 是否垂直显示
     * path: 图片路径,默认为 media/
     */
    public function show_image_lib()
    {
        //--------变量定义及声明[start]--------
        $data_in    = array();  // 表单输入相关数据
        $data_db    = array();  // 数据库相关数据
        $data_def   = array();  // 相关定义数据
        
        $data_in['get'] = array();// 保存表单获取到的get信息
        
        $data_in['search'] = array();// 处理后的查询参数数据
        $data_in['extra_vars'] = array();// 构造的额外需传递的参数数据
        $data_db['where'] = '';// 查询参数对应的查询条件语句(不含where关键字)
        
        $data_def['title'] = '';// 当前界面标题设置
        $data_def['text_menu'] = '';
        
        //--------变量定义及声明[end]--------
        
        //--------GET参数处理[start]--------
        $data_in['get']['ul_id'] = empty($_GET['ul_id']) ? 'image_lib_container_id' : $_GET['ul_id'];// ul容器id值
        $data_in['get']['inline'] = intval($_GET['inline']) ? true : false;// 是否是内嵌模式，如果是则本应用不加载任何所需库文件及框架
        $data_in['get']['steps'] = intval($_GET['steps']) ? intval($_GET['steps']) : 1;
        $data_in['get']['size'] = intval($_GET['size']) ? abs(intval($_GET['size'])) : 5;
        $data_in['get']['width'] = intval($_GET['width']) ? intval($_GET['width']) : 160;
        $data_in['get']['height'] = intval($_GET['height']) ? intval($_GET['height']) : 100;
        $data_in['get']['vertical'] = intval($_GET['vertical']) ? '1' : '0';
        
        $data_in['get']['path'] = isset($_GET['path']) ? $_GET['path'] : 'media/';
        
        //--------GET参数处理[end]--------
        
        //--------数据表查询操作[start]--------
        $data_def['path_image'] = SCAP_PATH_ROOT.$data_in['get']['path'];// 图片目录本地路径
        $data_def['url_image'] = $data_in['get']['path'];// 图片目录URL路径

		scap_load_module_class('module_g_image', 'image_dir');
		
		$image_dir = new image_dir($data_def['path_image'], $data_def['url_image']);
		
		$data_db['content'] = $image_dir->get_image_list();
		
		// 如果图片剩余数量小于显示的个数，则组件会出现bug，因此特殊处理
		if (count($data_db['content']) > 0 && $data_in['get']['size'] > count($data_db['content']))
		{
		    $data_in['get']['size'] = count($data_db['content']);
		}
		//--------数据表查询操作[end]--------
        
        //--------影响界面输出的$data_in数据预处理[start]--------
        
        //--------影响界面输出的$data_in数据预处理[end]-------
        
        //--------模版赋值[start]--------
        $data_out['config']['ul_id'] = $data_in['get']['ul_id'];
        $data_out['config']['steps'] = $data_in['get']['steps'];
        $data_out['config']['size'] = $data_in['get']['size'];
        $data_out['config']['width'] = $data_in['get']['width'];
        $data_out['config']['height'] = $data_in['get']['height'];
        $data_out['config']['vertical'] = $data_in['get']['vertical'];
		
        if (count($data_db['content']) == 0)
        {
            $data_out['text_no_data'] = "没有相关数据。";
        }
        else
        {
            $i = 0;
            foreach($data_db['content'] as $k => $v)
            {
                $data_out['data_list'][$i] = $v;
                $data_out['data_list'][$i]['sn'] = $i+1;
                $data_out['data_list'][$i]['row_color'] = scap_html::scap_row_color($i);
                
                $data_out['data_list'][$i]['name'] = $v['name'];
                $data_out['data_list'][$i]['width'] = $v['width'];
                $data_out['data_list'][$i]['height'] = $v['height'];
                $i ++;
            }
        }
        
        //--------模版赋值[end]----------
        
        //--------构造界面输出[start]--------
        $data_def['title'] .= '图片库';
        
        if ($data_in['get']['inline'])
        {
            $this->output_html($data_def['title'], 'show_image_lib.tpl', $data_out, false, false);
        }
        else
        {
            $url_carousellite = $GLOBALS['scap']['info']['site_url'].'/module_g_image/inc/third/jcarousellite/';
            $url_mousewheel = $GLOBALS['scap']['info']['site_url'].'/module_g_image/inc/third/mousewheel/';
            
            scap_module_ui::load_js_file($url_carousellite.'jquery.jcarousellite.min.js');
            scap_module_ui::load_js_file($url_mousewheel.'jquery.mousewheel.min.js');

            $this->output_html($data_def['title'], 'show_image_lib.tpl', $data_out, false, true, true);
        }
        //--------构造界面输出[end]----------
    }
}

?>