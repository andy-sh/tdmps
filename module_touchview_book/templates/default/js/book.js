/**
 * book front脚本主文件
 * 
 * @version $Id: book.js 127 2012-05-07 03:49:53Z liqt $
 */

/**
 * @fileoverview Main class includes initializers, constants, global handlers /
 * utilities, and layout update functions.
 */

/**
 * touch view相关变量域名
 */
var TV = TV || {};

/**
 * Global namespace.
 * @type {Object}
 */
var TT = TT || {};


/**
 * 书中一页的宽度(不包括修饰部分)
 * The width of one page (excluding jacket) in the book.
 * 
 * @type {number}
 */
TT.PAGE_WIDTH = 800;


/**
 * 书中一页的高度(不包括修饰部分)
 * The height of one page (excluding jacket) in the book.
 * 
 * @type {number}
 */
TT.PAGE_HEIGHT = 500;


/**
 * 整合应用页面的最小宽度
 * Minimum width of the whole app (when scaled to be smaller than this, scrollbars will appear).
 * 
 * @type {number}
 */
TT.PAGE_MIN_WIDTH = 1000;


/**
 * 整合应用页面的最小高度
 * Minimum width of the whole app (when scaled to be smaller than this, scrollbars will appear).
 * 
 * @type {number}
 */
TT.PAGE_MIN_HEIGHT = 680;


/**
 * Inner margin (x) of the book (space between where the book jacket and white paper).
 * @type {number}
 */
TT.PAGE_MARGIN_X = 32;


/**
 * Inner margin (y) of the book (space between where the book jacket and white paper).
 * @type {number}
 */
TT.PAGE_MARGIN_Y = 10;


/**
 * div id book的宽度（包含书套等）
 * The total width of the book, including jacket.
 * 
 * @type {number}
 */
TT.BOOK_WIDTH = 1660;


/**
 * div id book的宽度（包含书套等）
 * The total width of the book, including jacket.
 * 
 * @type {number}
 */
TT.BOOK_HEIGHT = 520;


/**
 * The width of the closed book, including jacket.
 * @type {number}
 */
TT.BOOK_WIDTH_CLOSED = TT.BOOK_WIDTH / 2;


/**
 * An offset applied to the horizontal positioning of the book (#book).
 * @type {number}
 */
TT.BOOK_OFFSET_X = 5;


/**
 * 浏览器信息
 * User agent.
 * 
 * @type {string}
 */
TT.UA = navigator.userAgent.toLowerCase();


/**
 * 是否是移动设备
 * Whether UA is a touch device.
 * 
 * @type {boolean}
 */
TT.IS_TOUCH_DEVICE = TT.UA.match(/android/) || TT.UA.match(/iphone/) || TT.UA.match(/ipad/) || TT.UA.match(/ipod/);

/**
 * 鼠标或键盘沉默开始时间
 */
TT.TIME_SILENCE_START = 0;

/**
 * 触发自动翻页开始的间隔时长（毫秒）
 */
TT.TIME_INTERVAL_START_AUTO_FLIP = SERVER_VARIABLES.AUTO_FLIP_WAITING*1000;

/**
 * 自动翻页开关
 */
TT.FLAG_AUTO_FLIP = SERVER_VARIABLES.AUTO_FLIP_SWITCH;

/**
 * 检查自动翻页的间隔时长（毫秒）
 * 也影响自动翻页速度
 */
TT.TIME_INTERVAL_CHECK_AUTO_FLIP = 6000;

// 设置模板所需图片
TV.TPL_IMG_FRONT_COVER = SERVER_VARIABLES.TEMPLATE+'front-cover.jpg';// 封面
TV.TPL_IMG_BACK_COVER = SERVER_VARIABLES.TEMPLATE+'back-cover.jpg';// 封底
TV.TPL_IMG_BACK_FLIP = SERVER_VARIABLES.TEMPLATE+'back-cover-flipped.jpg';// 封底的水平翻转
TV.TPL_IMG_LEFT_PAGE = SERVER_VARIABLES.TEMPLATE+'left-page.jpg';// 左页面
TV.TPL_IMG_LEFT_PAGE_FLIP = SERVER_VARIABLES.TEMPLATE+'left-page-flipped.jpg';// 左页面的水平翻转
TV.TPL_IMG_RIGHT_PAGE = SERVER_VARIABLES.TEMPLATE+'right-page.jpg';// 右页面
TV.TPL_IMG_RIGHT_PAGE_PAPER = SERVER_VARIABLES.TEMPLATE+'right-page-paper.jpg';// 右页面纸张
TV.TPL_IMG_THICK = SERVER_VARIABLES.TEMPLATE+'thick.jpg';// 封皮厚度填充图


/**
 * Initiates the main application logic. 
 * This is the first point at which any scripting logic is applied.
 */
TT.initialize = function()
{
	TT.preloader.initialize();// 预加载

	// Initialize managers, do not alter the order in which these are called.
	TT.storage.initialize();// 本地存储加载
//	TT.chapternav.initialize();// 章节导航初始化
	TT.paperstack.initialize();// 模拟书页厚度
	TT.tableofthings.initialize();// 书目初始化
	
	// Register event listeners.
	$(window).resize(TT.onWindowResize);// 窗口大小调整
	$(window).scroll(TT.onWindowScroll);// 窗口滚动
	
	// Trigger an initial update of the layout.
	TT.updateLayout();

	// Prevent native drag and drop behavior of all images. This is important since it is very easy to start dragging assets by mistake while trying to flip pages.
	$('img').mousedown(function(event) { event.preventDefault() });
};


