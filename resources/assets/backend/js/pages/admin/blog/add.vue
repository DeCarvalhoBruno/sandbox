<template>
    <div id="blog_post_container" class="container p-0 m-0">
        <div id="trumbowyg-icons" v-html="require('trumbowyg/dist/ui/icons.svg')"></div>
        <form @submit.prevent="save" id="form_edit_blog" ref="form_this">
            <div class="row p-0 m-0 mt-1">
                <div class="card col-lg">
                    <div class="container">
                        <div class="form-group row col-lg mt-1">
                            <div class="col-lg-10">
                                <input v-model="form.fields.blog_post_title" type="text" required autocomplete="off"
                                       name="blog_post_title" id="blog_post_title"
                                       class="form-control" maxlength="255"
                                       :class="{ 'is-invalid': form.errors.has('blog_post_title') }"
                                       :placeholder="$t('db.blog_post_title')"
                                       aria-describedby="help_blog_post_title"
                                       @change="changedField('blog_post_title')">
                                <small class="text-muted" v-show="url">{{url}}</small>
                            </div>
                            <div class="col-lg-2">
                                <v-button type="button" :loading="form.busy"
                                          class="btn btn-primary float-lg-right"
                                          @click="save">{{$t('general.save')}}
                                </v-button>
                            </div>
                        </div>
                        <div id="head_row" class="form-group row">
                            <div class="col-lg-6" id="head_col_draft">
                                <template v-if="form_status_editing">
                                    <select v-model="form.fields.blog_post_status"
                                            @change="changedField('blog_post_status')"
                                            class="custom-control custom-select">
                                        <option v-for="(idx,status) in status_list" :key="idx" :value="status"
                                        >{{$t(`constants.${status}`)}}
                                        </option>
                                    </select>
                                    <button type="button"
                                            class="btn btn-success btn-small"
                                            @click="toggleEditing('form_status_editing')">
                                        <fa icon="check"></fa>
                                    </button>
                                    <button type="button"
                                            class="btn btn-light btn-small"
                                            @click="toggleEditing('form_status_editing')">
                                        <fa icon="ban"></fa>
                                    </button>
                                </template>
                                <template v-else>
                                    <span>{{$t('general.status')}}: </span>
                                    <span class="form-field-togglable"
                                          @click="toggleEditing('form_status_editing')"
                                    >{{ ($te(`constants.${form.fields.blog_post_status}`))?
                                        $t(`constants.${form.fields.blog_post_status}`):
                                        ''}}</span>
                                </template>
                            </div>
                            <div class="col-lg-6 justify-content-center">
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
                                                        class="btn btn-success btn-small"
                                                        @click="toggleEditing('form_user_editing')">
                                                    <fa icon="check"></fa>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-light btn-small"
                                                        @click="toggleEditing('form_user_editing')">
                                                    <fa icon="ban"></fa>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <template v-else>
                                    <span>{{$t('pages.blog.author')}}: </span>
                                    <span class="form-field-togglable"
                                          @click="toggleEditing('form_user_editing')"
                                    >{{form.fields.blog_post_user}}</span>
                                </template>
                            </div>
                        </div>
                        <div class="form-group row col-lg mb-3 pl-2" style="min-height:2.5rem">
                            <div class="col-lg align-items-center">
                                <div class="row col" v-if="form_publish_date_editing">
                                    <datepicker
                                            ref="datePicker"
                                            v-model="current_publish_date"
                                            :name="'published_at'"
                                            :show-clear-button="false"
                                            @closed="changePublishDate"
                                    >
                                    </datepicker>
                                    <div class="form-inline">
                                        <input class="form-control input-hour-minute"
                                               :value="format(current_publish_date,'HH')"
                                               ref="inputHours"
                                        >:<input class="form-control input-hour-minute"
                                                 ref="inputMinutes"
                                                 :value="format(current_publish_date,'mm')">
                                    </div>
                                    <button type="button"
                                            class="btn btn-success btn-small"
                                            @click="validateAndGo">
                                        <fa icon="check"></fa>
                                    </button>
                                    <button type="button"
                                            class="btn btn-light btn-small"
                                            @click="toggleEditing('form_publish_date_editing')">
                                        <fa icon="ban"></fa>
                                    </button>
                                </div>
                                <div class="col pl-0" v-else>
                                        <span id="span-published-at">{{$t('pages.blog.published_at')}}&nbsp;<span
                                                id="span-nice-datetime"
                                                @click="toggleEditing('form_publish_date_editing')">{{formattedPublishedAt}}</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row p-0 m-0 mb-1">
                <div class="card col-lg p-0 m-0">
                    <div class="row p-0 m-0">
                        <trumbowyg v-model="form.fields.blog_post_content" :config="editorConfig"
                                   ref="inputBlogPostContent"
                                   class="form-control"
                                   name="content"></trumbowyg>

                        <!--<input type="text" v-model="message">-->
                        <!--<button type="button"-->
                        <!--v-clipboard:copy="message">Copy!</button>-->
                    </div>
                    <div class="row p-0 m-0 input-tag-wrapper">
                        <span class="badge badge-pill badge-light"
                              v-for="(badge, index) in form.fields.tags"
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
                                      id="blog_post_excerpt" rows="5" v-model="form.fields.blog_post_excerpt"
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
                                :target="form.fields.blog_post_slug"
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
    </div>
</template>

