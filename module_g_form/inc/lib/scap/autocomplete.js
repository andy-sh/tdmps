/**
 * autocomplete的scap js封装
 * 
 * @version $Id: autocomplete.js 508 2013-05-23 10:38:24Z liqt $
 * @creator liqt @ 2013-02-06 12:43:51 by caster0.0.2
 */
$.fn.extend(true, $.fn.scap.methods,  {
	/**
	 * autocomplete的scap js封装函数
	 * - 参考http://api.jqueryui.com/autocomplete/
	 * 
	 * @param url_json 选项json内容的url地址(json输出应包含 label(显示列表展示用),show(选中后数值),id(真正获取的值))，如'?m=module_taost2_teacher.ui_admin.search'
	 * @param show_info 供显示用的数值
	 * @param width 组件宽度
	 * @param readonly 是否不可选，true | false
	 * @param options 其他配置项(优先级高于默认值)：
	 * 	'callback_select':select的回调函数(函数参数示例：function(event, ui){alert(ui.item.id);})，在select事件最后被执行
	 *  'has_show_all_dom': true|false,是否构造点击显示所有的dom，默认为true
	 */
	autocomplete: function(url_json, show_info, width, readonly, options)
	{
		var options = options || {};
		
		var id = $(this).attr('id');// 当前dom id
		var id_show = id + '_show';// 构造显示dom
		
		if (is_empty(width))
		{
			width = $(this).width();
		}
		
		// 默认配置项
		var defaults = {
			show_dom_id: id + '_show',// 构造显示dom
			show_all_dom_id: id + '_show_all',// 构造显示所有的dom
			has_show_all_dom: true,// 是否构造点击显示所有的dom，默认为true
			show_dom_width: width, // 显示dom的宽度
			show_dom_value: show_info,// 显示dom默认的显示值
			callback_select: function(event, ui){}, // select的回调函数，在select事件最后被执行
			min_length: 1 // The minimum number of characters a user must type before a search is performed
		};
		
		// 合并的配置项
		var settings = $.extend({}, defaults, options);
		
		// 隐藏原组件，构造显示用input
		var auto_dom = "<input class='text' id='"+settings.show_dom_id+"' type='text'>";
		
		if (settings.has_show_all_dom && !readonly)
		{
			auto_dom += "<span title='显示所有选项' style='cursor:pointer;' id='"+settings.show_all_dom_id+"'>&#9660;</span>";
		}
		
		$(this).hide().before(auto_dom);
		
		$('#'+settings.show_dom_id).width(settings.show_dom_width).val(settings.show_dom_value);
		
		if (readonly)
		{
			$('#'+settings.show_dom_id).attr('readonly', true).addClass('readonly');
			return;
		}
		
		$('#'+settings.show_dom_id).autocomplete({
	        source: function(request, response)
	        {
	            $.getJSON(url_json, {q: request.term}, function(data)
	            {
	            	response($.map(data, function(value)
	            			{
	                        	return {
	                                    label: value.label,// auto中指定的显示的内容
	                                    value: value.show, // auto中指定的选择后获取的值
	                                    id: value.id // 对象id，为赋值用(自定义)
	                            };
	                         }
	            			)
	                 );
	              }
	            );
	        },
	        minLength: settings.min_length,
	        select: function (event, ui)
	        {
	            $('#'+id).val(ui.item.id);
	            // callback
	            settings.callback_select(event, ui);
	        }
	    });
		
		if (settings.has_show_all_dom)
		{
			// 注册显示所有点击事件
			$('#'+settings.show_all_dom_id).click(function(){
				$('#'+settings.show_dom_id).autocomplete('search', '*');
				$('#'+settings.show_dom_id).focus();
			});
		}
	}
});