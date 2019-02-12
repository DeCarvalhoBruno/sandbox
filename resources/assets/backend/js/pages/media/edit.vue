<template>
    <b-card no-body ref="cropperContainer">
        <form @submit.prevent="save" @keydown="form.onKeydown($event)">
            <b-tabs card>
                <b-tab :title="$t('general.media')" active>
                    <div class="col-md-8 offset-md-2">
                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <div class="col-md-6 offset-md-3">
                                    <img :src="getImageUrl(form.media_uuid, form.suffix, form.media_extension)"></img>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="created_at"
                                   class="col-md-3 col-form-label">{{$t('general.uploaded_on')}}</label>
                            <div class="col-md-9">
                                <input v-model="form.fields.created_at" type="text"
                                       id="created_at" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="media_title" class="col-md-3 col-form-label">{{$t('db.media_title')}}</label>
                            <div class="col-md-9">
                                <input v-model="form.fields.media_title" type="text"
                                       name="media_title" id="media_title" class="form-control"
                                       :class="{ 'is-invalid': form.errors.has('media_title') }"
                                       :placeholder="$t('db.media_title')"
                                       aria-describedby="help_media_title"
                                       autocomplete="off"
                                       @change="changedField('media_title')">
                                <has-error :form="form" field="media_title"></has-error>
                                <small id="help_media_title"
                                       class="text-muted">{{$t('form.description.media_title',[form.media_title])}}
                                </small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="media_alt" class="col-md-3 col-form-label">{{$t('db.media_alt')}}</label>
                            <div class="col-md-9">
                                <input v-model="form.fields.media_alt" type="text"
                                       name="media_alt" id="media_alt" class="form-control"
                                       :class="{ 'is-invalid': form.errors.has('media_alt') }"
                                       :placeholder="$t('db.media_alt')"
                                       aria-describedby="help_media_alt" autocomplete="off"
                                       @change="changedField('media_alt')">
                                <has-error :form="form" field="media_alt"></has-error>
                                <small id="help_media_alt"
                                       class="text-muted">{{$t('form.description.media_alt',[form.media_alt])}}
                                </small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="media_description" class="col-md-3 col-form-label">{{$t('db.media_description')}}</label>
                            <div class="col-md-9">
                                <textarea v-model="form.fields.media_description"
                                          name="media_description" id="media_description"
                                          class="form-control txtarea-noresize"
                                          :class="{ 'is-invalid': form.errors.has('media_description') }"
                                          :placeholder="$t('db.media_description')"
                                          aria-describedby="help_media_description"
                                          rows="4"
                                          @change="changedField('media_description')"></textarea>
                                <has-error :form="form" field="media_description"></has-error>
                                <small id="help_media_description"
                                       class="text-muted">
                                    {{$t('form.description.media_description',[form.media_description])}}
                                </small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="media_caption"
                                   class="col-md-3 col-form-label">{{$t('db.media_caption')}}</label>
                            <div class="col-md-9">
                                <textarea v-model="form.fields.media_caption"
                                          name="media_caption" id="media_caption" class="form-control txtarea-noresize"
                                          :class="{ 'is-invalid': form.errors.has('media_caption') }"
                                          :placeholder="$t('db.media_caption')"
                                          aria-describedby="help_media_caption"
                                          rows="4"
                                          @change="changedField('media_caption')"></textarea>
                                <has-error :form="form" field="media_caption"></has-error>
                                <small id="help_media_caption"
                                       class="text-muted">{{$t('form.description.media_caption',[form.media_caption])}}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center mb-4 mt-2">
                        <div>
                            <submit-button class="align-content-center"
                                      :loading="form.busy">{{ $t('general.update') }}
                            </submit-button>
                            <button type="button"
                                    class="btn btn-secondary"
                                    @click="$router.go(-1)">{{$t('general.cancel')}}
                            </button>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div>
                            <record-paginator
                                    :nav="nav"
                                    :is-loading="ajaxIsLoading"
                                    route-name="admin.media.edit"
                                    route-param-name="media"></record-paginator>
                        </div>
                    </div>
                </b-tab>
                <b-tab :title="$t('general.crop')" @click="cropperActive=true">
                    <cropper :cropper-active="cropperActive" :container-width="containerWidth"
                             :src="getImageUrl(form.media_uuid, null, form.media_extension)">
                        <template #cropper-actions>
                            <div class="col align-self-center">
                                <button class="btn btn-primary" @click="crop()" type="button"
                                >{{$t('media.cropper_crop_upload')}}
                                </button>
                                <button class="btn btn-primary btn-light" @click="cancel()" type="button"
                                >{{$t('general.cancel')}}
                                </button>
                            </div>
                        </template>
                    </cropper>
                </b-tab>
            </b-tabs>
        </form>
    </b-card>
</template>
<script>
  import Vue from 'vue'
  import SubmitButton from 'back_path/components/SubmitButton'
  import axios from 'axios'
  import Cropper from 'back_path/components/Cropper'
  import { Form, HasError, AlertForm } from 'back_path/components/form'
  import { Card, Tabs } from 'bootstrap-vue/es/components'
  import RecordPaginator from 'back_path/components/RecordPaginator'
  import MediaMixin from 'back_path/mixins/media'
  import FormMixin from 'back_path/mixins/form'

  Vue.use(Card)
  Vue.use(Tabs)

  export default {
    layout: 'basic',
    middleware: 'check-auth',
    name: 'media-edit',
    components: {
      SubmitButton,
      HasError,
      Form,
      RecordPaginator,
      Cropper
    },
    mixins: [
      MediaMixin,
      FormMixin
    ],
    data () {
      return {
        form: new Form(),
        mediaInfo: {},
        nav: {},
        type: null,
        media: null,
        ajaxIsLoading: false,
        containerWidth: 0,
        cropperActive: false
      }
    },
    mounted () {
      this.containerWidth = this.$refs.cropperContainer.clientWidth
    },
    watch: {
      '$route' () {
        this.ajaxIsLoading = true
        axios.get(`/ajax/admin/media/${this.$router.currentRoute.params.media}`).then(({data}) => {
          this.getInfo(data)
          this.ajaxIsLoading = false
        })
      }
    },
    methods: {
      async save () {
          await this.form.patch(`/ajax/admin/media/${this.form.fields.media_uuid}`)
          this.$store.dispatch(
            'session/setFlashMessage',
            {msg: {type: 'success', text: this.$t('message.media_update_ok')}}
          )
          this.$router.go(-1)
      },
      getInfo (data) {
        this.form = new Form(data.media, true)
        this.media = this.form.fields.media
        this.type = this.form.fields.type
        this.nav = data.nav
      }
    },
    beforeRouteEnter (to, from, next) {
      axios.get(`/ajax/admin/media/${to.params.media}`).then(({data}) => {
        next(vm => vm.getInfo(data))
      })
    }
  }
</script>