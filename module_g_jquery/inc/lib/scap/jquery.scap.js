/**
 * scap中js的服务类插件
 * - 用于scap中所提供各种js方法的插件域 
 * 
 * @version $Id: jquery.scap.js 224 2013-02-06 07:19:09Z liqt $
 * @creator liqt @ 2013-02-03 15:15:31 by caster0.0.2
 */
(function($){
	var methods = {
		
	};
	
	$.fn.scap = function( method )
	{
	    if ( methods[method] )
	    {
	    	return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
	    }
	    else
	    {
	    	console.log( '方法:' +  method + ' 不存在于jQuery.scap中。' );
	    }
	};
	$.fn.scap.methods = methods;
})(jQuery);