<?php
/**
 * page UI 文件
 * create time: 2011-12-28 下午02:46:49
 * @version $Id: class.ui_page.inc.php 161 2014-02-14 03:55:01Z liqt $
 * @author LiQintao
 */
use scap\module\g_tool\matrix;
use scap\module\g_tool\string;
use scap\module\g_template\template;
use scap\module\g_form\form;
use scap\module\g_jquery\jquery;

/**
 * page ui类
 *
 */
class ui_page extends scap_ui
{
    public function __construct()
    {
        parent::__construct();
        
        scap_append_module_include_path('module_g_00');
        scap_load_module_function('module_g_00', 'g');
        scap_load_module_define('module_touchview_basic', 'entity_id');
        scap_load_module_class('module_touchview_book', 'book');
    }
    
    /**
     * 保存page页面内容
     */
    public function save_page_content()
    {
        echo '';
    }
    
    /**
     * 编辑页面内容
     */
    public function edit_page_content()
    {
        //--------变量定义及声明[start]--------
        $data_in    = array();  // 表单输入相关数据
        $data_db    = array();  // 数据库相关数据
        $data_def   = array();  // 相关定义数据
        $data_flag  = array();  // 相关标志数据
        
        $data_in['get'] = array();// 保存表单获取到的get信息
        $data_in['post'] = array();// 保存表单post信息
        $data_in['content'] = array();// 保存主表单数据
        
        $data_def['parent_path'] = array();// 页面路径
        
        $data_def['title'] = '';// 当前界面标题设置
        $data_def['text_act'] = '';// 当前操作描述
        //--------变量定义及声明[end]--------
        
        //--------GET参数处理[start]--------
        $data_in['get']['p_id'] = $_GET['p_id'];// page id
        //--------GET参数处理[end]--------
        
        //--------操作类型分类处理[start]--------
        $data_def['title'] .="编辑页面";
        //--------操作类型分类处理[end]--------
        
        //--------消息/事件处理[start]--------
        switch ($this->current_event_name)
        {
            
        }
        //--------消息/事件处理[end]--------
        
        //--------数据表查询操作[start]--------
        $page = new page($data_in['get']['p_id']);
        $data_db['content'] = $page->read();
        
        // 获取对应book名称
        $data_db['content']['b_name'] = book::get_name_from_id($data_db['content']['b_id']);
        
        // 获取book最大的页码数
        $data_def['book_max_page_sn'] = page::get_child_max_sn($data_db['content']['b_id']);
        
        // 获取上一页
        if ($data_db['content']['p_sort_sn'] == 1)
        {
            // 如果是第一页，则上一页为最后一页
            $data_def['sort_sn_prev'] = $data_def['book_max_page_sn'];
        }
        else
        {
            $data_def['sort_sn_prev'] = $data_db['content']['p_sort_sn'] - 1;
        }
        
        // 获取下一页
        if ($data_db['content']['p_sort_sn'] == $data_def['book_max_page_sn'])
        {
            // 如果是末页，则下一页为第一页
            $data_def['sort_sn_next'] = 1;
        }
        else
        {
            $data_def['sort_sn_next'] = $data_db['content']['p_sort_sn'] + 1;
        }
        
        //--------数据表查询操作[end]--------
        
        //--------影响界面输出的$data_in数据预处理[start]--------
        if (!empty($data_db['content']))
        {
            $data_in['content'] = array_merge($data_in['content'], $data_db['content']);
        }
        
        if (!empty($data_in['post']))// 处理post上来的数据与其它数据来源的合并及相关转化
        {
            $data_in['content'] = array_merge($data_in['content'], $data_in['post']);
        }
        //--------影响界面输出的$data_in数据预处理[end]--------
        
        //--------html元素只读/必填/显示等逻辑设定[start]--------
        
        //--------html元素只读/必填/显示等逻辑设定[end]--------
        
        //--------模版赋值[start]--------
        $data_render['b_id'] = $data_db['content']['b_id'];
        $data_render['p_id'] = $data_in['get']['p_id'];
        $data_render['p_content'] = $data_db['content']['p_content'];
        
        $data_render['path_image'] = "media/{$data_db['content']['b_id']}/";// 当前书籍的image目录
        
        $data_render['tpl_page'] = page::get_page_tpl($data_in['get']['p_id']);
        if (empty($data_render['tpl_page']))
        {
            $data_render['tpl_page'] = 'tpl-page-default';
        }
        
        $data_render['top_page_name'] = page::get_top_page_name($data_in['get']['p_id']);
        $data_render['p_sort_sn'] = $data_db['content']['p_sort_sn'];
        $data_render['url_prev'] = scap_get_url(
            array('module' => $this->current_module_id, 'class' => $this->current_class_name, 'method' => $this->current_method_name),
            array('p_id' => page::get_id_from_sortsn($data_db['content']['b_id'], $data_def['sort_sn_prev']))
        );
        $data_render['url_next'] = scap_get_url(
            array('module' => $this->current_module_id, 'class' => $this->current_class_name, 'method' => $this->current_method_name),
            array('p_id' => page::get_id_from_sortsn($data_db['content']['b_id'], $data_def['sort_sn_next']))
        );
        
        $data_render['url_aloha'] = $data_def['url_aloha'] = $GLOBALS['scap']['info']['site_url'].'/module_touchview_basic/inc/third/aloha/';
        //--------模版赋值[end]----------
        
        //--------构造界面输出[start]--------
        jquery::load_min_base_file();
        form::load_show_system_info_base_file();
        self::insert_head_css_file(scap_get_css_url('module_touchview_book', 'book.reset.css'));
        self::insert_head_css_file(scap_get_css_url('module_touchview_book', 'book.page_edit.css'));
        self::insert_head_css_file($data_def['url_aloha'].'css/aloha.css');
        
        form::load_jcarousellite_base_file();
        form::load_colorbox_base_file();
        self::insert_head_js_file(scap_get_js_url('module_touchview_page', 'image_resize.js'));
        
        $this->render_tpl('edit.page_content.tpl', $data_render)->show_display_content();
        //--------构造界面输出[end]----------
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
        $data_in['get']['steps'] = intval($_GET['steps']) ? intval($_GET['steps']) : 3;
        $data_in['get']['size'] = intval($_GET['size']) ? abs(intval($_GET['size'])) : 5;
        $data_in['get']['width'] = intval($_GET['width']) ? intval($_GET['width']) : 150;
        $data_in['get']['height'] = intval($_GET['height']) ? intval($_GET['height']) : 100;
        $data_in['get']['vertical'] = intval($_GET['vertical']) ? '1' : '0';
        
        $data_in['get']['path'] = isset($_GET['path']) ? $_GET['path'] : 'media/';
        
        //--------GET参数处理[end]--------
        
        //--------数据表查询操作[start]--------
        $data_def['path_image'] = SCAP_PATH_ROOT.$data_in['get']['path'];// 图片目录本地路径
        $data_def['url_image'] = $data_in['get']['path'];// 图片目录URL路径
        
        // 如果指定目录不存在，则自动创建目录
        if (!is_dir($data_def['path_image']))
        {
            mkdir($data_def['path_image']);
        }

		scap_load_module_class('module_g_image', 'image_dir');
		
		$image_dir = new image_dir($data_def['path_image'], $data_def['url_image']);
		
		$data_db['content'] = $image_dir->get_image_list("*.{jpg,JPG,jpeg,JPEG,png,PNG,gif,GIF}");
		
		// 如果图片剩余数量小于显示的个数，则组件会出现bug，因此特殊处理
		if (count($data_db['content']) > 0 && $data_in['get']['size'] > count($data_db['content']))
		{
		    $data_in['get']['size'] = count($data_db['content']);
		}
		//--------数据表查询操作[end]--------
        
        //--------影响界面输出的$data_in数据预处理[start]--------
        
        //--------影响界面输出的$data_in数据预处理[end]-------
        
        //--------模版赋值[start]--------
        $data_render['config']['ul_id'] = $data_in['get']['ul_id'];
        $data_render['config']['steps'] = $data_in['get']['steps'];
        $data_render['config']['size'] = $data_in['get']['size'];
        $data_render['config']['width'] = $data_in['get']['width'];
        $data_render['config']['height'] = $data_in['get']['height'];
        $data_render['config']['vertical'] = $data_in['get']['vertical'];
		
        if (count($data_db['content']) == 0)
        {
            exit('暂无图片');
        }
        else
        {
            $i = 0;
            foreach($data_db['content'] as $k => $v)
            {
                $data_render['data_list'][$i] = $v;
                $data_render['data_list'][$i]['sn'] = $i+1;
                
                $data_render['data_list'][$i]['name'] = $v['name'];
                $data_render['data_list'][$i]['width'] = $v['width'];
                $data_render['data_list'][$i]['height'] = $v['height'];
                $i ++;
            }
        }
        
        //--------模版赋值[end]----------
        
        //--------构造界面输出[start]--------
        $this->render_tpl('show_image_lib.tpl', $data_render)->show_display_content();
        //--------构造界面输出[end]----------
    }
    
