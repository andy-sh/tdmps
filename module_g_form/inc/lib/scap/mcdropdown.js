/**
 * mcdropdown的scap js封装
 * 
 * @version $Id: mcdropdown.js 229 2013-02-06 12:58:04Z liqt $
 * @creator liqt @ 2013-02-04 10:00:09 by caster0.0.2
 */
$.fn.extend(true, $.fn.scap.methods,  {
	/**
	 * mcdropdown的scap便携封装函数
	 * 
	 * @param url_menu 选项内容的url地址，如'?m=module_g_category.ui_admin.load_category_list&id=category_id&type=1'
	 * @param post_name 提交数据时的名称，如'content[c_category_id]'
	 * @param menu_id 菜单数据区域的dom id
	 * @param select_value 选中的menu数据值
	 * @param readonly 是否不可选，true | false
	 * @param width 组件宽度
	 * @param options 其他配置项
	 */
	mcdropdown: function (url_menu, post_name, menu_id, select_value, readonly, width, options)
	{
		var options = options || {};
		
		if (!$(this).attr('id'))
		{
			console.log('mcdropdown调用需给定dom id值。');
			return;
		}
		
		// 默认配置项
		var defaults = {
			inputHiddenName:post_name,
			allowParentSelect:true,
			delim:">"
		};
		
		var id = '#'+$(this).attr('id');// id选择器
		
		// 合并的配置项
		var settings = $.extend({}, defaults, options);
		
		$.get(
				url_menu, 
				function(data)
				{
					$(id).after(data);// 放置menu数据区域
					$(id).mcDropdown("#"+menu_id, settings);
					var dd = $(id).mcDropdown();
					if (!is_empty(width))
					{
						$("div.mcdropdown").width(width);
					}
	                dd.setValue(select_value);
	                dd.disable(readonly);
				}
	    );
	}
});