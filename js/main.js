$(function() {
   $('#menu-button, #close-menu').click(function() {
      $('#menu-button, #close-menu').toggle();
      $('#menu').slideToggle();
   });

   // Masonry.js
   var $grid = $('.grid').imagesLoaded().progress( function() {
      // init Masonry after all images have loaded
      $grid.masonry({
         // options...
         itemSelector: '.grid-item',
         fitWidth: true
      });
   });

   $('.grid').imagesLoaded(function() {
      $('.masonry-image').each(function() {
         var height = $(this).height();
         // match overlay height with image height
         $(this).next().height(height);
      });
   });
});

$(window).on('load', function() {
   function adjustHeights() {
      var contentHeight = $('#footer').outerHeight() + $('#main').outerHeight();
      var fillerHeight = $('#header').outerHeight() > contentHeight ? $('#header').outerHeight() - contentHeight : 0;
      $('#filler').height(fillerHeight);
      $('#fake-header').height(contentHeight + fillerHeight);
   }

   adjustHeights();

   $(window).resize(adjustHeights);
});
