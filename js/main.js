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

   // GAGAN'S CODE
});
