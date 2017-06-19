/**
 * book.navigation
 * book front导航实现
 * 
 * @version $Id: book.navigation.js 128 2012-05-07 04:38:52Z liqt $
 */

/**
 * @fileoverview The navigation class ties the site together by making sure the
 * pagination and other in-site links are bound.
 */

/**
 * Sub-namespace.
 * @type {Object}
 */
TT.navigation = {};


/**
 * Transitioning from hard cover(硬封面) flag.
 * @type {boolean}
 */
TT.navigation.transitioningFromHardCover = false;


/**
 * Flags if the user has carried out ANY page navigation.
 * @type {boolean}
 */
TT.navigation.hasNavigated = false;


/**
 * Current page name.
 * @type {string}
 */
TT.navigation.currentPageName = '';


/**
 * 队列导航事件
 * Queue navigation events.
 * @type {Object}
 */
TT.navigation.enqueueNavigation = null;


/**
 * 导航功能初始化
 * Initialize navigation class.
 */
TT.navigation.initialize = function()
{
	$('#pages section:not(.current)').width(0).hide();// 隐藏非当前页页面

	TT.navigation.assignNavigationHandlers();

	// If we are starting up on the home page, activate the flip intro animation.
	if (TT.navigation.isHomePage()){
	}
	else {
		$('#front-cover').hide();
	}

	// If we are not on the credits page, make sure the back cover is hidden.
	if (!TT.navigation.isCreditsPage()) {
		$('#back-cover').hide();
	}

	if (TT.navigation.isTableOfThings()) {
		$('body').addClass('home').addClass('tot');
	}
	// If we are not on the home page or credits page at start, we must be in the
	// book state.
	else if (!TT.navigation.isHomePage() && !TT.navigation.isCreditsPage()) {
		$('body').addClass('book');
	}

	// Load images.
	TT.navigation.loadImages();

	TT.navigation.updatePageVisibility($('#pages section.current'));
};


/**
 * 指定导航处理程序
 * Assign navigation handlers.
 */
TT.navigation.assignNavigationHandlers = function()
{
	// Bind the Logo button in the header.
	$('header a.go-home').click(function(){
		TT.navigation.goToHome();
		return false;
	});

	// Bind the Credits button in the header.
	$('header a.book-list').click(function() {
		TT.navigation.goToBooklist();
		return false;
	});
	
	// Bind the Table of Things button in the header.
	$('header a.book-toc').click(function() {
		TT.tableofthings.show();
		return false;
	});

	// Bind the Back button in the Table of Things view.
	$('#table-of-contents a.go-back').click(function() {
		TT.tableofthings.hide();
		return false;
	});
};

/**
 * Is home page.
 * @return {boolean} Whether is home page.
 */
TT.navigation.isHomePage = function()
{
	return $('body').hasClass('home');
};


/**
 * 是否是credits页面
 * Is credits page.
 * 
 * @return {boolean} Whether is credits page.
 */
TT.navigation.isCreditsPage = function()
{
	return $('body').hasClass('credits');
};


/**
 * Is table of things.
 * @return {boolean} Whether is table of things.
 */
TT.navigation.isTableOfThings = function()
{
	return $('body').hasClass('tot');
};


/**
 * book是否打开
 * Is book open.
 * 
 * @return {boolean} Whether book is open.
 */
TT.navigation.isBookOpen = function()
{
	return $('body').hasClass('book');
};

/**
 * 检查在书籍封底前的page是否是最后一页
 * Checks if we are on the last page before the book ends (the page before the back cover).
 * 
 * @param {Object=} target Current page.
 * 
 * @return {boolean} Whether is last page.
 */
TT.navigation.isLastPage = function(target)
{
	if (target)
	{
		return target.next('section').length == 0 && !TT.navigation.isCreditsPage();
	}
	return $('#pages section.current').next('section').length == 0 && !TT.navigation.isCreditsPage();
};


/**
 * Checks if we are on the first page of the book.
 * 
 * @param {Object=} target Current page.
 * 
 * @return {boolean} Whether is first page.
 */
TT.navigation.isFirstPage = function(target)
{
	if (target)
	{
		return target.prev('section').length == 0 && !TT.navigation.isHomePage();
	}
	else
	{
		return $('#pages section.current').prev('section').length == 0 && !TT.navigation.isHomePage();
	}
};


/**
 * 从section元素的class name获取article的id
 * Get article title from section className.
 * 
 * @param {string} theClass The className from a <section> element.
 * 
 * @return {?string} Article title.
 */
