<?php
/**
 * description: 与scap相关的界面输出类
 * create time: 2006-11-16 16:48:29
 * @version $Id: core.class.scap_html.inc.php 50 2012-11-07 09:53:40Z liqt $
 * @author LiQintao(leeqintao@gmail.com)
 */

require_once(SCAP_PATH_LIBRARY.'class.html_output.php');
class scap_html extends html_output
{
    public function __construct()
    {

    }
    
	/**
	 * 索引排序输出函数
	 * 
	 * @param string $sort 排序标志:(ASC/DESC) ascend升序/descend降序
	 * @param string $var 排序对应的数据表项名称
	 * @param string $order 当前要排序的表项名称
	 * @param string $text 文本显示名称
	 * @param array $extra 额外参数数组(key => value)
	 * @param array $method 指定的方法调用信息数组,如果为空默认为当前调用
	 * 
	 * @return string 返回相应界面元素
	 */
	static public function scap_index_order($sort, $var, $order, $text, $extra = array(), $method = array())
	{
		if (empty($method))
		{
			$method['module'] = $GLOBALS['scap']['info']['current_module_id'];
			$method['class'] = $GLOBALS['scap']['info']['current_class'];
			$method['method'] = $GLOBALS['scap']['info']['current_method'];
		}
		
		if ($order == $var)
		{
			$sort = $sort == 'ASC' ? 'DESC' : 'ASC';
			$text = "<b>$text</b>".scap_html::image(array('border' => 0, 'src' => scap_get_image_url('module_basic', $sort=='ASC'? 'up-arrow.png' : 'down-arrow.png')));
		}
		else
		{
			$sort = 'ASC';
		}

		$params = $extra;
		$params['order'] = $var;
		$params['sort'] = $sort;
		
		$rtn = scap_html::anchor(array('title' => '点击可进行排序', 'href' => scap_get_url($method, $params)), $text);
		return $rtn;
	}
	
	/**
	 * 索引分页(减)输出函数
	 * 
	 * @param int $start 当前的开始位置(0-based)
	 * @param array $extra 额外参数数组(key => value)
	 * @param array $method 指定的方法调用信息数组,如果为空默认为当前调用
	 * 
	 * @return string 返回相应界面元素
	 */
	static public function scap_index_page_prev($start, $extra = array(), $method = array())
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
		if ($start > 0)
		{
			$params['start'] = 0;
			// first page
			$rtn .= scap_html::anchor(array('title' => '至首页', 'href' => scap_get_url($method, $params)), scap_html::image(array('border' => 0, 'src' => scap_get_image_url('module_basic', 'arrow-first.gif'))));
			
			$params['start'] = $start - 1;
			// previous page
			$rtn .= "&nbsp;".scap_html::anchor(array('title' => '往前翻页', 'href' => scap_get_url($method, $params)), scap_html::image(array('border' => 0, 'src' => scap_get_image_url('module_basic', 'arrow-prev.gif'))));
		}
		else
		{
			$rtn .= scap_html::image(array('title' => '已是首页', 'border' => 0, 'src' => scap_get_image_url('module_basic', 'arrow-first.gif')));
			$rtn .= "&nbsp;".scap_html::image(array('title' => '已是首页', 'border' => 0, 'src' => scap_get_image_url('module_basic', 'arrow-prev.gif')));
		}
		
