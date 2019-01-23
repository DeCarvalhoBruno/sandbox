<template>
    <b-card no-body>
        <form @submit.prevent="save">
            <b-tabs card>
                <b-tab :title="$t('breadcrumb.admin-groups-add')">
                    <div class="col-md-8 offset-md-2">
                        <div class="form-group row">
                            <label for="group_name"
                                   class="col-md-3 col-form-label">{{$t('db.group_name')}}</label>
                            <div class="col-md-9">
                                <input v-model="form.group_name" type="text"
                                       name="group_name" id="group_name" class="form-control"
                                       :class="{ 'is-invalid': form.errors.has('group_name') }"
                                       :placeholder="$t('db.group_name')"
                                       aria-describedby="help_group_name">
                                <has-error :form="form" field="group_name"></has-error>
                                <small id="help_group_name" class="text-muted">
                                    {{$t('form.description.group_name',[form.group_name])}}
                                </small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="group_mask" class="col-md-3 col-form-label">{{$t('db.group_mask')}}</label>
                            <div class="col-md-9">
                                <input v-model="form.group_mask" type="text"
                                       name="group_mask" id="group_mask" class="form-control"
                                       :class="{ 'is-invalid': form.errors.has('group_mask') }"
                                       :placeholder="$t('db.group_mask')"
                                       aria-describedby="help_group_mask">
                                <has-error :form="form" field="group_mask"></has-error>
                                <small id="help_group_mask" class="text-muted">{{$t('form.description.group_mask')}}
                                </small>
                            </div>
                        </div>
                    </div>
                </b-tab>
                <b-tab :title="$tc('general.permission',2)">
                    <div class="container">
                        <div class="callout callout-info">
                            <p>{{$t('pages.groups.info1')}}</p>
                            <p>{{$t('pages.groups.info2')}}</p>
                        </div>
                        <hr>
                        <div>
                            <div class="card mb-2" v-for="(permissionSet,entity) in permissions.default" :key="entity">
                                <div class="card-header">{{entity}}</div>
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
                                                        :hasPermission="hasPermission(permissions.computed,entity,type)">
                                                </button-circle>
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
                        {{ $t('general.create') }}
                    </v-button>
                </div>
            </div>
        </form>
    </b-card>
</template>

<script>
  import Vue from 'vue'
  import axios from 'axios'
  import { Card, Tabs } from 'bootstrap-vue/es/components'

  Vue.use(Card)
  Vue.use(Tabs)

  import Button from '~/components/Button'
  import Checkbox from '~/components/Checkbox'
  import { Form, HasError, AlertForm } from '~/components/form'
  import ButtonCircle from '~/components/ButtonCircle'
  import PermissionMixin from '~/mixins/permissions'

  export default {
    layout: 'basic',
    middleware: 'check-auth',
    name: 'group-edit',
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
        form: new Form({
          group_name:'',
          group_mask:''
        }),
        permissions: {}
      }
    },
    mixins: [PermissionMixin],
    methods: {
      getInfo (data) {
        this.permissions = data.permissions
      },
      async save () {
        try {
          this.form.addField('permissions', this.getPermissions(this.$refs.buttonCircle))
          const {data} = await this.form.post(`/ajax/admin/groups`)
          this.$router.push({name: 'admin.groups.index'})
          this.$store.dispatch('session/setAlertMessageSuccess', this.$t('message.group_create_ok'))
        } catch (e) {}
      }
    },
    beforeRouteEnter (to, from, next) {
      axios.get(`/ajax/admin/groups/create`).then(({data}) => {
        next(vm => vm.getInfo(data))
      })
    }
  }
</script>