TT.navigation.classToArticle = function(theClass)
{
	return theClass ? theClass.match(/title-([a-zA-Z-0-9]+)/)[1] : null;
};


/**
 * 从section元素的class name获取page number
 * Get article page number from section className.e.
 * 
 * @param {string} theClass The className from a <section> element.
 * 
 * @return {?number} Article page number.
 */
TT.navigation.classToArticlePage = function(theClass)
{
	return theClass ? parseInt(theClass.match(/page-([0-9]+)/)[1]) : null;
};


/**
 * 从section元素的class name获取globalPage
 * Get global page number from section className.e.
 * 
 * @param {string} theClass The className from a <section> element.
 * 
 * @return {?number} Global page number.
 */
TT.navigation.classToGlobalPage = function(theClass)
{
	return theClass ? parseInt(theClass.match(/globalPage-([0-9]+)/)[1]) : null;
};

/**
 * 获取当前章节id
 * Get current article ID.
 * 
 * @return {?string} Article ID.
 */
TT.navigation.getCurrentArticleId = function()
{
	return TT.navigation.classToArticle($('#pages section.current').attr('class'));
};


/**
 * 获取当前章节页码
 * Get current article page number.
 * 
 * @return {?number} Article page number.
 */
TT.navigation.getCurrentArticlePage = function()
{
	return TT.navigation.classToArticlePage($('#pages section.current').attr('class'));
};


/**
 * 翻到前一页
 * Load previous page if there is one.
 * 
 * @return {?boolean} False if conditions met.
 */
TT.navigation.goToPreviousPage = function()
{
	// Clean up remnant transition flags
	TT.navigation.cleanUpTransitions();

	// Don't allow any transitions while a hard cover is being turned
	if (TT.navigation.transitioningFromHardCover)
	{
		return false;
	}

	if (TT.navigation.isFirstPage())
	{
		// If we are on the first page of the book, navigate to the home view
		if (!TT.navigation.isHomePage()) {
			TT.pageflip.completeCurrentTurn();
			TT.navigation.goToHome();
		}
		return false;
	}

	TT.pageflip.completeCurrentTurn();

	var currentPage = $('#pages section.current');

	var prevArticle, prevPage = null;

	if (TT.navigation.isCreditsPage()) {
		prevArticle = TT.navigation.classToArticle(currentPage.attr('class'));
		prevPage = TT.navigation.classToArticlePage(currentPage.attr('class'));
	}
	else {
		prevArticle = TT.navigation.classToArticle(currentPage.prev('section')
				.attr('class'));
		prevPage = TT.navigation.classToArticlePage(currentPage.prev('section')
				.attr('class'));
	}

	TT.navigation.goToPage(prevArticle, prevPage);
};


/**
 * 跳转到下一页
 * Load next page if there is one.
 * 
 * @return {?boolean} False if conditions met.
 */
TT.navigation.goToNextPage = function()
{
//	TT.log('goToNextPage');
	// Clean up remnant transition flags
	TT.navigation.cleanUpTransitions();

	// Don't allow any transitions while a hard cover is being turned
	if (TT.navigation.transitioningFromHardCover)
	{
//		TT.log('transitioningFromHardCover true');
		return false;
	}
	
	if (TT.navigation.isLastPage() || TT.navigation.isCreditsPage())
	{
		// If we are on the last page of the book, navigate to the credits view
		if (!TT.navigation.isCreditsPage() || (TT.navigation.isCreditsPage() &&	TT.navigation.isBookOpen()))
		{
			TT.pageflip.completeCurrentTurn();
			TT.navigation.goToBooklist();
		}
		return false;
	}

	TT.pageflip.completeCurrentTurn();

	var currentPage = $('#pages section.current');

	var prevArticle, prevPage = null;

	if (TT.navigation.isHomePage()) {
		nextArticle = TT.navigation.classToArticle(currentPage.attr('class'));
		nextPage = TT.navigation.classToArticlePage(currentPage.attr('class'));
	}
	else {
		TT.pageflip.completeCurrentTurn();
		nextArticle = TT.navigation.classToArticle(currentPage.next('section')
				.attr('class'));
		nextPage = TT.navigation.classToArticlePage(currentPage.next('section')
				.attr('class'));
	}

	TT.navigation.goToPage(nextArticle, nextPage);
};


/**
 * 通过硬翻页效果到达封面
 * Navigates to the home page through a hard flip transition.
 * 
 * @param {boolean} fromHistoryChange Whether from history change.
 */
