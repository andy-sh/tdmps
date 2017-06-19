/**
 * book.storage
 * book front存储实现
 * 
 * @version $Id: book.storage.js 62 2012-03-03 04:05:16Z liqt $ 
 */

/**
 * @fileoverview Manages the downloading and storage of the book contents.
 * 
 * 存储的localStorage有两个：book(书籍id),data(页面内容)
 *
 * The first time the user visits the application, content will be downloaded
 * from the server and stored locally on the client's machine using HTML5's
 * Local Storage API. 
 */

/**
 * Sub-namespace.
 * @type {Object}
 */
TT.storage = {};

/**
 * Storage contents.
 */
TT.storage.contents = '';


/**
 * Article list.
 */
TT.storage.data = {
	articles: {} // 存储书籍内容
};


/**
 * 初始存储类
 * Initialize storage class.
 */
TT.storage.initialize = function()
{
	TT.storage.routeDataRequest();
};

/**
 * 存储book内容数据到本地
 * Save local storage data.
 */
TT.storage.save = function()
{
	if (TT.storage.supportsLocalStorage())
	{
		localStorage.data = $.toJSON(TT.storage.data);
	}
};


/**
 * 检查是否支持本地存储
 * Check for localStorage support.
 * 
 * @return {boolean} Whether UA supports local storage.
 */
TT.storage.supportsLocalStorage = function()
{
	return ('localStorage' in window) && window['localStorage'] !== null;
};


/**
 * 从server获取书籍内容
 * Get articles from server, append to DOM and put in local storage.
 */
TT.storage.getArticlesFromServer = function() {
	TT.log('Getting articles from server.');

	$.ajax({
		url: '?m=module_touchview_book.ui_book_front.get_pages&b_id='+SERVER_VARIABLES.BOOK_ID,
		contentType: 'text/html;charset=UTF-8',
		success: function(data) {
			var globalPageCounter = 0;

			TT.storage.data.articles = {};

			$(data).each(function() {
				var articleId = $(this).attr('id');
				$(this).find('section').each(function(i) {
					globalPageCounter++;

					$(this).addClass('globalPage-' + globalPageCounter).css('zIndex', 500 - globalPageCounter).hide();

					// If local storage is supported, save the content for this page.
					if (TT.storage.supportsLocalStorage())
					{
						TT.storage.data.articles['?m=module_touchview_book.ui_book_front.book&article=' + articleId + '&page=' + (i + 1)] 
						= $('<div>').append($(this).clone()).remove().html();
					}
					
					$('#pages').append($('<div>').append($(this).clone()).remove().html());
				});
			});

			TT.storage.save();

			TT.storage.activateCurrentPageAndSetPageCount();
		}
	});
};

/**
 * 从本地获取书籍内容
 * Get articles from local storage and append to DOM.
 */
TT.storage.getArticlesFromStorage = function()
{
	TT.log('Getting articles from storage.');
	
	if (localStorage.data)
	{
		TT.storage.data = $.parseJSON(localStorage.data);
	}else // If there is no data in local storage we have to update.
	{
		TT.storage.getArticlesFromServer();
		return;
	}

	for (var articlePath in TT.storage.data.articles)
	{
		$('#pages').append(TT.storage.data.articles[articlePath]);
	}

	TT.storage.activateCurrentPageAndSetPageCount();
};


/**
 * 检查并获得页面数据来源
 * Route data request to server or local storage.
 */
TT.storage.routeDataRequest = function()
{
	if (!TT.storage.supportsLocalStorage())
	{
		TT.storage.getArticlesFromServer();
	}else
	{
		TT.log('Book id on server is: ' + SERVER_VARIABLES.BOOK_ID);
		TT.log('Book id on local is: ' + localStorage.book);

		if (SERVER_VARIABLES.BOOK_ID != localStorage.book || SERVER_VARIABLES.FORCE_UPDATE)
		{
			localStorage.book = SERVER_VARIABLES.BOOK_ID;
			TT.storage.getArticlesFromServer();
		} else
		{
			TT.storage.getArticlesFromStorage();
		}

	}
};

/**
 * Take original article and insert into dynamically loaded list; set current page number.
 */
TT.storage.activateCurrentPageAndSetPageCount = function()
{
	var $origArticle = $('#pages section').eq(0);
	$origArticle.attr('id', 'original');

	$('#pages section:not(#original)').each(function(i) {
		if ($(this).hasClass($origArticle.attr('class')))
		{
			$origArticle.remove();
			$(this).addClass('current').show().next('section').show();
			$('<span id="currentPage">' + parseFloat(i + 1) + '</span>').appendTo('body');
		}
	});

	if ($('#pages section.current').length === 0)
	{
		$('#pages section').first().addClass('current');
	}

	// If the app starts with a "view" class (home/credits) then we need to manually select the current page.
	if ($('body').hasClass('home')) {
		$('#pages section').removeClass('current');
		$('#pages section').first().addClass('current');
	}
	else if ($('body').hasClass('credits')) {
		$('#pages section').removeClass('current');
		$('#pages section').last().addClass('current');
	}

	TT.preloader.onContentsLoaded();
//	TT.log('TT.storage.activateCurrentPageAndSetPageCount');
};