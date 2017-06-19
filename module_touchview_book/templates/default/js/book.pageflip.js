/**
 * book.pageflip
 * 翻页效果实现js文件
 * 
 * @version $Id: book.pageflip.js 146 2012-10-10 05:16:32Z liqt $
 */

/**
 * @fileoverview Handles the logic and rendering of the page flip transitions in the book.
 *
 * There are two types of flips available:
 * 1) Soft flip: used between pages inside of the book.
 * 2) Hard flip: used from/to the front or back cover.
 *
 * Both of the flip types are rendered on <canvas>, the hard flip uses images
 * as textures while the soft flips use only procedurally drawn bitmaps.
 */

/**
 * Sub-namespace.
 * @type {Object}
 */
TT.pageflip = {};


/**
 * The width of region where hinting is triggered.
 */
TT.pageflip.HINT_WIDTH = 300;

/**
 * Padding that will be added below and above the canvas.
 */
TT.pageflip.CANVAS_VERTICAL_PADDING = 80;


/**
 * Padding that will be added to the left and right of the canvas.
 */
TT.pageflip.CANVAS_HORIZONTAL_PADDING = 20;


/**
 * The total size of the canvas, including padding.
 */
TT.pageflip.CANVAS_WIDTH = TT.BOOK_WIDTH +  (TT.pageflip.CANVAS_HORIZONTAL_PADDING * 2);


/**
 * The total size of the canvas, including padding.
 */
TT.pageflip.CANVAS_HEIGHT = TT.BOOK_HEIGHT +  (TT.pageflip.CANVAS_VERTICAL_PADDING * 2);


/**
 * 每秒被canvas渲染的次数
 * The number of times the canvas will be rendered per second.
 */
TT.pageflip.FRAMERATE = 30;


/**
 * The maximum number of milliseconds that can pass between mouse down-up on the
 * hinting for it to flip directly.
 */
TT.pageflip.CLICK_FREQUENCY = 350;


/**
 * The type of flips that can be rendered.
 */
TT.pageflip.SOFT_FLIP = 'soft';


/**
 * The type of flips that can be rendered.
 */
TT.pageflip.HARD_FLIP = 'hard';


/**
 * A listing of all available page elempents in the DOM.
 */
TT.pageflip.pages = [];


/**
 * Holds flip instances, each renders its own flip animation.
 */
TT.pageflip.flips = [];


/**
 * Used to render all flips on.
 */
TT.pageflip.canvas = null;


/**
 * 2D context of TT.pageflip.canvas.
 */
TT.pageflip.context = null;


/**
 * The area of the canvas that needs to be cleared, update during every render loop.
 */
TT.pageflip.dirtyRegion = new Region();


/**
 * 是否拖拽页面的标志
 * Flags if the user is currently dragging the page.
 */
TT.pageflip.dragging = false;


/**
 * Flags if a page turn animation is currently being run.
 */
TT.pageflip.turning = false;


/**
 * 鼠标是否在提示区域标志
 * Flags if the mouse cursor is over the hinting area.
 */
TT.pageflip.hinting = false;


/**
 * 循环计时器id
 * Interval used to invoke the drawing loop.
 */
TT.pageflip.loopInterval = -1;


/**
 * 鼠标当前位置，在移动时更新
 * The current mouse position, updated on mouse move.
 */
TT.pageflip.mouse = { x: 0, y: 0, down: false };


/**
 * 最近鼠标位置列表
 * A list of the most recent mouse positions.
 */
TT.pageflip.mouseHistory = [];


/**
 * Page flip skew object.
 */
TT.pageflip.skew = { top: 0, topTarget: 0, bottom: 0, bottomTarget: 0 };


/**
 * 翻页鼠标按下时间
 * Page flip mouse down time tracker.
 */
TT.pageflip.mouseDownTime = 0;


/**
 * The current hard cover texture and a list of all available hard cover textures.
 */
TT.pageflip.texture = null;


/**
 * Textures.
 */
TT.pageflip.textures = {};


/**
 * Flipped left page.
 */
TT.pageflip.flippedLeftPage = null;


/**
 * Flipped back cover.
 */
TT.pageflip.flippedBackCover = null;


/**
 * Keep track of when, and in which direction, we are navigating through the
 * keyboard so that we and set a limit.
 */
TT.pageflip.lastKeyboardNavigationTime = 0;


/**
 * Keep track of when, and in which direction, we are navigating through the
 * keyboard so that we and set a limit.
 */
TT.pageflip.lastKeyboardNavigationDirection = null;


/**
 * Flags if interaction events are currently attached.
 */
TT.pageflip.eventsAreBound = null;


/**
 * Initialize page flip class.
 */
TT.pageflip.initialize = function()
{
	TT.pageflip.createCanvas();// 生成canvas元素
	TT.pageflip.createTextures();// 获得相关素材

	if (TT.pageflip.eventsAreBound == null)
	{
		TT.pageflip.registerEventListeners();
	}

	$(document).bind('keydown', TT.pageflip.onKeyPress);
};


/**
 * 注册事件监听器
 * Register event listeners.
 */
TT.pageflip.registerEventListeners = function()
{
	TT.pageflip.unregisterEventListeners();

	TT.pageflip.eventsAreBound = true;

	// Register the mouse listeners.
	$(document).bind('mousemove', TT.pageflip.onMouseMove);// 绑定鼠标移动事件
	$(document).bind('mousedown', TT.pageflip.onMouseDown);
	$(document).bind('mouseup', TT.pageflip.onMouseUp);

	if (TT.IS_TOUCH_DEVICE)
	{
		document.addEventListener('touchstart', TT.pageflip.onTouchStart, false);
		document.addEventListener('touchmove', TT.pageflip.onTouchMove, false);
		document.addEventListener('touchend', TT.pageflip.onTouchEnd, false);
	}
};


/**
 * 注销事件监听
 * Unregister event listeners.
 */
TT.pageflip.unregisterEventListeners = function()
{
	TT.pageflip.eventsAreBound = false;

	$(document).unbind('mousemove', TT.pageflip.onMouseMove);
	$(document).unbind('mousedown', TT.pageflip.onMouseDown);
	$(document).unbind('mouseup', TT.pageflip.onMouseUp);

	if (TT.IS_TOUCH_DEVICE)// 移动设备
	{
		document.removeEventListener('touchstart', TT.pageflip.onTouchStart);
		document.removeEventListener('touchmove', TT.pageflip.onTouchMove);
		document.removeEventListener('touchend', TT.pageflip.onTouchEnd);
	}
};


/**
 * 创建为封面、封底、左边页、右边页使用的材质
 * Creates the textures that will be used for the front cover, back cover, left page and right page.
 */
