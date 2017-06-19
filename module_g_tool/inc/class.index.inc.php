<?php
/**
 * 索引相关类
 * 
 * @package module_g_tool
 * @subpackage model
 * @version $Id: class.index.inc.php 715 2013-08-22 08:42:55Z liqt $
 * @creator liqt @ 2013-01-24 15:33:23 by caster0.0.2
 */
namespace scap\module\g_tool;

/**
 * 索引相关类
 * 
 */
class index
{
    /**
     * 显示分页步长的选择
     * 
     * @param int $steps 查询的步长(最大个数)
     * @param array $step_options 步长选项集合
     * @param array $extra 额外参数数组(key => value)
     * @param array $method 指定的方法调用信息数组,如果为空默认为当前调用
     * 
     * @return string 返回相应界面元素
     */
    public static function index_steps_select($steps, $steps_options = array(5, 10, 20, 40, 80), $extra = array(), $method = array())
    {
        $rtn = '';
        if (empty($method))
        {
            $method['module'] = $GLOBALS['scap']['info']['current_module_id'];
            $method['class'] = $GLOBALS['scap']['info']['current_class'];
            $method['method'] = $GLOBALS['scap']['info']['current_method'];
        }
    
        $params = $extra;
    
        $start = intval($start);
        $pages = intval($pages);
    
        foreach($steps_options as $k => $v)
        {
            $params['steps'] = $v;
            $rtn .= "&nbsp;".\scap_html::anchor(array('class' => 'step_number '.(($steps == $v) ? 'current' : ''), 'title' => "每页显示{$v}个", 'href' => scap_get_url($method, $params)), $v);
        }
    
        return $rtn;
    }
    
    
    /**
     * 显示索引分页
     * liqt:花费2h 2011-5-19 9:30-11:30
     * 输出设计：(假设共有51页)
     * 1.当前页是第1页：
     * 显示 [1] 2 3 4 5 ... 51 下一页
     * 2.当前页是最后一页：
     * 显示 上一页 1 ... 47 48 49 50 [51]
     * 3.当前页是第10页
     * 显示 上一页 1 ... 8 9 [10] 11 12 ... 51 下一页
     * 
     * 原则：显示当前页前后2页链接，同时总是显示首页和尾页链接及 上一页 下一页链接。
     * 
     * @param int $start 当前的开始位置(0-based)
     * @param int $pages 共有多少页
     * @param array $extra 额外参数数组(key => value)
     * @param array $method 指定的方法调用信息数组,如果为空默认为当前调用
     * @param bool $flag_always_show 总是显示，默认false(如果页数不大于1，则不显示)
     * 
     * @return string 返回相应界面元素
     */
    public static function index_split_pages(
            $start, $pages, $extra = array(), $method = array(), $flag_always_show = false, 
            $config_text = array('page_next' => '下一页', 'page_prev' => '上一页')
    )
    {
        $data_out = array();
        $data_in = array();
        $params = array();//参数信息
        $rtn = '';
        
        // 没有匹配结果
        if ($pages < 1 || ($pages == 1 && !$flag_always_show))
        {
            return $rtn;
        }
        
        if (empty($method))
        {
            $method['module'] = $GLOBALS['scap']['info']['current_module_id'];
            $method['class'] = $GLOBALS['scap']['info']['current_class'];
            $method['method'] = $GLOBALS['scap']['info']['current_method'];
        }
        
        $data_in['first_page'] = 1;// 第一页
        $data_in['current_page'] = intval($start) + 1;// 当前页码
        $data_in['pre_page'] = $data_in['current_page'] - 1;// 上一页码
        $data_in['pre2_page'] = $data_in['pre_page'] - 1;// 上2页码
        $data_in['next_page'] = $data_in['current_page'] + 1;// 下一页码
        $data_in['next2_page'] = $data_in['next_page'] + 1;// 下2页码
        $data_in['last_page'] = intval($pages);// 最后一页
        
        // 构造first page链接
        $params['first_page'] = $extra;
        $params['first_page']['start'] = $data_in['first_page'] - 1;
        $data_out['first_page'] = \scap_html::anchor(array('class' => 'page_item first', 'href' => scap_get_url($method, $params['first_page'])), "<span>{$data_in['first_page']}</span>");
        
        // 构造pre page链接
        $params['pre_page'] = $extra;
        $params['pre_page']['start'] = $data_in['pre_page'] - 1;
        $data_out['pre_page'] = \scap_html::anchor(array('class' => 'page_prev', 'href' => scap_get_url($method, $params['pre_page'])), "<span>{$config_text['page_prev']}</span>");
        $data_out['pre_page_number'] = \scap_html::anchor(array('class' => 'page_number', 'href' => scap_get_url($method, $params['pre_page'])), "<span>{$data_in['pre_page']}</span>");
        
        // 构造pre2 page链接
        $params['pre2_page'] = $extra;
        $params['pre2_page']['start'] = $data_in['pre2_page'] - 1;
        $data_out['pre2_page_number'] = \scap_html::anchor(array('class' => 'page_number', 'href' => scap_get_url($method, $params['pre2_page'])), "<span>{$data_in['pre2_page']}</span>");
        // 如果current page是倒数1页或者倒数2页，后置页不够2，则我们补在前置页中，让 前置页+当前页+后置页 总数保持在5
    //    if ($data_in['current_page'] == $data_in['last_page'] || $data_in['current_page'] == ($data_in['last_page'] - 1))
    //    {
    //        $data_in['pre2_page'] = $data_in['pre2_page'] - 1;
    //        $params['pre2_page']['start'] = $data_in['pre2_page'] - 1;
    //        $data_out['pre2_page_number'] .= \scap_html::anchor(array('href' => scap_get_url($method, $params['pre2_page'])), "<span class='page_number'>{$data_in['pre2_page']}</span>");
    //    }
        
        // 构造current page
        $data_out['current_page'] = "<a href='#' class='page_number current'><span>{$data_in['current_page']}</span></a>";
        
        // 构造next page链接
        $params['next_page'] = $extra;
        $params['next_page']['start'] = $data_in['next_page'] - 1;
        // 下一页按钮
        $data_out['next_page'] = \scap_html::anchor(array('class' => 'page_next', 'href' => scap_get_url($method, $params['next_page'])), "<span>{$config_text['page_next']}</span>");
        // 当前后一页
        $data_out['next_page_number'] = \scap_html::anchor(array('class' => 'page_number', 'href' => scap_get_url($method, $params['next_page'])), "<span>{$data_in['next_page']}</span>");
        
        // 构造next2 page链接
        $params['next2_page'] = $extra;
        $params['next2_page']['start'] = $data_in['next2_page'] - 1;
        // 当前后两页
        $data_out['next2_page_number'] = \scap_html::anchor(array('class' => 'page_number', 'href' => scap_get_url($method, $params['next2_page'])), "<span>{$data_in['next2_page']}</span>");
        // 如果current page是第1页或者第2页，前置页不够2，则我们补在后置页中，让 前置页+当前页+后置页 总数保持在5
        if ($data_in['current_page'] == $data_in['first_page'] || $data_in['current_page'] == ($data_in['first_page'] + 1))
        {
            $data_in['next2_page'] = $data_in['next2_page'] + 1;
            $params['next2_page']['start'] = $data_in['next2_page'] - 1;
            $data_out['next2_page_number'] .= \scap_html::anchor(array('class' => 'page_number', 'href' => scap_get_url($method, $params['next2_page'])), "<span>{$data_in['next2_page']}</span>");
        }
        
        
        // 构造last page链接
        $params['last_page'] = $extra;
        $params['last_page']['start'] = $data_in['last_page'] - 1;
        $data_out['last_page'] = \scap_html::anchor(array('class' => 'page_number page_last', 'href' => scap_get_url($method, $params['last_page'])), "<span>{$data_in['last_page']}</span>");
        
        // 构造前置省略符
        $data_out['pre_ellipsis'] = "<span class='page_number_dots'> ... </span>";
        
        // 构造后置省略符
        $data_out['next_ellipsis'] = "<span class='page_number_dots'> ... </span>";
        
        // 处理特殊情况
        if ($data_in['current_page'] == $data_in['first_page'])
        {
            $data_out['first_page'] = '';
            $data_out['pre_page'] = '';
            $data_out['pre_page_number'] = '';
            $data_out['pre_ellipsis'] = '';
        }
        
        if ($data_in['pre2_page'] <= 0)
        {
            $data_out['pre2_page_number'] = '';
            $data_out['pre_ellipsis'] = '';
        }
        
        if ($data_in['pre_page'] == $data_in['first_page'] || $data_in['pre2_page'] == $data_in['first_page'])
        {
            $data_out['first_page'] = '';
            $data_out['pre_ellipsis'] = '';
        }
        
        if (($data_in['pre2_page'] - $data_in['first_page']) < 2)
        {
            $data_out['pre_ellipsis'] = '';
        }
        
        if ($data_in['current_page'] == $data_in['last_page'])
        {
            $data_out['last_page'] = '';
            $data_out['next_page'] = '';
            $data_out['next_page_number'] = '';
            $data_out['next_ellipsis'] = '';
        }
        
        if ($data_in['next2_page'] > $data_in['last_page'])
        {
            $data_out['next2_page_number'] = '';
            $data_out['next_ellipsis'] = '';
        }
        
        if ($data_in['next_page'] == $data_in['last_page'] || $data_in['next2_page'] == $data_in['last_page'])
        {
            $data_out['last_page'] = '';
            $data_out['next_ellipsis'] = '';
        }
        
        if (($data_in['last_page'] - $data_in['next2_page']) < 2)
        {
            $data_out['next_ellipsis'] = '';
        }
        
        $rtn .= $data_out['pre_page'];
        $rtn .= $data_out['first_page'];
        $rtn .= $data_out['pre_ellipsis'];
        $rtn .= $data_out['pre2_page_number'];
        $rtn .= $data_out['pre_page_number'];
        $rtn .= $data_out['current_page'];
        $rtn .= $data_out['next_page_number'];
        $rtn .= $data_out['next2_page_number'];
        $rtn .= $data_out['next_ellipsis'];
        $rtn .= $data_out['last_page'];
        $rtn .= $data_out['next_page'];
    //    echo $data_out['pre2_page_number'];exit;
        
        return $rtn;
    }
    
