<template>
    <div class="container">

        <div class="col-md-4">
            <div class="small-box">
                <div class="inner">
                    <h3>{{info.users}}</h3>

                    <p>{{$tc('db.users',2)}}</p>
                </div>
                <div class="icon">
                    <fa icon="user"></fa>
                </div>
                <router-link :to="{ name: 'admin.users.index' }" class="nav-link small-box-footer"
                             active-class="active">
                    <span class="sdffsd">More info <i class="fa fa-arrow-circle-right"></i></span>
                </router-link>
            </div>
        </div>
    </div>
</template>
<script>
  import axios from 'axios'

  export default {
    middleware: 'check-auth',
    layout: 'basic',
    name: 'dashboard',
    data () {
      return {
        info: {}
      }
    },
    computed: {
      counts () {
        return this.info
      }
    },
    methods: {
      getInfo(data)
      {
        this.info = data
      }
    },
    beforeRouteEnter (to, from, next) {
      axios.get(`/ajax/admin/dashboard`).then(({data}) => {
        next(vm => vm.getInfo(data, false))
      })
    }
  }
</script>