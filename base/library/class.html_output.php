<?php
/**
 * description: html界面输出封装类
 * create time: 2006-10-19 11:13:17
 * @version $Id: class.html_output.php 4 2012-07-18 06:40:23Z liqt $
 * @author LiQintao(leeqintao@gmail.com)
 */

class html_output
{
	/**
	 * input (type:text) output
	 * 默认class是'text',方便控制css样式为.text
	 * 
	 * @param array $parameters key: name/id/title/value/size/maxlength/...
	 * @param bool $flag_readonly
	 * @param bool $flag_disable
	 * @param bool $flag_show 是否显示输出
	 * @param bool $flag_required 是否必填项
	 * @param bool $auto_class 自动生成的class类名称
	 * 
	 * @return string html输出文本
	 */
	static public function input_text($parameters, $flag_readonly = false, $flag_disable = false, $flag_show = true, $flag_required = false, $auto_class = array('type' => 'text', 'readonly' => 'readonly', 'required' => 'required'))
	{
		if (!$flag_show)
		{
			return '';
		}
		
		if (!is_array($parameters))
		{
			return '';
		}
		
		$out = '<input type="text" ';
		
		// 加载可自动设置的class
		if (!empty($parameters['class']) || !empty($auto_class['type']) || !empty($auto_class['readonly']) || !empty($auto_class['required']))
		{
			$out .= 'class="'.$parameters['class'].' '.$auto_class['type'];
			
			if ($flag_readonly && !empty($auto_class['readonly']))
			{
				$out .=  ' '.$auto_class['readonly'];
			}
			
			if ($flag_required && !empty($auto_class['required']))
			{
				$out .=  ' '.$auto_class['required'];
			}
			
			$out .= '" ';
		}
		
		foreach($parameters as $k => $v)
		{
			if (strcasecmp($k, 'class') == 0)
			{
				continue;
			}
			$value = htmlspecialchars($v);// Convert special characters to HTML entities
			$out .= "$k=\"$value\" ";
		}
		
		if ($flag_readonly)
		{
			$out .= ' readonly="readonly" ';
		}
		
		if ($flag_disable)
		{
			$out .= ' disabled="disabled" ';
		}
		
		$out .= ' />';
		return $out;
	}
	
	/**
	 * input (type:hidden) output
	 * 
	 * @param array $parameters key: name/id/title/value/size/maxlength/...
	 * @param bool $flag_show 是否显示输出
	 * 
	 * @return string html输出文本
	 */
	static public function input_hidden($parameters, $flag_show = true)
	{
		if (!$flag_show)
		{
			return '';
		}
		
		if (!is_array($parameters))
		{
			return '';
		}
		
		$out = '<input type="hidden" ';
		
		foreach($parameters as $k => $v)
		{
			$value = htmlspecialchars($v);// Convert special characters to HTML entities
			$out .= "$k=\"$value\" ";
		}
		
		$out .= ' />';
		return $out;
	}

	/**
	 * input (type:password) output
	 * 
	 * @param array $parameters key: name/id/title/value/size/maxlength/...
	 * @param bool $flag_readonly
	 * @param bool $flag_disable
	 * @param bool $flag_show 是否显示输出
	 * 
	 * @return string html输出文本
	 */
	static public function input_password($parameters, $flag_readonly = false, $flag_disable = false, $flag_show = true)
	{
		if (!$flag_show)
		{
			return '';
		}
		
		if (!is_array($parameters))
		{
			return '';
		}
		
		$out = '<input type="password" ';
		
		if (!isset($parameters['class']))
		{
			$out .= 'class="text" ';
		}
		
		foreach($parameters as $k => $v)
		{
			$value = htmlspecialchars($v);// Convert special characters to HTML entities
			$out .= "$k=\"$value\" ";
		}
		
		if ($flag_readonly)
		{
			$out .= ' readonly="readonly" ';
		}
		
		if ($flag_disable)
		{
			$out .= ' disabled="disabled" ';
		}
		
		$out .= ' />';
		return $out;
	}
	
	/**
	 * input (type:submit) output
	 * 
	 * @param array $parameters key: name/id/title/value/size/...
	 * @param bool $flag_readonly
	 * @param bool $flag_disable
	 * @param bool $flag_show 是否显示输出
	 * 
	 * @return string html输出文本
	 */
	static public function input_submit($parameters, $flag_disable = false, $flag_show = true)
	{
		if (!$flag_show)
		{
			return '';
		}
		
		if (!is_array($parameters))
		{
			return '';
		}
		
		$out = '<input type="submit" ';
		
		if (empty($parameters['class']))
		{
			$out .= 'class="input_submit" ';
		}
		
		foreach($parameters as $k => $v)
		{
			$value = htmlspecialchars($v);// Convert special characters to HTML entities
			$out .= "$k=\"$value\" ";
		}
		
		if ($flag_disable)
		{
			$out .= ' disabled="disabled" ';
		}
		
		$out .= ' />';
		return $out;
	}
	
