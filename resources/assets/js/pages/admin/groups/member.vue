<template>
    <div class="card">
        <div class="card-body">
            <div class="card">
                <div class="card-header">
                    Add members
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="input-tag-container col-md-8">
                            <input-tag :typeahead="true" :elementId="'full_name'" :placeholder="$t('')"></input-tag>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  import InputTag from '~/components/InputTag'

  export default {
    name: 'member',
    layout: 'basic',
    middleware: 'check-auth',
    components: {
      InputTag
    },
    data () {
      return {}
    }
  }
</script>