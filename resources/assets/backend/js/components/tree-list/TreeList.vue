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
  import axios from 'axios'

  export default {
    name: 'tree-list',
    components: {
      TreeListItem
    },
    props: {
      data: {required: true}
    },
    watch: {
      data (incoming) {
        this.treeData = incoming
      }
    },
    methods: {
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
      async handleThis (nodeMap, data, node) {
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
              node.open = true
              node.children.push({label: '', open: true, mode: 6, children: []})
              break
            case 'edit':
              node.mode = 2
              break
            case 'update':
              if (mode.mode === 6) {

              } else {
                await axios.post(
                  `/ajax/admin/blog/categories/update/${node.id}`,
                  {label: node.label}
                )
              }
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
            let n = await this.handleThis(nodeMap.slice(1), data, subNode)
            if (n) {
              td.push(n)
            }
          }
          node.children = td
        }
        return node
      },
      async test (node) {

      }
    },
    data () {
      return {
        treeData: Array
        /*
        Modes
            Neutral:1
            Edit:2
            Add:6
         */
        // treeData: [
        //   {
        //     label: 'Vehicles',
        //     open: true,
        //     mode: 1,
        //     children: [
        //       {label: 'trucks', open: true, mode: 1, children: []},
        //       {label: 'cars', open: true, mode: 1, children: []},
        //       {
        //         label: 'compact',
        //         open: true,
        //         mode: 1,
        //         children: [
        //           {
        //             label: 'compact 1',
        //             open: true,
        //             mode: 1,
        //             children: [
        //               {label: 'compact 1.1', open: true, mode: 1, children: []},
        //               {label: 'compact 1.2', open: true, mode: 1, children: []}
        //             ]
        //           },
        //           {label: 'compact 2', open: true, mode: 1, children: []},
        //           {label: 'compact 3', open: true, mode: 1, children: []},
        //           {
        //             label: 'compact 4',
        //             open: true,
        //             mode: 1,
        //             children: [
        //               {label: 'compact 4.1', open: true, mode: 1, children: []},
        //               {label: 'compact 4.2', open: true, mode: 1, children: []}
        //             ]
        //           }
        //         ]
        //       }
        //     ]
        //   }
        // ],
        // treeData: [
        //   {
        //     'label': 'Europe',
        //     'open': true,
        //     'mode': 1,
        //     'children': [
        //       {
        //         'label': 'France',
        //         'open': true,
        //         'mode': 1,
        //         'children': [
        //           {
        //             'label': 'Ile de France',
        //             'open': true,
        //             'mode': 1,
        //             'children': [
        //               {'label': 'Paris', 'open': true, 'mode': 1, 'children': []},
        //               {'label': 'Montreuil', 'open': true, 'mode': 1, 'children': []},
        //               {'label': 'Boulogne-Billancourt', 'open': true, 'mode': 1, 'children': []}]
        //           },
        //           {
        //             'label': 'Centre',
        //             'open': true,
        //             'mode': 1,
        //             'children': [
        //               {'label': 'Orleans', 'open': true, 'mode': 1, 'children': []},
        //               {'label': 'Chateauneuf-Sur-Loire', 'open': true, 'mode': 1, 'children': []},
        //               {'label': 'Sully-Sur-Loire', 'open': true, 'mode': 1, 'children': []}]
        //           }]
        //       },
        //       {
        //         'label': 'Germany',
        //         'open': true,
        //         'mode': 1,
        //         'children': [
        //           {
        //             'label': 'Baden-Wurtenberg',
        //             'open': true,
        //             'mode': 1,
        //             'children': [{'label': 'Munich', 'open': true, 'mode': 1, 'children': []}]
        //           }]
        //       }]
        //   },
        //   {
        //     'label': 'North America',
        //     'open': true,
        //     'mode': 1,
        //     'children': [
        //       {
        //         'label': 'United States',
        //         'open': true,
        //         'mode': 1,
        //         'children': [
        //           {
        //             'label': 'Pennsylvania',
        //             'open': true,
        //             'mode': 1,
        //             'children': [
        //               {'label': 'Philadelphia', 'open': true, 'mode': 1, 'children': []},
        //               {'label': 'Pittsburgh', 'open': true, 'mode': 1, 'children': []}]
        //           }]
        //       }]
        //   }]
      }
    }
  }
</script>