	/**
	 * input (type:button) output
	 * 
	 * @param array $parameters key: name/id/title/value/size/...
	 * @param bool $flag_readonly
	 * @param bool $flag_disable
	 * @param bool $flag_show 是否显示输出
	 * 
	 * @return string html输出文本
	 */
	static public function input_button($parameters, $flag_disable = false, $flag_show = true)
	{
		if (!$flag_show)
		{
			return '';
		}
		
		if (!is_array($parameters))
		{
			return '';
		}
		
		$out = '<input type="button" ';
		
		if (empty($parameters['class']))
		{
			$out .= 'class="input_button" ';
		}
		
		foreach($parameters as $k => $v)
		{
			$value = htmlspecialchars($v);// Convert special characters to HTML entities
			$out .= "$k=\"$value\" ";
		}
		
		if ($flag_disable)
		{
			$out .= ' disabled="disabled" ';
		}
		
		$out .= ' />';
		return $out;
	}
	
	/**
	 * input (type:radio) output
	 * 
	 * @param array $parameters key: name/id/title/value/size/...
	 * @param bool $flag_readonly
	 * @param bool $flag_disable
	 * @param bool $flag_show 是否显示输出
	 * 
	 * @return string html输出文本
	 */
	static public function input_radio($parameters, $flag_disable = false, $flag_show = true)
	{
		if (!$flag_show)
		{
			return '';
		}
		
		if (!is_array($parameters))
		{
			return '';
		}
		
		$out = '<input type="radio" ';
		
		if (empty($parameters['class']))
		{
			$out .= 'class="input_radio" ';
		}
		
		foreach($parameters as $k => $v)
		{
			$value = htmlspecialchars($v);// Convert special characters to HTML entities
			$out .= "$k=\"$value\" ";
		}
		
		if ($flag_disable)
		{
			$out .= ' disabled="disabled" ';
		}
		
		$out .= ' />';
		return $out;
	}
	
	/**
	 * image output
	 * 
	 * @param array $parameters key: src/title/alt/width/height/border...
	 * @param bool $flag_show 是否显示输出
	 */
	static public function image($parameters, $flag_show = true)
	{
		if (!$flag_show)
		{
			return '';
		}
		
		if (!is_array($parameters))
		{
			return '';
		}
		
		$out = '<img ';
		
		foreach($parameters as $k => $v)
		{
			$value = htmlspecialchars($v);// Convert special characters to HTML entities
			$out .= "$k=\"$value\" ";
		}
		
		$out .= ' />';
		return $out;
	}
	
	/**
	 * 锚标输出
	 * 
	 * @param array $parameters key: href/title...
	 * @param string $content 显示描述
	 * @param bool $flag_show 是否显示输出
	 */
	static public function anchor($parameters, $content, $flag_show = true)
	{
		if (!$flag_show)
		{
			return '';
		}
		
		if (!is_array($parameters))
		{
			return '';
		}
		
		$out = '<a ';
		
		foreach($parameters as $k => $v)
		{
			$value = htmlspecialchars($v);// Convert special characters to HTML entities
			$out .= "$k=\"$value\" ";
		}
		
		$out .= ' >'.$content.'</a>';
		return $out;
	}
	
	/**
	 * create select menu
	 * 
	 * @param array $parameters key: class/dir/id/lang/multiple/name/size/style/tabindex/title/...
	 * @param array $options select的option选项: key=>value
	 * @param array $selects 当前所选项的集合
	 * @param bool $flag_readonly 以只读方式输出
	 * @param bool $flag_disable
	 * @param bool $flag_show 是否显示输出
	 * @param bool $flag_required 是否必填项
	 * @param bool $icon_require 必填标志的图象url
	 * 
	 * @return string html输出文本
	 */
	static public function select($parameters, $options, $selects = array(), $flag_readonly = false, $flag_disable = false, $flag_show = true, $flag_required = false, $icon_require = '')
	{
		$out = '';
		
		if (!$flag_show || !is_array($parameters))
		{
			return '';
		}
		
		$out = '<select ';
		
		foreach($parameters as $k => $v)
		{
			$value = htmlspecialchars($v);// Convert special characters to HTML entities
			$out .= "$k=\"$value\" ";
		}
		
		if ($flag_disable)
		{
			$out .= ' disabled="disabled" ';
		}
		
		$out .= '>';
		
		foreach($options as $k => $v)
		{
			$out .= '<option value="'.htmlspecialchars($k).'"';
			
			if (in_array($k, $selects))
			{
				$out .= ' selected="1"';
			}
			$out .= '>'.$v.'</option>'."\n";
		}
		
		$out .= '</select>';
		
		if ($flag_readonly)
		{
			$out = '';
			foreach($options as $k => $v)
			{
				if (in_array($k, $selects))
				{
					$out .= $v;
					$out .= html_output::input_hidden(array_merge($parameters, array('value' => $k)));
				}
				
			}
		}
		
		if ($flag_required && !empty($icon_require))
		{
			$out .= html_output::image(array('src' => $icon_require));
		}
		return $out;
	}
	
