$(function() {
   // prettyPhoto
   $("a[rel^='prettyPhoto']").prettyPhoto({
      social_tools:''
   });
});

$(window).load(function(){
   var $container = $('.portfolioContainer');//THIS IS THE NAME OF THE CLASS FOR THE CONTAINER THAT WILL HOLD THE PORTFOLIO IMAGES
   $container.isotope({
      filter: '*',
      animationOptions: {
         duration: 750,      //TIMING IN MS
         easing: 'linear',   //EASING
         queue: false
      }
   });

   $('.portfolioFilter a').click(function() {
      $('.portfolioFilter .current').removeClass('current');
      $(this).addClass('current');

      var selector = $(this).attr('data-filter');
      $container.isotope({
         filter: selector,
         animationOptions: {
               duration: 750,     //TIMING IN MS
               easing: 'linear',  //EASING
               queue: false
         }
      });
      return false;
   });
});
