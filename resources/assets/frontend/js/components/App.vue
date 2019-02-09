<script>
  import Pages from '~/pages'
  import Vue from 'vue'

  let pageSelector = document.head.querySelector('meta[name="page-id"]')
  let token = (pageSelector) ? pageSelector.content : null

  async function pageLoader (token, pages) {
    if (pages.hasOwnProperty(token)) {
      // import(`~/pages/${token}`)
      await import(/* webpackChunkName: "p-" */ `~/${pages[token].page}`)
    }
  }(token, Pages)

  let getComponentsToLoadFromList = function getComponentsToLoadFromList (token, pages) {
    if (pages.hasOwnProperty(token)) {
      return pages[token].components
    }
    return []
  }

  const requireContext = require.context('./', false, /.*\.vue$/)

  const componentList = requireContext.keys()
    .map(file =>
      [file.replace(/(^.\/)|(\.vue$)/g, ''), requireContext(file)]
    )
    .reduce((components, [name, component]) => {
      components[name] = component
      return components
    }, {})

  let ComponentsList = getComponentsToLoadFromList(token, Pages)

  ComponentsList.forEach(componentName => {
    Vue.component(componentName, componentList[componentName])
  })

  export default {
    el: '#app'
  }
</script>