(function () {
  var header = $('header')
  var wrapper = $('div#wrapper')
  header.before(wrapper)
  var offsetTop = wrapper.offset().top
  var fixedTopClass = 'fixed-top'
  // var windowScrollToTop = $(window).scrollTop()
  $(window).scroll(function () {
    var outerHeight = header.outerHeight()
    var thisScrollTop = $(this).scrollTop()

    if (thisScrollTop <= offsetTop && (header.hasClass(fixedTopClass))) {
      header.removeClass(fixedTopClass)
      wrapper.height(0)
    }
    if (offsetTop + outerHeight + 20 <= thisScrollTop) {
      header.addClass(fixedTopClass)
      wrapper.height(outerHeight)
      // windowScrollToTop = thisScrollTop
    }
  }).scroll()
})(jQuery);
(function($, viewport){
  $(document).ready(function() {

    // Executes only in XS breakpoint
    if(viewport.is('xs')) {
      // ...
    }

    // Executes in SM, MD and LG breakpoints
    if(viewport.is('>=sm')) {
      // ...
    }

    // Executes in XS and SM breakpoints
    if(viewport.is('<md')) {
      // ...
    }

    // Execute code each time window size changes
    // $(window).resize(
    //   viewport.changed(function() {
    //   })
    // );
  });
})(jQuery, ResponsiveBootstrapToolkit);