TT.pageflip.createTextures = function()
{
	// 左边页面翻动元素
	TT.pageflip.flippedLeftPage = $('<img>', {
		src: $('#left-page img').attr('data-src-flipped'),
		width: $('#left-page img').attr('width'),
		height: $('#left-page img').attr('height')
	})[0];

	// 封底翻动元素
	TT.pageflip.flippedBackCover = $('<img>', {
		src: $('#back-cover img').attr('data-src-flipped'),
		width: $('#back-cover img').attr('width'),
		height: $('#back-cover img').attr('height')
	})[0];

//	TT.pageflip.textures.front = $('#front-cover img')[0];//封面
	TT.pageflip.textures.front = $('#front-cover canvas')[0];//封面 @?为什么不能直接用canvas的id
	TT.pageflip.textures.back = TT.pageflip.flippedBackCover;//封底
	TT.pageflip.textures.left = TT.pageflip.flippedLeftPage;//左边页
	TT.pageflip.textures.right = $('#right-page img')[0];//右边页
	
	// 构造book封面：1.加载背景图 2.加入书名
	var img_front_cover = new Image();
	img_front_cover.src = TV.TPL_IMG_FRONT_COVER;
	
	var ctx_front_cover = TT.pageflip.textures.front.getContext('2d');
	ctx_front_cover.font="40pt 宋体";
	ctx_front_cover.textAlign = 'center';
//	ctx_front_cover.strokeStyle = "#fff"; // stroke color
	ctx_front_cover.fillStyle = "#fff"; // stroke color
	
	img_front_cover.onload = function()
	{
		ctx_front_cover.drawImage(img_front_cover, 0, 0);
//		ctx_front_cover.strokeText(SERVER_VARIABLES.BOOK_NAME, 300, 260, 400);
		ctx_front_cover.fillText(SERVER_VARIABLES.BOOK_NAME, (TT.pageflip.textures.front.width / 2), (TT.pageflip.textures.front.height / 2 -10), 820);
	};
};


/**
 * 创建可供渲染的canvas元素
 * Creates the canvas element that all page flips will be rendered on.
 */
TT.pageflip.createCanvas = function()
{
	// Create the canvas element that the page flip will be drawn on.
	//<canvas id="pageflip" style="position: absolute; top: -80px; left: -20px; z-index: 0;" width="1700" height="680"></canvas>
	TT.pageflip.canvas = $('<canvas id="pageflip"></canvas>');
	
	TT.pageflip.canvas.css({
		position: 'absolute',
		top: -TT.pageflip.CANVAS_VERTICAL_PADDING,
		left: -TT.pageflip.CANVAS_HORIZONTAL_PADDING,
		zIndex: 0
	});

	TT.pageflip.canvas[0].width = TT.pageflip.CANVAS_WIDTH;
	TT.pageflip.canvas[0].height = TT.pageflip.CANVAS_HEIGHT;
	TT.pageflip.context = TT.pageflip.canvas[0].getContext('2d');

	// Add the canvas to the DOM.
	TT.pageflip.canvas.appendTo($('#book'));
};


/**
 * Creates a book texture from an image but draws it in a <canvas> element.
 * This allows us to apply transformation such as scaling and rotaiton.
 * @param {HTMLImageElement} image The image texture to use.
 * @param {Object} translation x/y translation offsets.
 * @param {Object} scale x/y scaling multipliers.
 * @param {Object} rotation the angle to rotate by.
 * @return {HTMLCanvasElement} containing a drawing of the image texture with
 *     the specified transformation.
 */
TT.pageflip.createCanvasTexture = function(image, translation, scale, rotation) {

  // Create a canvas element to hold our texture.
  var canvas = $('<canvas></canvas>');

  canvas.css({
    position: 'absolute',
    display: 'block'
  });

  canvas[0].width = TT.BOOK_WIDTH_CLOSED;
  canvas[0].height = TT.BOOK_HEIGHT;

  var context = canvas[0].getContext('2d');

  context.translate(translation.x, translation.y);
  context.scale(scale.x, scale.y);
  context.rotate(rotation);

  // Draw a copy of the back cover using the above transformation.
  context.drawImage(image, 0, 0);

  return canvas[0];
};


/**
 * 卷页效果动画实现
 * Activates the page flip rendering engine.
 */
TT.pageflip.activate = function()
{
	// Only activate if the redraw loop is not already running.
	if (TT.pageflip.loopInterval == -1)
	{
		clearInterval(TT.pageflip.loopInterval);// 清除循环计时器
		TT.pageflip.loopInterval = setInterval(TT.pageflip.redraw, 1000 / TT.pageflip.FRAMERATE);// 创建循环计时器
	}

	// While the page flip is being rendered, it needs to be on top of the HTML content.
	TT.pageflip.canvas.css('z-index', 1010);
};


/**
 * Deactivates the page flip rendering engine.
 */
TT.pageflip.deactivate = function()
{
	clearInterval(TT.pageflip.loopInterval);
	TT.pageflip.loopInterval = -1;

	// Make sure that we don't let any drawings remain in the canvas.
	TT.pageflip.context.clearRect(0, 0, TT.pageflip.CANVAS_WIDTH, TT.pageflip.CANVAS_HEIGHT);

	// The canvas can not be on top of the HTML content while it is not being rendered since it would block interaction.
	TT.pageflip.canvas.css('z-index', 0);
};


/**
 * 重绘页面翻动效果
 * Redraws the page flipt so that the current folding properties are reflected visually.
 */
TT.pageflip.redraw = function()
{
	// Canvas and context shorthands.
	var cvs = TT.pageflip.canvas[0];
	var ctx = TT.pageflip.context;

	// Clear the dirty region of the canvas.@?
	var dirtyRect = TT.pageflip.dirtyRegion.toRectangle(40);

	if (dirtyRect.width > 1 && dirtyRect.height > 1)
	{
		ctx.clearRect(dirtyRect.x, dirtyRect.y, dirtyRect.width, dirtyRect.height);// 清除区域
	}

	// Uncomment the following three lines to display the redraw region
	// 取消下三行注释则显示重绘区域，调试用
	// ctx.clearRect( 0, 0, TT.pageflip.CANVAS_WIDTH, TT.pageflip.CANVAS_HEIGHT );
	// ctx.fillStyle = 'rgba(0,255,0,0.3)';
	// ctx.fillRect( dirtyRect.x, dirtyRect.y, dirtyRect.width, dirtyRect.height );

	TT.pageflip.dirtyRegion.reset();

	// Loop through and draw each flip.
	for (var i = 0, len = TT.pageflip.flips.length; i < len; i++)
	{
		var flip = TT.pageflip.flips[i];

		if (flip.type == TT.pageflip.HARD_FLIP)
		{
			TT.pageflip.renderHardFlip(flip);
		}
		else
		{
			TT.pageflip.renderSoftFlip(flip);
		}
	}

	// Clean up unused flip instances.
	TT.pageflip.removeInactiveFlips();
};


/**
 * 渲染soft page翻页效果
 * Render and update a soft page flip based on the passed in definition.
 * 
 * @param {Flip} flip The definition of this flip which determines how the flip should be rendered.
 * 
 * @return {boolean} Whether successful or not.
 */
