/**
 * 显示scap系统信息方法实现
 * 
 * @version $Id: show_system_info.js 488 2013-05-10 06:45:52Z liqt $
 * @creator liqt @ 2013-02-06 12:43:51 by caster0.0.2
 */
$.fn.extend(true, $.fn.scap.methods,  {
	/**
	 * 加载并显示scap系统信息函数
	 * 
	 * @param options pnotify的其他配置项,参看 https://github.com/sciactive/pnotify
	 */
	show_system_info: function(options)
	{
		var options = options || {};
		
		$.getJSON("?m=module_g_form.ui_server.load_system_info", function(data)
		{
		   	$.each(data, function(){
		   		$.fn.scap('pnotify', this.type, this.text, '', options);
		   	});
		});
	}
});