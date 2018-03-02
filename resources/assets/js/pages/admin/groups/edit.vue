<template>
    <div class="card">
        <div class="card-body">
            <div class="col-md-8 offset-md-2">
                <form @submit.prevent="save" @keydown="form.onKeydown($event)">
                    <div class="form-group row">
                        <label for="group_name" class="col-md-3 col-form-label">{{$t('db.group_name')}}</label>
                        <div class="col-md-9">
                            <input v-model="form.group_name" type="text"
                                   name="group_name" id="group_name" class="form-control" :class="{ 'is-invalid': form.errors.has('group_name') }"
                                   :placeholder="$t('db.group_name')"
                                   aria-describedby="help_group_name">
                            <has-error :form="form" field="group_name"/>
                            <small id="help_group_name" class="text-muted">{{$t('form.description.group_name')}}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="group_mask" class="col-md-3 col-form-label">{{$t('db.group_mask')}}</label>
                        <div class="col-md-9">
                            <input v-model="form.group_mask" type="text"
                                   name="group_mask" id="group_mask" class="form-control" :class="{ 'is-invalid': form.errors.has('group_mask') }"
                                   :placeholder="$t('db.group_mask')"
                                   aria-describedby="help_group_mask">
                            <has-error :form="form" field="group_mask"/>
                            <small id="help_group_mask" class="text-muted">{{$t('form.description.group_mask')}}</small>
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
    name: 'group-edit',
    components: {
      'v-button': Button,
      Checkbox,
      HasError,
      AlertForm
    },
    data () {
      return {
        form: new Form(),
        group: this.$router.currentRoute.params.group,
      }
    },
    methods: {
      getInfo (data) {
        this.form = new Form(data)
      },
      async save () {
        try {
          const {data} = await this.form.patch(`/ajax/admin/groups/${this.group}`)
          this.$store.dispatch('session/setMessageSuccess',this.$t('message.group_update_ok'))
          this.$router.push({name: 'admin.groups.index'})
        } catch (e) {

        }

      }
    },
    beforeRouteEnter (to, from, next) {
      axios.get(`/ajax/admin/groups/${to.params.group}`).then(({data})=>{
        next(vm => vm.getInfo(data))
      })
    },
  }
</script>