/**
 * book.html5shiv
 * 
 * @version $Id: book.html5shiv.js 24 2012-01-19 02:26:50Z liqt $
 */

/**
 * @fileoverview Make required HTML5 elements work everywhere.
 */

/**
 * Self-executing function to 'shiv' the HTML5 elements.
 */
(function() {
  var elements = [
    'header',
    'nav',
    'footer',
    'section'
  ];

  while (elements.length) {
    document.createElement(elements.pop());
  }
})();