TT.pageflip.renderSoftFlip = function(flip)
{
	// Create a shorthand for the mouse position.
	var mouse = TT.pageflip.mouse;

	// The skew properties that will be applied to the fold(折叠,对折).
	var skew = TT.pageflip.skew;

	// Canvas and context shorthands.
	var cvs = TT.pageflip.canvas[0];
	var ctx = TT.pageflip.context;

	// Determine which the current visible page is (the page we are navigating AWAY from by flipping).
	var currentPage = flip.currentPage;

	if (flip.direction === -1)
	{
		currentPage = flip.targetPage;
	}
	else
	{
		flip.targetPage.width(TT.PAGE_WIDTH);
	}

	// If dragging is in progress we will handle that and avoid checking for hints.
	if (TT.pageflip.dragging && !flip.consumed)// 被拖拽，并且是在非正常用户区域(页面边缘)
	{
		// Limit the mouse position to the page bounds.
		mouse.x = Math.max(Math.min(mouse.x, TT.PAGE_WIDTH), -TT.PAGE_WIDTH);
		mouse.y = Math.max(Math.min(mouse.y, TT.PAGE_HEIGHT), 0);

		// Determine where the fold should be.
		// 计算哪里需要拖拽的进度比率
		flip.progress = Math.min(mouse.x / TT.PAGE_WIDTH, 1);
	}
	else // 鼠标位于页面边缘，自动卷曲
	{
		var distance = Math.abs(flip.target - flip.progress);
		var speed = flip.target == -1 ? 0.3 : 0.2;

		// The easing equation that will be used for the flip.
		var ease = distance < 1 ? speed + Math.abs(flip.progress * (1 - speed)) : speed;
		ease *= Math.max(1 - Math.abs(flip.progress), flip.target == 1 ? 0.5 : 0.2);

		// Ease progress towards the target.
		flip.progress += (flip.target - flip.progress) * ease;

		// Check if the flip progress is very cloes to the flip target, if it is then this flip is now completed.
		if (Math.round(flip.progress * 99) == Math.round(flip.target * 99))
		{
			flip.progress = flip.target;
			flip.x = TT.PAGE_WIDTH * flip.progress;

			// Ensure that the page masking is up to date.
			currentPage.css({ width: flip.x });

			// Returning here means this last state is not drawn, we don't want that to happen when hinting.
			if (flip.target == 1 || flip.target == -1)// 取消翻页
			{
				flip.consumed = true;
				TT.pageflip.completeCurrentTurn();
				return false;
			}
		}

	}

	// Make sure the x position of the flip reflects the current flip progress.
	flip.x = TT.PAGE_WIDTH * flip.progress;

	// Determine the strength of the fold depending on where the mouse cursor is being dragged.
	flip.strength = 1 - (flip.x / TT.PAGE_WIDTH);

	// Fade out the flipped page during the last bit of transition.
	if (flip.target == -1 && flip.progress < -0.9)
	{
		flip.alpha = 1 - ((Math.abs(flip.progress) - 0.9) / 0.1);
	}

	var shadowAlpha = Math.min(1 - ((Math.abs(flip.progress) - 0.75) / 0.25), 1);

	// A measure of fold strength that ranges from 0-1 and is highest (1) at the book spine.
	var centralizedFoldStrength = flip.strength > 1 ? 2 - flip.strength : flip.strength;

	// How far the page should outdent vertically due to perspective.
	var verticalOutdent = 40 * centralizedFoldStrength;

	// How wide the folded page should be spread.
	var horizontalSpread = (TT.PAGE_WIDTH * 0.5) * flip.strength * 0.95;

	if (flip.x + horizontalSpread < 0)
	{
		horizontalSpread = Math.abs(flip.x);
	}

	if (TT.navigation.isCreditsPage())
	{
		horizontalSpread = 0;
	}

	// The maximum width of the left and right side shadows.
	var shadowSpread = (TT.PAGE_WIDTH * 0.5) * Math.max(Math.min(flip.strength, 0.5), 0);

	var rightShadowWidth = (TT.PAGE_WIDTH * 0.5) * Math.max(Math.min(flip.strength, 0.5), 0);
	var leftShadowWidth = (TT.PAGE_WIDTH * 0.5) * Math.max(Math.min(centralizedFoldStrength, 0.5), 0);
	var foldShadowWidth = (TT.PAGE_WIDTH * 0.9) * Math.max(Math.min(flip.strength, 0.05), 0);

	// Cut the current page where the fold is.
	currentPage.css({ width: Math.max(flip.x + horizontalSpread * 0.5, 0) });

	// If the page is being dragged, apply skewing to it depending on the mouse y position of the mouse.
	if (TT.pageflip.dragging)
	{
		skew.topTarget = Math.max(Math.min((mouse.y / (TT.PAGE_HEIGHT * 0.5)), 1), 0) * (40 * centralizedFoldStrength);
		skew.bottomTarget = Math.max(Math.min(1 - (mouse.y - (TT.PAGE_HEIGHT * 0.5)) / (TT.PAGE_HEIGHT * 0.5), 1), 0) *	(40 * centralizedFoldStrength);
	}
	else {
		skew.topTarget = 0;
		skew.bottomTarget = 0;
	}

	// Ensure that there is absolutely no skewing when the flip is entirely in its rested state.
	if (flip.progress === 1)
	{
		skew.top = 0;
		skew.bottom = 0;
	}

	// Animate the skew.
	skew.top += (skew.topTarget - skew.top) * 0.3;
	skew.bottom += (skew.bottomTarget - skew.bottom) * 0.3;

	// Make sure the flip is rendered on the right side of the incision which masks the page contents.
	flip.x += horizontalSpread;

	// Offset that will be used to translate the canvas coordinate space to
	// simulate the top of the book spine being 0,0 (the real 0,0 is actually at
	// the top left corner of the full book spread).
	var drawingOffset = {
			x: TT.pageflip.CANVAS_HORIZONTAL_PADDING + TT.PAGE_MARGIN_X + TT.PAGE_WIDTH,
			y: TT.pageflip.CANVAS_VERTICAL_PADDING + TT.PAGE_MARGIN_Y
	};

	// Offset by the page margin.
	ctx.save();
	ctx.translate(drawingOffset.x, drawingOffset.y);
	ctx.globalAlpha = flip.alpha;

	if (flip.direction == -1)
	{
		ctx.globalCompositeOperation = 'destination-over';
	}

	// Enhance the fold line by drawing a straight vertical line.
	ctx.strokeStyle = 'rgba(0,0,0,0.1)';
	ctx.lineWidth = 0.5;
	ctx.beginPath();
	ctx.moveTo(flip.x + 1, 0);
	ctx.lineTo(flip.x + 1, TT.PAGE_HEIGHT);
	ctx.stroke();

	// Folder paper gradient.
	var foldGradient = ctx.createLinearGradient(flip.x - shadowSpread, 0, flip.x, 0);
	foldGradient.addColorStop(0.35, '#fafafa');
	foldGradient.addColorStop(0.73, '#eeeeee');
	foldGradient.addColorStop(0.9, '#fafafa');
	foldGradient.addColorStop(1.0, '#e2e2e2');

	// Folded paper style.
	ctx.fillStyle = foldGradient;
	ctx.strokeStyle = 'rgba(0,0,0,0.1)';
	ctx.lineWidth = 0.5;

	// Draw the folded piece of paper.
	ctx.beginPath();
	ctx.moveTo(flip.x, 0);
	ctx.lineTo(flip.x, TT.PAGE_HEIGHT);
	ctx.quadraticCurveTo(flip.x, TT.PAGE_HEIGHT + (verticalOutdent * 1.9),	flip.x - horizontalSpread + skew.bottom, TT.PAGE_HEIGHT + verticalOutdent);
	ctx.lineTo(flip.x - horizontalSpread + skew.top, -verticalOutdent);
	ctx.quadraticCurveTo(flip.x, -verticalOutdent * 1.9, flip.x, 0);

	ctx.fill();
//	ctx.stroke();

	// Draw a sharp shadow of the fold to the left.
	if (!TT.IS_TOUCH_DEVICE)
	{
		ctx.beginPath();
		ctx.strokeStyle = 'rgba(0,0,0,' + (0.04 * shadowAlpha) + ')';
		ctx.lineWidth = 20 * shadowAlpha;
		ctx.beginPath();
		ctx.moveTo(flip.x + skew.top - horizontalSpread, -verticalOutdent * 0.5);
		ctx.lineTo(flip.x + skew.bottom - horizontalSpread, TT.PAGE_HEIGHT + (verticalOutdent * 0.5));
		ctx.stroke();
		
	
		// Right side drop shadow gradient.
		var rightShadowGradient = ctx.createLinearGradient(flip.x, 0, flip.x + rightShadowWidth, 0);
		rightShadowGradient.addColorStop(0, 'rgba(0,0,0,' + (shadowAlpha * 0.1) + ')');
		rightShadowGradient.addColorStop(0.8, 'rgba(0,0,0,0.0)');
	
		ctx.save();
		ctx.globalCompositeOperation = 'destination-over';
	
		ctx.fillStyle = rightShadowGradient;
		ctx.beginPath();
		ctx.moveTo(flip.x, 0);
		ctx.lineTo(flip.x + rightShadowWidth, 0);
		ctx.lineTo(flip.x + rightShadowWidth, TT.PAGE_HEIGHT);
		ctx.lineTo(flip.x, TT.PAGE_HEIGHT);
		ctx.fill();
	
		// Fold drop shadow gradient.
		var foldShadowGradient = ctx.createLinearGradient(flip.x, 0, flip.x + foldShadowWidth, 0);
		foldShadowGradient.addColorStop(0, 'rgba(0,0,0,' + (shadowAlpha * 0.15) + ')');
		foldShadowGradient.addColorStop(1, 'rgba(0,0,0,0.0)');
	
		ctx.fillStyle = foldShadowGradient;
		ctx.beginPath();
		ctx.moveTo(flip.x, 0);
		ctx.lineTo(flip.x + foldShadowWidth, 0);
		ctx.lineTo(flip.x + foldShadowWidth, TT.PAGE_HEIGHT);
		ctx.lineTo(flip.x, TT.PAGE_HEIGHT);
		ctx.fill();
	
		ctx.restore();
	
		// Left side drop shadow gradient.
		var leftShadowGradient = ctx.createLinearGradient(flip.x - horizontalSpread - leftShadowWidth, 0, flip.x - horizontalSpread, 0);
		leftShadowGradient.addColorStop(0, 'rgba(0,0,0,0.0)');
		leftShadowGradient.addColorStop(1, 'rgba(0,0,0,' + (shadowAlpha * 0.05) + ')');
	
		ctx.fillStyle = leftShadowGradient;
		ctx.beginPath();
		ctx.moveTo(flip.x - horizontalSpread + skew.top - leftShadowWidth, 0);
		ctx.lineTo(flip.x - horizontalSpread + skew.top, 0);
		ctx.lineTo(flip.x - horizontalSpread + skew.bottom, TT.PAGE_HEIGHT);
		ctx.lineTo(flip.x - horizontalSpread + skew.bottom - leftShadowWidth, TT.PAGE_HEIGHT);
		ctx.fill();
	}
	// Restore the co-ordinate space.
	ctx.restore();

	TT.pageflip.dirtyRegion.inflate(TT.PAGE_WIDTH +	TT.pageflip.CANVAS_HORIZONTAL_PADDING + flip.x - horizontalSpread -	leftShadowWidth, 0);
	TT.pageflip.dirtyRegion.inflate(TT.PAGE_WIDTH +	TT.pageflip.CANVAS_HORIZONTAL_PADDING + flip.x + rightShadowWidth, TT.pageflip.CANVAS_HEIGHT);
};


