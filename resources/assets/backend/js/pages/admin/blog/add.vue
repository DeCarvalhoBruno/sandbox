<template>
    <div class="container p-0 m-0">
        <div id="trumbowyg-icons" v-html="require('trumbowyg/dist/ui/icons.svg')"></div>
        <form @submit.prevent="save">
            <div class="row p-0 m-0 mb-1">
                <div class="card col-lg">
                    <div class="container">
                        <div class="form-group row p-2">
                            <div class="col-lg-5">
                                <span>{{$t('general.status')}}: </span>
                                <template v-if="form_status_editing">
                                    <select v-model="form.blog_post_status">
                                        <option v-for="(idx,status) in status_list" :key="idx" :value="status">
                                            {{$t(`constants.${status}`)}}
                                        </option>
                                    </select>
                                        <button type="button"
                                                class="btn btn-default btn-small"
                                                @click="toggleEditing('form_status_editing')"
                                        >{{$t('general.ok')}}</button>
                                </template>
                                <template v-else>
                                    <span @click="toggleEditing('form_status_editing')"
                                          style="text-decoration: underline;color:blue;cursor:pointer">

                                    {{ ($te(`constants.${form.blog_post_status}`))?$t(`constants.${form.blog_post_status}`):''}}
                                    </span>
                                </template>
                            </div>
                            <div class="col-lg-5">
                            </div>
                            <div class="col-lg-2">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                        <div class="form-group row col-lg-10">
                            <div class="col-lg">
                                <input v-model="form.blog_post_title" type="text"
                                       name="blog_post_title" id="blog_post_title" class="form-control"
                                       :class="{ 'is-invalid': form.errors.has('blog_post_title') }"
                                       :placeholder="$t('db.blog_post_title')"
                                       aria-describedby="help_blog_post_title">
                                <has-error :form="form" field="blog_post_title"/>
                            </div>
                        </div>
                        <div class="form-group row col-lg p-0 m-0">
                            <!--<span>BLOG POST SLUG</span>-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="row p-0 m-0">
                <div id="editor_wrapper" class="card col-lg p-0 m-o">

                    <!--<editor v-model="form.editorInput" :init="editorConfig"></editor>-->
                    <trumbowyg v-model="form.blog_post_content" :config="editorConfig" class="form-control"
                               name="content"></trumbowyg>

                    <!--<input type="text" v-model="message">-->
                    <!--<button type="button"-->
                    <!--v-clipboard:copy="message">Copy!</button>-->

                </div>
            </div>
        </form>
        <div class="row p-0 m-0">
            <div class="card">

            </div>
        </div>
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
      // Editor
    },
    data () {
      return {
        form_status_editing: false,
        modal: false,
        editorConfig: this.getConfig(),
        status_list: null,
        current_status: '',
        form: new Form({
          blog_post_content: '',
          blog_post_title: '',
          blog_post_status: '',
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
            ['superscript', 'subscript'],
            ['link'],
            ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
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
      toggleEditing (value) {
        this[value] = !this[value]
      },
      async save () {
        try {
        } catch (e) {}
      },
      getInfo (data) {
        this.form = new Form(data.record)
        this.status_list = data.status_list
        this.current_status = this.$t(`constants.${data.record.blog_post_status}`)

      }
    },
    beforeRouteEnter (to, from, next) {
      axios.get(`/ajax/admin/blog/add`).then(({data}) => {
        next(vm => vm.getInfo(data))
      })
    }
  }
</script>