	/**
	 * <label>...</lable>输出
	 * 
	 * @param array $parameters key: accesskey/class/dir/for/id/title...
	 * @param string $content 显示描述
	 * @param bool $flag_show 是否显示输出
	 */
	static public function label($parameters, $content, $flag_show = true)
	{
		if (!$flag_show || !is_array($parameters))
		{
			return '';
		}
		
		$out = '<label ';
		
		foreach($parameters as $k => $v)
		{
			$value = htmlspecialchars($v);// Convert special characters to HTML entities
			$out .= "$k=\"$value\" ";
		}
		
		$out .= ' >'.$content.'</label>';
		return $out;
	}
	
	/**
	 * textarea  output
	 * 
	 * @param array $parameters key: name/id/cols/rows/wrap/style/tabindex/title/dir/accesskey/lang...
	 * @param string $content 显示描述
	 * @param bool $flag_readonly
	 * @param bool $flag_disable
	 * @param bool $flag_show 是否显示输出
	 * @param bool $flag_required 是否必填项
	 * @param bool $auto_class 自动生成的class类名称
	 * 
	 * @return string html输出文本
	 */
	static public function textarea($parameters, $content, $flag_readonly = false, $flag_disable = false, $flag_show = true, $flag_required = false, $auto_class = array('readonly' => 'readonly', 'required' => 'required'))
	{
		if (!$flag_show)
		{
			return '';
		}
		
		if (!is_array($parameters))
		{
			return '';
		}
		
		$out = '<textarea ';
		
		// 加载可自动设置的class
		if (!empty($parameters['class']) || !empty($auto_class['readonly']) || !empty($auto_class['required']))
		{
			$out .= 'class="'.$parameters['class'];
			
			if ($flag_readonly && !empty($auto_class['readonly']))
			{
				$out .=  ' '.$auto_class['readonly'];
			}
			
			if ($flag_required && !empty($auto_class['required']))
			{
				$out .=  ' '.$auto_class['required'];
			}
			
			$out .= '" ';
		}
		
		foreach($parameters as $k => $v)
		{
			if (strcasecmp($k, 'class') == 0)
			{
				continue;
			}
			
			$value = htmlspecialchars($v);// Convert special characters to HTML entities
			$out .= "$k=\"$value\" ";
		}
		
		if ($flag_readonly)
		{
			$out .= ' readonly="readonly" ';
		}
		
		if ($flag_disable)
		{
			$out .= ' disabled="disabled" ';
		}
		
		$out .= ' >'.htmlspecialchars($content).'</textarea>';
		return $out;
	}
	
	/**
	 * <button>...</button> output
	 * 
	 * @param array $parameters key: accesskey/class/dir/name/id/value/lang/style/tabindex/title/...
	 * 		Standard Events:onclick, ondblclick, onmousedown, onmouseup, onmouseover, onmousemove, 
	 * 						onmouseout,onkeypress, onkeydown, onkeyup
	 * @param string $type the value maybe:button, reset, submit
	 * @param bool $flag_disable
	 * @param bool $flag_show 是否显示输出
	 * 
	 * @return string html输出文本
	 */
	static public function button($parameters, $type, $content, $flag_disable = false, $flag_show = true)
	{
		if (!$flag_show || !is_array($parameters))
		{
			return '';
		}
		
		$out = "<button type=\"$type\" ";
		
		foreach($parameters as $k => $v)
		{
			$value = htmlspecialchars($v);// Convert special characters to HTML entities
			$out .= "$k=\"$value\" ";
		}
		
		if ($flag_disable)
		{
			$out .= ' disabled="disabled" ';
		}
		
		$out .= ' >'.$content.'</button>';
		return $out;
	}
	
