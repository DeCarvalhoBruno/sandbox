<template>
    <div id="blog_post_container" class="container p-0 m-0">
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
                                                <input-tag-search :typeahead="true"
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
                                    >{{form.blog_post_user}}</span>
                                </template>
                            </div>
                            <div class="col-lg-4 form-head-row">
                                <button type="button"
                                        class="btn btn-primary float-lg-right"
                                        @click="save">{{$t('general.save')}}
                                </button>
                            </div>
                        </div>
                        <div class="form-group row col-lg-10">
                            <div class="col-lg">
                                <input v-model="form.blog_post_title" type="text" required autocomplete="off"
                                       name="blog_post_title" id="blog_post_title"
                                       class="form-control" maxlength="255"
                                       :class="{ 'is-invalid': form.errors.has('blog_post_title') }"
                                       :placeholder="$t('db.blog_post_title')"
                                       aria-describedby="help_blog_post_title">
                                <small class="text-muted" v-show="url">{{url}}</small>
                            </div>
                        </div>
                        <div class="form-group row col-lg p-0 m-0">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row p-0 m-0 mb-1">
                <div class="card col-lg p-0 m-0">
                    <div class="row p-0 m-0">
                        <trumbowyg v-model="form.blog_post_content" :config="editorConfig"
                                   class="form-control"
                                   @input="changedField('blog_post_content')"
                                   name="content"></trumbowyg>

                        <!--<input type="text" v-model="message">-->
                        <!--<button type="button"-->
                        <!--v-clipboard:copy="message">Copy!</button>-->
                    </div>
                    <div class="row p-0 m-0 input-tag-wrapper">
                        <span class="badge badge-pill badge-light"
                              v-for="(badge, index) in form.tags"
                              :key="index">
                        <span v-html="badge" @click.prevent="removeTag(index)"></span>
                            <fa icon="tag" class="badge-tag-icon"></fa>
                        </span>
                        <input type="text"
                               ref="tag"
                               v-model="tagInput"
                               @keyup.enter="addTag"
                               maxlength="35"
                               :placeholder="$t('pages.blog.add_tag_pholder')"/>
                    </div>
                </div>
            </div>
            <div class="row p-0 m-0 mb-1">
                <div class="card col-lg-6 p-0 m-0">
                    <div class="card-header bg-transparent">{{$t('pages.blog.blog_post_excerpt')}}</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="blog_post_excerpt"></label>
                            <textarea class="form-control" name="blog_post_excerpt"
                                      id="blog_post_excerpt" rows="5" v-model="form.blog_post_excerpt"
                                      placeholder="Post Excerpt"
                                      @input="changedField('blog_post_excerpt')"></textarea>
                            <small id="help_new_group_name" class="text-muted"
                            >{{$t('pages.blog.excerpt_label')}}
                            </small>
                        </div>
                    </div>
                </div>
                <div class="card col-lg-6 p-0 m-0">
                    <div class="card-header bg-transparent">{{$t('pages.blog.categories')}}</div>
                    <div class="card-body">
                        <div class="mini-tree-list-container container">
                            <div class="row">
                                <tree-list :data="this.blog_post_categories"
                                           :edit-mode="false"
                                           :add-root-button-label="$t('pages.blog.add_root_button')"
                                           @tree-category-selected="categorySelected"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row p-0 m-0">
                <div class="card col-lg p-0 m-0">
                    <div class="card-header bg-transparent">{{$t('pages.blog.media')}}</div>
                    <div class="card-body">
                        <image-uploader
                                :target="form.blog_post_slug"
                                type="blog_posts"
                                media="image"
                                :is-active="this.saveMode==='edit'"
                                :thumbnails-parent="thumbnails"
                                @images-updated="updateThumbnails"/>
                    </div>
                </div>
            </div>
            <div class="row p-0 mt-5 mb-5">
                <div class="row p-0 mt-5 mb-5">
                </div>
            </div>
        </form>
        <media-modal :show-modal="modal_show"
                     @modal-close="modal_show=false"></media-modal>
    </div>
</template>

<script>
  import axios from 'axios'
  import Button from '~/components/Button'
  import Trumbowyg from '~/components/wysiwyg/Trumbowyg'
  import { Form, HasError, AlertForm } from '~/components/form'
  import TreeList from '~/components/tree-list/TreeList'
  import InputTagSearch from '~/components/InputTagSearch'
  import ImageUploader from '~/components/media/ImageUploader'

  import MediaModal from '~/components/media/MediaModal'
  // import swal from 'sweetalert2'
  import swal from '~/mixins/sweet-alert'
  import form from '~/mixins/form'

  // import VueClipboard from 'vue-clipboard2'
  //import Vue from 'vue'
  // VueClipboard.config.autoSetContainer = true // add this line
  // Vue.use(VueClipboard)

  export default {
    layout: 'basic',
    middleware: 'check-auth',
    name: 'blog-post-add',
    components: {
      'v-button': Button,
      HasError,
      AlertForm,
      Trumbowyg,
      InputTagSearch,
      TreeList,
      MediaModal,
      ImageUploader
    },
    mixins: [
      swal,
      form
    ],
    data () {
      return {
        modal_show: false,
        form_status_editing: false,
        form_user_editing: false,
        modal: false,
        editorConfig: this.getConfig(),
        status_list: null,
        current_status: '',
        url: null,
        saveMode: null,
        blog_post_categories: [],
        tagInput: '',
        thumbnails: [],
        form: new Form({
          blog_post_content: '',
          blog_post_title: '',
          blog_post_status: '',
          blog_post_user: '',
          categories: [],
          tags: []
        })
      }
    },
    methods: {
      updateThumbnails (data) {
        this.thumbnails = data
      },
      addTag () {
        if (this.tagInput) {
          this.form.tags.push(this.tagInput)
          this.tagInput = ''
          this.$refs.tag.focus()
          this.changedField('tags')
        }
      },
      removeTag (index) {
        this.form.tags.splice(index, 1)
        this.$refs.tag.focus()
        this.changedField('tags')
      },
      categorySelected (val, mode) {
        if (mode === 'add') {
          if (this.form.categories.indexOf(val) === -1) {
            this.form.categories.push(val)
          }
        } else {
          let i = this.form.categories.indexOf(val)
          if (i > -1) {
            this.form.categories.splice(i, 1)
          }
        }
      },
      getConfig () {
        let vm = this
        return {
          semantic: false,
          btns: [
            ['viewHTML'],
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
      async save () {
        if (!this.form.blog_post_title) {
          return
        }
        try {
          let suffix, saveMode, msg
          let route = this.$route
          if ((route.name.lastIndexOf('add') > 0)) {
            saveMode = suffix = 'create'
            msg = this.$t('pages.blog.add_success')
          } else {
            saveMode = 'edit'
            suffix = `${saveMode}/${route.params.slug}`
            msg = this.$t('pages.blog.save_success')
          }
          let {data} = await this.form.post(`/ajax/admin/blog/post/${suffix}`)
          this.url = data.url
          if (saveMode === 'create') {
            this.form.addField('blog_post_slug', data.blog_post_slug)
          } else {
            this.form.blog_post_slug = data.blog_post_slug
          }
          this.saveMode = 'edit'
          this.swalNotification('success', msg)
        } catch (e) {}
      },
      getInfo (data, saveMode) {
        this.form = new Form(data.record)
        this.status_list = data.status_list
        this.url = data.url
        this.current_status = this.$t(`constants.${data.record.blog_post_status}`)
        this.saveMode = saveMode
        this.blog_post_categories = data.blog_post_categories
        this.thumbnails = data.thumbnails
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