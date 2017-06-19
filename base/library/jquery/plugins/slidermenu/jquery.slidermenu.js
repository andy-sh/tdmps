/**
 * jQuery SliderMenu plugin - v 1.0.0
 * Author: Gao Xiang
 *
 */

/**
 * options: 参数说明
 * 
 * accordion: [bool]true/false 是否手风琴样式
 * isTitleLink: [bool]true/false 一级菜单是否为可点的链接 如是则一级菜单右边有触发伸缩的箭头
 * width: [int]n(像素) 菜单宽度
 * speed: [string]/[int] 伸缩动画速度, 'fast'/'normal'/'slow'/n(毫秒)
 * act: [string]'click'/'hover' 展开菜单的鼠标动作 注:如为hover，手风琴样式和非手风琴样式的效果是一样的
 * borderRadius: [int]n(像素) 圆角半径
 */
(function($) {
    $.fn.slidermenu = function(options) {
		var version = '1.0.0';
		var opts = $.extend({}, $.fn.slidermenu.defaults, options);
	    return this.each(function() {
	        var o = $.meta ? $.extend({}, opts, $this.data()) : opts;
	        $this = $(this);
	        $this.addClass('slidermenu');
	        if(o.isTitleLink)
	        {
	        	$this.children('.trigger').after('<div class="trigger-arrow"></div>');
	        	$this.children(".trigger.active").next('.trigger-arrow').addClass('active');
	        	$this.children('.trigger-arrow').css({float:'left', width:'30px'});
	        	$this.children('.trigger').addClass('trigger-link').css({width:o.width-30+'px', float:'left', clear:'left'});
	        	$this.children('.trigger').removeClass('trigger');
	        	$this.children('.trigger-arrow').addClass('trigger');
	        	$this.children(".trigger-link").mouseover(function(){
	        		$(this).addClass('hover-trigger-link').next(".trigger-arrow").addClass('hover-trigger-arrow');
    	    	}).mouseout(function(){
	    	        $(this).removeClass('hover-trigger-link').next(".trigger-arrow").removeClass('hover-trigger-arrow');
    	    	});
	        	$this.children(".trigger-arrow").mouseover(function(){
	        		$(this).addClass('hover-trigger-arrow').prev('.trigger-link').addClass('hover-trigger-link');
    	    	}).mouseout(function(){
	    	        $(this).removeClass('hover-trigger-arrow').prev('.trigger-link').removeClass('hover-trigger-link');
    	    	});
	        }
	        else
	        {
	        	$this.children('.trigger').addClass('trigger-link-no-arrow').css({"width":o.width+'px'});
	        	$(".trigger-link-no-arrow a").each(function(){$(this).after($(this).html())});
	        	$(".trigger-link-no-arrow a").remove();
	        	$this.children(".trigger-link-no-arrow").mouseover(function(){
	        		$(this).addClass('hover-trigger-link-no-arrow');
    	    	}).mouseout(function(){
	    	        $(this).removeClass('hover-trigger-link-no-arrow');
    	    	});
	        }
	        $this.children('.menu').children('ul').children('li').mouseover(function(){
        		$(this).addClass('hover-li');
	    	}).mouseout(function(){
    	        $(this).removeClass('hover-li');
	    	});
	        $this.append("<div style='clear:both'></div>");
	        $this.children(".menu").last().css({
	        	'border-top':'0px solid #BBB', 
	        	'border-bottom':'1px solid #BBB', 
	        	'-moz-border-radius-bottomleft':o.borderRadius+'px',
	        	'-moz-border-radius-bottomright':o.borderRadius+'px',
	        	'border-bottom-left-radius':o.borderRadius+'px',
	        	'border-bottom-right-radius':o.borderRadius+'px'
	        	});
	        $this.children(".trigger-link-no-arrow").first().css({
	        	'-moz-border-radius-topleft':o.borderRadius+'px',
	        	'-moz-border-radius-topright':o.borderRadius+'px',
	        	'border-top-left-radius':o.borderRadius+'px',
	        	'border-top-right-radius':o.borderRadius+'px'
	        	});
	        $this.children(".trigger-link-no-arrow").last().css({
	        	'-moz-border-radius-bottomleft':o.borderRadius+'px',
	        	'-moz-border-radius-bottomright':o.borderRadius+'px',
	        	'border-bottom-left-radius':o.borderRadius+'px',
	        	'border-bottom-right-radius':o.borderRadius+'px',
	        	'border-bottom':'1px solid #BBB'
	        	});
	        $this.children(".trigger-link-no-arrow").last().filter('.active').css({
	        	'-moz-border-radius-bottomleft':0+'px',
	        	'-moz-border-radius-bottomright':0+'px',
	        	'border-bottom-left-radius':0+'px',
	        	'border-bottom-right-radius':0+'px'
	        	});
	        $this.children('.menu').children('ul').children('li').css({width:o.width-10+'px'});
	        $this.children('.menu').css({width:o.width+'px', clear:'left'});
	        $this.children(".trigger").show();
	    	$this.children(".trigger").next('.menu').hide();
	    	$this.children(".trigger.active").next('.menu').show();
	    	if(o.act == 'click')
	    	{
    	    	$this.children(".trigger").click(function(){
    	    	    if(o.accordion)
    	    	    {
    	    	        if($(this).next(".menu").is(':hidden'))
    	    	        {
    	    	            $(this).parent().children(".trigger").removeClass('active').next('.menu').slideUp(o.speed);
    		                $(this).toggleClass('active').next(".menu").slideDown(o.speed);
    		            }
    	    	    }
    	    	    else
    	    	    {
    	    		    $(this).toggleClass('active').next(".menu").slideToggle(o.speed);
    	    		}
    	    	    $this.children(".trigger-link-no-arrow").last().css({
    		        	'-moz-border-radius-bottomleft':o.borderRadius+'px',
    		        	'-moz-border-radius-bottomright':o.borderRadius+'px',
    		        	'border-bottom-left-radius':o.borderRadius+'px',
    		        	'border-bottom-right-radius':o.borderRadius+'px',
    		        	'border-bottom':'1px solid #BBB'
    		        	});
    	    	    $this.children(".trigger-link-no-arrow").last().filter('.active').css({
    		        	'-moz-border-radius-bottomleft':0+'px',
    		        	'-moz-border-radius-bottomright':0+'px',
    		        	'border-bottom-left-radius':0+'px',
    		        	'border-bottom-right-radius':0+'px'
    		        	});
    	    	});
    	    }
    	    if(o.act == 'hover')
    	    {
    	        $this.children(".trigger").mouseover(function(){
	    	        if($(this).next(".menu").is(':hidden'))
	    	        {
	    	            $(this).parent().children(".trigger").removeClass('active').next('.menu').slideUp(o.speed);
		                $(this).toggleClass('active').next(".menu").slideDown(o.speed);
		            }
	    	        $this.children(".trigger-link-no-arrow").last().css({
	    	        	'-moz-border-radius-bottomleft':o.borderRadius+'px',
	    	        	'-moz-border-radius-bottomright':o.borderRadius+'px',
	    	        	'border-bottom-left-radius':o.borderRadius+'px',
	    	        	'border-bottom-right-radius':o.borderRadius+'px',
	    	        	'border-bottom':'1px solid #BBB'
	    	        	});
	    	        $this.children(".trigger-link-no-arrow").last().filter('.active').css({
	    	        	'-moz-border-radius-bottomleft':0+'px',
	    	        	'-moz-border-radius-bottomright':0+'px',
	    	        	'border-bottom-left-radius':0+'px',
	    	        	'border-bottom-right-radius':0+'px'
	    	        	});
    	    	});
    	    }
	    });
	};
	
	$.fn.slidermenu.defaults = {
        accordion: false,
        isTitleLink: false,
        width: '180',
        speed: 'slow',
        act: 'click',
        borderRadius: '0'
    };
})(jQuery);
