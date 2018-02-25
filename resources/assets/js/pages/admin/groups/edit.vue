<template>
    <div class="card">
        <div class="card-body">
            <div class="col-md-8 offset-md-2">
                <form @submit.prevent="save" @keydown="form.onKeydown($event)">

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
          const {data} = await this.form.patch(`/ajax/admin/group/${this.group}/update`)
        } catch (e) {

        }
        this.$store.dispatch('session/setMessage',this.$t('message.group_update_ok'))
        this.$router.push({name: 'admin.group.index'})
      }
    },
    beforeRouteEnter (to, from, next) {
      axios.get(`/ajax/admin/group/${to.params.group}`).then(({data})=>{
        next(vm => vm.getInfo(data))
      })
    },
  }
</script>