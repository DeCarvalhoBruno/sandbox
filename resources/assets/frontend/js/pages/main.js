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

    //Detect touch based devices so we can make layout adjustments
    //especially when it has to do with hover/click behavior
    var noTouch = false
    if (!('ontouchstart' in document.documentElement)) {
      $('body').addClass('no-touch')
      noTouch = true
    }

    //@TODO: remove this from production code
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

    //Action for the "back to top" button
    $('#scroll-up').click(function (e) {
      e.preventDefault()
      $('html, body').animate({
        scrollTop: 0
      }, 1000)
    })

    //Action for the newsletter subscription form
    $('#form-newsletter-subscribe').on('submit', function (e) {
      e.preventDefault()
      var formData = new FormData(document.forms['form-newsletter-subscribe'])
      axios.post('/email/subscribe/newsletter', formData)
        .then(function (response) {
          swal.fire({
            title: response.data.title,
            text: response.data.text,
            showConfirmButton: true,
            showCancelButton: false
          })
        }).catch(function () {
        document.location = document.location.href
      })
    })

    //@TODO: remove this from production code
    $(window).resize(
      viewport.changed(function () {
        $('h1.viewport').html(viewport.current())
      })
    )
    $('#app').Lazy()

  })
})(jQuery, ResponsiveBootstrapToolkit);
(function () {
  if (window.hasOwnProperty('config')) {
    if (window.config.hasOwnProperty('msg')) {
      swal.fire({
        type: window.config.msg.type,
        title: window.config.msg.title,
        position: 'top-end',
        toast: true,
        showConfirmButton: false,
        timer: 8000
      })
    }
  }
})(jQuery)

var handleSingleClickSignOn = function (googleyolo) {
  // if (window.location.href.match(/localhost/)===null) {
  googleyolo.retrieve({
    supportedAuthMethods: [
      'https://accounts.google.com'
    ],
    supportedIdTokenProviders: [
      {
        uri: 'https://accounts.google.com',
        clientId: window.config.gapi_client
      }
    ],
    context: 'continue'
  }).catch(function (e) {
    googleyolo.hint({
      supportedAuthMethods: [
        'https://accounts.google.com'
      ],
      supportedIdTokenProviders: [
        {
          uri: 'https://accounts.google.com',
          clientId: window.config.gapi_client
        }
      ]
    }).then(function (credentials) {
      axios.post('/oauth-yolo', {
        domain: credentials.authDomain,
        full_name: credentials.displayName,
        email: credentials.id,
        avatar: credentials.profilePicture,
        google_token: credentials.idToken
      }).then(function (data) {

      })
    }).catch(function (error) {

    })
  })
}
if (window.hasOwnProperty('config')) {
  if (window.config.auth_check === false &&
    window.config.google_verified === false) {
    window.onGoogleYoloLoad = handleSingleClickSignOn
  }
}