/**
 * 渲染hard page翻页效果
 * Render and update a hard page flip based on the passed in definition.
 * 
 * @param {Flip} flip The definition of this flip which determines how the flip should be rendered.
 */
TT.pageflip.renderHardFlip = function(flip)
{
	// Create a shorthand for the mouse position.
	var mouse = TT.pageflip.mouse;

	// 偏移属性。The skew properties that will be applied to the fold.
	var skew = TT.pageflip.skew;

	// Canvas and context shorthands.
	var cvs = TT.pageflip.canvas[0];
	var ctx = TT.pageflip.context;

	// Determine which the current visible page is (the page we are navigating AWAY from by flipping).
	var currentPage = flip.currentPage;

	if (flip.direction === -1)// 左翻页
	{
		currentPage = flip.targetPage;
	}

	// If dragging is in progress we will handle that and avoid checking for hints.
	if (TT.pageflip.dragging)
	{
		// Limit the mouse position to the page bounds.
		mouse.x = Math.max(Math.min(mouse.x, TT.PAGE_WIDTH), -TT.PAGE_WIDTH);
		mouse.y = Math.max(Math.min(mouse.y, TT.PAGE_HEIGHT), 0);

		flip.target = mouse.x / TT.PAGE_WIDTH;
		flip.progress += (flip.target - flip.progress) * 0.4;
	}
	else
	{
		if (Math.abs(flip.target) === 1)
		{
			// Ease-in-out.
			flip.progress += Math.max(0.5 * (1 - Math.abs(flip.progress)), 0.02) * (flip.target < flip.progress ? -1 : 1);
			flip.progress = Math.max(Math.min(flip.progress, 1), -1);
		}
		else
		{
			flip.progress += (flip.target - flip.progress) * 0.4;
//			TT.log('transitioningFromHardCover:'+TT.navigation.transitioningFromHardCover+' target:'+flip.target+' progress:'+flip.progress);
		}

		// Check if the flip progress is very cloes to the flip target, if it is then this flip is now completed.
		if (flip.progress === 1 || flip.progress === -1)
		{
			flip.progress = flip.target;
			flip.x = TT.PAGE_WIDTH * flip.progress;

			if (TT.navigation.isCreditsPage())
			{
				// Ensure that the page masking is up to date.
				currentPage.width(flip.x);
			}

			// Returning here means this last state is not drawn, we don't want that to happen when hinting.
			if (flip.target == 1 || flip.target == -1)
			{
				flip.consumed = true;
				TT.pageflip.completeCurrentTurn();
			}
		}
		
	}

	// The big if maze below is... not that easy to decipher. In essence it
	// monitors the current progress of the flip and determines which texture to
	// use based on that. Furthermore it also shows and hides DOM elements based
	// on flipping progress.
	if (TT.navigation.isHomePage())
	{
		// If we are within a X% range of the goal, show the original front cover
		// (it will be underneath the canvas but appear once the hard flip is over).
		if (flip.progress > 0.99) {
			$('#front-cover').show();
		}
		else {
			$('#front-cover').hide();
		}

		if (flip.progress > 0) {

			// We are on the right side of flipping, use the front cover texture.
			TT.pageflip.texture = TT.pageflip.textures.front;

			$('#left-page').width(0).hide();
		}
		else {

			// We are on the left side of flipping, use the page texture.
			TT.pageflip.texture = TT.pageflip.textures.left;

			if (flip.progress < -0.99) {

				// We have flipped almost fully into the book, so we need to show the
				// left-page underneath the ongoing flip.
				$('#left-page').show().width(TT.BOOK_WIDTH_CLOSED);
			}
			else {
				$('#left-page').width(0).hide();
				$('#right-page').show();
			}
		}
		
		if (!TT.IS_TOUCH_DEVICE)// 阴影效果
		{
			$('#page-shadow-overlay').stop(true, true).fadeTo(0.1, flip.progress * 0.3);
		}
	}
	else if (TT.navigation.isCreditsPage() || TT.navigation.isLastPage())
	{

		// If we are within a X% range of the goal, show the original back cover
		// (it will be underneath the canvas but appear once the hard flip is over).
		if (flip.progress < -0.998) {
			if (TT.navigation.isCreditsPage()) {
				$('#back-cover').show();
			}
			else {
				$('#left-page').show().width(TT.BOOK_WIDTH_CLOSED);
			}
		}
		else {
			$('#back-cover').hide();
			$('body').addClass('credits');
		}

		if (flip.progress > 0) {
			TT.pageflip.texture = TT.pageflip.textures.right;

			if (flip.progress > 0.996) {
				$('#right-page').show();
				$('body').removeClass('credits');
			}
			else {
				$('#right-page').hide();
			}
		}
		else {
			TT.pageflip.texture = TT.pageflip.textures.back;
		}
	}
	else {
		$('#right-page').show();
		$('#left-page').show().width(TT.BOOK_WIDTH_CLOSED);
	}

	// Fade out the flipped page during the last bit of transition.
	if (flip.target == -1 && flip.progress < -0.95) {
		flip.alpha = 1 - ((Math.abs(flip.progress) - 0.95) / 0.05);
	}

	// Make sure the x position of the flip reflects the current flip progress.
	flip.x = TT.PAGE_WIDTH * flip.progress;

	// Determine the strength of the fold depending on where the mouse cursor is
	// being dragged.
	flip.strength = 1 - (flip.x / TT.PAGE_WIDTH);

	var centralizedFoldStrength = flip.strength > 1 ? 2 - flip.strength : flip.strength;

	if (TT.navigation.isCreditsPage() || TT.navigation.isLastPage()) {

		// Cut the current page where the fold is.
		currentPage.css({width: Math.max(flip.x, 0)});
	}

	// Offset by the page margin.
	ctx.save();

	ctx.translate(TT.pageflip.CANVAS_HORIZONTAL_PADDING + TT.BOOK_WIDTH_CLOSED,	TT.pageflip.CANVAS_VERTICAL_PADDING);

	var scaleX = flip.progress;
	var scaleY = 0;

	// A higher factor will increase the percieved perspective.
	var scaleYFactor = 0.35;

	// Determine the actual scale multiplier.
	var scaleYFinal = 1 + (1 * scaleYFactor) * centralizedFoldStrength;

	var width = TT.BOOK_WIDTH_CLOSED;
	var height = TT.BOOK_HEIGHT;

	// The number of segments/columns that the distorted image will consist of.
	var segments = Math.round(40 + (30 * (0.9999 - ((TT.BOOK_WIDTH_CLOSED *	scaleX)) / TT.BOOK_WIDTH_CLOSED)));

	// Make sure that each segment consists of at least one pixel.
	segments = Math.min(width, segments);

	// The width of each segment.
	var segmentWidth = width / segments;

	// The thickness of the perspective bit.
	var thickness = 10 * centralizedFoldStrength;

	// Offesets used to position and distort the perspective bit.
	var hoffset = flip.progress <= 0.05 ? 1 + (1 - (Math.max(flip.progress, 0) / 0.05)) * -thickness : -1;
	var voffset = { left: Math.abs(Math.min(flip.progress, 0)) * 2,	right: flip.progress * 2 };

	// If there is some perspective (i.e. 1%+) we draw a solid colored blue box
	// behind the cover. This block gets anti-aliased by the browser and hence
	// smoothens the otherwise pixelated edges of the cover.
	
	if (Math.abs(scaleX) < 0.99) {
		var ext = ((height - (height * scaleYFinal)) / 2);
		
		// 填充书籍封皮厚度
		var image_thick = new Image();
		image_thick.src = TV.TPL_IMG_THICK;
		
		image_thick.onload = function()
		{
			ctx.fillStyle = ctx.createPattern(image_thick, "repeat");
		};
		
		ctx.beginPath();
		ctx.moveTo(0, -0.5);
		ctx.lineTo((width * scaleX) - (2 * scaleX), ext - 0.5);
		ctx.lineTo((width * scaleX) + (thickness + hoffset), ext + voffset.right);
		ctx.lineTo((width * scaleX) + (thickness + hoffset), ext + (height * scaleYFinal) - voffset.right);
		ctx.lineTo((width * scaleX) - (2 * scaleX), ext + (height * scaleYFinal) + 0.5);
		ctx.lineTo(0, height + 0.5);
		ctx.closePath();
		ctx.fill();
	}

	for (var i = 0; i < segments; i++)
	{
		// Determine how much this segment should scale vertically.
		scaleY = 1 + (i / segments) * scaleYFactor * centralizedFoldStrength;

		// Offset the segment upwards by half of the added height.
		var y = (height - (height * scaleY)) / 2;

		var sw = i >= segments - 1 ? segmentWidth : segmentWidth + 3;

		ctx.save();
		ctx.translate(0, y);
		ctx.transform(scaleX, 0, 0, scaleY, 0, 0);
		
		ctx.drawImage(TT.pageflip.texture, i * segmentWidth, 0, sw, height,	i * segmentWidth, 0, sw, height);

		ctx.restore();
	}

	// Color intensity of the perspective bit.
	var intensity = Math.max(Math.abs(centralizedFoldStrength), 0.9);

	// Perspective drawing settings.
	var ps = {
			top: {
				x: (width * scaleX) + hoffset,
				y: (height - (height * scaleY)) / 2
			},
			bottom: {
				x: (width * scaleX) + hoffset,
				y: ((height - (height * scaleY)) / 2) + height * scaleY
			}
	};

	// 填充书籍封皮厚度
	var image_thick = new Image();
	image_thick.src = TV.TPL_IMG_THICK;
	
	image_thick.onload = function()
	{
		ctx.fillStyle = ctx.createPattern(image_thick, "repeat");
	};
	
	ctx.beginPath();
	ctx.moveTo(ps.top.x, ps.top.y + voffset.left);
	ctx.lineTo(ps.top.x + thickness, ps.top.y + voffset.right);
	ctx.lineTo(ps.bottom.x + thickness, ps.bottom.y - voffset.right);
	ctx.lineTo(ps.bottom.x, ps.bottom.y - voffset.left);
	ctx.closePath();
//	ctx.fill();

	TT.pageflip.dirtyRegion.inflate(TT.PAGE_WIDTH +	TT.pageflip.CANVAS_HORIZONTAL_PADDING + TT.PAGE_MARGIN_X - thickness, ps.top.y + TT.pageflip.CANVAS_VERTICAL_PADDING);
	TT.pageflip.dirtyRegion.inflate(TT.PAGE_WIDTH +	TT.pageflip.CANVAS_HORIZONTAL_PADDING + (width * scaleX) + thickness, ps.bottom.y + TT.pageflip.CANVAS_VERTICAL_PADDING);

	// Restore the co-ordinate space.
	ctx.restore();
};


