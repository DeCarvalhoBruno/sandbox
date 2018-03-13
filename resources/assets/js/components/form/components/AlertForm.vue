<template>
    <div>
        <b-alert v-if="form.errors.any()" :show="true" variant="danger">
            <div v-if="message" v-html="message"></div>
            <div v-else>{{$t('error.form')}}</div>
            <ul v-if="showErrors">
                <li v-for="error in form.errors.flatten()" v-html="error"></li>
            </ul>
        </b-alert>
        <b-alert v-else-if="form.successful&&message!=null" :show="true" variant="success">
            <div v-html="message"></div>
        </b-alert>
    </div>
</template>

<script>
  import Vue from 'vue'
  import { Alert } from 'bootstrap-vue/es/components'
  Vue.use(Alert)

  export default {
    name: 'alert-form',
    components: {
      Alert
    },
    props: {
      form: {
        type: Object,
        required: true
      },
      alertMessage: {
        type: String,
        required: false,
        default: null
      },
      showErrors: {
        type: Boolean,
        required: false,
        default: false
      }
    }
  }
</script>
