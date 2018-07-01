<template>
    <ul>
        <li>
            <div class="li-wrapper">
                <span v-if="hasChildren" @click="toggleShow(node.label)">
                    <fa class="li-indicator" v-if="node.open" icon="minus"></fa>
                    <fa class="li-indicator" v-else icon="plus"></fa>
                </span>
                <template v-if="(node.mode&2)!==0">
                    <input v-focus type="text" v-model="newValue" @focus="$event.target.select()"
                           @keyup.enter="updateItem" autocomplete="false"
                           placeholder="Category name" @blur="cancelItem"/>
                    <button class="btn btn-sm" type="button" title="confirm" @click="updateItem">
                        <fa icon="check"></fa>
                    </button>
                    <button class="btn btn-sm" type="button" title="cancel" @click="cancelItem">
                        <fa icon="ban"></fa>
                    </button>
                </template>
                <template v-else>
                    <span>{{node.label}}</span>
                    <div class="li-btn-group">
                        <button type="button" class="btn btn-circle btn-default" @click="addItem"
                                :title="`Add child to ${node.label}`">
                            <fa icon="plus"/>
                        </button>
                        <button type="button" class="btn btn-circle btn-default" @click="editItem"
                                :title="`Add child to ${node.label}`">
                            <fa icon="pencil-alt"/>
                        </button>
                    </div>
                </template>
            </div>
            <ul v-if="node.open" v-for="child of node.children">
                <tree-list-item :node="child" @event="emits"/>
            </ul>
        </li>
    </ul>
</template>
<script>
  // import Vue from 'vue'
  // import VShowSlide from '~/directives/VShowSlide'
  //
  // Vue.use(new VShowSlide(), {
  //   customEasing: {
  //     exampleEasing: 'cubic-bezier(0.68, -0.55, 0.265, 1.55)'
  //   }
  // })

  export default {
    name: 'tree-list-item',
    props: {
      node: {required: true}
    },
    computed: {
      hasChildren () {
        return this.node.children.length > 0
      }
    },
    directives: {
      focus: {
        // directive definition
        inserted: function (el) {
          el.focus()
        }
      }
    },
    data: function () {
      return {
        newValue: null
      }
    },
    mounted () {
      this.newValue = this.node.label
    },
    methods: {
      addItem () {
        this.emits([], {method: 'add', target: this.node.label})
      },
      editItem () {
        this.emits([], {method: 'edit', target: this.node.label})
      },
      updateItem () {
        this.emits([], {method: 'update', newValue: this.newValue, target: this.node.label})
      },
      cancelItem () {
        switch (this.node.mode) {
          case 6:
            this.emits([], {method: 'delete', target: this.node.label})
            break
          default:
            this.emits([], {method: 'cancel', target: this.node.label})
        }
      },
      toggleShow () {
        this.emits([], {method: 'toggleShow', target: this.node.label})
      },
      emits (nodeMap, data) {
        this.$emit('event', [this.node.label].concat(nodeMap), data)
      }
    }
  }
</script>