	/**
	 * check box output
	 * 
	 * @param array $parameters key: name/id/title/value/size/maxlength/...
	 * @param bool $flag_checked 是否被选中
	 * @param bool $flag_disable
	 * @param bool $flag_show 是否显示输出
	 * 
	 * @return string html输出文本
	 */
	static public function checkbox($parameters, $flag_checked = false, $flag_disable = false, $flag_show = true)
	{
		if (!$flag_show)
		{
			return '';
		}
		
		if (!is_array($parameters))
		{
			return '';
		}
		
		$out = '<input type="checkbox" ';
		
		foreach($parameters as $k => $v)
		{
			$value = htmlspecialchars($v);// Convert special characters to HTML entities
			$out .= "$k=\"$value\" ";
		}
		
		if ($flag_checked)
		{
			$out .= ' checked="checked" ';
		}
		
		if ($flag_disable)
		{
			$out .= ' disabled="disabled" ';
		}
		
		$out .= ' />';
		return $out;
	}
	
	/**
	 * input (type:file) output
	 * 
	 * @param array $parameters key: name/id/title/value/size/maxlength/...
	 * @param bool $flag_readonly
	 * @param bool $flag_disable
	 * @param bool $flag_show 是否显示输出
	 * 
	 * @return string html输出文本
	 */
	static public function input_file($parameters, $flag_readonly = false, $flag_disable = false, $flag_show = true)
	{
		if (!$flag_show)
		{
			return '';
		}
		
		if (!is_array($parameters))
		{
			return '';
		}
		
		$out = '<input type="file" ';
		
		foreach($parameters as $k => $v)
		{
			$value = htmlspecialchars($v);// Convert special characters to HTML entities
			$out .= "$k=\"$value\" ";
		}
		
		if ($flag_readonly)
		{
			$out .= ' readonly="readonly" ';
		}
		
		if ($flag_disable)
		{
			$out .= ' disabled="disabled" ';
		}
		
		$out .= ' />';
		return $out;
	}
	
	/**
	 * 关闭当前窗口(链接形式)
	 * 
	 * @param string $close_text 显示描述
	 * @param array $parameters key: title/class...
	 * @param bool $flag_show 是否显示输出
	 * 
	 * @return string html输出文本
	 */
	static public function close_window_link($close_text='Close', $parameters=array(), $flag_show = true)
	{
		$out = '';
		
		$parameters['href'] = 'javascript: window.close();';
		$out = html_output::anchor($parameters, $close_text, $flag_show);
		
		return $out;
	}
	
	/**
	 * 关闭当前窗口(button形式)
	 * 
	 * @param array $parameters key: name/id/title/value/size/...
	 * @param bool $flag_readonly
	 * @param bool $flag_disable
	 * @param bool $flag_show 是否显示输出
	 * 
	 * @return string html输出文本
	 */
	static public function close_window_button($parameters=array('value' => 'Close'), $flag_disable = false, $flag_show = true)
	{
		$out = '';
		$parameters['onclick'] = 'window.close();';
		$out = html_output::input_button($parameters, $flag_disable, $flag_show);
		
		return $out;
	}

	/**
	 * <span>...</span>输出
	 * 
	 * @param array $parameters key: 
	 * 		class="class name(s)" dir="ltr | rtl"  id="unique alphanumeric string"
	 * 		lang="language code"  style="style information"   title="advisory text"
	 * @param string $content 显示描述
	 * @param bool $flag_show 是否显示输出
	 */
	static public function span($parameters, $content, $flag_show = true)
	{
		if (!$flag_show || !is_array($parameters))
		{
			return '';
		}
		
		$out = '<span ';
		
		foreach($parameters as $k => $v)
		{
			$value = htmlspecialchars($v);// Convert special characters to HTML entities
			$out .= "$k=\"$value\" ";
		}
		
		$out .= ' >'.$content.'</span>';
		return $out;
	}
	
	/**
	 * [js]使浏览器状态栏显示指定信息
	 * 
	 * @param string $msg 显示描述
	 * @param bool $flag_show 是否显示输出
	 * 
	 * @return string html输出文本
	 */
	static public function js_show_msg_in_status($msg = '', $flag_show = true)
	{
		if (!$flag_show)
		{
			return '';
		}
		
		$out = "window.status = '$msg'; return true;";
		
		return $out;
	}
	
	/**
	 * [js]Javascript嵌套标记元素
	 * 
	 * @param string $content js语句内容
	 * 
	 * @return string html输出文本
	 */
	static public function js_tag($content)
	{
		$out = "<script type=\"text/javascript\">$content</script>";
		
		return $out;
	}
	
	/**
	 * [js]Javascript 文件嵌套标记元素
	 * 
	 * @param string $src js文件位置
	 * 
	 * @return string html输出文本
	 */
	static public function js_file_tag($src)
	{
		$out = "<script type=\"text/javascript\" src=\"$src\"></script>";
		
		return $out;
	}
}
?>