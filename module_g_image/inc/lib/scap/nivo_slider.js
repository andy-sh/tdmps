/**
 * 图片预览相关实现
 * 
 * @version $Id: nivo_slider.js 739 2013-09-27 09:21:42Z liqt $
 * @creator liqt @ 2013-02-26 21:39:32 by caster0.0.2
 */
$.fn.extend(true, $.fn.scap.methods,  {
	/**
	 * 配合上传图片前实现本地预览功能
	 * 
	 * @param options 配置项
	 */
	nivo_slider: function(options)
	{
		var options = options || {};
		
		// 默认配置项
		var defaults = {
			effect: 'sliceUpDownLeft',
			directionNav: true,
			controlNav: false,
			pauseTime: 5000
		};
		
		// 合并的配置项
		var settings = $.extend({}, defaults, options);
		
		$(this).nivoSlider(settings);
	}
});