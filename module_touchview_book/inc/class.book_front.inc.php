<?php
/**
 * book为前台展示服务的类文件
 * create time: 2012-1-17 上午11:03:00
 * @version $Id: class.book_front.inc.php 127 2012-05-07 03:49:53Z liqt $
 * @author LiQintao
 */

require_once(SCAP_PATH_ROOT.'module_touchview_basic/inc/third/browser.php');
scap_load_module_class('module_touchview_page', 'page');

/**
 * book front服务类
 */
class book_front
{
    /**
     * book id
     * @var string
     */
    private $current_book_id = '';
    
    /**
     * book object
     * @var object book
     */
    private $book = NULL;
    
    /**
     * book的逻辑页面数据结构
     * @var array
     */
    public $pages = array();
    
    /**
     * book当前页的view
     * @var string
     */
    private $current_view = '';
    
    /**
     * book当前页的article
     * @var string
     */
    private $current_article = '';

    /**
     * book当前页的page
     * @var string
     */
    private $current_page = 1;
    
    /**
     * 构造函数
     */
    public function __construct($book_id)
    {
        $this->current_book_id = $book_id;
        
        $this->book = new book($this->current_book_id);
        
        $this->generate_pages();
        
        $browser = new Browser();
        define( 'BROWSER_NAME', $browser->getBrowser() );
        define( 'BROWSER_VERSION', $browser->getVersion() );
    }
    
    /**
     * 设置当前view
     * @param string $view 视图类型:home,credits,tot,404
     */
    public function set_current_view($view)
    {
        $this->current_view = $view;
    }
    
    /**
     * 设置当前article
     * @param string $article 当前页所属article
     */
    public function set_current_article($article)
    {
        $this->current_article = $article;
    }
    
    /**
     * 设置当前page
     * @param string $page 当前页的page
     */
    public function set_current_page($page)
    {
        $this->current_page = $page;
    }
    
    public static function get_array_first_index($arr)
    {
        foreach ($arr as $key => $value)
        {
	        return $key;
        }
    }
    
    public static function get_array_last_index($arr)
    {
        $result = '';
        foreach ($arr as $key => $value)
        {
            $result = $key;
        }
        return $result;
    }
    
    public function is_touchdevice()
    {
        return preg_match( '/iPad|iPhone|iPod|Android/', BROWSER_NAME );
    }
    
    /**
     * 设置body class
     */
    public function body_class()
    {
        $bodyClass = '';

        if( isset($_GET['view']) && ($_GET['view'] == 'home' || $_GET['view'] == 'credits' || $_GET['view'] == 'tot' || $_GET['view'] == '404' ) ) {
            $bodyClass .= $_GET['view'] . ' ';
        }

        if( $this->is_touchdevice() ) {
            $bodyClass .= 'touch-device ';
        }

        return $bodyClass;
    }
    
    /**
     * 获取相邻section的id
     * 
     * @param string $order next:prev
     * 
     * @return string
     */
    public function get_neighbour_section_id($order)
    {
        $id = '';
        $keys = array_keys($this->pages);
        $position = array_search($this->current_article, $keys);
        
        if (isset($keys[$position + 1]) || isset($keys[$position - 1]))
        {
            $id = ($order == 'next') ? $keys[$position + 1] : $keys[$position - 1];
        }
        return $id;
    }
    
    /**
     * 获取上一页链接
     * 
     * @return string 
     */
    public function get_prev_page()
    {
        $url = '';
        
        if ($this->current_view == 'home')
        {
            return $url;
        }
        elseif ($this->current_view == 'credits')
        {
            $article = book_front::get_array_last_index($this->pages);
        }
        
        $pre_article = $this->get_neighbour_section_id('prev');
        if (empty($pre_article) && $this->current_page == 1)// 第一页内容
        {
            // 上一页为封面
            $url = scap_get_url(array('module'=> 'module_touchview_book', 'class' => 'ui_book_front', 'method' => 'book'), array('view' => 'home', 'b_id' => $this->current_book_id));
        }
        else
        {
            if ($this->current_page == 1)// 本章节第一页，对应上一章节的最后一页
            {
                $article = $pre_article;
                $page = $this->pages[$pre_article]['numberOfPages'];
            }
            else// 本章节的上一页
            {
                $article = $this->current_article;
                $page = $this->current_page - 1;
            }

            $url = scap_get_url(array('module'=> 'module_touchview_book', 'class' => 'ui_book_front', 'method' => 'book'), array('article' => $article, 'page' => $page, 'b_id' => $this->current_book_id));
        }
        
        return $url;
    }
    
