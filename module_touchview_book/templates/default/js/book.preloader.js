/**
 * book.preloader
 * 
 * @version $Id: book.preloader.js 109 2012-03-29 12:17:32Z liqt $ 
 */

/**
 * @fileoverview Handles preloading of the most crucial and heavy weight files
 * in the app. Also displays a progress indication to the user. While this
 * manager handles loading of some assets, you should be aware that the content
 * (read: everything inside of the book) is loaded in the TT.storage manager.
 * The preloader (i.e. this manager) will wait for the content to be loaded
 * before continuing with application startup.
 */

/**
 * Sub-namespace.
 * @type {Object}
 */
TT.preloader = {};


/**
 * Flags if the assets, such as images, have been loaded.
 */
TT.preloader.assetsComplete = false;


/**
 * Flags if the contents, text & markup, has been downloaded from the server.
 */
TT.preloader.contentsComplete = false;


/**
 * Flags if the preloader has finished all its loading operations and started up
 * the app.
 */
TT.preloader.finished = false;


/**
 * Used to track progress of assets loading.
 */
TT.preloader.assetsLoaded = 0;


/**
 * Used to track progress of assets loading.
 */
TT.preloader.assetsToLoad = 0;


/**
 * 预加载初始化
 * Initializes the preloader by loading in, and tracking the progress of, downloading assets.
 */
TT.preloader.initialize = function()
{
	// Activate the book flipping animation in the preloader.
	TT.preloader.animation.initialize();
	TT.preloader.animation.activate();

	// Define how many asset we will wait for load callbacks from.
	TT.preloader.assetsToLoad = 7;

	// Define the images that we will be loading.
	var frontImage = new Image();
	var backImage = new Image();
	var rightImage = new Image();
	var leftImage = new Image();
	var paperImage = new Image();
	var leftFlippedImage = new Image();
	var backImageFlipped = new Image();

	// Add the images preload assets to the queue.
	TT.preloader.addAssetToPreloadQueue($(frontImage));
	TT.preloader.addAssetToPreloadQueue($(backImage));
	TT.preloader.addAssetToPreloadQueue($(rightImage));
	TT.preloader.addAssetToPreloadQueue($(leftImage));
	TT.preloader.addAssetToPreloadQueue($(paperImage));
	TT.preloader.addAssetToPreloadQueue($(leftFlippedImage));
	TT.preloader.addAssetToPreloadQueue($(backImageFlipped));

	// Fade in the preloader animation and progress bar.
	$('#preloader .contents').delay(50).animate({ opacity: 1 }, 300);

	// Now that the images are registered in the queue, start loading them.
	frontImage.src = TV.TPL_IMG_FRONT_COVER;
	backImage.src = TV.TPL_IMG_BACK_COVER;
	rightImage.src = TV.TPL_IMG_RIGHT_PAGE;
	leftImage.src = TV.TPL_IMG_LEFT_PAGE;
	paperImage.src = TV.TPL_IMG_RIGHT_PAGE_PAPER;
	leftFlippedImage.src = TV.TPL_IMG_LEFT_PAGE_FLIP;
	backImageFlipped.src = TV.TPL_IMG_BACK_FLIP;
};


/**
 * 更新进度条
 * Updates the visual progress meter to match the current status of download progress.
 */
TT.preloader.updateMeter = function()
{
	// The total number of segments to split the progress into.
	var segmentsTotal = TT.preloader.assetsToLoad;

	// Number of completed loading segments.
	var segmentsComplete = TT.preloader.assetsLoaded;

	if (TT.preloader.contentsComplete)
	{
		segmentsComplete++;
	}

	var progress = Math.min(segmentsComplete / segmentsTotal, 1);
	var progressWidth = progress * $('#preloader .progress').width();
	
	$('#preloader .progress .fill').width(progressWidth);
};


/**
 * Adds an asset to the preload queue if it is not already loaded.
 * 
 * @param {Object} asset A jQuery wrapped <img/> element to monitor load events on.
 */
TT.preloader.addAssetToPreloadQueue = function(asset)
{
	asset.load(TT.preloader.onAssetLoaded);
	asset.error(TT.preloader.onAssetLoaded);
};