/**
 * Goes through all currently instantiated flips and deletes the ones that are
 * not in use anymore. If there are no active flips left at all, the rendering
 * enginge is deactivated.
 */
TT.pageflip.removeInactiveFlips = function() {

  // Counter for the current number of active flips.
  var activeFlips = 0;

  // Loop through and delete inactive flips.
  for (var i = 0; i < TT.pageflip.flips.length; i++) {

    // Fetch a reference to the current Flip instance.
    var flip = TT.pageflip.flips[i];

    // Has this flip reached its end point?
    if (flip.progress === flip.target && (flip.target === 1 || flip.target === -1))
    {
    	TT.pageflip.flips.splice(i, 1);
    	i--;
    }
    else
    {
    	activeFlips++;
    }
  }

  if (activeFlips == 0) {
    // Deactive redrawing.
    TT.pageflip.deactivate();
  }
};


/**
 * 清除flips中的所有hard flip
 * Removes all currently existing hard flips, no matter what state they are in.
 */
TT.pageflip.removeHardFlips = function()
{
	// Loop through and delete hard flips.
	for (var i = 0; i < TT.pageflip.flips.length; i++)
	{
		var flip = TT.pageflip.flips[i];

		if (flip.type == TT.pageflip.HARD_FLIP)
		{
			TT.pageflip.flips.splice(i, 1);
			i--;
		}
	}
};


