import Vue from 'vue'
import App from './components/App'
import 'front_path/plugins'
import i18n from 'front_path/plugins/i18n'
import { loadMessages } from './plugins/i18n'

(async function () {
  await loadMessages()
})().then(() => {
  new Vue({
    i18n,
    ...App,
    data () {
      return {
        offsetTop: null,
        fixedTopClass: 'fixed-top',
        headerClass: null
      }
    },
    mounted () {
      // Action for the "back to top" button
      $('#scroll-up').click(function (e) {
        e.preventDefault()
        $('html, body').animate({
          scrollTop: 0
        }, 500)
      })
      this.offsetTop = this.$refs.wrapper.clientHeight
      window.addEventListener('scroll', this.handleScroll)
    },
    methods: {
      handleScroll () {
        let outerHeight = this.$refs.mainHeader.clientHeight
        let thisScrollTop = window.scrollY

        if (thisScrollTop <= this.offsetTop && this.headerClass) {
          this.headerClass = null
        }
        if (this.offsetTop + outerHeight + 20 <= thisScrollTop) {
          this.headerClass = this.fixedTopClass
        }

        // Show/Hide "back to top" button
        if ($(window).scrollTop() > 200) {
          $('#scroll-up').fadeIn()
        } else {
          $('#scroll-up').fadeOut()
        }
      }
    }
  }).$mount('#app')
})

/*
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
 */
