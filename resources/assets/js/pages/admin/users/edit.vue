<template>
    <div class="card">
        <div class="card-body">
            <div class="col-md-8 offset-md-2">
                <form @submit.prevent="save" @keydown="form.onKeydown($event)">
                    <div class="form-group row">
                        <label for="new_username" class="col-md-3 col-form-label">{{$t('db.new_username')}}</label>
                        <div class="col-md-9">
                            <input v-model="form.new_username" type="text"
                                   name="new_username" id="new_username" class="form-control" :class="{ 'is-invalid': form.errors.has('new_username') }"
                                   :placeholder="$t('db.new_username')"
                                   aria-describedby="help_new_username">
                            <has-error :form="form" field="new_username"/>
                            <small id="help_new_username" class="text-muted">{{$t('form.description.new_username',[form.username])}}</small>
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
                            <small id="help_first_name" class="text-muted">{{$t('form.description.first_name')}}</small>
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
                            <small id="help_last_name" class="text-muted">{{$t('form.description.last_name')}}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="new_email" class="col-md-3 col-form-label">{{$t('db.new_email')}}</label>
                        <div class="col-md-9">
                            <input v-model="form.new_email" type="text"
                                   name="new_email" id="new_email" class="form-control" :class="{ 'is-invalid': form.errors.has('new_email') }"
                                   :placeholder="$t('db.new_email')"
                                   aria-describedby="help_new_email">
                            <has-error :form="form" field="new_email"/>
                            <small id="help_new_email" class="text-muted">{{$t('form.description.new_email',[form.email])}}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6 offset-md-3  mt-4">
                            <v-button :loading="form.busy">
                                {{ $t('general.update') }}
                            </v-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
  import Button from '~/components/Button'
  import Checkbox from '~/components/Checkbox'
  import { Form, HasError, AlertForm } from '~/components/form'
  import axios from 'axios'

  export default {
    layout: 'basic',
    middleware: 'check-auth',
    name: 'user-edit',
    components: {
      'v-button': Button,
      Checkbox,
      HasError,
      AlertForm
    },
    data () {
      return {
        form: new Form(),
        user_id: this.$router.currentRoute.params.user_id,
        userInfo: null
      }
    },
    methods: {
      getUserInfo (data) {
        this.form = new Form(data)
        // this.form.fill(data)
      },
      async save () {
        try {
          const {data} = await this.form.patch(`/ajax/admin/user/${this.user_id}/update`)
        } catch (e) {

        }
        this.$store.dispatch('session/setMessage',this.$t('message.user_update_ok'))
        this.$router.push({name: 'admin.user.index'})
      }
    },
    beforeRouteEnter (to, from, next) {
      axios.get(`/ajax/admin/user/${to.params.user_id}`).then(({data})=>{
        next(vm => vm.getUserInfo(data))
      })
    },
  }
</script>