/**
 * 加载完成后显示book
 * Called when the contents of the application has finished loading and we are ready to show the book.
 */
TT.startup = function()
{
	// Initialize the managers which have depenencies on content being loaded.
	TT.navigation.initialize();
	TT.pageflip.initialize();
	TT.history.initialize();

	// Update the navigation selections.
//	TT.chapternav.updateSelection();
	TT.tableofthings.updateSelection();

	// Update the paper stack to match the current page.
	TT.paperstack.updateStack();

	// 获取当前时间戳
	TT.TIME_SILENCE_START = Date.parse(new Date());
	
	$('body').mousemove(function(event){
		// 鼠标移动清零沉默开始时间
		TT.TIME_SILENCE_START = 0;
	});
	
	// 定时检查是否自动翻页
	setInterval('TT.navigation.autoPageFlip()', TT.TIME_INTERVAL_CHECK_AUTO_FLIP);
};


/**
 * 窗口调整大小时的触发事件
 * Event handler for window.onresize, results in an update of the layout.
 * 
 * @param {Object} event Resize event object.
 */
TT.onWindowResize = function(event)
{
	TT.updateLayout();
};


/**
 * 窗口滚动条变化时的触发事件
 * Event handler for window.scroll, results in an update to certain parts of the layout.
 * 
 * @param {Object} event Scroll event object.
 */
TT.onWindowScroll = function(event)
{
	TT.updateLayout(true);
};


/**
 * 更新需要JS控制位置的所有布局元素
 * 
 * Updates the layout of all elements that require JS controlled positioning.
 * 
 * This is typically elements that are centered but with limits on min and max positions.
 *
 * Note that most of these elements will originally be positioned entirely via
 * CSS. JS control over the positioning is especially important for resizing
 * logic, explicit control of overflows, centering etc.
 *
 * @param {boolean} fromScroll Flags if this update to the layout originates from the application being scrolled.
 */
TT.updateLayout = function(fromScroll)
{
//	TT.log('updateLayout');
	// Fetch the application size
	var applicationSize = {
		width: $(window).width(),
		height: $(window).height()
	};
	
	// If we are not below the minimum size of the app, overflow should always be hidden.
	$('body').css({
		overflowX: applicationSize.width < TT.PAGE_MIN_WIDTH ? 'auto' : 'hidden',
		overflowY: applicationSize.height < TT.PAGE_MIN_HEIGHT ? 'auto' : 'hidden'
	});

	// Limit the screen size to the bounds
	applicationSize.width = Math.max(applicationSize.width, TT.PAGE_MIN_WIDTH);
	applicationSize.height = Math.max(applicationSize.height, TT.PAGE_MIN_HEIGHT);

	// Determine the center point of the application
	var center = { x: applicationSize.width * 0.5, y: applicationSize.height * 0.5 };

	// Only update component positioning if this update does not originate from a scroll event.
	if (!fromScroll)
	{
		// Align the book to the center of the page with the right side page in focus.
		$('#book').css({
			left: center.x - (TT.BOOK_WIDTH * 0.5) - (TT.BOOK_WIDTH_CLOSED * 0.5) +	TT.BOOK_OFFSET_X,
			top: center.y - (TT.BOOK_HEIGHT * 0.5),
			margin: 0
		});

		// Align the table of contents to the center of the screen
		$('#table-of-contents div.center').css({
			left: center.x - (parseInt($('#table-of-contents div.center').innerWidth()) * 0.5),
			top: center.y -	(parseInt($('#table-of-contents div.center').height()) * 0.5),
			margin: 0
		});

		// Set explicit sizes to certain elements (100% width is not desirable due to the min size logic).
		$('#table-of-contents, header, footer').css({
			width: applicationSize.width
		});

		// Align the credits to the center of the screen
		$('#credits').css({
			left: center.x - ($('#credits').width() * 0.5),
			top: center.y - ($('#credits').height() * 0.5),
			margin: 0
		});

		// Align the footer to the bottom of the application
		$('footer').css({
			top: applicationSize.height - $('footer').height(),
			margin: 0
		});

		// Align the chapter nav to the bottom center of the book
		$('#chapter-nav').css({
			left: center.x - ($('#chapter-nav').width() * 0.5) + 5 + TT.BOOK_OFFSET_X,
			top: $('footer').position().top - $('#chapter-nav').outerHeight() + 5
		});
	}

};


/**
 * Outputs a log of the passed in object. This is centralized in one method so
 * that we can keep info logs around the site and easily disable/enable them
 * when jumping between live/dev.
 * @param {string} o Message to log to console.
 */
TT.log = function(o)
{
	if (window.console && o)
	{
		window.console.log(o);
	}
};

/**
 * 返回当前时间戳
 * A global shorthand for retrieving the current time.
 * 
 * @return {Object} Date object.
 */
TT.time = function()
{
	return new Date().getTime();
};

/**
 * 暂停代码执行函数
 * 
 * @param ms 毫秒数
 */
TT.sleep = function(ms)
{
	var dt = new Date();
	dt.setTime(dt.getTime() + ms);
	while (new Date().getTime() < dt.getTime());
}


/**
 * Assign namespace to window object.
 */
window['TT'] = TT;
