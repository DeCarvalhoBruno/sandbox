<template>
    <div class="list-tree container">
        <div class="row mb-3">
            <search :terms="searchTerms" @show="handleEvent"/>
        </div>
        <div class="row">
            <button type="button" class="btn btn-primary" @click="addRoot">Add Root Category</button>
            <tree-list-item v-for="(node,idx) in treeData"
                            :key="idx"
                            :node="node"
                            @event="handleEvent">
            </tree-list-item>
        </div>
    </div>
</template>
<script>
  import TreeListItem from '~/components/tree-list/TreeListItem'
  import Search from '~/components/tree-list/Search'
  import axios from 'axios'

  export default {
    name: 'tree-list',
    components: {
      TreeListItem,
      Search
    },
    props: {
      data: {required: true}
    },
    data () {
      return {
        treeData: [],
        searchInput: null
      }
    },
    computed: {
      searchTerms () {
        // return a list of search terms for autocomplete
        let result = []
        for (let node of this.treeData) {
          result = result.concat(this.buildSearchTerms(node))
        }
        console.log(result)
        return result
      }
    },
    watch: {
      data (incoming) {
        this.treeData = incoming
      }
    },
    methods: {
      buildSearchTerms (node) {
        let result = [{label: node.label, path: []}]
        for (let subNodes of node.children) {
          result = result.concat(this.buildSearchTerms(subNodes))
        }
        // then add our name to the "path" of each child
        for (let searchTerm of result) {
          searchTerm.path = [node.label].concat(searchTerm.path)
        }
        return result
      },
      async addRoot () {
        this.treeData.push({label: '', id: null, open: true, mode: 6, children: []})
      },
      async handleEvent (nodeMap, data) {
        let td = []
        for (let node of this.treeData) {
          let n = await this.handleThis(nodeMap, data, node)
          if (n) {
            td.push(n)
          }
        }
        this.treeData = td
      },
      async handleThis (nodeMap, payload, node) {
        let vm = this
        let failed = false
        if (node.label !== nodeMap[0]) {
          node.mode = 1
          return node
        }

        if (payload.method === 'show') {
          node.open = true
        }

        //Modes: Default:1, Edit:2, Add:6
        if (node.label === payload.target) {
          switch (payload.method) {
            case 'toggleShow':
              node.open = !node.open
              break
            case 'add':
              node.open = true
              node.children.push({label: '', id: node.id, open: true, mode: 6, children: []})
              break
            case 'edit':
              node.mode = 2
              break
            case 'update':
              if (node.mode === 6) {
                await axios.post(
                  `/ajax/admin/blog/categories/create`,
                  {label: payload.newValue, id: node.id}
                ).then(({data}) => {
                  if (data.hasOwnProperty('id')) {
                    node.id = data.id
                  } else {
                    vm.$emit('has-errored',
                      `Creation of category "${payload.newValue}" failed. Please try again.`
                    )
                    failed = true
                  }
                })
              } else {
                await axios.post(
                  `/ajax/admin/blog/categories/update/${node.id}`,
                  {label: payload.newValue}
                )
              }
              node.label = payload.newValue
              node.mode = 1
              break
            case 'cancelCreation':
              //If the user creates a node a changes their mind
              return
            case 'delete':
              axios.post(
                `/ajax/admin/blog/categories/delete/${node.id}`
              )
              //Not returning the node will effectively remove it from the tree.
              return
            case 'cancel':
              node.mode = 1
              break
          }
        } else if (node.children.length) {
          let td = []
          for (let subNode of node.children) {
            let n = await this.handleThis(nodeMap.slice(1), payload, subNode)
            if (n) {
              td.push(n)
            }
          }
          node.children = td
        }
        if (failed) return
        return node
      }
    }
  }
</script>