    /**
     * 编辑页面内容
     * - 采用传统编辑器
     */
    public function edit()
    {
        //--------变量定义及声明[start]--------
        $data_in    = array();  // 表单输入相关数据
        $data_db    = array();  // 数据库相关数据
        $data_def   = array();  // 相关定义数据
        $data_flag  = array();  // 相关标志数据
        
        $data_in['get'] = array();// 保存表单获取到的get信息
        $data_in['post'] = array();// 保存表单post信息
        $data_in['content'] = array();// 保存主表单数据
        
        $data_def['parent_path'] = array();// 页面路径
        
        $data_def['title'] = '';// 当前界面标题设置
        $data_def['text_act'] = '';// 当前操作描述
        //--------变量定义及声明[end]--------
        
        //--------GET参数处理[start]--------
        $data_in['get']['p_id'] = $_GET['p_id'];// page id
        //--------GET参数处理[end]--------
        
        //--------操作类型分类处理[start]--------
        $data_def['title'] .="编辑页面";
        //--------操作类型分类处理[end]--------
        
        //--------消息/事件处理[start]--------
        switch ($this->current_event_name)
        {
            
        }
        //--------消息/事件处理[end]--------
        
        //--------数据表查询操作[start]--------
        $page = new page($data_in['get']['p_id']);
        $data_db['content'] = $page->read();
        
        // 获取对应book名称
        $data_db['content']['b_name'] = book::get_name_from_id($data_db['content']['b_id']);
        
        // 获取book最大的页码数
        $data_def['book_max_page_sn'] = page::get_child_max_sn($data_db['content']['b_id']);
        
        // 获取上一页
        if ($data_db['content']['p_sort_sn'] == 1)
        {
            // 如果是第一页，则上一页为最后一页
            $data_def['sort_sn_prev'] = $data_def['book_max_page_sn'];
        }
        else
        {
            $data_def['sort_sn_prev'] = $data_db['content']['p_sort_sn'] - 1;
        }
        
        // 获取下一页
        if ($data_db['content']['p_sort_sn'] == $data_def['book_max_page_sn'])
        {
            // 如果是末页，则下一页为第一页
            $data_def['sort_sn_next'] = 1;
        }
        else
        {
            $data_def['sort_sn_next'] = $data_db['content']['p_sort_sn'] + 1;
        }
        
        //--------数据表查询操作[end]--------
        
        //--------影响界面输出的$data_in数据预处理[start]--------
        if (!empty($data_db['content']))
        {
            $data_in['content'] = array_merge($data_in['content'], $data_db['content']);
        }
        
        if (!empty($data_in['post']))// 处理post上来的数据与其它数据来源的合并及相关转化
        {
            $data_in['content'] = array_merge($data_in['content'], $data_in['post']);
        }
        //--------影响界面输出的$data_in数据预处理[end]--------
        
        //--------html元素只读/必填/显示等逻辑设定[start]--------
        
        //--------html元素只读/必填/显示等逻辑设定[end]--------
        
        //--------模版赋值[start]--------
        $data_render['b_id'] = $data_db['content']['b_id'];
        $data_render['p_id'] = $data_in['get']['p_id'];
        $data_render['p_content'] = $data_db['content']['p_content'];
        
        $data_render['path_image'] = "media/{$data_db['content']['b_id']}/";// 当前书籍的image目录
        
        $data_render['tpl_page'] = page::get_page_tpl($data_in['get']['p_id']);
        if (empty($data_render['tpl_page']))
        {
            $data_render['tpl_page'] = 'tpl-page-default';
        }
        
        $data_render['top_page_name'] = page::get_top_page_name($data_in['get']['p_id']);
        $data_render['p_sort_sn'] = $data_db['content']['p_sort_sn'];
        $data_render['url_prev'] = scap_get_url(
            array('module' => $this->current_module_id, 'class' => $this->current_class_name, 'method' => $this->current_method_name),
            array('p_id' => page::get_id_from_sortsn($data_db['content']['b_id'], $data_def['sort_sn_prev']))
        );
        $data_render['url_next'] = scap_get_url(
            array('module' => $this->current_module_id, 'class' => $this->current_class_name, 'method' => $this->current_method_name),
            array('p_id' => page::get_id_from_sortsn($data_db['content']['b_id'], $data_def['sort_sn_next']))
        );
        
        $data_render['url_book_css'] = scap_get_css_url('module_touchview_book', '');
        //--------模版赋值[end]----------
        
        //--------构造界面输出[start]--------
        jquery::load_min_base_file();
        form::load_show_system_info_base_file();
        form::load_xheditor_base_file();// 加载xheditor
        self::insert_head_css_file(scap_get_css_url('module_touchview_book', 'book.page_edit.css'));
        
        $this->render_tpl('edit.page.tpl', $data_render)->show_display_content();
        //--------构造界面输出[end]----------
    }
}
?>