<template>
    <div class="list-tree">
        <tree-list-item v-for="(node,idx) in treeData"
                        :key="idx"
                        :node="node"
                        @event="handleEvent">
        </tree-list-item>
    </div>
</template>
<script>
  import TreeListItem from '~/components/tree-list/TreeListItem'

  export default {
    name: 'tree-list',
    components: {
      TreeListItem
    },
    methods: {
      handleEvent (nodeMap, data) {
        let td = []
        for (let node of this.treeData) {
          let n = this.handleThis(nodeMap, data, node)
          if (n) {
            td.push(n)
          }
        }
        this.treeData = td
      },
      handleThis (nodeMap, data, node) {
        if (node.label !== nodeMap[0]) {
          return node
        }

        if (data.method === 'show') {
          node.open = true
        }

        /*
            Modes
                Neutral:1
                Edit:2
                Add:6
        */
        if (node.label === data.target) {
          switch (data.method) {
            case 'toggleShow':
              node.open = !node.open
              break
            case 'add':
              node.children.push({label: '', open: true, mode: 6, children: []})
              break
            case 'edit':
              node.mode = 2
              break
            case 'update':
              node.label = data.newValue
              node.mode = 1
              break
            case 'delete':
              //Not returning the node will effectively remove it from the tree.
              return
            case 'cancel':
              node.mode = 1
              break
          }
        } else if (node.children.length) {
          let td = []
          for (let subNode of node.children) {
            let n = this.handleThis(nodeMap.slice(1), data, subNode)
            if (n) {
              td.push(n)
            }
          }
          node.children = td
        }
        return node
      }
    },
    data () {
      return {
        /*
        Modes
            Neutral:1
            Edit:2
            Add:6
         */
        treeData: [
          {
            label: 'Vehicles',
            open: true,
            mode: 1,
            children: [
              {label: 'trucks', open: true, mode: 1, children: []},
              {label: 'cars', open: true, mode: 1, children: []},
              {
                label: 'compact',
                open: true,
                mode: 1,
                children: [
                  {
                    label: 'compact 1',
                    open: true,
                    mode: 1,
                    children: [
                      {label: 'compact 1.1', open: true, mode: 1, children: []},
                      {label: 'compact 1.2', open: true, mode: 1, children: []}
                    ]
                  },
                  {label: 'compact 2', open: true, mode: 1, children: []},
                  {label: 'compact 3', open: true, mode: 1, children: []},
                  {
                    label: 'compact 4',
                    open: true,
                    mode: 1,
                    children: [
                      {label: 'compact 4.1', open: true, mode: 1, children: []},
                      {label: 'compact 4.2', open: true, mode: 1, children: []}
                    ]
                  }
                ]
              }
            ]
          }
        ]
      }
    }
  }
</script>