<template>
    <b-card no-body>
        <form @submit.prevent="save" @keydown="form.onKeydown($event)">
            <b-tabs card>
                <b-tab :title="form.group_name">
                    <div class="col-md-8 offset-md-2">
                        <div class="form-group row">
                            <label for="new_group_name"
                                   class="col-md-3 col-form-label">{{$t('db.new_group_name')}}</label>
                            <div class="col-md-9">
                                <input v-model="form.new_group_name" type="text"
                                       name="new_group_name" id="new_group_name" class="form-control"
                                       :class="{ 'is-invalid': form.errors.has('new_group_name') }"
                                       :placeholder="$t('db.new_group_name')"
                                       aria-describedby="help_new_group_name">
                                <has-error :form="form" field="new_group_name"></has-error>
                                <small id="help_new_group_name" class="text-muted">
                                    {{$t('form.description.new_group_name',[form.group_name])}}
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
                            <div class="card mb-2" v-for="(permissionSet,entity) in permissions.default"
                                 :key="entity">
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
                                                        :hasPermission="hasPermission(permissions.computed,entity,type)"
                                                >
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
                        {{ $t('general.update') }}
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
        form: new Form(),
        group: this.$router.currentRoute.params.group,
        permissions: {}
      }
    },
    mixins: [PermissionMixin],
    methods: {
      getInfo (data) {
        this.form = new Form(data.group)
        this.permissions = data.permissions
      },
      async save () {
        try {
          this.form.addField('permissions', this.getPermissions(this.$refs.buttonCircle))
          await this.form.patch(`/ajax/admin/groups/${this.group}`)
          this.$store.dispatch(
            'session/setFlashMessage',
            {msg: {type: 'success', text: this.$t('message.group_update_ok')}}
          )
          this.$router.push({name: 'admin.groups.index'})
        } catch (e) {}
      }
    },
    beforeRouteEnter (to, from, next) {
      axios.get(`/ajax/admin/groups/${to.params.group}`).then(({data}) => {
        next(vm => vm.getInfo(data))
      })
    }
  }
</script>