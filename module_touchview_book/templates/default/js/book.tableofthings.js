/**
 * book.tableofthings
 * book front目录展示控制
 * 
 * @version $Id: book.tableofthings.js 127 2012-05-07 03:49:53Z liqt $
 */

/**
 * @fileoverview Controls the an hiding of the Table of Things view as well as
 * any interaction and navigation therein.
 */

/**
 * Sub-namespace.
 * @type {Object}
 */
TT.tableofthings = {};


/**
 * The number of columns which the table should be built out of.
 */
TT.tableofthings.COLUMNS = 5;


/**
 * Flags if the table of things view is currently being shown.
 */
TT.tableofthings.visible = false;


/**
 * 初始化目录
 * Initialize table of things class.
 */
TT.tableofthings.initialize = function()
{
	// Register event listeners.
	$('#table-of-contents ul li').click(TT.tableofthings.onChapterClick);
};

/**
 * 更新选择的目录
 * Updates the selection in the Table of Things. 
 * By default, this will select the item that corresponds to the currently selected page.
 * 
 * @param {string} overrideArticleId If this is specified, the item that corresponds to this ID will be selected.
 */
TT.tableofthings.updateSelection = function(overrideArticleId)
{
	// Fetch the article name of the currently selected page.
	var selectedArticleId =	TT.navigation.classToArticle($('#pages section.current').attr('class'));

	// If an override article ID is specified, use that instead of the current page.
	if (overrideArticleId)
	{
		selectedArticleId = overrideArticleId;
	}

	// Remove selection from all elements.
	$('#table-of-contents ul li').removeClass('selected');

	// If the selected article is valid, find the corresponding element and select it.
	if (selectedArticleId)
	{
		var element = $('#table-of-contents ul li').find('[data-article*=' + selectedArticleId + ']');

		if (element && element.parent())
		{
			element.parents('li').addClass('selected');
		}
	}
};


/**
 * 显示book章节目录
 * Show table of things.
 */
TT.tableofthings.show = function()
{
	if (!TT.tableofthings.visible)
	{
		$('body').addClass('tot');

		// Fade in the entire component.
		$('#table-of-contents').stop(true, true).show().fadeTo(200, 1);

		// Make sure the header is fully visible (it fades out when hidden).
		$('#table-of-contents div.header').stop().css({
			opacity: 1
		});

		// Force the layout to update now that the dimensions of the component can be accessed.
		TT.updateLayout();
	}

	TT.tableofthings.visible = true;

	TT.pageflip.unregisterEventListeners();
};


/**
 * 隐藏书籍目录层
 * Hide table of things.
 */
TT.tableofthings.hide = function()
{
	$('body').removeClass('tot');

	// Fade out the entire component.
	$('#table-of-contents').delay(200).fadeTo(200, 0, function() {
		$(this).hide();
	});

	// Fade out the header faster than the component.
	$('#table-of-contents div.header').stop().fadeTo(150, 0);

	var length = $('#table-of-contents ul li').length;

	TT.tableofthings.visible = false;
	
	TT.pageflip.registerEventListeners();

	TT.updateLayout();
};


/**
 * 目录点击事件
 * On click table of things chapter.
 * 
 * @param {Object} event Event object.
 * 
 * @return {boolean} Return false.
 */
TT.tableofthings.onChapterClick = function(event)
{
	if ($('body').hasClass('tot'))
	{
		var articleId = $(event.target).parents('li').children('a').attr('data-article');

		if (!articleId)
		{
			articleId = $(event.target).children('a').attr('data-article');
		}

		if (TT.navigation.goToPage(articleId, 1))
		{
			TT.tableofthings.hide();

//			TT.chapternav.updateSelection(articleId);
			TT.tableofthings.updateSelection(articleId);
		}
	}

	return false;
};