<?php
/**
 * book相关前台展示UI文件
 * create time: 2012-1-17 上午10:51:47
 * @version $Id: class.ui_book_front.inc.php 158 2014-02-12 03:32:58Z liqt $
 * @author LiQintao
 */
use scap\module\g_tool\matrix;
use scap\module\g_tool\string;
use scap\module\g_template\template;
use scap\module\g_form\form;

class ui_book_front extends scap_ui
{
    public function __construct()
    {
        parent::__construct();
        
        scap_load_module_define('module_touchview_book', 'book_status');
    }
    
    /**
     * 显示书籍样式
     */
    public function book()
    {
        //--------变量定义及声明[start]--------
        $data_in    = array();  // 表单输入相关数据
        $data_db    = array();  // 数据库相关数据
        $data_def   = array();  // 相关定义数据
        $data_flag  = array();  // 相关标志数据
        $data_render = array();
        
        $data_in['get'] = array();// 保存表单获取到的get信息
        $data_in['post'] = array();// 保存表单post信息
        $data_in['content'] = array();// 保存主表单数据
        
        $data_def['title'] = '';// 当前界面标题设置
        $data_def['text_act'] = '';// 当前操作描述
        //--------变量定义及声明[end]--------
        
        //--------GET参数处理[start]--------
        $data_in['get']['b_id'] = $_GET['b_id'];
        $data_in['get']['force_update'] = empty($_GET['force_update']) ? 0 : 1;// 强制更新标志
        $data_in['get']['view'] = $_GET['view'];// 视图类型:home,credits,tot,404
        $data_in['get']['article'] = $_GET['article'];// 书籍所属章节
        $data_in['get']['page'] = !empty($_GET['page']) ? $_GET['page'] : 1;// 页码
        //--------GET参数处理[end]--------
        
        //--------操作类型分类处理[start]--------
        
        //--------操作类型分类处理[end]--------
        
        //--------消息/事件处理[start]--------
        switch ($this->current_event_name)
        {
            
        }
        //--------消息/事件处理[end]--------
        
        //--------数据表查询操作[start]--------
        $book = new book($data_in['get']['b_id']);
        $book_front = new book_front($data_in['get']['b_id']);
        $book_front->set_current_view($data_in['get']['view']);
        $book_front->set_current_article($data_in['get']['article']);
        $book_front->set_current_page($data_in['get']['page']);
        $data_db['book'] = $book->read();
        $data_db['book_list'] = book::get_book_list();
        //--------数据表查询操作[end]--------
        
        //--------影响界面输出的$data_in数据预处理[start]--------
        
        //--------影响界面输出的$data_in数据预处理[end]--------
        
        //--------html元素只读/必填/显示等逻辑设定[start]--------
        
        //--------html元素只读/必填/显示等逻辑设定[end]--------
        
        //--------模版赋值[start]--------
        
        $data_render['b_id'] = $data_in['get']['b_id'];
        $data_render['path_template'] = "module_touchview_book/templates/default/book_templates/".book::get_book_tpl($data_in['get']['b_id'])."/";
        $data_render['force_update'] = $data_in['get']['force_update'];
        $data_render['b_name'] = $data_db['book']['b_name'];
        $data_render['b_description'] = $data_db['book']['b_description'];
        $data_render['config_auto_flip_switch'] = book::get_config_book_auto_flip_switch();
        $data_render['config_auto_flip_waiting'] = book::get_config_book_auto_flip_waiting();
        
        $data_render['is_article'] = !empty($data_in['get']['article']);
        // 生成当前page显示信息（用于url page定位）
        $data_render['current_page'] = array();
        if ($data_render['is_article'])
        {
            $data_render['current_page']['class'] = "title-".$book_front->pages[$data_in['get']['article']]['stub']; 
            $data_render['current_page']['class'] .= " page-".$data_in['get']['page']; 
            
            $data_render['current_page']['number'] = $data_in['get']['page'];
            $data_render['current_page']['title'] = $book_front->pages[$data_in['get']['article']]['title'];
            $data_render['current_page']['subtitle'] = $book_front->pages[$data_in['get']['article']]['subtitle'];
            $data_render['current_page']['content'] = html_entity_decode($book_front->pages[$data_in['get']['article']]['contents'][$data_in['get']['page']-1]['content']);
        }
        
        $data_render['body_class'] = $book_front->body_class();
        
        $data_render['book_list'] = array();
        $i = 0;
        foreach($data_db['book_list'] as $v)
        {
            $data_render['book_list'][$i]['class'] = ($data_in['get']['b_id'] == $v['b_id']) ? 'selected' : '';
            $data_render['book_list'][$i]['link'] = scap_get_url(array('module'=> 'module_touchview_book', 'class' => 'ui_book_front', 'method' => 'book'), array('view' => 'home', 'force_update' => '1', 'b_id' => $v['b_id']));
            $data_render['book_list'][$i]['name'] = $v['b_name'];

            $i ++;
        }
        
        $data_render['nav_list'] = array();
        
        $i = 0;
        foreach ( $book_front->pages as $key => $value )
        {
            if (!empty($value['hidden']))
            {
                continue;
            }
            $data_render['nav_list'][$i]['chapter_counter'] = $i+1;
            $data_render['nav_list'][$i]['data_list']  = ' data-title="'.$value['title'].'"';
            $data_render['nav_list'][$i]['data_list'] .= ' data-subtitle="'.str_replace( '"', "'", $value['subtitle'] ).'"';
            $data_render['nav_list'][$i]['data_list'] .= ' data-article="'.$key.'"';
            $data_render['nav_list'][$i]['data_list'] .= ' data-globalstartpage="'.$value['globalStartPage'].'"';
            $data_render['nav_list'][$i]['data_list'] .= ' data-globalendpage="'.$value['globalEndPage'].'"';
            $data_render['nav_list'][$i]['data_list'] .= ' data-numberofpages="'.$value['numberOfPages'].'"';
            	
            $data_render['nav_list'][$i]['class'] = $value['active'] ? $key : 'disabled '.$key;
            $data_render['nav_list'][$i]['title'] = $value['title'];
            $data_render['nav_list'][$i]['link'] = scap_get_url(array('module'=> 'module_touchview_book', 'class' => 'ui_book_front', 'method' => 'book'), array('article' => $data_render['nav_list'][$i]['class']));
            
            if( $value['globalStartPage'] == $value['globalEndPage'] )
            {
                $data_render['nav_list'][$i]['pages'] = ''.$value['globalStartPage'];
            }
            else
            {
                $data_render['nav_list'][$i]['pages'] = ''.$value['globalStartPage'].'-'.$value['globalEndPage'];
            }
            
            $i ++;
        }
        
        $data_render['toc_list'] = array();
        
        $i = 0;
        foreach ( $book_front->pages as $key => $value )
        {
            if (!empty($value['hidden']))
            {
                continue;
            }
            $data_render['toc_list'][$i]['chapter_counter'] = $i+1;
            $data_render['toc_list'][$i]['class'] = $value['active'] ? $key : 'disabled '.$key;
            $data_render['toc_list'][$i]['link'] = scap_get_url(array('module'=> 'module_touchview_book', 'class' => 'ui_book_front', 'method' => 'book'), array('article' => $key));
            $data_render['toc_list'][$i]['article'] =  $key;
            $data_render['toc_list'][$i]['title'] =  $value['title'];
            $data_render['toc_list'][$i]['sub_title'] =  str_replace( '"', "'", $value['subtitle'] );
            
            $i ++;
        }
        
        // 书籍的总页数
        $data_render['pages_count'] = book::get_book_page_count($data_in['get']['b_id']);
        
        $data_render['url_jquery'] = \scap\module\g_jquery\jquery::get_js_url();
        //--------模版赋值[end]----------
        
        //--------构造界面输出[start]--------
        
        $this->render_tpl('front.book.tpl', $data_render)->show_display_content();
        //--------构造界面输出[end]----------
    }
    
    /**
     * 获得book的页面数据
     */
    public function get_pages()
    {
        $data_in['get']['b_id'] = $_GET['b_id'];
        $data_render = '';
        
        $book_front = new book_front($data_in['get']['b_id']);
        
        foreach($book_front->pages as $k => $v)
        {
            if( $v['active'] ) 
            {
                $data_render .= "<article id='{$k}'>";
                
                foreach($v['contents'] as $k2 => $v2)
                {
                    $temp_page = $k2 + 1;
                    if (!empty($v2['id']))
                    {
                        $temp_tpl = page::get_page_tpl($v2['id']);
                    }
                    $data_render .=<<<HTML
<section class='title-{$v['stub']} page-{$temp_page}' title='{$v['title']}'>
	<div class='page'>
		<div class='page-title'>{$v['title']}</div>
		<div class='page-content {$temp_tpl}'>{$v2['content']}</div>
		<span class="pageNumber">{$v2['page_number']}</span>
	</div>
</section>
HTML;
                }
                
                $data_render .= "</article>";
            }
        }
        
        echo $data_render;
    }
}
?>