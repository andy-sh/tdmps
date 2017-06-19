/**
 * jcarousellite的scap js封装
 * 
 * @version $Id: jcarousellite.js 737 2013-09-27 09:02:56Z liqt $
 * @creator liqt @ 2013-07-01 12:43:51 by caster0.0.2
 */
$.fn.extend(true, $.fn.scap.methods,  {
	/**
	 * jcarousellite的scap便携封装函数
	 * - 走马灯组件
	 * - 参数说明：http://www.gmarwaha.com/jquery/jcarousellite/?#doc
	 * 
	 * @param options 配置项
	 */
	jcarousellite: function(options)
	{
		var options = options || {};
		
		// 默认配置项
		var defaults = {
			btnPrev: null, // Selector for the "Previous" button
			btnNext: null, // Selector for the "Next" button. 
			btnGo: null,   // the number of levels of sub-menus that remain open or are restored using pathClass
			mouseWheel: true, // you will be able to navigate your carousel using the mouse wheel.
			auto: 3000,   // The value you specify is the amount of time between 2 consecutive slides.
			speed: 1000,   // Specifying a speed will slow-down or speed-up the sliding speed of your carousel. Providing 0, will remove the slide effect.
			easing: null, // You can specify any easing effect. Note: You need easing plugin for that. Once specified, the carousel will slide based on the provided easing effect.
			vertical: false, // Determines the direction of the carousel. true, means the carousel will display vertically. 
			circular: true,  // Setting it to true enables circular navigation.
			visible: 4, // This specifies the number of items visible at all times within the carousel. The default is 3. You are even free to experiment with real numbers. Eg: "3.5" will have 3 items fully visible and the last item half visible.
			start: 0, // You can specify from which item the carousel should start
			scroll: 1, // you can specify the number of items to scroll when you click the next or prev buttons
			beforeStart: function(){}, // Callback function that should be invoked before the animation starts
			afterEnd: function(){} // Callback function that should be invoked after the animation ends
		};
		
		// 合并的配置项
		var settings = $.extend(true, defaults, options);
		
		$(this).jCarouselLite(settings);
	}
});