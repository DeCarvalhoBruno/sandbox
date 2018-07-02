<template>
    <div class="container p-0 m-0">
        <div id="trumbowyg-icons" v-html="require('trumbowyg/dist/ui/icons.svg')"></div>
        <form @submit.prevent="save" id="form_edit_blog">
            <div class="row p-0 m-0 mb-1">
                <div class="card col-lg">
                    <div class="container">
                        <div class="form-group row p-2">
                            <div class="col-lg-4 form-head-row">
                                <span>{{$t('general.status')}}: </span>
                                <template v-if="form_status_editing">
                                    <select v-model="form.blog_post_status"
                                            @change="changedField('blog_post_status')">
                                        <option v-for="(idx,status) in status_list" :key="idx" :value="status">
                                            {{$t(`constants.${status}`)}}
                                        </option>
                                    </select>
                                    <button type="button"
                                            class="btn btn-default btn-small"
                                            @click="toggleEditing('form_status_editing')"
                                    >{{$t('general.ok')}}
                                    </button>
                                </template>
                                <template v-else>
                                    <span class="form-field-togglable"
                                          @click="toggleEditing('form_status_editing')">
                                    {{ ($te(`constants.${form.blog_post_status}`))?
                                        $t(`constants.${form.blog_post_status}`):
                                        ''}}
                                    </span>
                                </template>
                            </div>
                            <div class="col-lg-4 form-head-row">
                                <div v-if="form_user_editing" class="container m-0 p-0">
                                    <div class="row">
                                        <div class="m-0 p-0 col-lg d-inline-flex">
                                            <div class="container d-inline-flex">
                                                <input-tag :typeahead="true"
                                                           :placeholder="$t('pages.members.member_search')"
                                                           :searchUrl="'/ajax/admin/users/search'"
                                                           @updateAddedItems="updateUsers"
                                                           limit="2"/>
                                                <button type="button"
                                                        class="btn btn-default btn-small"
                                                        @click="toggleEditing('form_user_editing')"
                                                >{{$t('general.ok')}}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <template v-else>
                                    <span>{{$t('pages.blog.author')}}: </span>
                                    <span class="form-field-togglable"
                                          @click="toggleEditing('form_user_editing')"
                                    >
{{form.blog_post_user}}
                                    </span>
                                </template>
                            </div>
                            <div class="col-lg-4 form-head-row">
                                <button type="submit" class="btn btn-primary float-lg-right">Save</button>
                            </div>
                        </div>
                        <div class="form-group row col-lg-10">
                            <div class="col-lg">
                                <input v-model="form.blog_post_title" type="text" required autocomplete="off"
                                       name="blog_post_title" id="blog_post_title" class="form-control"
                                       :class="{ 'is-invalid': form.errors.has('blog_post_title') }"
                                       :placeholder="$t('db.blog_post_title')"
                                       aria-describedby="help_blog_post_title"
                                       @input="changedField('blog_post_title')">
                                <small class="text-muted" v-show="blog_post_slug">{{blog_post_slug}}</small>
                            </div>
                        </div>
                        <div class="form-group row col-lg p-0 m-0">
                            <!--<span>BLOG POST SLUG</span>-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="row p-0 m-0 mb-1">
                <div class="card col-lg p-0 m-0">

                    <!--<editor v-model="form.editorInput" :init="editorConfig"></editor>-->
                    <trumbowyg v-model="form.blog_post_content" :config="editorConfig"
                               class="form-control"
                               @input="changedField('blog_post_content')"
                               name="content"></trumbowyg>

                    <!--<input type="text" v-model="message">-->
                    <!--<button type="button"-->
                    <!--v-clipboard:copy="message">Copy!</button>-->

                </div>
            </div>
            <div class="row p-0 m-0 mb-1">
                <div class="card col-lg-8 p-0 m-0">
                    <div class="card-header bg-transparent">Excerpt</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="blog_post_excerpt"></label>
                            <textarea class="form-control" name="blog_post_excerpt"
                                      id="blog_post_excerpt" rows="5" v-model="form.blog_post_excerpt"
                                      placeholder="Post Excerpt"
                                      @input="changedField('blog_post_excerpt')"></textarea>
                            <small id="help_new_group_name" class="text-muted">
                                This user-defined summary of the post can be displayed on the frontpage.
                            </small>
                        </div>
                    </div>
                </div>
                <div class="card col-lg-4 p-0 m-0">
                    <div class="card-header bg-transparent">Featured image</div>
                    <div class="card-body">
                    </div>
                </div>
            </div>
            <div class="row p-0 m-0 mb-1">
                <div class="card col-lg p-0 m-0">
                    <div class="card-header bg-transparent">Media</div>
                    <div class="card-body">
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
  import Vue from 'vue'
  import axios from 'axios'
  import Button from '~/components/Button'
  import Trumbowyg from '~/components/wysiwyg/Trumbowyg'
  import { Form, HasError, AlertForm } from '~/components/form'
  // import Editor from '@tinymce/tinymce-vue';
  import VueClipboard from 'vue-clipboard2'
  import InputTag from '~/components/InputTag'

  // VueClipboard.config.autoSetContainer = true // add this line
  Vue.use(VueClipboard)

  export default {
    layout: 'basic',
    middleware: 'check-auth',
    name: 'blog-post-add',
    components: {
      'v-button': Button,
      HasError,
      AlertForm,
      Trumbowyg,
      InputTag
      // Editor
    },
    data () {
      return {
        form_status_editing: false,
        form_user_editing: false,
        modal: false,
        editorConfig: this.getConfig(),
        status_list: null,
        current_status: '',
        blog_post_slug: null,
        saveMode: null,
        form: new Form({
          blog_post_content: '',
          blog_post_title: '',
          blog_post_status: '',
          blog_post_user: ''
        })
      }
    },
    methods: {
      getConfig () {
        let vm = this
        return {
          imageWidthModalEdit: true,
          semantic: false,
          btns: [
            ['viewHTML'],
            ['undo', 'redo'], // Only supported in Blink browsers
            ['formatting'],
            ['strong', 'em', 'del'],
            ['link'],
            ['justifyFull'],
            ['unorderedList', 'orderedList'],
            ['horizontalRule'],
            ['removeformat'],
            ['insertImage'],
            ['fullscreen']
          ],
          plugins: {}
          // btnsDef: {
          //   image: {
          //     fn: function() {
          //
          //       // vm.modal=true
          //     },
          //     ico: 'insert-image'
          //   }
          // },
        }
      },
      updateUsers (users) {
        if (users.length > 0) {
          this.form.blog_post_user = users[0].id
        }
      },
      toggleEditing (value) {
        this[value] = !this[value]
      },
      changedField (field) {
        this.form.addChangedField(field)
      },
      async save () {
        try {
          let suffix, saveMode
          let route = this.$route
          if ((route.name.lastIndexOf('add') > 0)) {
            saveMode = suffix = 'create'
          } else {
            saveMode = 'edit'
            suffix = `${saveMode}/${route.params.slug}`
          }
          const {data} = await this.form.post(`/ajax/admin/blog/post/${suffix}`)
          this.blog_post_slug = data.blog_post_slug
          this.saveMode = 'edit'
        } catch (e) {}
      },
      getInfo (data, saveMode) {
        this.form = new Form(data.record)
        this.status_list = data.status_list
        this.current_status = this.$t(`constants.${data.record.blog_post_status}`)
        this.saveMode = saveMode
      }
    },
    beforeRouteEnter (to, from, next) {
      let suffix, saveMode
      if ((to.name.lastIndexOf('add') > 0)) {
        saveMode = suffix = 'create'
      } else {
        saveMode = 'edit'
        suffix = `${saveMode}/${to.params.slug}`
      }

      let url = `/ajax/admin/blog/post/${suffix}`
      axios.get(url).then(({data}) => {
        next(vm => vm.getInfo(data, saveMode))
      })
    }
  }
</script>