		return $rtn;
	}
	
	/**
	 * 索引分页(加)输出函数
	 * 
	 * @param int $start 当前的开始位置(0-based)
	 * @param int $pages 共有多少页
	 * @param array $extra 额外参数数组(key => value)
	 * @param array $method 指定的方法调用信息数组,如果为空默认为当前调用
	 * 
	 * @return string 返回相应界面元素
	 */
	static public function scap_index_page_next($start, $pages, $extra = array(), $method = array())
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
		
		if (($start + 1) < $pages)
		{
			$params['start'] = $start + 1;
			// next page
			$rtn .= scap_html::anchor(array('title' => '往后翻页', 'href' => scap_get_url($method, $params)), scap_html::image(array('border' => 0, 'src' => scap_get_image_url('module_basic', 'arrow-next.gif'))));
		
			$params['start'] = $pages - 1;
			// last page
			$rtn .= "&nbsp;".scap_html::anchor(array('title' => '至尾页', 'href' => scap_get_url($method, $params)), scap_html::image(array('border' => 0, 'src' => scap_get_image_url('module_basic', 'arrow-last.gif'))));
		}
		else
		{
			$rtn .= scap_html::image(array('title' => '已是尾页', 'border' => 0, 'src' => scap_get_image_url('module_basic', 'arrow-next.gif')));
			$rtn .= "&nbsp;".scap_html::image(array('title' => '已是尾页', 'border' => 0, 'src' => scap_get_image_url('module_basic', 'arrow-last.gif')));
		}
		
		return $rtn;
	}
	
	/**
	 * 显示当前索引页面条目信息
	 * 
	 * @param int $start 当前的开始位置(0-based)
	 * @param int $steps 查询的步长(最大个数)
	 * @param int $pages 共有多少页
	 * @param int $total 查询结果的总数目
	 * 
	 * @return string 返回相应界面元素
	 */
	static public function scap_index_page_tip($start, $steps, $pages, $total)
	{
		$rtn = '';
		
		$text = sprintf("第 <span style=\"color:#00681C;font-weight:bold;\">%d－%d</span> 条（共 <b>%d</b> 条）&nbsp;&nbsp;第 <span style=\"color:#00681C;font-weight:bold;\">%d</span> 页（共 <b>%d</b> 页）", 
						($start * $steps + 1),
						($pages - $start) > 1 ? ($start * $steps + $steps) : $total,
						$total,
						($start + 1),
						$pages
				);
		$rtn = $text;
		return $rtn;
	}
	
	/**
	 * 显示索引分页选择
	 * 
	 * @param int $start 当前的开始位置(0-based)
	 * @param int $pages 共有多少页
	 * 
	 * @return string 返回相应界面元素
	 */
	static public function scap_index_pages_select($start, $pages)
	{
		$rtn = '';
		
		// 构造option
		$options = array();
		for($i = 0; $i < $pages; $i ++)
		{
			$options[$i] = ($i + 1).'页';
		}
		$selects[] = $start;
		$rtn .= scap_html::select(array('name' => 'sel_page', 'onchange' => 'this.form.submit();', 'title' => '分页选择，可通过选择页面直接进行跳转。'), $options, $selects);
		
		return $rtn;
	}
	
	/**
	 * 获取分页选择的当前所选页面
	 * 
	 * @return int 返回当前所选择的页面数值或者NULL
	 */
	static public function scap_index_pages_select_get()
	{
		if (!is_null($_POST['sel_page']))
		{
			$rtn = intval($_POST['sel_page']);
		}
		else
		{
			$rtn = NULL;
		}
		
		return $rtn;
	}
	
	/**
	 * 显示分页步长的选择
	 * 
	 * @param int $steps 查询的步长(最大个数)
	 * @param array $step_options 步长选项集合
	 * 
	 * @return string 返回相应界面元素
	 */
	static public function scap_index_steps_select($steps, $steps_options = array(5, 10, 20, 40, 80))
	{
		$rtn = '';
		
		// 构造option
		$options = array();
		foreach($steps_options as $k => $v)
		{
			$options[$v] = $v.'条';
		}
		
		$selects[] = $steps;
		$rtn .= scap_html::select(array('name' => 'sel_steps', 'onchange' => 'this.form.submit();', 'title' => '每页显示最大条目选择，可通过选择选项改变每页显示最大的条目数。'), $options, $selects);
		
		return $rtn;
	}
	
	/**
	 * 获取分页选择的当前所选页面
	 * 
	 * @return int 返回当前所选择的页面数值或者NULL
	 */
	static public function scap_index_steps_select_get()
	{
		if (!is_null($_POST['sel_steps']))
		{
			$rtn = intval($_POST['sel_steps']);
		}
		else
		{
			$rtn = NULL;
		}
		
		return $rtn;
	}
	
	/**
	 * 构造行交叉底色的css背景色代码
	 * 
	 * @param int $count 行数计数器
	 * @param string $bgcolor1 偶数行背景颜色，默认值为"#FFFFE7"
	 * @param string $bgcolor2 奇数行背景颜色，默认值为"#E8EEF7"
	 * 
	 * @return string 背景色的css语法描述
	 */
	static public function scap_row_color($count, $bgcolor1 = "#FFFFE7", $bgcolor2 = "#E8EEF7")
	{
		$rtn = 'background-color:';
		
		$rtn .= ($count % 2 == 0) ? $bgcolor1 : $bgcolor2;
		$rtn .= ';';
		
		return $rtn;
	}
	
	/**
	 * 构造指定系统帐户的显示信息：帐户显示名称[帐户登录名称]
	 * 
	 * @param string $id_value 要查询帐户的id值
	 * @param string $id_name id值对应的内部数据表项名称：只能是a_s_id或a_c_login_id，默认为 a_s_id
	 * 
	 * @return string 帐户的显示信息：帐户显示名称[帐户登录名称]
	 */
	static public function scap_show_account($id_value, $id_name = 'a_s_id')
	{
		$rtn = '';
		$array_info = scap_get_account_info($id_value, $id_name);
		
		$array_info['a_c_display_name'] = empty($array_info['a_c_display_name']) ? '-' : $array_info['a_c_display_name'];
		$array_info['a_c_login_id'] = empty($array_info['a_c_login_id']) ? '-' : $array_info['a_c_login_id'];
		
		$rtn = "{$array_info['a_c_display_name']}[{$array_info['a_c_login_id']}]";
		return $rtn;
	}
	
	/**
	 * 构造针对select的系统帐户选项:选项显示为帐户显示名称[帐户登录名称]
	 * 
	 * @param string $where 查询条件表述
	 * 
	 * @return select的options数组
	 */
	static public function scap_create_options_accounts($where = '')
	{
		$rtn = array();
		
		$list = scap_get_account_list($where);
		
		foreach($list as $k => $v)
		{
			$rtn[$v['a_s_id']] = "{$v['a_c_display_name']}[{$v['a_c_login_id']}]";
		}
		
		return $rtn;
	}
	
	
	/**
	 * 构造系统显示信息的提示图标的完整路径
	 * 
	 * @param string $type maybe:tip/warn/error
	 * 
	 * @return string 指定提示类型的图标url
	 */
	static public function scap_get_icon_tip_url($type)
	{
		$rtn = scap_get_image_url('module_basic', "$type.gif");
		return $rtn;
	}
	
	/**
	 * 构造带日历选择的按钮
	 * 
	 * @param array $parameters key:inputField/button/ifFormat/showsTime/singleClick/step
	 *  The "params" is a single object that can have the following properties:
	 *
	 *    prop. name   | description
	 *  -------------------------------------------------------------------------------------------------
	 *   inputField    | the ID of an input field to store the date
	 *   displayArea   | the ID of a DIV or other element to show the date
	 *   button        | ID of a button or other element that will trigger the calendar
	 *   eventName     | event that will trigger the calendar, without the "on" prefix (default: "click")
	 *   ifFormat      | date format that will be stored in the input field
	 *   daFormat      | the date format that will be used to display the date in displayArea
	 *   singleClick   | (true/false) wether the calendar is in single click mode or not (default: true)
	 *   firstDay      | numeric: 0 to 6.  "0" means display Sunday first, "1" means display Monday first, etc.
	 *   align         | alignment (default: "Br"); if you don't know what's this see the calendar documentation
	 *   range         | array with 2 elements.  Default: [1900, 2999] -- the range of years available
	 *   weekNumbers   | (true/false) if it's true (default) the calendar will display week numbers
	 *   flat          | null or element ID; if not null the calendar will be a flat calendar having the parent with the given ID
	 *   flatCallback  | function that receives a JS Date object and returns an URL to point the browser to (for flat calendar)
	 *   disableFunc   | function that receives a JS Date object and should return true if that date has to be disabled in the calendar
	 *   onSelect      | function that gets called when a date is selected.  You don't _have_ to supply this (the default is generally okay)
	 *   onClose       | function that gets called when the calendar is closed.  [default]
	 *   onUpdate      | function that gets called after the date is updated in the input field.  Receives a reference to the calendar.
	 *   date          | the date that the calendar will be initially displayed to
	 *   showsTime     | default: false; if true the calendar will include a time selector
	 *   timeFormat    | the time format; can be "12" or "24", default is "12"
	 *   electric      | if true (default) then given fields/date areas are updated for each move; otherwise they're updated only on close
	 *   step          | configures the step of the years in drop-down boxes; default: 2
	 *   position      | configures the calendar absolute position; default: null
	 *   cache         | if "true" (but default: "false") it will reuse the same calendar object, where possible
	 *   showOthers    | if "true" (but default: "false") it will show days from other months too
	 * 
	 * @param string $button_id 按钮的id
	 * @param string $button_content 按钮的显示内容
	 * @param bool $flag_show 是否显示输出
	 * 
	 * @return string html输出文本
	 */
	static public function scap_select_calendar($parameters, $button_content = '...', $flag_show = true)
	{
		$out = '';
		
		if (!$flag_show)
		{
			return $out;
		}
		
		if (!is_array($parameters))
		{
			return $out;
		}
		
		$jstr = '';
	        
	    foreach($parameters as $key => $val)
	    {
	    	if (is_bool($val))
	    	{
	    		$val = $val ? 'true' : 'false';
	    	}
	    	else if (!is_numeric($val))
	    	{
	    		$val = '"'.$val.'"';
	    	}
	    	
	    	if ($jstr)
	    	{
	    		$jstr .= ',';
	    	}
	    	
	    	$jstr .= '"' . $key . '":' . $val;
	    }
	        
		if (!empty($parameters['button']))
		{
			$out .= "<button type=\"reset\" id=\"{$parameters['button']}\">{$button_content}</button>";
		}
		$out .= "\n<script type=\"text/javascript\">";
		$out .= "\nCalendar.setup({";
		$out .= "\n".$jstr;
		$out .= "\n});";
		$out .= "\n</script>";
		
		return $out;
	}
	
   /**
     * 构造jqueryui日历
     * 
     * @param inputFieldID 触发日历控件的input id
     * @param showtime 是否显示时间， 如果为true，则可在参数数组中加入timepicker的配置
     * 
     * ampm: true 是否显示上午下午
     * timeFormat: 'h-m' 时间格式  hh-mm
     * showHour showMinute showSecond 分别显示时分秒
     * stepHour stepMinute stepSecond 分别设置时分秒滑动步长
     * hour minute second 分别设置初始时分秒
     * 
     * @param array $parameters 参数(参见http://jqueryui.com/demos/datepicker  options)
     * @param bool $flag_show 是否显示输出
     * 
     * @return string html输出文本
     */
    static public function scap_jqueryui_calendar($inputFieldID, $showtime, $parameters = array(), $flag_show = true)
    {
        $out = '';
        
        if (!$flag_show)
        {
            return $out;
        }
        
        if (!is_array($parameters))
        {
            return $out;
        }
        
        if($showtime)
        {
            $picker = 'datetimepicker';
        }
        else
        {
            $picker = 'datepicker';
        }
        
        $jstr = '';
            
        foreach($parameters as $key => $val)
        {
            if (is_bool($val))
            {
                $val = $val ? 'true' : 'false';
            }
            else if (!is_numeric($val))
            {
                $val = '"'.$val.'"';
            }
            
            if ($jstr)
            {
                $jstr .= ',';
            }
            
            $jstr .= '"' . $key . '":' . $val;
        }
        
        $out .= "\n<script type=\"text/javascript\">$(document).ready(function(){";
        $out .= "\n$(\"#".$inputFieldID."\").".$picker."({";
        $out .= "\n".$jstr;
        $out .= "\n});})";
        $out .= "\n</script>";
        
        return $out;
    }
    
	/**
	 * 上标信息[提示]标志
	 * 
	 * @param string $tip_text 需要提示的文本内容
	 * @param string $show_text 显示的名称文本
	 * 
	 * @return string html输出文本
	 */
	static public function scap_info_sup_tip($tip_text, $show_text = 'tip', $flag_show = true)
	{
		$out = '';
		
		$style = 'font-size:9px; font-weight:normal; letter-spacing:normal; cursor:help; vertical-align:super; color:#43A102;';
		
		$out = scap_html::span(array('title' => $tip_text, 'style' => $style), $show_text, $flag_show);
		
		return $out;
	}
	
	/**
	 * 上标信息[提示]标志
	 * 
	 * @param string $help_text 需要提示的文本内容
	 * @param string $show_text 显示的名称文本
	 * 
	 * @return string html输出文本
	 */
	static public function scap_info_sup_help($help_text, $show_text = '?', $flag_show = true)
	{
		$out = '';
		
		$style = 'font-size:9px; font-weight:normal; letter-spacing:normal; cursor:help;vertical-align: super;color: #FF8C05;';
		
		$out = scap_html::span(array('title' => $help_text, 'style' => $style), $show_text, $flag_show);
		
		return $out;
	}
	
	/**
	 * wz_tooltip提示信息(已废弃，为向前兼容暂时保留)
	 * 
	 * @param string $content 信息内容
	 * @param array $parameters 参数数组
	 * @param bool $flag_is_tag 信息内容对象是否是tag
	 * @param bool $flag_show 是否显示
	 * 
	 * @return string html输出文本
	 */
	static public function scap_wz_tooltip($content, $parameters = array(), $flag_is_tag = false, $flag_show = true)
	{
		$out = '';
        
        if (!$flag_show)
        {
            return $out;
        }
        
//      $out .=  $flag_is_tag ? "TagToTip" : "Tip";
//      $out .= "('$content'";
//      foreach($parameters as $k => $v)
//      {
//          $out .= ",".strtoupper($k).",$v";
//      }
//      $out .= ")";
        
        $out = <<<JS
$(this).attr('title', '{$content}');        
JS;
        
        return $out;
	}
	
	/**
	 * 下拉菜单组件
	 * 需要加载jquery/jkoutlinemenu组件
	 *
	 * @param string $attachment_id 依附div或者anchor的id
	 * @param string $menu_id 菜单div的id
	 * @param array $menu_link 菜单项数组
	 * @param string $event_type mouseover|click 默认为mouseover
	 * @param int $menu_width 默认null
	 * @param int $menu_height 默认null
	 * @param bool $flag_show 默认true
	 * 
	 * @return string html输出文本
	 */
	static public function scap_create_dropdown_menu($attachment_id, $menu_id, $menu_link = array(), $event_type = 'mouseover', $menu_width = 'null', $menu_height = 'null', $flag_show = true)
	{
		$out = '';
		
		if (!$flag_show)
		{
			return $out;
		}
		
		if (!empty($menu_link) && is_array($menu_link))// 构造菜单
		{
			$out .= "<div id=\"{$menu_id}\" class=\"outlinemenu\"><ul>";
			foreach($menu_link as $k => $v)
			{
				$out .= "<li>$v</li>";
			}
			$out .= "</ul></div>";
		}
		
		$out .= "<script type=\"text/javascript\">";
		$out .= "jkoutlinemenu.definemenu(\"{$attachment_id}\", \"{$menu_id}\", \"{$event_type}\", $menu_width, $menu_height);";
		$out .= "</script>";
		
		return $out;
	}
	
	/**
	 * 构造支持jquery fg menu的链接函数
	 * 
	 * @param string $menu_id 被关联菜单的id(需要带#号)或者是菜单文件的url
	 * @param string $link_id 当前链接id
	 * @param string $name 链接名称
	 * @param string $style 额外style设定
	 * @param bool $flag_show 是否输出，默认true
	 * 
	 * @return string 组件html
	 */
    static public function scap_create_fg_menu_link($menu_id, $link_id, $name, $style='', $flag_show = true)
    {
        $out = '';
        
        if (!$flag_show)
        {
            return $out;
        }
        
        $out .= <<<HTML
<a tabindex="0" href="{$menu_id}" class="fg-button fg-button-icon-right" id="{$link_id}" style="{$style}">
{$name}
</a>
HTML;
        return $out;
    }
    
    /**
     * 构造portal的widget
     * 
     * @param string $url widget中的iframe的地址
     * @param int $height 高度
     * @param string $title_text 标题文本
     * @param bool $flag_title 是否显示标题
     * @param string $style 附加的iframe样式
     * 
     * @return string 组件html
     */
    static public function scap_create_widget($url, $height, $flag_title, $title_text, $style = '')
    {
        $out = '';
        if($flag_title)
        {
            $iframe_height = $height - 2;
            $out .= <<<HTML
<div style="margin:0 0 0 10px;">
    <div class="portal_widget_title"><span>{$title_text}</span></div>
    <div class="portal_widget no_title">
    	<div style="height:{$height}px">
    		<iframe src="{$url}" frameborder=0 scrolling="no" width="100%" height="{$iframe_height}px style="{$style}"></iframe>
    	</div>
    </div>
</div>
HTML;
        }
        else
        {
            $iframe_height = $height - 12;
            $out .= <<<HTML
<div style="margin:0 0 10px 10px;">
    <div class="portal_widget">
    	<div style="height:{$height}px">
    		<iframe src="{$url}" frameborder=0 scrolling="no" width="100%" height="{$iframe_height}px" style="margin-top:10px;{$style}"></iframe>
    	</div>
    </div>
</div>
HTML;
        }
        return $out;
    }
}
?>