/**
 * asset加载完成后事件
 * Event handler for which handles the load event as assets finish preloading.
 * 
 * @param {Object} event Event object.
 */
TT.preloader.onAssetLoaded = function(event)
{
	// Check if we just finished loading the last asset in the queue.
	if (++TT.preloader.assetsLoaded >= TT.preloader.assetsToLoad)
	{
		TT.preloader.onAllAssetsLoaded();
	}
	
	TT.log('Asset preloader: ' + $(event.target).attr('src') + ' [' + TT.preloader.assetsLoaded + '/' + TT.preloader.assetsToLoad + ']');

	TT.preloader.updateMeter();
};


/**
 * Wraps up the preload process, this will only happen once during the application lifespan.
 */
TT.preloader.onAllAssetsLoaded = function()
{
	if (!TT.preloader.assetsComplete && TT.preloader.assetsLoaded >= TT.preloader.assetsToLoad)
	{
		// Flag that all assets are loaded.
		TT.preloader.assetsComplete = true;
		TT.preloader.finish();
	}

	TT.preloader.updateMeter();
};


/**
 * Called when the contents (text & markup) has been downloaded from the server.
 */
TT.preloader.onContentsLoaded = function()
{
	if (!TT.preloader.contentsComplete)
	{
		// Flag that all contents is loaded.
		TT.preloader.contentsComplete = true;
		TT.preloader.finish();
	}

	TT.preloader.updateMeter();
};


/**
 * Attempts to finish the preloader progress, if all assets and contents is
 * loaded this will result in the preloader hiding and telling the main app to
 * proceed with startup.
 */
TT.preloader.finish = function()
{
	// Only proceed if both contents and assets are fully loaded. Furthermore,
	// make sure that the preloader has not already finished by checking the
	// TT.preloader.finished flag.
	if (TT.preloader.contentsComplete && TT.preloader.assetsComplete &&	!TT.preloader.finished)
	{
		// Fade out the preloader graphicand remove it from the DOM.
		$('#preloader').stop(true, true).fadeOut(200, function() {
			TT.preloader.animation.deactivate();
			$(this).remove();
		});

		// Fade in the book.
		$('#book').css({opacity: 0}).show().delay(200).fadeTo(700, 1);

		// Force an update in layout and move on to starting up the app.
		TT.updateLayout();
		TT.startup();

		TT.preloader.finished = true;
	}

	TT.preloader.updateMeter();
};

/**
 * This class handles the rendering of the book animation that is displayed
 * during the preload flow.
 */
TT.preloader.animation = {};


/**
 * 循环间隔值
 * Preloader loop interval.
 */
TT.preloader.animation.loopInterval = -1;


/**
 * Preloader width.
 */
TT.preloader.animation.WIDTH = 89;


/**
 * Preloader height.
 */
TT.preloader.animation.HEIGHT = 29;


/**
 * Preloader vspace.
 */
TT.preloader.animation.VSPACE = 20;


/**
 * Preloader canvas.
 */
TT.preloader.animation.canvas = null;


/**
 * Preloader context.
 */
TT.preloader.animation.context = null;


/**
 * Preloader flip.
 */
TT.preloader.animation.flip = {
  progress: 0,
  alpha: 0
};


/**
 * 预加载动画初始化
 * Prelaoder animation init.
 * 
 * @this {Object} Preloader animation class.
 */
TT.preloader.animation.initialize = function()
{
	this.canvas = $('#preloader .animation');
	
	if (this.canvas[0])
	{
		this.canvas[0].width = this.WIDTH;
		this.canvas[0].height = this.HEIGHT + (this.VSPACE * 2);
		this.context = this.canvas[0].getContext('2d');// 实例化context
	}
};


/**
 * 激活预加载动画
 * Preloader animation activate.
 */
TT.preloader.animation.activate = function()
{
	if (TT.preloader.animation.loopInterval == -1)
	{
		TT.preloader.animation.flip.progress = 1;
		TT.preloader.animation.loopInterval = setInterval(
			function()
			{
				if (TT.preloader.animation.canvas[0])
				{
					TT.preloader.animation.render();
				}
			} ,
			32
		);
	}
};


