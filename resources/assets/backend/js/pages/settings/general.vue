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
                <input type="checkbox" name="robots"
                       class="custom-control-input" id="chk-robots"
                       v-model="form.fields.robots">
                <label class="custom-control-label" for="chk-robots">{{ $t('pages.settings.allow_robots') }}</label>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-3 col-form-label text-md-right"></label>
            <div class="col-md-8">
              <div class="custom-control custom-switch">
                <input type="checkbox" name="jsonld"
                       class="custom-control-input" value="" id="chk-json-ld"
                       v-model="form.fields.jsonld">
                <label class="custom-control-label" for="chk-json-ld">{{ $t('pages.settings.jsonld_on') }}</label>
              </div>
            </div>
          </div>
          <div class="row form-group ">
            <label for="input-url" class="col-md-3 col-form-label text-md-right">{{ $t('pages.settings.entity_url')
              }}</label>
            <div class="col-md-8">
              <input type="text" class="form-control" name="entity_url"
                     id="input-url" autocomplete="off"
                     v-model="form.fields.entity.url">
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
                <label for="input-name" class="col-md-3 col-form-label text-md-right">{{
                  $t('pages.settings.entity_name') }}</label>
                <div class="col-md-8">
                  <input type="text" class="form-control" name="entity_name"
                         id="input-name" autocomplete="off"
                         v-model="form.fields.entity.name">
                </div>
              </div>
            </div>
          </div>
          <div v-else class="form-group row">
            <div class="container">
              <div class="row form-group ">
                <label class="col-md-3 col-form-label text-md-right">{{ $t('pages.settings.entity_logo')
                  }}</label>
                <div class="col-md-8">
                  <div class="custom-file">
                    <input type="file" class="custom-file-input" accept="image/*" name="entity_logo"
                           id="input-logo" ref="orgLogoFile" @change="showLogo">
                    <label class="custom-file-label" for="input-logo">{{$t('general.choose_file')}}</label>
                  </div>
                  <div>
                    <img v-if="organizationLogo!==null" :src="organizationLogo" class="w-50 mt-3 image-fluid img-thumbnail rounded mx-auto d-block">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <p class="font-italic">{{ $t('pages.settings.entity_social_help') }}</p>
          <div v-for="(field, idx) in form.fields.links" class="form-group row" :key="'links'+idx">
            <label class="col-md-3 col-form-label text-md-right"></label>
            <div class="col-md-8 form-inline">
              <span class="w-75 mr-2">{{field}}</span>
              <button type="button" class="btn btn-danger" @click="delLink(idx)"><i class="fa fa-minus"></i></button>
            </div>
          </div>
          <div class="form-group row">
            <label for="input-link" class="col-md-3 col-form-label text-md-right">{{ $t('pages.settings.social_url')
              }}</label>
            <div class="col-md-8 form-inline">
              <input type="text" class="form-control w-75 mr-2" id="input-link" ref="inputLink" autocomplete="off">
              <button type="button" class="btn btn-info" @click="addLink"><i class="fa fa-plus"></i></button>
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
  import ButtonGroup from 'back_path/components/ButtonGroup'

  export default {
    name: 'website-general',
    components: {
      Form,
      ButtonGroup
    },
    data () {
      return {
        form: new Form({
          entity_type: 'person',
          entity: {},
          links: []
        }),
        entityType: {
          person: this.$t('pages.settings.entity_person'),
          organization: this.$t('pages.settings.entity_organization')
        },
        organizationLogo: null
      }
    },
    methods: {
      addLink () {
        let link = this.$refs.inputLink.value
        if (link) {
          this.form.fields.links.push(link)
          this.$refs.inputLink.value = ''
          this.$refs.inputLink.focus()
        }
      },
      delLink (index) {
        this.form.fields.links.splice(index, 1)
      },
      showLogo (e) {
        if (!e.target.files.length) return
        let file = e.target.files[0]
        let reader = new FileReader()
        reader.readAsDataURL(file)
        reader.onload = e => {
          this.organizationLogo = e.target.result
        }
        this.form.fields.entity.logo = file
      },
      changeEntity (entity) {
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