<template>
  <div class="container p-0 m-0">
    <div class="row p-0 m-0">
      <div class="col p-0 m-0">
        <form @submit.prevent="save">
          <div class="form-group row">
            <label for="site_description" class="col-md-3 col-form-label text-md-right">{{
              $t('pages.settings.site_description') }}</label>
            <div class="col-md-8">
              <textarea v-model="form.fields.site_description"
                        name="site_description" id="site_description"
                        class="form-control" autocomplete="off"></textarea>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-3 col-form-label text-md-right"></label>
            <div class="col-md-8">
              <div class="custom-control custom-switch">
                <input type="checkbox"
                       class="custom-control-input" value="" id="chk-robots"
                       v-model="form.fields.robots">
                <label class="custom-control-label" for="chk-robots">{{ $t('pages.settings.allow_robots') }}</label>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-3 col-form-label text-md-right">{{ $t('pages.settings.entity_types') }}</label>
            <div class="col-md-8">
              <button-group @active-changed="changeEntity" :field-name="'entity-type'"
                            :active="form.fields.entity_type" :choices="entityType"></button-group>
            </div>
          </div>
          <div v-if="form.fields.entity_type==='person'" class="form-group row">
              <div class="container">
                <div class="row form-group ">
                  <label for="input-given-name" class="col-md-3 col-form-label text-md-right"></label>
                  <div class="col-md-8">
                  <input type="text" class="form-control" id="input-given-name" v-model="form.fields.entity.given-name">
                  </div>
                </div>
                <div class="row form-group ">
                  <label for="input-family-name" class="col-md-3 col-form-label text-md-right"></label>
                  <div class="col-md-8">
                  <input type="text" class="form-control" id="input-family-name" v-model="form.fields.entity.family-name">
                  </div>
                </div>
              </div>
          </div>
          <div v-else class="form-group row">

          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from 'axios'
  import { Form, HasError, AlertForm } from 'back_path/components/form'
  import ButtonGroup from 'back_path/components/ButtonGroup'

  export default {
    name: 'website-general',
    components: {
      Form,
      ButtonGroup
    },
    data () {
      return {
        form: new Form({entity_type: 'person', entity: {}}),
        entityType: {
          person: this.$t('pages.settings.entity_person'),
          organization: this.$t('pages.settings.entity_organization')
        }
      }
    },
    methods: {
      changeEntity(entity){
        this.form.fields.entity_type = entity
      },
      async save () {

      }
    },
    metaInfo () {
      return {title: ''}
    }
  }
</script>