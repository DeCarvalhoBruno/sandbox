(function () {
  var header = $('header')
  if (!header.length)
    return
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
    if ($(this).scrollTop() > 200) {
      $('#scroll-up').fadeIn()
    } else {
      $('#scroll-up').fadeOut()
    }
  })
})(jQuery);
(function ($, viewport) {
  $(document).ready(function () {

    var noTouch = false
    if (!('ontouchstart' in document.documentElement)) {
      $('body').addClass('no-touch')
      noTouch = true
    }

    $('<h1 class="viewport" ' +
      'style="position:absolute;bottom:4rem;right:1rem;font-weight: bold;"></h1>')
      .appendTo('body')
    $('h1.viewport').html(viewport.current())

    if (viewport.is('>=sm')) {
      if (noTouch) {
        $('.dropdown-hover')
          .hover(
            function () {
              $(this).addClass('show')
            },
            function () {
              $(this).removeClass('show')
            }
          )
      }
    }
    $('#scroll-up').click(function(e) {
      e.preventDefault()
      $('html, body').animate({
        scrollTop: 0
      }, 1000)
    });

    $(window).resize(
      viewport.changed(function () {
        $('h1.viewport').html(viewport.current())
      })
    )
  })
})(jQuery, ResponsiveBootstrapToolkit);
(function () {
  if(window.hasOwnProperty('config')){
    if(window.config.hasOwnProperty('msg')){
      swal.fire({
        type:window.config.msg.type,
        title:window.config.msg.title,
        position: 'top-end',
        toast: true,
        showConfirmButton: false,
        timer: 4000
      })
    }
  }
})(jQuery);