    /**
     * 获取下一页链接
     * 
     * @return string 
     */
    public function get_next_page()
    {
        $article = '';
        $page = NULL;
        
        if ($this->current_view == 'home')
        {
            $article = book_front::get_array_first_index($this->pages);
        }
        elseif ($this->current_view == 'credits')
        {
            return false;
        }
        elseif ($this->current_article == 'theend')
        {
            $article = 'credits';
        }
        
        if ($this->pages[$this->current_article]['numberOfPages'] == $this->current_page)
        {
            if (!$this->pages[$this->get_neighbour_section_id('next')]['active'])
            {
                $article = 'theend';
            }
            else
            {
                $article = $this->get_neighbour_section_id('next');
            }
        }
        elseif ($this->pages[$this->current_article]['numberOfPages'] > $this->current_page)
        {
            $article = $this->current_article;
            $page = $this->current_page + 1;
        }
        
        $url = scap_get_url(array('module'=> 'module_touchview_book', 'class' => 'ui_book_front', 'method' => 'book'), array('b_id' => $this->current_book_id, 'article' => $article, 'page' => $page));
        
        return $url;
    }
    
    /**
     * 自动生成book的可展示页面数据结构
     * 
     * Defines the content structure of the book using:（来自于20things的定义）
     *
     * stub: 			章节sn(目前只读取第一级章节)
     * numberOfPages:	章节的页面总数
     * title:			章节name
     * subtitle:		子标题(暂无)
     * hidden:			当前章节是否不出现在导航中
     * active:			?是否有效(暂时全部有效)?
     * order:			?Determines the order in which chapters appear.
     * contents:		array('name' => '','content' => ''),章节下所有页面内容
     * globalStartPage:	section开始页码
     * globalEndPage:	section结束页码
     */
    protected function generate_pages()
    {
        $data_in = array();
        $this->pages = array();
        
        // 获得book顶级page列表信息
        $data_in['page_top'] = page::get_child_page_list($this->current_book_id);
        
        $i = 0;
        foreach($data_in['page_top'] as $k => $v)
        {
            $temp_child_pages = array();// 存储当前page下的所有子page列表
            page::get_all_child_pageid_list($v['p_id'], $temp_child_pages);
            
            $this->pages[$v['p_sn']]['stub'] = $v['p_sn'];
            $this->pages[$v['p_sn']]['title'] = $v['p_name'];
            $this->pages[$v['p_sn']]['numberOfPages'] = count($temp_child_pages) + 1;
            $this->pages[$v['p_sn']]['subtitle'] = '';
            $this->pages[$v['p_sn']]['active'] = true;
            $this->pages[$v['p_sn']]['order'] = $i;
            $this->pages[$v['p_sn']]['hidden'] = ($v['p_type'] != TYPE_PAGE_SECTION);// 仅章节才在导航栏出现
            
            $this->pages[$v['p_sn']]['contents'] = array();
            $this->pages[$v['p_sn']]['contents'][] = array('id' => $v['p_id'], 'name' => $v['p_name'], 'content' => $v['p_content'], 'page_number' => $v['p_sort_sn']);
            foreach($temp_child_pages as $v2)
            {
                $this->pages[$v['p_sn']]['contents'][] = array('id' => $v2, 'name' => page::get_name_from_id($v2), 'content' => page::get_content_from_id($v2), 'page_number' => page::get_sortsn_from_id($v2));
            }
            $i ++;
        }
        //the end page
        $this->pages['theend'] = array(
			'stub' => 'theend',
			'numberOfPages' => 1,
			'title' => '',
			'subtitle' => '',
			'active' => true,
		    'hidden' => true,
			'order' => $i,
            'contents' => array(array('name' => '', 'content' => ''))
		);
		
		// Set up page counters
		$totalNumberOfPages = 1;
		foreach ($this->pages as $k => $v)
		{
		    $this->pages[$k]['globalStartPage'] = $totalNumberOfPages;
		    $totalNumberOfPages += $this->pages[$k]['numberOfPages'];
		    $this->pages[$k]['globalEndPage'] = $totalNumberOfPages - 1;
		}
        
    }
    
}
?>