import ResponsiveBootstrapToolkit from '../plugins/jquery/bootstrap-toolkit'
import axios from 'axios'
import swal from 'sweetalert2/dist/sweetalert2.js'

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

    // var img = document.getElementsByTagName('img')
    // for (var i = 0; i < img.length; i++) {
    //   if (img[i].getAttribute('data-src')) {
    //     img[i].setAttribute('src', img[i].getAttribute('data-src'))
    //     img[i].removeAttribute('data-src')
    //   }
    // }

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
        google_token: credentials.idToken,
        avatar: credentials.profilePicture
      }).then(function (data) {
        window.location.href = window.location.pathname +
          window.location.search + window.location.hash
      })
    }).catch(function (error) {
      //@TODO: handle 422s
    })
  })
}
if (window.hasOwnProperty('config')) {
  if (window.config.auth_check === false &&
    window.config.google_verified === false) {
    window.onGoogleYoloLoad = handleSingleClickSignOn
  }
}

var lazy = []
registerListener('load', setLazy)
registerListener('load', lazyLoad)
registerListener('scroll', lazyLoad)
registerListener('resize', lazyLoad)

function setLazy () {
  lazy = document.getElementsByClassName('lazy')
}

function lazyLoad () {
  for (var i = 0; i < lazy.length; i++) {
    if (isInViewport(lazy[i])) {
      if (lazy[i].getAttribute('data-src')) {
        lazy[i].src = lazy[i].getAttribute('data-src')
        lazy[i].removeAttribute('data-src')
      }
    }
  }
  if (lazy.length === 0) {
    window.removeEventListener('scroll', lazyLoad)
  }
  cleanLazy()
}

function cleanLazy () {
  lazy = Array.prototype.filter.call(lazy,
    function (l) { return l.getAttribute('data-src')})
}

function isInViewport (el) {
  var rect = el.getBoundingClientRect()

  return (
    rect.bottom >= 0 &&
    rect.right >= 0 &&
    rect.top <= (window.innerHeight || document.documentElement.clientHeight) &&
    rect.left <= (window.innerWidth || document.documentElement.clientWidth)
  )
}

function registerListener (event, func) {
  if (window.addEventListener) {
    window.addEventListener(event, func)
  } else {
    window.attachEvent('on' + event, func)
  }
}
