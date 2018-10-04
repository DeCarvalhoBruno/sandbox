import '~/plugins'
import Pages from '~/pages'

let f = async function pageLoader (token,pages) {
  if (pages.hasOwnProperty(token)) {
    // import(`~/pages/${token}`)
    await import(/* webpackChunkName: "p-" */ `~/${pages[token]}`)
  }
}

let pageSelector = document.head.querySelector('meta[name="p-token"]')
let token = (pageSelector) ? pageSelector.content : null
f(token,Pages)