/**
 * 执行页面间翻页
 * Executes a flip between two pages.
 * 
 * @param {Object} currentPage The page that we are flipping away from.
 * @param {Object} targetPage The page that we are flipping to.
 * @param {number} direction Used to chose which animation that should be shown,-1 means left and +1 means right.
 * @param {string} type The type of flip that should be used for the page turn (i.e. 'soft' or 'hard').
 */
TT.pageflip.turnToPage = function(currentPage, targetPage, direction, type)
{
	// If we are initiating a hard flip, remove any other ongoing hard flips.
	if (type == TT.pageflip.HARD_FLIP && !TT.pageflip.dragging)
	{
		TT.pageflip.removeHardFlips();
	}

	var flip = TT.pageflip.getCurrentFlip();

	// If the current flip is consumed (automatically flipping) we can't use and need to define a new one.
	if (flip.consumed)
	{
		flip = TT.pageflip.createFlip();
	}

	// We are no longer dragging, but a flip just started turning the page.
	TT.pageflip.dragging = false;
	TT.pageflip.turning = true;
	TT.pageflip.hinting = false;

	// Store the properties specific to this flip.
	flip.currentPage = currentPage;
	flip.targetPage = targetPage;
	flip.direction = direction;

	flip.alpha = 1;
	flip.consumed = true;

	flip.type = type || TT.pageflip.SOFT_FLIP;

	// Set the target fold to be a full page turn.
	flip.target = -1;

	if (direction === -1)
	{
		flip.target = 1;
		flip.progress = -1;
	}

	TT.pageflip.activate();

	TT.pageflip.redraw();
};


/**
 * 强制任何翻页立即完成@?
 * Forces any ongoing flip to immediately complete.
 */
TT.pageflip.completeCurrentTurn = function()
{
	if (TT.pageflip.turning)
	{
		// Flag that we are no longer turning.
		TT.pageflip.turning = false;

		var flip = TT.pageflip.flips[TT.pageflip.flips.length - 1];

		if (flip)
		{
			// Inform the navigation class of the page flip completion.
			TT.navigation.updateCurrentPointer(flip.currentPage, flip.targetPage);
		}
	}
};


/**
 * 获取当前翻页对象
 * Retrieves the current Flip instance, if there are no instances of Flip, a new one is created.
 *
 * @return {Flip} A flip definition object.
 */
TT.pageflip.getCurrentFlip = function()
{
	if (TT.pageflip.flips.length == 0) {
		// There were no flips, so we a create one
		TT.pageflip.createFlip();
	}

	return TT.pageflip.flips[TT.pageflip.flips.length - 1];
};


/**
 * Create flip.
 * 
 * @return {Flip} a new flip definition object.
 */
TT.pageflip.createFlip = function()
{
	// Remove flips if there are too many going on concurrently.
	if (TT.pageflip.flips.length > 3)
	{
		TT.pageflip.flips = TT.pageflip.flips.splice(4, 99);// 删除4以后的99个动画
	}
	var flip = new TT.pageflip.Flip();// 构造一个翻页动画数据结构
	TT.pageflip.flips.push(flip);// 构造的flip入栈

	return flip;
};


/**
 * 计算提示区域
 * Calculates and returns the region in which hinting should be triggered. 
 * The regions differs between normal soft pages and the hard cover.
 * 
 * @return {Region} A region definition of the area in which hinting should be triggered when the mouse is moved.
 */
TT.pageflip.getHintRegion = function()
{
	var region = new Region();

	if (TT.navigation.isHomePage() || TT.navigation.isLastPage() ||	TT.navigation.isCreditsPage())
	{
		region.left = TT.BOOK_WIDTH_CLOSED - TT.pageflip.HINT_WIDTH;
		region.right = TT.BOOK_WIDTH_CLOSED;
	}
	else {
		region.left = TT.PAGE_WIDTH - TT.pageflip.HINT_WIDTH;
		region.right = TT.PAGE_WIDTH;
	}

	region.top = 0;
	region.bottom = TT.PAGE_HEIGHT;
//	TT.log('getHintRegion: top '+region.top+';bottom '+region.bottom+';left '+region.left+';right '+region.right);
	
	return region;
};

/**
 * 是否在提示区域
 * Is mouse in hint region?
 * 
 * @return {boolean} Whether mouse is in hint region.
 */
TT.pageflip.isMouseInHintRegion = function()
{
	var result = TT.pageflip.getHintRegion().contains(TT.pageflip.mouse.x, TT.pageflip.mouse.y);
//	TT.log('isMouseInHintRegion:y='+TT.pageflip.mouse.y+' x='+TT.pageflip.mouse.x);
	return result;
};

/**
 * 计算前翻页点击区域
 */
TT.pageflip.getPrevHintRegion = function()
{
	var region = new Region();

	region.left = -800;
	region.right = -40;
	region.top = 0;
	region.bottom = TT.PAGE_HEIGHT;
//	TT.log('getPrevHintRegion: top '+region.top+';bottom '+region.bottom+';left '+region.left+';right '+region.right);
	
	return region;
}


/**
 * 是否在向前翻页提示区域
 * Is mouse in hint region?
 * 
 * @return {boolean} Whether mouse is in hint region.
 */
