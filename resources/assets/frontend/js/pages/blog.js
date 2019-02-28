$(document).ready(function () {
  $('.twitter-container').each(function (i, elm) {
    window.tweetLoader.load(function (err, twttr) {
      if (err) {
        console.error(err)
        return
      }
      twttr.widgets.createTweet(
        elm.id.substring(6),
        elm,
        {
          omit_script: true,
          align: 'center',
          dnt: true,
          hide_thread: true
        }
      )
    })
  })
})
