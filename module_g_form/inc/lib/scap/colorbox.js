/**
 * colorbox的scap js封装
 * 
 * @version $Id: colorbox.js 488 2013-05-10 06:45:52Z liqt $
 * @creator liqt @ 2013-02-04 12:43:51 by caster0.0.2
 */
$.fn.extend(true, $.fn.scap.methods,  {
	/**
	 * colorbox的scap便携封装函数
	 * 
	 * @param options 配置项
	 */
	colorbox: function(options)
	{
		var options = options || {};
		
		// 默认配置项
		var defaults = {
			overlayClose: true,// If false, disables closing ColorBox by clicking on the background overlay.
			scrolling: false, // If false, Colorbox will hide scrollbars for overflowing content.
			current: "第{current}张图(共{total}张)",
			previous: "向前",
			next: "向后",
			close: "关闭",
			xhrError: "内容加载失败。",
			imgError: "图片加载失败。"
		};
		
		// 合并的配置项
		var settings = $.extend(true, defaults, options);
		
		if ($(this).selector)// 如果指定dom
		{
			$(this).colorbox(settings);
		}
		else
		{
			$.colorbox(settings);
		}
	}
});