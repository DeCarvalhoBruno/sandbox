<template>
    <b-card no-body>
        <form @submit.prevent="save" @keydown="form.onKeydown($event)">
            <b-tabs card>
                <b-tab :title="form.media_title" active>
                    <div class="col-md-8 offset-md-2">
                         <div class="form-group row">
                            <label for="created_at" class="col-md-3 col-form-label">{{$t('general.uploaded_on')}}</label>
                            <div class="col-md-9">
                                <input v-model="form.created_at" type="text"
                                       id="created_at" class="form-control"
                                       :class="{ 'is-invalid': form.errors.has('created_at') }" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="media_title" class="col-md-3 col-form-label">{{$t('db.media_title')}}</label>
                            <div class="col-md-9">
                                <input v-model="form.media_title" type="text"
                                       name="media_title" id="media_title" class="form-control"
                                       :class="{ 'is-invalid': form.errors.has('media_title') }"
                                       :placeholder="$t('db.media_title')"
                                       aria-describedby="help_media_title">
                                <has-error :form="form" field="media_title"></has-error>
                                <small id="help_media_title"
                                       class="text-muted">{{$t('form.description.media_title',[form.media_title])}}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-6 offset-md-3 mb-4">
                            <v-button class="align-content-center"
                                      :loading="form.busy">{{ $t('general.update') }}
                            </v-button>
                        </div>
                    </div>
                </b-tab>
            </b-tabs>
        </form>
    </b-card>
</template>


<script>
    /*
    media_alt: null
    media_caption: null
    media_description: null
    media_extension: "jpg"
    media_filename: "meeting-room-730679.jpg"
 */
  import Vue from 'vue'
  import Button from '~/components/Button'
  import axios from 'axios'
  import { Form, HasError, AlertForm } from '~/components/form'
  import { Card, Tabs } from 'bootstrap-vue/es/components'

  Vue.use(Card)
  Vue.use(Tabs)

  export default {
    layout: 'basic',
    middleware: 'check-auth',
    name: 'media-edit',
    components: {
      'v-button': Button,
      HasError,
      Form
    },
    data () {
      return {
        form: new Form(),
        mediaInfo: {}

      }
    },
    methods: {
      getInfo (data) {
        this.form = new Form(data.media)
      }
    },
    beforeRouteEnter (to, from, next) {
      axios.get(`/ajax/admin/media/${to.params.media}`).then(({data}) => {
        next(vm => vm.getInfo(data))
      })
    }
  }
</script>