TT.navigation.goToHome = function(fromHistoryChange)
{
	if (TT.navigation.isHomePage())
	{
		if ($('body').hasClass('tot'))// 当前是目录页面
		{
			TT.tableofthings.hide();
		}
		return;
	}
	
	TT.tableofthings.hide();

	// 从最后一页翻转到封面，先到封底，再到封面
	if (TT.navigation.isCreditsPage())
	{
		TT.navigation.enqueueNavigation = {
				call: function() {
					delete this.call;// This callback should only be triggered once
					setTimeout(TT.navigation.goToHome, 1);// Timeout used to exit cycle
				}
		};

		TT.navigation.goToPage(TT.history.THEEND, 1, false);

		return;
	}

	// Update the current page name
	TT.navigation.currentPageName = TT.history.HOME;

	$('#back-cover').hide();

	// Add the view specific body class
	$('body').removeClass('book').removeClass(TT.history.CREDITS).addClass(TT.history.HOME);

	// Flag that we are not transition away from a hardcover
	TT.navigation.transitioningFromHardCover = false;
//	TT.log('[goToHome]set TT.navigation.transitioningFromHardCover false');

	// Make sure that the first page is marked as current (in case the header nav buttons are used).
	$('#pages section').removeClass('current');
	$('#pages section').first().addClass('current');

	// The currently visible page, i.e. the page we are leaving
	var currentPage = $('#pages section.current');
	currentPage.width(TT.PAGE_WIDTH);

	if (!fromHistoryChange)
	{
		TT.history.pushState('?m=module_touchview_book.ui_book_front.book&view=home&b_id='+SERVER_VARIABLES.BOOK_ID);
	}

	// Execute the transition to the home page
	TT.pageflip.turnToPage(currentPage, currentPage, -1, TT.pageflip.HARD_FLIP);
};


/**
 * 导航到creadits页面
 * Navigates to the credits page through a hard flip transition.
 * 
 * @param {boolean} fromHistoryChange Whether from history change.
 */
TT.navigation.goToBooklist = function(fromHistoryChange)
{
	TT.tableofthings.hide();// 隐藏book目录层

	if (!TT.navigation.isCreditsPage() || (TT.navigation.isCreditsPage() &&	TT.navigation.isBookOpen()) )
	{
		// 如果不是最后一页
		if ( (TT.navigation.isBookOpen() || TT.navigation.isHomePage()) && (!TT.navigation.isLastPage() && !TT.navigation.isCreditsPage()) )
		{
			TT.navigation.enqueueNavigation = {
				call: function() {
						// This callback should only be triggered once
						delete this.call;
						// Timeout used to exit cycle
						setTimeout(TT.navigation.goToBooklist, 1);
				}
			};
			TT.navigation.goToPage(TT.history.THEEND, 1, true);
			TT.paperstack.updateStack(1);//更新book右侧的页面厚度

			return;
		}

		// Update the current page name
		TT.navigation.currentPageName = TT.history.CREDITS;

		$('#page-shadow-overlay').hide();
		$('#front-cover').hide();

		// Add the view specific body class
		$('body').removeClass('book').removeClass(TT.history.HOME).addClass(TT.history.CREDITS);

		// Flag that we are not transition away from a hardcover
		TT.navigation.transitioningFromHardCover = false;
//		TT.log('[goToBooklist]set TT.navigation.transitioningFromHardCover false');

		// Make sure that the last page is marked as current (in case the header nav buttons are used).
		// 把最后一页设为当前页(目的是要强制显示封底页面)
		$('#pages section').removeClass('current');
		$('#pages section').last().addClass('current');

		var currentPage = $('#pages section.current');
		
		// 翻页显示封底页
		TT.navigation.updatePageVisibility(currentPage, 1);
		
		if (!fromHistoryChange)// 非源自记录历史的请求，则更新当前URL为credits
		{
			// Push the current URL to the history
			TT.history.pushState('?m=module_touchview_book.ui_book_front.book&view=credits&b_id='+SERVER_VARIABLES.BOOK_ID);
		}

		// Execute the transition to the credits page
		TT.pageflip.turnToPage(currentPage, currentPage, 1, TT.pageflip.HARD_FLIP);
	}
};


/**
 * 导航到指定页面
 * Navigates to a specific page in the book.
 * 
 * @param {string} articleId 章节id
 * @param {number} pageNumber The page number (relative to the article) that should be navigated to.
 * @param {boolean} fromHistoryChange 是否标志为历史变化，如果是，则不更新当前链接，否，则更新当前链接。If flagged as true, there will be no transition.
 * 
 * @return {boolean} True if successful.
 */
