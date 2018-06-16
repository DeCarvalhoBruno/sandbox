$(function () {
  'use strict'

  $('[data-toggle="push-menu"]').pushMenu()

  var $pushMenu = $('[data-toggle="push-menu"]').data('lte.pushmenu')
  var $layout = $('body').data('lte.layout')

  function changeLayout (cls) {
    $('body').toggleClass(cls)
    if ($('body').hasClass('fixed') && cls == 'fixed') {
      $pushMenu.expandOnHover()
      $layout.activate()
    }
  }

  function setup () {
    $('[data-layout]').on('click', function () {
      changeLayout($(this).data('layout'))
    })

    $('[data-enable="expandOnHover"]').on('click', function () {
      $(this).attr('disabled', true)
      $pushMenu.expandOnHover()
      if (!$('body').hasClass('sidebar-collapse'))
        $('[data-layout="sidebar-collapse"]').click()
    })
  }
  setup()
})
