<template>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{$t('pages.members.group_name')}}&nbsp;{{this.$route.params.group}}</h5>
            <div class="card mb-3">
                <div class="card-header">
                    {{$t('pages.members.edit_preview')}}
                </div>
                <div class="card-body">
                    <div class="row" v-if="removedUsers.length>0||addedUsers.length>0">
                        <div class="col-md">
                            <div class="row">
                                <p>{{$t('pages.members.user_add_tag')}}</p>
                            </div>
                            <div class="row">
                                <ul v-if="addedUsers.length>0" class="list-group">
                                    <li v-for="addedUser in addedUsers"
                                        class="list-group-item list-group-item-action list-group-item-success">
                                        {{addedUser.text}}
                                    </li>
                                </ul>
                                <p v-else>{{$t('pages.members.user_no_add')}}</p>
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="row">
                                <p>{{$t('pages.members.user_remove_tag')}}</p>
                            </div>
                            <ul v-if="removedUsers.length>0" class="list-group">
                                <li v-for="removedUser in removedUsers"
                                    class="list-group-item list-group-item-action list-group-item-danger">
                                    {{removedUser.text}}
                                </li>
                            </ul>
                            <p v-else>{{$t('pages.members.user_no_remove')}}</p>
                        </div>
                    </div>
                    <p v-else>{{$t('pages.members.no_changes')}}</p>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header">
                    {{$t('pages.members.add_members')}}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="search-spinner-wrapper">
                            <fa icon="cog" size="lg" :spin="isAnimated"/>
                        </div>
                        <div class="input-tag-container col-md-8">
                            <input-tag :typeahead="true"
                                       :elementId="'full_name'"
                                       :placeholder="$t('pages.members.member_search')"
                                       @searching="searching"
                                       @searched="searched"
                                       @updateAddedUsers="updateAddedUsers"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header">
                    {{$t('pages.members.remove_members')}}
                </div>
                <div class="card-body">
                    <div v-if="members.length>0">
                        <div class="container row">
                            <p>{{$t('pages.members.current_members')}}</p>
                        </div>
                        <div class="container row">
                            <ul class="list-group">
                                <li v-for="member in members" class="list-group-item list-group-item-action">
                                    {{member.full_name}}
                                </li>
                            </ul>
                        </div>
                    </div>
                    <p v-else>{{$t('pages.members.user_none')}}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  import InputTag from '~/components/InputTag'
  import axios from 'axios'

  export default {
    name: 'member',
    layout: 'basic',
    middleware: 'check-auth',
    components: {
      InputTag
    },
    data () {
      return {
        isAnimated: false,
        addedUsers: [],
        removedUsers: [],
        members: []
      }
    },
    methods: {
      searching () {
        this.isAnimated = true
      },
      searched () {
        this.isAnimated = false
      },
      updateAddedUsers (users) {
        this.addedUsers = users
      },
      getInfo (data) {
        this.members = data
      }
    },
    beforeRouteEnter (to, from, next) {
      axios.get(`/ajax/admin/groups/${to.params.group}/members`).then(({data}) => {
        next(vm => vm.getInfo(data))
      })
    }
  }
</script>