/**
 * Preloader animation deactivate.
 */
TT.preloader.animation.deactivate = function()
{
	clearInterval(TT.preloader.animation.loopInterval);
	TT.preloader.animation.loopInterval = -1;
};


/**
 * 预加载动画渲染
 * Preloader animation render.
 * 
 * @this {Object} Preloader animation class.
 */
TT.preloader.animation.render = function()
{
	this.context.clearRect(0, 0, this.WIDTH, this.HEIGHT + (this.VSPACE * 2));

	this.context.save();
	this.context.translate(0, this.VSPACE);

	this.context.fillStyle = '#f4f4f4';
	this.context.fillRect(0, 0, this.WIDTH, this.HEIGHT);

	this.context.fillStyle = '#999999';
	this.context.fillRect(0, 0, this.WIDTH, 1);
	this.context.fillRect(0, this.HEIGHT, this.WIDTH, 2);
	this.context.fillRect(0, 0, 1, this.HEIGHT);
	this.context.fillRect(this.WIDTH - 1, 0, 1, this.HEIGHT);
	this.context.fillRect(Math.floor(this.WIDTH * 0.5), 0, 1, this.HEIGHT);
	this.context.fillRect(54, 8, 25, 2);
	this.context.fillRect(54, 11, 25, 2);
	this.context.fillRect(54, 14, 25, 2);
	this.context.fillRect(54, 17, 25, 2);
	this.context.fillRect(54, 20, 25, 2);

	this.context.translate(0, 1);// 移动画布

	// Ease the progress towards the final (-1) position [ease-in-out].
	TT.preloader.animation.flip.progress -= Math.max(0.12 * (1 - Math.abs(TT.preloader.animation.flip.progress)), 0.02);

	// Fade in at the start and out at the end.
	TT.preloader.animation.flip.alpha = 1 - ((Math.abs(TT.preloader.animation.flip.progress) - 0.7) / 0.3);

	// Since the easing is a constant reduction in progress, we can use it as a
	// delay to determine when the progress (and flip) should be reset. Lower number means a longer delay.
	if (TT.preloader.animation.flip.progress <= -1.1)
	{
		TT.preloader.animation.flip.progress = 1;
	}

	var strength = 1 - Math.abs(TT.preloader.animation.flip.progress);
	var anchorOutdent = strength * 12;
	var controlOutdent = strength * 8;

	// The source position of the page flip (center of the book).
	var source = {
			top: { x: this.WIDTH * 0.5, y: 0 },
			bottom: { x: this.WIDTH * 0.5, y: this.HEIGHT}
	};

	// The destination position where the page is current reaching from the source.
	var destination = {
			top: { x: source.top.x +
				(this.WIDTH * TT.preloader.animation.flip.progress * 0.55), y: 0 -
				anchorOutdent },
				bottom: { x: source.bottom.x + (this.WIDTH *
						TT.preloader.animation.flip.progress * 0.55), y: this.HEIGHT -
						anchorOutdent }
	};

	// Control position used to bend the page.
	var control = {
		top: { x: source.top.x + (12 * TT.preloader.animation.flip.progress), y: -controlOutdent },
		bottom: { x: source.bottom.x + (12 * TT.preloader.animation.flip.progress),	y: this.HEIGHT - controlOutdent }
	};

	this.context.fillStyle = 'rgba(245,245,245,' + TT.preloader.animation.flip.alpha + ')';
	this.context.strokeStyle = 'rgba(90,90,90,' + TT.preloader.animation.flip.alpha + ')';

	this.context.beginPath();
	this.context.moveTo(source.top.x, source.top.y);
	this.context.quadraticCurveTo(control.top.x, control.top.y,	destination.top.x, destination.top.y);
	this.context.lineTo(destination.bottom.x, destination.bottom.y);
	this.context.quadraticCurveTo(control.bottom.x, control.bottom.y, source.bottom.x, source.bottom.y);

	this.context.fill();
	this.context.stroke();

	this.context.restore();
};