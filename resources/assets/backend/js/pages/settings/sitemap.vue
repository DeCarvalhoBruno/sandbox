<template>
  <div class="container p-0 m-0">
    <div class="row p-0 m-0">
      <div class="col p-0 m-0">
        <form @submit.prevent="save">
          <div class="form-group row">
            <label class="col-md-3 col-form-label text-md-right"></label>
            <div class="col-md-8">
              <div class="custom-control custom-switch">
                <input type="checkbox" name="sitemap"
                       class="custom-control-input" id="chk-sitemap"
                       v-model="form.fields.sitemap">
                <label class="custom-control-label" for="chk-sitemap">{{ $t('pages.settings.enable_sitemap')
                  }}</label>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-3 col-form-label text-md-right"></label>
            <div class="col-md-8">


              ADD LINKS MANUALLY




            </div>
          </div>
          <div class="form-group row">
            <div class="col-md-9 ml-md-auto">
              <submit-button :loading="form.busy">{{ $t('general.update') }}</submit-button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from 'axios'
  import { Form, HasError, AlertForm } from 'back_path/components/form'
  import SubmitButton from 'back_path/components/SubmitButton'
  import swal from 'back_path/mixins/sweet-alert'

  export default {
    name: 'settings-social',
    components: {
      Form,
      SubmitButton
    },
    mixins: [
      swal
    ],
    data () {
      return {
        form: new Form({
          sitemap:null
        })
      }
    },
    methods: {
      async save () {
        await this.form.post('/ajax/admin/settings/social')
        this.swalNotification('success', this.$t('message.settings_updated'))
      },
      getInfo (data) {
        if (data.settings != null) {
          this.form = new Form(data.settings)
        }
      }
    },
    metaInfo () {
      return {title: this.$t('title.settings_sitemap')}
    },
    beforeRouteEnter (to, from, next) {
      axios.get('/ajax/admin/settings/social').then(({data}) => {
        next(vm => vm.getInfo(data))
      })
    }
  }
</script>