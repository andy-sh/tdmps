/**
 * book.history
 * 
 * @version $Id: book.history.js 41 2012-02-15 07:54:45Z liqt $
 */

/**
 * @fileoverview Manages the application history for browser navigations
 * such as forward/backwards. Two different approaches are used for this:
 * 1) Modern browsers with support for HTML5's History API
 *    will use non-hash URL's such as www.example.com/chapter/page.
 * 2) Browser that do NOT support the History API will fall
 *    back to using hash URL's such as www.example.com/#/chapter/page.
 */

/**
 * Sub-namespace.
 * @type {Object}
 */
TT.history = {};

/**
 * Table of things URL stub.
 * @type {string}
 */
TT.history.TABLE_OF_CONTENTS = 'table-of-things';


/**
 * Home URL stub.
 * @type {string}
 */
TT.history.HOME = 'home';

/**
 * The end URL stub.
 * @type {string}
 */
TT.history.THEEND = 'theend';


/**
 * Credits URL stub.
 * @type {string}
 */
TT.history.CREDITS = 'credits';


/**
 * Previous URL stub.
 * @type {string}
 */
TT.history.previousHash = '';

/**
 * Determine history capabilities and initiate approrpriate monitoring.
 */
TT.history.initialize = function()
{

};


/**
 * Check if HTML5's History API is supported.
 * 
 * @return {boolean} Whether HTML5 history is supportd.
 */
TT.history.supportsHistoryPushState = function()
{
	return ('pushState' in window.history) && window.history['pushState'] !== null;
};

/**
 * Pushes a URL to the history stack, effectively causing that URL to become the current location.
 * @param {string} url The URL that should be pushed to the history stack.
 */
TT.history.pushState = function(url)
{
//	TT.log('TT.history.pushState:'+url);
	window.history.pushState('', '', url);
};
