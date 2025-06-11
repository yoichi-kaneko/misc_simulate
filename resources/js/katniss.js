/*!
 * Katniss v2.0.0 (https://themepixels.me/starlight)
 * Copyright 2017-2018 ThemePixels
 * Licensed under ThemeForest License
 */

'use strict';

$(document).ready(function() {

  // hiding all sub nav in left sidebar by default.
  $('.nav-sub').slideUp();

  // showing sub navigation to nav with sub nav.
  $('.with-sub.active + .nav-sub').slideDown();

  // showing sub menu while hiding others
  $('.with-sub').on('click', function(e) {
    e.preventDefault();

    var nextElem = $(this).next();
    if(!nextElem.is(':visible')) {
      $('.nav-sub').slideUp();
    }
    nextElem.slideToggle();
  });

  // showing and hiding left sidebar
  $('#naviconMenu').on('click', function(e) {
    e.preventDefault();
    $('body').toggleClass('hide-left');
  });

  // pushing to/back left sidebar
  $('#naviconMenuMobile').on('click', function(e) {
    e.preventDefault();
    $('body').toggleClass('show-left');
  });

  // highlight syntax highlighter
  $('pre code').each(function(i, block) {
    hljs.highlightBlock(block);
  });

});