TT.navigation.goToPage = function(articleId, pageNumber, fromHistoryChange)
{
	if (!articleId)
	{
		return false;
	}
//	TT.log('gotopage:articleId '+articleId+' pageNumber '+pageNumber+' fromHistoryChange '+fromHistoryChange);
	
	TT.navigation.loadImages(articleId, pageNumber);
	
	// 如果是credits页面，并且目标页面不是theend
	if (TT.navigation.isCreditsPage() && articleId !== TT.history.THEEND)
	{
		TT.navigation.enqueueNavigation = {
			articleId: articleId,
			pageNumber: pageNumber,
			fromHistoryChange: fromHistoryChange,
			call: function() {
				delete this.call;// This callback should only be triggered once
				TT.navigation.goToPage(this.articleId, this.pageNumber,	this.fromHistoryChange);
			}
		};

		articleId = TT.history.THEEND;// 强制先到theend页面
		pageNumber = 1;
	}

	// The currently visible page, i.e. the page we are leaving
	var currentPage = $('#pages section.current');// 当前页面

	// The page that we are navigating too, this page will be the new "currentPage".
	var targetPage = $('#pages section.title-' + articleId + '.page-' +	pageNumber);// 目标页面

	TT.navigation.hasNavigated = true;// 已有导航标志

	TT.tableofthings.hide();// 隐藏book目录层

	// We should never navigate to the page we are already on.
	var isSamePageInBook = (currentPage.attr('class') === targetPage.attr('class'));
	var isSamePageOverall = (targetPage.attr('class') === TT.navigation.currentPageName);

	if ((!isSamePageOverall && !isSamePageInBook) || (TT.navigation.isHomePage() || TT.navigation.isCreditsPage()) )
	{
		TT.navigation.currentPageName = targetPage.attr('class');

		var type = TT.pageflip.SOFT_FLIP;// Assume that we will be doing a soft flip

		// If we are on either the home or credits pages, change the transition to hard cover.
		if (TT.navigation.isHomePage() || TT.navigation.isCreditsPage() )
		{
			type = TT.pageflip.HARD_FLIP;
			TT.navigation.transitioningFromHardCover = true;
//			TT.log('[goToPage]set TT.navigation.transitioningFromHardCover true');
		}

		// Determine the global page numbers of the current and target pages
		var currentGlobalPageNumber = TT.navigation.classToGlobalPage($('.current').attr('class'));
		var targetGlobalPageNumber = TT.navigation.classToGlobalPage(targetPage.attr('class'));

		if (currentGlobalPageNumber != null && targetGlobalPageNumber != null)
		{
			// Determine how many pages we are stepping past
			var steps = Math.abs(currentGlobalPageNumber - targetGlobalPageNumber);

			// Using the global page numbers, we can determine which direction we are navigating in.
			var direction = targetGlobalPageNumber > currentGlobalPageNumber ? 1 : -1;

			// Special case for the home and credits pages which don't have page numbers and directions.
			if (targetGlobalPageNumber == currentGlobalPageNumber)
			{
				direction = TT.navigation.isHomePage() ? 1 : -1;
			}

			// 计算更新相关页面显示
			TT.navigation.updatePageVisibility(targetPage, direction, steps);

			// Execute the transition from the current to the target page
			TT.pageflip.turnToPage(currentPage, targetPage, direction, type);

			if (!fromHistoryChange)
			{
				// Push the current URL to the history
				TT.history.pushState('?m=module_touchview_book.ui_book_front.book&article=' + articleId + '&page=' + pageNumber + '&b_id='+SERVER_VARIABLES.BOOK_ID);
			}

			TT.navigation.updatePageReferences(articleId);

//			TT.log('section:'+articleId+' page:'+pageNumber);

			return true;
		}
	}

	return false;
};


/**
 * 隐藏不相关的section,显示目标section
 * Hides uninvolved sections, shows target section and prepares previous width.
 * 
 * @param {Object} targetPage Target page.
 * @param {number} direction 页面方向，向前为1，向后为-1. Direction of page movement.
 * @param {number} steps Number of steps/pages being moved.
 */