	/**
	 * 索引排序输出函数
	 * 
	 * @param string $sort 排序标志:(ASC/DESC) ascend升序/descend降序
	 * @param string $var 排序对应的数据表项名称
	 * @param string $order 当前要排序的表项名称
	 * @param string $text 文本显示名称
	 * @param array $extra 额外参数数组(key => value)
	 * @param string $default_sort 默认排序，默认为DESC
	 * @param array $method 指定的方法调用信息数组,如果为空默认为当前调用
	 * 
	 * @return string 返回相应界面元素
	 */
	static public function index_order($sort, $var, $order, $text, $extra = array(), $default_sort = 'DESC', $method = array())
	{
		if (empty($method))
		{
			$method['module'] = $GLOBALS['scap']['info']['current_module_id'];
			$method['class'] = $GLOBALS['scap']['info']['current_class'];
			$method['method'] = $GLOBALS['scap']['info']['current_method'];
		}
		
		// 当前span类名称
		$class = 'sort-select-no';//默认为未选中
		
		if ($order == $var)
		{
		    $class = ($sort == 'ASC') ? 'sort-select-asc' : 'sort-select-desc';
			$sort = ($sort == 'ASC') ? 'DESC' : 'ASC';
		}
		else
		{
			$sort = $default_sort;
		}
        
		$text = "<span class='{$class}'><span class='text'>{$text}</span><span class='icon'></span></span>";
		$params = $extra;
		$params['order'] = $var;
		$params['sort'] = $sort;
		
		$rtn = \scap_html::anchor(array('title' => '点击可进行排序', 'href' => scap_get_url($method, $params)), $text);
		return $rtn;
	}
}
?>