<template>
    <div class="container p-0 m-0">
    <div id="trumbowyg-icons" v-html="require('trumbowyg/dist/ui/icons.svg')"></div>
        <div class="row p-0 m-0 mb-1">
            <div class="card col-lg">
                <div class="container">
                    <div class="form-group row p-2">
                        <div class="col-lg-10">

                        </div>
                        <div class="col-lg-2">
                            <button type="submit" class="btn btn-primary">Save</button>

                        </div>
                    </div>
                    <div class="form-group row col-lg-8">
                        <label for="group_name"
                               class="col-md-3 col-form-label"></label>
                        <div class="col-md-9">
                            <input v-model="form.group_name" type="text"
                                   name="group_name" id="group_name" class="form-control"
                                   :class="{ 'is-invalid': form.errors.has('group_name') }"
                                   :placeholder="$t('db.group_name')"
                                   aria-describedby="help_group_name">
                            <has-error :form="form" field="group_name"/>
                            <small id="help_group_name" class="text-muted">
                                {{$t('form.description.group_name',[form.group_name])}}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row p-0 m-0">
            <div id="editor_wrapper" class="card col-lg p-0 m-o">
                <form @submit.prevent="save">
                    <!--<editor v-model="form.editorInput" :init="editorConfig"></editor>-->
                    <trumbowyg v-model="form.editorInput" :config="editorConfig" class="form-control"
                               name="content"></trumbowyg>
                </form>
                <!--<input type="text" v-model="message">-->
                <!--<button type="button"-->
                <!--v-clipboard:copy="message">Copy!</button>-->

            </div>
        </div>
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
      Trumbowyg
      // Editor
    },
    data () {
      return {
        modal: false,
        editorConfig: this.getConfig(),
        form: new Form({
          editorInput: ''
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
      test () {

      },
      async save () {
        try {
        } catch (e) {}
      }
    }
  }
</script>