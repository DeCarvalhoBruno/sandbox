<template>
    <b-card no-body>
        <form @submit.prevent="save" @keydown="form.onKeydown($event)">
            <b-tabs card>
                <b-tab :title="userInfo.full_name" active>
                    <div class="col-md-8 offset-md-2">
                        <div class="form-group row">
                            <label for="new_username" class="col-md-3 col-form-label">{{$t('db.new_username')}}</label>
                            <div class="col-md-9">
                                <input v-model="form.new_username" type="text"
                                       name="new_username" id="new_username" class="form-control"
                                       :class="{ 'is-invalid': form.errors.has('new_username') }"
                                       :placeholder="$t('db.new_username')"
                                       aria-describedby="help_new_username">
                                <has-error :form="form" field="new_username"/>
                                <small id="help_new_username" class="text-muted">
                                    {{$t('form.description.new_username',[form.username])}}
                                </small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="first_name" class="col-md-3 col-form-label">{{$t('db.first_name')}}</label>
                            <div class="col-md-9">
                                <input v-model="form.first_name" type="text"
                                       name="first_name" id="first_name" class="form-control"
                                       :class="{ 'is-invalid': form.errors.has('first_name') }"
                                       :placeholder="$t('db.first_name')"
                                       aria-describedby="help_first_name">
                                <has-error :form="form" field="first_name"/>
                                <small id="help_first_name" class="text-muted">{{$t('form.description.first_name')}}
                                </small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="last_name" class="col-md-3 col-form-label">{{$t('db.last_name')}}</label>
                            <div class="col-md-9">
                                <input v-model="form.last_name" type="text"
                                       name="last_name" id="last_name" class="form-control"
                                       :class="{ 'is-invalid': form.errors.has('last_name') }"
                                       :placeholder="$t('db.last_name')"
                                       aria-describedby="help_last_name">
                                <has-error :form="form" field="last_name"/>
                                <small id="help_last_name" class="text-muted">{{$t('form.description.last_name')}}
                                </small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="new_email" class="col-md-3 col-form-label">{{$t('db.new_email')}}</label>
                            <div class="col-md-9">
                                <input v-model="form.new_email" type="text"
                                       name="new_email" id="new_email" class="form-control"
                                       :class="{ 'is-invalid': form.errors.has('new_email') }"
                                       :placeholder="$t('db.new_email')"
                                       aria-describedby="help_new_email">
                                <has-error :form="form" field="new_email"/>
                                <small id="help_new_email" class="text-muted">
                                    {{$t('form.description.new_email',[form.email])}}
                                </small>
                            </div>
                        </div>
                    </div>
                </b-tab>
                <b-tab :title="$t('general.permissions')">
                    <div class="container">
                        <div class="callout callout-warning">
                            <p><span class="callout-tag callout-tag-warning"><fa icon="exclamation"/></span> Setting
                                individual permissions for this user will override permissions set on groups of which
                                the
                                user is a member.
                            </p>
                            <p>We recommend setting permissions on groups instead, and use individual user permissions
                                to
                                handle exceptions.</p>
                        </div>
                        <div>
                            <div class="card mb-2" v-for="(permissionSet,entity) in permissions.default" :key="entity">
                                <div class="card-header">{{entity}}</div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <thead>
                                        <tr>
                                            <th>Permission</th>
                                            <th>Value</th>
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
                                                />
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
                    <v-button class="align-content-center" :loading="form.busy">
                        {{ $t('general.update') }}
                    </v-button>
                </div>
            </div>
        </form>
    </b-card>
</template>

<script>
  import Vue from 'vue'
  import Button from '~/components/Button'
  import Checkbox from '~/components/Checkbox'
  import PermissionMixin from '~/mixins/permissions'
  import { Form, HasError, AlertForm } from '~/components/form'
  import { Card, Tabs } from 'bootstrap-vue/es/components'
  import ButtonCircle from '~/components/ButtonCircle'
  import axios from 'axios'

  Vue.use(Card)
  Vue.use(Tabs)

  export default {
    layout: 'basic',
    middleware: 'check-auth',
    name: 'user-edit',
    components: {
      'v-button': Button,
      Checkbox,
      HasError,
      AlertForm,
      Card,
      Tabs,
      ButtonCircle
    },
    data () {
      return {
        form: new Form(),
        username: this.$router.currentRoute.params.user,
        userInfo: {},
        permissions: {}
      }
    },
    mixins: [PermissionMixin],
    methods: {
      getInfo (data) {
        this.form = new Form(data.user)
        this.userInfo = data.user
        this.permissions = data.permissions
      },
      async save () {
        try {
          this.form.addField('permissions', this.getPermissions(this.$refs.buttonCircle))
          const {data} = await this.form.patch(`/ajax/admin/users/${this.username}`)
          this.$store.dispatch('session/setMessageSuccess', this.$t('message.user_update_ok'))
          this.$router.push({name: 'admin.users.index'})
        } catch (e) {
        }
      }
    },
    beforeRouteEnter (to, from, next) {
      axios.get(`/ajax/admin/users/${to.params.user}`).then(({data}) => {
        next(vm => vm.getInfo(data))
      })
    }
  }
</script>