<script>
  import axios from 'axios'
  import dayjs from 'dayjs'
  import fr from 'dayjs/locale/fr'
  import Button from '~/components/Button'
  import Trumbowyg from '~/components/wysiwyg/Trumbowyg'
  import { Form, HasError, AlertForm } from '~/components/form'
  import TreeList from '~/components/tree-list/TreeList'
  import InputTagSearch from '~/components/InputTagSearch'
  import Datepicker from '~/components/Datepicker'
  import ImageUploader from '~/components/media/ImageUploader'

  import swal from '~/mixins/sweet-alert'
  import form from '~/mixins/form'
  import { deepCopy } from '../../../components/form/util'

  export default {
    layout: 'basic',
    middleware: 'check-auth',
    name: 'blog-post-add',
    components: {
      'v-button': Button,
      HasError,
      Trumbowyg,
      InputTagSearch,
      TreeList,
      ImageUploader,
      Datepicker
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
        form_publish_date_editing: false,
        modal: false,
        editorConfig: this.getConfig(),
        status_list: null,
        current_status: '',
        current_publish_date: new Date(),
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
    computed: {
      formattedPublishedAt () {
        return dayjs(this.current_publish_date)
          .locale(this.$store.getters['lang/locale'])
          .format(this.$store.getters['lang/dateTimeFormat'])
      }
    },
    watch: {
      current_publish_date (value) {
        this.form.fields.published_at = value
      }
    },
    mounted () {
      let vm = this
      //We have to set a listener like this because the input event is emitted because the input field
      //might be populated with text that is not user input and trigger the changedField method prematurely
      this.$refs.inputBlogPostContent.$on('tbw-init', () => {
        vm.$refs.inputBlogPostContent.$on('input', () => {
          vm.changedField('blog_post_content')
        })
      })
      window.addEventListener('beforeunload', this.checkBeforeUnload)
    },
    beforeDestroy () {
      window.removeEventListener('beforeunload', this.checkBeforeUnload)
    },
    methods: {
      checkBeforeUnload (event) {
        if (this.form.hasDetectedChanges()) {
          var confirmationMessage = '_'
          event.returnValue = confirmationMessage
          return confirmationMessage
        }
      },
      format (value, format) {
        return dayjs(value).format(format)
      },
      validateAndGo () {
        let currentDate = dayjs(this.current_publish_date).format('YYYY-MM-DD')
        let dateTime = dayjs(currentDate + ' ' + this.$refs.inputHours.value + ':' + this.$refs.inputMinutes.value)

        if (dateTime.isValid()) {
          this.current_publish_date = dateTime.toDate()
          this.toggleEditing('form_publish_date_editing')
        }
      },
      updateThumbnails (data) {
        this.thumbnails = data
      },
      addTag () {
        if (this.tagInput) {
          this.form.fields.tags.push(this.tagInput)
          this.tagInput = ''
          this.$refs.tag.focus()
          this.changedField('tags')
        }
      },
      changePublishDate (value) {
        this.current_publish_date = dayjs(
          dayjs(value).format('YYYY-MM-DD') + ' ' +
          this.$refs.inputHours.value + ':' +
          this.$refs.inputMinutes.value).toDate()
      },
      removeTag (index) {
        this.form.fields.tags.splice(index, 1)
        this.$refs.tag.focus()
        this.changedField('tags')
      },
      categorySelected (val, mode) {
        if (mode === 'add') {
          if (this.form.fields.categories.indexOf(val) === -1) {
            this.form.fields.categories.push(val)
            this.changedField('categories')
          }
        } else {
          let i = this.form.fields.categories.indexOf(val)
          if (i > -1) {
            this.form.fields.categories.splice(i, 1)
          }

        }
      },
      getConfig () {
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
          this.form.fields.blog_post_user = users[0].id
          this.changedField('blog_post_user')
        }
      },
      toggleEditing (value) {
        this[value] = !this[value]
      },
      async save () {
        if (!this.form.fields.blog_post_title) {
          return
        }
        let suffix, msg
        let route = this.$route
        if ((route.name.lastIndexOf('add') > 0)) {
          this.saveMode = suffix = 'create'
          msg = this.$t('pages.blog.add_success')
        } else {
          this.saveMode = 'edit'
          suffix = `${this.saveMode}/${route.params.slug}`
          msg = this.$t('pages.blog.save_success')
        }
        this.form.fields.published_at = dayjs(this.form.fields.published_at).format('YYYYMMDDHHmm')
        let formBeforeSave = this.form.getFormData()
        let {data} = await this.form.post(`/ajax/admin/blog/post/${suffix}`)
        this.form = new Form(formBeforeSave, true)
        this.form.resetChangedFields()
        this.url = data.url
        if (this.saveMode === 'create') {
          this.form.addField('blog_post_slug', data.blog_post_slug)
          this.$router.replace({name: 'admin.blog.edit', params: {slug: data.blog_post_slug}})
        } else {
          this.form.fields.blog_post_slug = data.blog_post_slug
        }
        this.swalNotification('success', msg)
      },
      getInfo (data, saveMode) {
        this.form = new Form(data.record)
        if (saveMode === 'create') {
          this.form.addField('published_at', new Date())
        } else {
          this.form.setTrackChanges(true)
          this.current_publish_date = dayjs(this.form.fields.published_at).toDate()
        }
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
    },
    beforeRouteLeave (to, from, next) {
      if (this.form.hasDetectedChanges()) {
        this.swalSaveWarning().then((result) => {
          if (result.value) {
            next()
          } else {
            next(false)
          }
        })
      } else {
        next()
      }
    }
  }
</script>