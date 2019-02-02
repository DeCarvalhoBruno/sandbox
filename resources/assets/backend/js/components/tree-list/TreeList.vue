<template>
    <div class="tree-list container">
        <div class="row mb-3">
            <div class="col-lg">
                <search :terms="searchTerms" @show="searchEvent"/>
            </div>
        </div>
        <div class="row p-0 m-0">
            <button v-if="editMode" type="button"
                    class="btn btn-primary"
                    @click="addRoot">{{addRootButtonLabel}}
            </button>
            <button type="button"
                    class="btn btn-primary"
                    @click="toggleExpand">{{!treeExpanded?$t('general.expand_all'):$t('general.collapse_all')}}
            </button>
        </div>
        <div class="row p-0 mt-3 ml-1 tree-container">
            <tree-list-item v-for="(node,idx) in treeData"
                            :key="idx"
                            :node="node"
                            @event="handleEvent"
                            @category-selected="categorySelected"
                            :edit-mode="editMode">
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
      data: {required: true},
      editMode: {default: true},
      addRootButtonLabel:{required: true}
    },
    data () {
      return {
        treeData: [],
        searchInput: null,
        searchTerms: [],
        buffer: [],
        treeExpanded: false
      }
    },
    watch: {
      data (incoming) {
        this.treeData = incoming
      },
      treeData () {
        this.searchTerms = []
        for (let i in this.treeData) {
          this.searchTerms = this.searchTerms.concat(this.buildSearchTerms(this.treeData[i]))
        }
      }
    },
    methods: {
      categorySelected (val, mode) {
        this.$emit('tree-category-selected', val, mode)
      },
      async toggleExpand () {
        this.treeExpanded = !this.treeExpanded
        for (let node of this.searchTerms) {
          node.data.method = this.treeExpanded ? 'open' : 'close'
          await this.handleEvent(node.nodeMap, node.data)
        }
      },
      buildSearchTerms (node) {
        let result = [{data: {target: node.label}, nodeMap: []}]
        for (let subNodes of node.children) {
          result = result.concat(this.buildSearchTerms(subNodes))
        }

        for (let term of result) {
          term.nodeMap = [node.label].concat(term.nodeMap)
          term.data.method = 'show'
        }
        return result
      },
      addRoot () {
        this.treeData.push({label: '', id: null, open: true, selected:false, mode: 6, children: []})
      },
      async handleEvent (nodeMap, payload) {
        await this.checkBuffer()
        let td = []
        for (let node of this.treeData) {
          let n = await this.handleThis(nodeMap, payload, node)
          if (n) {
            td.push(n)
          }
        }
        this.treeData = td
      },
      //Called in case we need to cancel out some action we did just before
      //like remove search highlighting
      async checkBuffer () {
        if (this.buffer.length > 0) {
          let tmp = this.buffer
          this.buffer = []
          await this.handleEvent(tmp[0], tmp[1])
        }
      },
      async searchEvent (nodeMap, payload) {
        await this.handleEvent(nodeMap, payload)

        //We keep the search params in a buffer
        //so we can undo the search highlight effect
        payload.method = 'cancel'
        this.buffer = [nodeMap, payload]
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
            case 'show':
              node.mode = 5
              break
            case 'open':
              node.open = true
              break
            case 'close':
              node.open = false
              break
            case 'toggleShow':
              node.open = !node.open
              break
            case 'add':
              node.open = true
              node.children.push({label: '', id: node.id, open: true, selected:false, mode: 6, children: []})
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
            case 'reset':
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