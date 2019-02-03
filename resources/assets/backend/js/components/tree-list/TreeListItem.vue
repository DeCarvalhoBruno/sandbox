<template>
    <ul>
        <li>
            <div class="li-wrapper" :class="[hasChildren?'':'childless']">
                <span v-if="hasChildren" @click="toggleShow(node.label)">
                    <fa class="li-indicator" v-if="node.open" icon="minus"></fa>
                    <fa class="li-indicator" v-else icon="plus"></fa>
                </span>
                <template v-if="editMode">
                    <template v-if="(node.mode&2)!==0">
                        <div class="li-input-wrapper">
                            <input class="li-input"
                                   type="text"
                                   v-model="newValue" @focus="$event.target.select()"
                                   @keyup.enter="updateItem" autocomplete="false"
                                   @keyup.escape="cancelItem"
                                   placeholder="Category name" v-focus/>
                            <div class="li-btn-group" :class="[isUpdating?'updating':'']">
                                <template v-if="!isUpdating">
                                    <button class="btn btn-sm" type="button" :disabled="!newValue" :aria-disabled="!newValue"
                                            title="confirm" @click="updateItem">
                                        <fa icon="check"></fa>
                                    </button>
                                    <button class="btn btn-sm" type="button" title="cancel" @click="cancelItem">
                                        <fa icon="ban"></fa>
                                    </button>
                                </template>
                                <template v-else>
                                    <fa class="fa sync-icon" icon="sync" spin></fa>
                                </template>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="li-btn-group-wrapper" :class="{'tree-searched':node.mode===5}" @dblclick="toggleShow(node.label)">
                            <span class="li-label">{{node.label}}</span>
                            <div class="li-btn-group">
                                <button type="button" class="btn btn-sm btn-default" @click="addItem"
                                        :title="$t('pages.blog_categories.add_child_node',{name:node.label})">
                                    <fa icon="plus"></fa>
                                </button>
                                <button type="button" class="btn btn-sm btn-default" @click="editItem"
                                        :title="$t('pages.blog_categories.edit_node',{name:node.label})">
                                    <fa icon="pencil-alt"></fa>
                                </button>
                                <button type="button" class="btn btn-sm btn-default" @click="deleteItem"
                                        :title="$t('pages.blog_categories.delete_node',{name:node.label})">
                                    <fa icon="trash-alt"></fa>
                                </button>
                            </div>
                        </div>
                    </template>
                </template>
                <template v-else>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox"
                               v-model="isChecked">
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
        isUpdating: false
      }
    },
    computed: {
      hasChildren () {
        return this.node.children.length > 0
      },
      isChecked: {
        get () {
          return this.node.selected
        },
        set (value) {
          this.node.selected = value
        }
      }
    },
    watch: {
      isChecked (value) {
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
        this.$emit('event', [this.node.id].concat(nodeMap), data)
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
        if (this.newValue.length)  {
          this.isUpdating = true
          this.emits([], {method: 'update', newValue: this.newValue, target: {id:this.node.id,label:this.node.label}})
        }else{
          this.cancelItem()
        }
      },
      cancelItem () {
        // this.newValue = this.node.label
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
        return {method: method, target: {id:this.node.id,label:this.node.label}}
      }
    }
  }
</script>