TT.pageflip.isMouseInPrevHintRegion = function()
{
	var result = false;
	// 如果拖拽右页到左页区域，则忽略之
	if (!TT.navigation.isHomePage() && !TT.pageflip.dragging)
	{
		result = TT.pageflip.getPrevHintRegion().contains(TT.pageflip.mouse.x, TT.pageflip.mouse.y);
	}
//	TT.log('isMouseInPrevHintRegion:y='+TT.pageflip.mouse.y+' x='+TT.pageflip.mouse.x);
	return result;
};


/**
 * On key press.
 * @param {Object} event Event object.
 * @return {boolean} Return false.
 */
TT.pageflip.onKeyPress = function(event)
{
	// 键盘按下清零沉默开始时间
	TT.TIME_SILENCE_START = 0;
	
    // Check if we have passed the minimum time limit on opposing direction keyboard navigations.
    var hasPassedLockdown = TT.time() - TT.pageflip.lastKeyboardNavigationTime > 1000;

    if (event.keyCode == 37 && (TT.pageflip.lastKeyboardNavigationDirection === -1 || hasPassedLockdown))
    {
    	TT.navigation.goToPreviousPage();
    	TT.pageflip.lastKeyboardNavigationDirection = -1;
    	TT.pageflip.lastKeyboardNavigationTime = TT.time();
    	event.preventDefault();
    	return false;
    }
    else if (event.keyCode == 39 && (TT.pageflip.lastKeyboardNavigationDirection === 1 || hasPassedLockdown))
    {
    	TT.navigation.goToNextPage();
    	TT.pageflip.lastKeyboardNavigationDirection = 1;
    	TT.pageflip.lastKeyboardNavigationTime = TT.time();
    	event.preventDefault();
    	return false;
    }
    else if (event.keyCode == 27)
    {
    	event.preventDefault();
    	return false;
    }
};


/**
 * 鼠标按下事件
 * Handle pointer down event.
 */
TT.pageflip.handlePointerDown = function()
{
//	TT.log('handlePointerDown');
	
	// Check if the mouse is within the hit area. If it is then we initiate dragging.
	// 当不在硬翻页时才声效，fixed:点击封面翻页时，当翻页未完成，再次点击翻页区域，会出现bug，造成TT.pageflip.renderHardFlip死循环
	if (TT.pageflip.isMouseInHintRegion() && !TT.navigation.transitioningFromHardCover)
	{
		$('body').css('cursor', 'pointer');

		if (TT.time() - TT.pageflip.mouseDownTime > TT.pageflip.CLICK_FREQUENCY)
		{
			TT.pageflip.dragging = true;
		}

		// Store the mouse down time.
		TT.pageflip.mouseDownTime = TT.time();

		TT.pageflip.activate();
	}
};


/**
 * 鼠标移动事件
 * Handle pointer move event.
 */
TT.pageflip.handlePointerMove = function()
{
	var hinting = TT.pageflip.hinting;

	// Assume that we are not hinting and try to prove otherwise.
	TT.pageflip.hinting = false;

	$('body').css('cursor', '');
	
	// If we are not dragging or running the turn animation, check for and update the hinting.
	if (!TT.pageflip.dragging && !TT.pageflip.turning && (!TT.navigation.isCreditsPage() || (TT.navigation.isCreditsPage() && TT.navigation.isBookOpen())))
	{
//		TT.log('handlePointerMove:1');
		// Fetch a reference to the current flip.
		var flip = TT.pageflip.getCurrentFlip();

		// If this flip is below zero progress it's in use, so we create a new flip.
		if (flip.progress < 0)
		{
			flip = TT.pageflip.createFlip();
		}

		var isHardCover = (TT.navigation.isHomePage() || TT.navigation.isLastPage() || (TT.navigation.isCreditsPage() && TT.navigation.isBookOpen()));

		// 设置正确的翻页类型
		flip.type = isHardCover ? TT.pageflip.HARD_FLIP : TT.pageflip.SOFT_FLIP;

		// Is the cursor within the hint area?
		if (TT.pageflip.isMouseInHintRegion())// 在翻页提示区域内
		{
			if (TT.pageflip.mouseHistory[4])
			{
				var distanceX = TT.pageflip.mouse.x - TT.pageflip.mouseHistory[4].x;//@?
				var distanceY = TT.pageflip.mouse.y - TT.pageflip.mouseHistory[4].y;
				var distanceTravelled = Math.sqrt(distanceX * distanceX + distanceY * distanceY);// 平方根
			}
			else
			{
				var distanceTravelled = 0;
			}

			if (!TT.navigation.isHomePage() || distanceTravelled < 100)
			{
				flip.target = Math.min(TT.pageflip.mouse.x / TT.PAGE_WIDTH, 0.98);

				$('body').css('cursor', 'pointer');// 设置手型指针
				
				TT.pageflip.activate();// 卷页效果动画
				TT.pageflip.hinting = true;

				if (TT.navigation.isHomePage())
				{
					flip.target = Math.min(TT.pageflip.mouse.x / TT.PAGE_WIDTH, 0.95);

					// We are on the home page and hinting, make sure the current page is
					// visible.
					$('#pages section.current').show().width(TT.PAGE_WIDTH);
				}else 
				{
					// We not on the home page, make sure the page below the current is shown.
					$('#pages section.current').next('section').show().width(TT.PAGE_WIDTH);
				}
			}

		}
		else if (TT.pageflip.isMouseInPrevHintRegion() && !TT.navigation.isHomePage())// 向前翻页区域
		{
			$('body').css('cursor', 'pointer');// 设置手型指针
		}
		else if (flip.progress !== 1 && flip.target !== -1)
		{
			if (TT.pageflip.hinting == true)
			{
				$('#pages section.current').next('section').width(0);
			}

			// Reset the page to its resting state.
			flip.target = 1.0;
			TT.pageflip.activate();
			TT.pageflip.hinting = false;
		}
	}
	else if (TT.pageflip.dragging)// If we are draggin the home page beyond a certain point, take over an flip automatically.
	{
//		TT.log('handlePointerMove:2');
		if (TT.pageflip.getCurrentFlip().type != TT.pageflip.HARD_FLIP)
		{
			TT.pageflip.getCurrentFlip().alpha = 1;
		}
	}
	else if (TT.navigation.isCreditsPage() && TT.pageflip.isMouseInPrevHintRegion())// 向前翻页区域
	{
		$('body').css('cursor', 'pointer');// 设置手型指针
	}

	// Remove trailing mouse history.
	while (TT.pageflip.mouseHistory.length > 9)
	{
		TT.pageflip.mouseHistory.pop();
	}

	// Push current mouse position to history.
	TT.pageflip.mouseHistory.unshift(TT.pageflip.mouse);
};


/**
 * 鼠标释放事件
 * Handle pointer up event.
 * 
 * @return {boolean} Return false.
 */
TT.pageflip.handlePointerUp = function()
{
	// If the time between press down and release is below the frequency, flip to the next page.
	if (TT.time() - TT.pageflip.mouseDownTime < TT.pageflip.CLICK_FREQUENCY)
	{
		TT.navigation.goToNextPage();
		TT.pageflip.dragging = false;
		return false;
	}
	
	// 翻到前页
	if (TT.pageflip.isMouseInPrevHintRegion())
	{
		TT.navigation.goToPreviousPage();
		return false;
	}

	// Was the cursor being held down and is the mouse further to the left than the drop-treshold?
	if (TT.pageflip.dragging && TT.pageflip.mouse.x < TT.PAGE_WIDTH * 0.45)
	{
		// Try to go to the next page.
		var succeeded = TT.navigation.goToNextPage();

		// If there is no next page, drop the page.
		if (succeeded == false)
		{
			TT.pageflip.dragging = false;
		}
	}
	else 
	{
		TT.pageflip.dragging = false;

		TT.pageflip.handlePointerMove();
	}
};


