<template>
  <div class="card">
    <form @submit.prevent="save" @keydown="form.onKeydown($event)">
      <b-tabs card lazy>
        <b-tab :title="form.fields.full_name" active>
          <div class="col-md-8 offset-md-2">
            <div v-if="mediaData" class="form-group row justify-content-center">
              <img :src="getImageUrl(mediaData.uuid,null,mediaData.ext)"/>
            </div>
            <div class="form-group row">
              <label for="new_username"
                     class="col-md-3 col-form-label"
                     :class="{ 'is-invalid': form.errors.has('new_username') }"
              >{{$t('db.new_username')}}</label>
              <div class="col-md-9">
                <input v-model="form.fields.new_username" type="text" autocomplete="off"
                       name="new_username" id="new_username" class="form-control"
                       :class="{ 'is-invalid': form.errors.has('new_username') }"
                       :placeholder="$t('db.new_username')"
                       maxlength="25"
                       aria-describedby="help_new_username"
                       @change="changedField('new_username')">
                <has-error :form="form" field="new_username"></has-error>
                <small id="help_new_username" class="text-muted">
                  {{$t('form.description.new_username',[form.fields.username])}}
                </small>
              </div>
            </div>
            <div class="form-group row">
              <label for="first_name" class="col-md-3 col-form-label">{{$t('db.first_name')}}</label>
              <div class="col-md-9">
                <input v-model="form.fields.first_name" type="text" autocomplete="off"
                       name="first_name" id="first_name" class="form-control"
                       :class="{ 'is-invalid': form.errors.has('first_name') }"
                       :placeholder="$t('db.first_name')"
                       aria-describedby="help_first_name"
                       @change="changedField('first_name')">
                <has-error :form="form" field="first_name"></has-error>
                <small id="help_first_name" class="text-muted">{{$t('form.description.first_name')}}
                </small>
              </div>
            </div>
            <div class="form-group row">
              <label for="last_name" class="col-md-3 col-form-label">{{$t('db.last_name')}}</label>
              <div class="col-md-9">
                <input v-model="form.fields.last_name" type="text" autocomplete="off"
                       name="last_name" id="last_name" class="form-control"
                       :class="{ 'is-invalid': form.errors.has('last_name') }"
                       :placeholder="$t('db.last_name')"
                       aria-describedby="help_last_name"
                       @change="changedField('last_name')">
                <has-error :form="form" field="last_name"></has-error>
                <small id="help_last_name" class="text-muted">{{$t('form.description.last_name')}}
                </small>
              </div>
            </div>
            <div class="form-group row">
              <label for="new_email" class="col-md-3 col-form-label">{{$t('db.new_email')}}</label>
              <div class="col-md-9">
                <input v-model="form.fields.new_email" type="text" autocomplete="off"
                       name="new_email" id="new_email" class="form-control"
                       :class="{ 'is-invalid': form.errors.has('new_email') }"
                       :placeholder="$t('db.new_email')"
                       aria-describedby="help_new_email"
                       @change="changedField('new_email')">
                <has-error :form="form" field="new_email"></has-error>
                <small id="help_new_email" class="text-muted">
                  {{$t('form.description.new_email',[form.fields.email])}}
                </small>
              </div>
            </div>
          </div>
        </b-tab>
        <b-tab :title="$tc('general.permission',2)" :disabled="checkPermissions()">
          <div class="container">
            <div class="callout callout-warning">
              <p><span class="callout-tag callout-tag-warning">
                <i class="fa fa-exclamation"></i>
              </span>
                &nbsp;{{$t('pages.users.warning1')}}
              </p>
              <p>
                {{$t('pages.users.warning2')}}
              </p>
            </div>
            <div>
              <div class="card mb-2" v-for="(permissionSet,entity) in permissions.default" :key="entity">
                <div class="card-header">{{$tc(`db.${entity}`,2)}}</div>
                <div class="card-body">
                  <table class="table table-sm">
                    <thead>
                    <tr>
                      <th>{{$tc('general.permission',1)}}</th>
                      <th>{{$t('general.toggle')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(maskValue,type) in permissionSet" :key="entity+type">
                      <td>{{type}}</td>
                      <td>
                        <button-circle
                            ref="buttonCircle"
                            :maskval="maskValue"
                            :entity="entity"
                            :enabled="hasPermission(permissions.computed,entity,type)"
                            :hasPermission="hasPermission(permissions.computed,entity,type)"
                            @clicked="changedField('permissions')"/>
                      </td>
                    </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </b-tab>
      </b-tabs>
      <div class="row justify-content-center">
        <div class="col-md-6 offset-md-3 mb-4">
          <submit-button :loading="form.busy" :value="$t('general.update')">
          </submit-button>
          <button v-if="intended!==null"
                  type="button"
                  class="btn btn-secondary"
                  @click="redirect()">{{$t('general.cancel')}}
          </button>
        </div>
      </div>
    </form>
    <div v-if="Object.keys(nav).length>0" class="row justify-content-center">
      <div>
        <record-paginator
            :nav="nav"
            :is-loading="ajaxIsLoading"
            route-name="admin.users.edit"
            route-param-name="user"></record-paginator>
      </div>
    </div>
  </div>
</template>

<script>
  import Vue from 'vue'
  import SubmitButton from 'back_path/components/SubmitButton'
  import Checkbox from 'back_path/components/Checkbox'
  import PermissionMixin from 'back_path/mixins/permissions'
  import FormMixin from 'back_path/mixins/form'
  import MediaMixin from 'back_path/mixins/media'
  import { Form, HasError, AlertForm } from 'back_path/components/form'
  import { Tabs } from 'bootstrap-vue/es/components'
  import ButtonCircle from 'back_path/components/ButtonCircle'
  import RecordPaginator from 'back_path/components/RecordPaginator'
  import axios from 'axios'

  Vue.use(Tabs)

  export default {
    layout: 'basic',
    middleware: 'check-auth',
    name: 'user-edit',
    components: {
      SubmitButton,
      Checkbox,
      HasError,
      AlertForm,
      Tabs,
      ButtonCircle,
      RecordPaginator
    },
    data () {
      return {
        form: new Form(),
        permissions: {},
        nav: {},
        ajaxIsLoading: false,
        intended: null,
        mediaData:null,
        entity:'users',
        media:'image_avatar'
      }
    },
    mixins: [
      PermissionMixin,
      FormMixin,
      MediaMixin,
    ],
    watch: {
      '$route' () {
        this.ajaxIsLoading = true
        axios.get(`/ajax/admin/users/${this.$router.currentRoute.params.user}`).then(({data}) => {
          this.getInfo(data)
          this.ajaxIsLoading = false
        })
      }
    },
    methods: {
      redirect () {
        this.$router.push(this.intended)
      },
      getInfo (data) {
        this.form = new Form(data.user, true)
        this.permissions = data.permissions
        this.nav = data.nav
        this.mediaData = data.media
        let intended = this.$store.getters['session/intendedUrl']
        if (intended === null) {
          this.intended = {name: 'admin.users.index'}
        } else {
          this.intended = this.$store.getters['session/intendedUrl']
        }
      },
      async save () {
        this.form.addField('permissions', this.getPermissions(this.$refs.buttonCircle))
        await this.form.patch(`/ajax/admin/users/${this.$router.currentRoute.params.user}`)
        this.$store.dispatch('session/setFlashMessage',
          {msg: {type: 'success', text: this.$t('message.user_update_ok')}})
        this.$router.push({name: 'admin.users.index'})
      }
    },
    beforeRouteEnter (to, from, next) {
      axios.get(`/ajax/admin/users/${to.params.user}`).then(({data}) => {
        next(vm => vm.getInfo(data))
      })
    },
    metaInfo () {
      return {title: this.$t('title.user_edit')}
    }
  }
</script>