TT.navigation.updatePageVisibility = function(targetPage, direction, steps)
{
	steps = steps || 0;
	
	// Store the depth of the current page
	var currentDepth = parseInt($('#pages section.current').css('z-index'));

	// If we are jumping multiple steps or are on the home page, then use compare with the depth of the page we are going to.
	if (steps > 1 || TT.navigation.isHomePage())
	{
		currentDepth = parseInt(targetPage.css('z-index'));
	}

	// All pages that are at a higher depth than the current page need to be set to zero width.(page越大，zindex值越小)
	$('#pages section:not(.current)').each(function() {
		var z = parseInt($(this).css('z-index'));

		if (z > currentDepth)
		{
			$(this).width(0).hide().css('top');// 隐藏小于当前页的section @?
		}
		else if (z < currentDepth - 1)// Hide all pages further ahead in the book, improves performance since the browser does not need to render all pages all of the time.
		{
			$(this).hide();
		}
	});

	// Show the page we are navigating too
	targetPage.show();

	// Special case, if we are navigating from the home page multiple steps into the book, make sure the current (first) page is hidden.
	if (steps > 1 && direction == 1 && TT.navigation.isHomePage())
	{
		$('#pages section.current').width(0).hide();
		targetPage.width(TT.PAGE_WIDTH).show();
	}

	if (!TT.navigation.isHomePage())
	{
		$('#left-page').width(TT.BOOK_WIDTH_CLOSED).show();
	}
};


/**
 * Update current pointer.
 * @param {Object} currentPage Current page.
 * @param {Object} targetPage Target page.
 */
TT.navigation.updateCurrentPointer = function(currentPage, targetPage)
{
	if (TT.navigation.transitioningFromHardCover)
	{
		$('body').removeClass(TT.history.HOME).removeClass(TT.history.CREDITS).addClass('book');

		$('#page-shadow-overlay').hide();

		TT.navigation.transitioningFromHardCover = false;
//		TT.log('[updateCurrentPointer]set TT.navigation.transitioningFromHardCover false');
	}

	currentPage.removeClass('current');
	targetPage.addClass('current');

	TT.navigation.updatePageReferences();

	if (TT.navigation.enqueueNavigation && TT.navigation.enqueueNavigation.call)
	{
		TT.navigation.enqueueNavigation.call();
		TT.navigation.enqueueNavigation = null;
	}
};


/**
 * Update page references.
 * @param {string} articleId Article ID.
 */
TT.navigation.updatePageReferences = function(articleId) {
//  TT.chapternav.updateSelection(articleId);
  TT.tableofthings.updateSelection(articleId);
  TT.paperstack.updateStack();
};


/**
 * Clean up transitions.
 * 
 * @param {Object} currentPage Current page.
 * @param {Object} targetPage Target page.
 */
TT.navigation.cleanUpTransitions = function(currentPage, targetPage)
{
	TT.pageflip.removeInactiveFlips();

	if (TT.pageflip.flips.length == 0)
	{
//		TT.log('cleanUpTransitions');
		TT.navigation.transitioningFromHardCover = false;
//		TT.log('[cleanUpTransitions]set TT.navigation.transitioningFromHardCover false');
	}
	else
	{
//		TT.log('[cleanUpTransitions]TT.pageflip.flips.length:'+TT.pageflip.flips.length);
	}
};


/**
 * Load images for current page, preload for subsequent pages.
 * @param {string} articleId Article ID.
 * @param {pageNumber} pageNumber Page number.
 */
TT.navigation.loadImages = function(articleId, pageNumber) {
  var cur = articleId && pageNumber ? $('#pages section.title-' + articleId +
      '.page-' + pageNumber) : $('#pages section.current');
  var pages = [cur];
  if (cur.prev('section').length) pages.push(cur.prev('section'));
  if (cur.next('section').length) pages.push(cur.next('section'));

  for (var i = 0; i < pages.length; i++) {
    pages[i].find('img').each(function() {
      if ($(this).attr('src') !== $(this).attr('data-src')) {
        $(this).attr('src', $(this).attr('data-src'));
      }
    });
  }

};

/**
 * 自动翻页功能函数
 */
TT.navigation.autoPageFlip = function() {
	if (!TT.FLAG_AUTO_FLIP)
	{
		return;
	}
	
	var time_now = Date.parse(new Date()); 
	if (TT.TIME_SILENCE_START > 0 && (time_now - TT.TIME_SILENCE_START) >= TT.TIME_INTERVAL_START_AUTO_FLIP)
	{
		TT.log('sleep:auto page flip.['+time_now+']');
		if (TT.navigation.goToNextPage() === false)// 如果是最后一页则自动到封面
		{
			TT.navigation.goToHome();
		}
	}
	else if (TT.TIME_SILENCE_START == 0)// 如果沉默开始时间为0，则设置当前时间
	{
		TT.log('wake.['+time_now+']');
		TT.TIME_SILENCE_START = time_now;
	}
}