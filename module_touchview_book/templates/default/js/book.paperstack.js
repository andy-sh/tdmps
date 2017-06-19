/**
 * book.paperstack
 * 
 * @version $Id: book.paperstack.js 146 2012-10-10 05:16:32Z liqt $
 */

/**
 * @fileoverview 书籍所剩纸张厚度效果实现 
 * On the right hand side of the book there is an indication of
 * depth depending on how far into the book you are. This manager updates said stack.
 */

/**
 * Sub-namespace.
 * @type {Object}
 */
TT.paperstack = {};


/**
 * The HTML element that contains the paper elements.
 */
TT.paperstack.container = null;


/**
 * Initialize paper stack class.
 */
TT.paperstack.initialize = function()
{
	TT.paperstack.container = $('#paperstack');
};


/**
 * 根据阅读进度更新book右侧的页面厚度
 * Updates the number of currently visible papers in the stack that appears on
 * the right side of the book depending on reading progress.
 * 
 * @param {number=} overrideProgress If specified, this value (on a range of 0-1) 
 * will be used in place of the progress that the currently selected page reflects.
 */
TT.paperstack.updateStack = function(overrideProgress)
{
	var availablePapers = $('div.paper', TT.paperstack.container).length;// 获取paper的个数
	var currentPageNumber = TT.navigation.classToGlobalPage($('.current').attr('class'));
	
	overrideProgress = (overrideProgress ? overrideProgress : TT.paperstack.getProgress());
	
	// 计算可见的page堆积数
	var visiblePapers = Math.round(((1 - overrideProgress) * availablePapers));
//	TT.log('visiblePapers:'+visiblePapers+':'+currentPageNumber+'['+SERVER_VARIABLES.PAGES_COUNT+']');
	
	if (visiblePapers != 0)
	{
		$('.paper:lt(' + visiblePapers + ')', TT.paperstack.container).css({ opacity: 1 });// 显示小于可视页数的paper
		$('.paper:gt(' + visiblePapers + ')', TT.paperstack.container).css({ opacity: 0 });// 隐藏大于可视页数的paper
		if (!TT.IS_TOUCH_DEVICE) $('.shadow', TT.paperstack.container).css({ opacity: 1 });// 显示阴影
	}
	else
	{
		$('.paper', TT.paperstack.container).css({ opacity: 0 });
		if (!TT.IS_TOUCH_DEVICE) $('.shadow', TT.paperstack.container).css({ opacity: 0 });
	}
	
	if (!TT.IS_TOUCH_DEVICE) $('.shadow', TT.paperstack.container).css({ marginLeft: -9 + visiblePapers });
};

/**
 * 计算阅读进度
 */
TT.paperstack.getProgress = function()
{
	// 当前第几页
	var currentPageNumber = TT.navigation.classToGlobalPage($('.current').attr('class'));

	if (TT.navigation.isHomePage())
	{
		return 0;
	}
	else if (TT.navigation.isCreditsPage() || TT.navigation.isLastPage())
	{
		return 1;
	}

	return Math.min((currentPageNumber/SERVER_VARIABLES.PAGES_COUNT), 1);
};