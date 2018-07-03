<template>
    <ul>
        <li>
            <div class="li-wrapper">
                <span v-if="hasChildren" @click="toggleShow(node.label)">
                    <fa class="li-indicator" v-if="node.open" icon="minus"></fa>
                    <fa class="li-indicator" v-else icon="plus"></fa>
                </span>
                <template v-if="editMode">
                    <template v-if="(node.mode&2)!==0">
                        <input class="li-input"
                               v-focus type="text"
                               v-model="newValue" @focus="$event.target.select()"
                               @keyup.enter="updateItem" autocomplete="false"
                               @keyup.escape="cancelItem"
                               placeholder="Category name"/>

                        <template v-if="!isUpdating">
                            <button class="btn btn-sm" type="button"
                                    title="confirm" @click="updateItem">
                                <fa icon="check"/>
                            </button>
                            <button class="btn btn-sm" type="button" title="cancel" @click="cancelItem">
                                <fa icon="ban"/>
                            </button>
                        </template>
                        <template v-else>
                            <fa class="fa sync-icon" icon="sync" spin></fa>
                        </template>
                    </template>
                    <template v-else>
                    <span class="li-label" :class="{'li-label-searched':node.mode===5}"
                          @dblclick="toggleShow(node.label)">{{node.label}}</span>
                        <div class="li-btn-group">
                            <button type="button" class="btn btn-circle btn-default" @click="addItem"
                                    :title="`Add child to ${node.label}`">
                                <fa icon="plus"/>
                            </button>
                            <button type="button" class="btn btn-circle btn-default" @click="editItem"
                                    :title="`Add child to ${node.label}`">
                                <fa icon="pencil-alt"/>
                            </button>
                            <button type="button" class="btn btn-circle btn-default" @click="deleteItem"
                                    :title="`Delete ${node.label}`">
                                <fa icon="trash-alt"/>
                            </button>
                        </div>
                    </template>
                </template>
                <template v-else>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox"
                               :value="node.id" v-model="selectedCategory">
                        <label class="li-label form-check-label" :class="{'li-label-searched':node.mode===5}"
                               @dblclick="toggleShow(node.label)">{{node.label}}</label>
                    </div>

                </template>
            </div>
            <ul v-if="node.open" v-for="child of node.children">
                <tree-list-item :node="child" @event="emits" @category-selected="categorySelected"
                                :edit-mode="editMode"/>
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
      node: {required: true},
      editMode: {
        default: true
      }
    },
    data: function () {
      return {
        newValue: null,
        isUpdating: false,
        selectedCategory: null
      }
    },
    computed: {
      hasChildren () {
        return this.node.children.length > 0
      }
    },
    watch: {
      selectedCategory (value) {
        this.categorySelected(
          this.node.id,
          (value) ? 'add' : 'del'
        )
      }
    },
    directives: {
      focus: {
        inserted: function (el) {
          el.focus()
        }
      }
    },
    updated () {
      if (this.node.mode === 1) {
        this.isUpdating = false
      }
    },
    mounted () {
      //Making sure the html input element has an initial value
      this.newValue = this.node.label
    },
    methods: {
      emits (nodeMap, data) {
        this.$emit('event', [this.node.label].concat(nodeMap), data)
      },
      categorySelected (value, mode) {
        this.$emit('category-selected', value, mode)
      },
      addItem () {
        this.emits([], this.makeDataObject('add'))
      },
      editItem () {
        this.emits([], this.makeDataObject('edit'))
      },
      deleteItem () {
        this.emits([], this.makeDataObject('delete'))
      },
      updateItem () {
        this.isUpdating = true
        this.emits([], {method: 'update', newValue: this.newValue, target: this.node.label})
      },
      cancelItem () {
        this.newValue = this.node.label
        switch (this.node.mode) {
          case 6:
            this.emits([], this.makeDataObject('cancelCreation'))
            break
          default:
            this.emits([], this.makeDataObject('cancel'))
        }
      },
      toggleShow () {
        this.emits([], this.makeDataObject('toggleShow'))
      },
      makeDataObject (method) {
        return {method: method, target: this.node.label}
      }
    }
  }
</script>