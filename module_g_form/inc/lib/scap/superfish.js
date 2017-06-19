/**
 * superfish的scap js封装
 * 
 * @version $Id: superfish.js 295 2013-03-03 15:40:27Z liqt $
 * @creator liqt @ 2013-02-04 12:43:51 by caster0.0.2
 */
$.fn.extend(true, $.fn.scap.methods,  {
	/**
	 * superfish的scap便携封装函数
	 * 
	 * @param options 配置项
	 */
	superfish: function(options)
	{
		var options = options || {};
		
		// 默认配置项
		var defaults = {
			hoverClass:    'sfHover',          // the class applied to hovered list items
			pathClass:     'overideThisToUse', // the class you have applied to list items that lead to the current page
			pathLevels:    1,                  // the number of levels of sub-menus that remain open or are restored using pathClass
			delay:         100,                // the delay in milliseconds that the mouse can remain outside a sub-menu without it closing
			animation:     {opacity:'show'},   // an object equivalent to first parameter of jQuery’s .animate() method. Used to animate the sub-menu open
			animationOut:  {opacity:'hide'},   // an object equivalent to first parameter of jQuery’s .animate() method Used to animate the sub-menu closed
			speed:         'normal',           // speed of the opening animation. Equivalent to second parameter of jQuery’s .animate() method
			speedOut:      'fast',             // speed of the closing animation. Equivalent to second parameter of jQuery’s .animate() method
			autoArrows:    false,               // if true, arrow mark-up generated automatically = cleaner source code at expense of initialisation performance
			disableHI:     false,              // set to true to disable hoverIntent detection
			useClick:      false,              // set this to true to require a click to open and close sub-menus. Note that the link will never be followed when in this mode
			onInit:        function(){},       // callback function fires once Superfish is initialised – 'this' is the containing ul
			onBeforeShow:  function(){},       // callback function fires just before reveal animation begins – 'this' is the ul about to open
			onShow:        function(){},       // callback function fires once reveal animation completed – 'this' is the opened ul
			onHide:        function(){},       // callback function fires after a sub-menu has closed – 'this' is the ul that just closed
			onIdle:        function(){}        // callback function fires when the 'current' sub-menu is restored (if using pathClass functionality)
		};
		
		// 合并的配置项
		var settings = $.extend(true, defaults, options);
		
		$(this).superfish(settings);
	}
});