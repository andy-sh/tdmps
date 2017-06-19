/**
 * pnotify的scap js封装
 * 
 * @version $Id: pnotify.js 361 2013-03-20 07:57:36Z liqt $
 * @creator liqt @ 2013-02-06 12:43:51 by caster0.0.2
 */
$.fn.extend(true, $.fn.scap.methods,  {
	/**
	 * pnotify的scap便携封装函数
	 * 
	 * @param type Type of the notice. "notice/warn", "info", "success/tip", or "error".
	 * @param text 内容
	 * @param title 标题
	 * @param options 其他配置项,参看 https://github.com/sciactive/pnotify
	 */
	pnotify: function(type, text, title, options)
	{
		var options = options || {};
		
		switch(type)
		{
			case 'notice':
			case 'warn':
				options.type = 'notice';
				break;
			case 'info':
				options.type = 'info';
				break;
			case 'success':
			case 'tip':
				options.type = 'success';
				break;
			case 'error':
				options.type = 'error';
				break;
			default:
				options.type = 'info';
		}
		
		if (!is_empty(title))
		{
			options.title = title;
		}
		
		options.text = text;
		
		// 默认配置项
		var defaults = {
			styling: 'jqueryui',
			sticker: false,
			hide: true,
			delay: 1500,
			mouse_reset: true,
			history: false,
			opacity: 1
		};
		
		// 合并的配置项
		var settings = $.extend({}, defaults, options);
		
		$.pnotify(settings);
	}
});