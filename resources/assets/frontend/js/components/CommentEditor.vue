<template>
  <div class="card comment-editor-container">
    <div>
      <tiptap ref="tiptap"
              :edit-mode="currentEditMode"
              :is-root-elem="isRootElem"
              :content="txtContent"></tiptap>
      <div class="w-100">
        <div class="pull-right p-2">
          <button type="button" class="btn btn-outline-primary"
                  @click="cancelEditing">Cancel
          </button>
          <button type="button" class="btn btn-primary" @click="save">Submit</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  export default {
    name: 'comment-editor',
    props: {
      editMode: {required: true},
      contents: {type: String, default: () => null},
      isRootElem: {type: Boolean},
      slug: {type: String}
    },
    components: {
      Tiptap: () => import('front_path/components/Tiptap')
    },
    watch: {
      watch: {
        contents () {
          this.txtContent = this.contents
        }
      }
    },
    computed: {
      txtContent () {
        return this.contents
      },
      currentEditMode () {
        return this.editMode
      }
    },
    data () {
      return {}
    },
    methods: {
      cancelEditing () {
        this.$emit('cancelled')
      },
      save () {
        let txt = this.$refs.tiptap.getData()
        this.$root.$emit('submitComment', {
          comment: {
            reply_to: this.slug,
            txt: txt,
            mode: this.currentEditMode === 1 || this.currentEditMode === true ? 'create' : 'edit'
          }
        })
        this.$emit('submitted',{txt:txt})
      }
    }
  }
</script>