/**
 * Event handler for document.onmousedown.
 * @param {Object} event Event object.
 * @return {boolean} Return false.
 */
TT.pageflip.onMouseDown = function(event) {

  // Flag that the mouse is down.
  TT.pageflip.mouse.down = true;

  // Update the mouse position.
  TT.pageflip.updateRelativeMousePosition(event.clientX, event.clientY);

  TT.pageflip.handlePointerDown();

  if (TT.pageflip.isMouseInHintRegion()) {
    event.preventDefault();
    return false;
  }
};


/**
 * 鼠标移动事件
 * Event handler for document.onmousemove.
 * 
 * @param {Object} event Event object.
 */
TT.pageflip.onMouseMove = function(event)
{
	// Update the mouse position.
	TT.pageflip.updateRelativeMousePosition(event.clientX, event.clientY);

	TT.pageflip.handlePointerMove();
};


/**
 * Event handler for document.onmouseup.
 * @param {Object} event Event object.
 */
TT.pageflip.onMouseUp = function(event) {

  // Flag that the mouse isn't down anymore.
  TT.pageflip.mouse.down = false;

  // Update the mouse position.
  TT.pageflip.updateRelativeMousePosition(event.clientX, event.clientY);

  TT.pageflip.handlePointerUp();
};


/**
 * Event handler for document.ontouchstart.
 * @param {Object} event Event object.
 */
TT.pageflip.onTouchStart = function(event) {
  if (event.touches.length == 1) {
    var globalX = event.touches[0].pageX -
        (window.innerWidth - TT.PAGE_WIDTH) * 0.5;
    var globalY = event.touches[0].pageY -
        (window.innerHeight - TT.PAGE_HEIGHT) * 0.5;

    // Update the mouse position.
    TT.pageflip.updateRelativeMousePosition(globalX, globalY);

    // Flag that the mouse is down.
    TT.pageflip.mouse.down = true;

    // Is the cursor within the hint area?
    if (TT.pageflip.isMouseInHintRegion()) {
      event.preventDefault();

      TT.pageflip.handlePointerDown();
    }
  }
};


/**
 * Event handler for document.ontouchmove.
 * @param {Object} event Event object.
 */
TT.pageflip.onTouchMove = function(event) {
  if (event.touches.length == 1) {
    var globalX = event.touches[0].pageX -
        (window.innerWidth - TT.PAGE_WIDTH) * 0.5;
    var globalY = event.touches[0].pageY -
        (window.innerHeight - TT.PAGE_HEIGHT) * 0.5;

    // Update the mouse position.
    TT.pageflip.updateRelativeMousePosition(globalX, globalY);

    // Is the cursor within the hint area?
    if (TT.pageflip.isMouseInHintRegion()) {
      event.preventDefault();

      TT.pageflip.handlePointerMove();
    }
  }
};


/**
 * Event handler for document.ontouchend.
 * @param {Object} event Event object.
 */
TT.pageflip.onTouchEnd = function(event) {

  // Flag that the mouse isn't down anymore.
  TT.pageflip.mouse.down = false;

  TT.pageflip.handlePointerUp();
};


/**
 * 获取鼠标相对右侧页面左上角的位置坐标
 * Gets the mouse position (x,y) in a coordinate space where 0,0 is the top left corner of the right side page.
 * 
 * @param {number} globalX The x position of the mouse in the window.
 * @param {number} globalY The y position of the mouse in the window.
 * 
 * @return {Object} containing the relative mouse position.
 */
TT.pageflip.getRelativeMousePosition = function(globalX, globalY)
{
	// Grab the mouse position from the event.
	var point = { x: globalX, y: globalY };

	// Offset the mouse position so that 0,0 is the top left of the left side page.
	point.x -= $('#pages').offset().left + TT.PAGE_WIDTH;
	point.y -= $('#pages').offset().top;
//	TT.log('globalX:'+globalX+',globalY:'+globalY);
//	TT.log('pointX:'+point.x+',pointY:'+point.y);
	return point;
};


/**
 * 更新鼠标相对位置信息
 * A shorthand for transforming a global mouse position to be relative to the
 * book AND update the pageflip class mouse state to reflect this.
 * 
 * @param {number} globalX The x position of the mouse in the window.
 * @param {number} globalY The y position of the mouse in the window.
 */
TT.pageflip.updateRelativeMousePosition = function(globalX, globalY)
{
	var point = TT.pageflip.getRelativeMousePosition(globalX, globalY);

	TT.pageflip.mouse.x = point.x;
	TT.pageflip.mouse.y = point.y;
};


/**
 * 构造一个翻转动画
 * The flip class is used to describe one flip animation.
 * 
 * @this {Object} Flip class.
 */
TT.pageflip.Flip = function()
{
	this.id = Math.round(Math.random() * 1000);// 生成随机整型id
	this.currentPage = $('#pages section.current');
	this.targetPage = $('#pages section.current');
	this.direction = -1;
	this.progress = 1;// 拖拽页面距离进度
	this.target = 1;
	this.strength = 0;
	this.alpha = 1;
	this.type = TT.pageflip.SOFT_FLIP;
	this.x = 0;
	this.consumed = false;//正常用户区域(非页面边缘)
};


/**
 * 定义一个矩形区域。用于重绘。
 * Defines a rectangular region. Typically used to manage redraw regions.
 * 
 * @this {Object} Flip class.
 */
function Region()
{
  this.left = 999999;
  this.top = 999999;
  this.right = 0;
  this.bottom = 0;
}


/**
 * Region reset.
 */
Region.prototype.reset = function()
{
	this.left = 999999;
	this.top = 999999;
	this.right = 0;
	this.bottom = 0;
};


/**
 * Region iflate.
 * @param {number} x Position.
 * @param {number} y Position.
 */
Region.prototype.inflate = function(x, y) {
  this.left = Math.min(this.left, x);
  this.top = Math.min(this.top, y);
  this.right = Math.max(this.right, x);
  this.bottom = Math.max(this.bottom, y);
};


/**
 * 判断坐标是否在区域内
 * Region contains.
 * 
 * @param {number} x Position.
 * @param {number} y Position.
 * 
 * @return {boolean} Whether it contains point.
 */
Region.prototype.contains = function(x, y)
{
//	TT.log('top:'+this.top+' bottom:'+this.bottom+' left:'+this.left+' right:'+this.right);
	return x > this.left && x < this.right && y > this.top && y < this.bottom;
};


/**
 * 区域加入空白 @?
 * Region toRectangle.
 * 
 * @param {number} padding Padding.
 * @return {Object} Calcs.
 */
Region.prototype.toRectangle = function(padding)
{
	padding |= 0;

	return {
		x: this.left - padding,
		y: this.top - padding,
		width: this.right - this.left + (padding * 2),
		height: this.bottom - this.top + (padding * 2)
	};
};