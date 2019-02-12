<script>
  import Pages from 'front_path/pages'
  import Vue from 'vue'

  let pageSelector = document.head.querySelector('meta[name="page-id"]')
  let token = (pageSelector) ? pageSelector.content : null

  let getComponentsToLoadFromList = function getComponentsToLoadFromList (token, pages) {
    if (pages.hasOwnProperty(token)) {
      return pages[token].components
    }
    return []
  }
  const frontendContext = require.context('./', false, /.*\.vue$/)
  const backendContext = require.context('back_path/components', false, /[^App\.vue].*\.vue$/)

  const frontend = frontendContext.keys()
    .map(file =>
      [file.replace(/(^.\/)|(\.vue$)/g, ''), frontendContext(file)]
    )
    .reduce((components, [name, component]) => {
      components[name] = component
      return components
    }, {})
  const backend = backendContext.keys()
    .map(file =>
      [file.replace(/(^.\/)|(\.vue$)/g, ''), backendContext(file)]
    )
    .reduce((components, [name, component]) => {
      components[name] = component
      return components
    }, {})

  let ComponentsToLoadList = getComponentsToLoadFromList(token, Pages)

  if (ComponentsToLoadList.hasOwnProperty('frontend')) {
    ComponentsToLoadList.frontend.forEach(componentName => {
      Vue.component(componentName, frontend[componentName])
    })
  }
  if (ComponentsToLoadList.hasOwnProperty('backend')) {
    ComponentsToLoadList.backend.forEach(componentName => {
      Vue.component(componentName, backend[componentName])
    })
  }

  async function pageLoader (token, pages) {
    if (pages.hasOwnProperty(token)) {
      if (pages[token].hasOwnProperty(page)) {
        await import(/* webpackChunkName: "p-" */ `front_path/${pages[token].page}`)
      }
    }
  }  (token, Pages)


  export default {}
</script>