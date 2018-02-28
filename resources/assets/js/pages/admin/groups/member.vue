<template>
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                <button class="btn btn-primary float-right" :disabled="removedUsers.length===0&&addedUsers.length===0">
                    {{$t('general.save_changes')}}
                </button>
                <h5>{{$t('pages.members.group_name')}}&nbsp;{{this.$route.params.group}}
                </h5>
            </div>
            <div id="member_edit_preview" class="card">
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
                                <li v-for="(removedUser,idx) in removedUsers" :key="idx"
                                    class="list-group-item list-group-item-action list-group-item-danger">
                                    {{removedUser.text}}
                                    <i href="#" class="button-list-close"
                                       @click="returnToUsersList(removedUser,idx)"></i>
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
                            <fa icon="cog" size="lg" :spin="addIsAnimated"/>
                        </div>
                        <div class="input-tag-container col-md-8">
                            <input-tag :typeahead="true"
                                       :placeholder="$t('pages.members.member_search')"
                                       @searching="addSearching"
                                       @searched="addSearched"
                                       @updateAddedItems="updateAddedUsers"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header">
                    {{$t('pages.members.remove_members')}}
                </div>
                <div class="card-body">
                    <div class="row" v-if="this.userCount>5">
                        <div class="search-spinner-wrapper">
                            <fa icon="cog" size="lg" :spin="delIsAnimated"/>
                        </div>
                        <div class="input-tag-container col-md-8">
                            <input-tag :typeahead="true"
                                       :placeholder="$t('pages.members.member_search')"
                                       @searching="delSearching"
                                       @searched="delSearched"
                                       @updateAddedItems="updateRemovedUsers"/>
                        </div>
                    </div>
                    <div v-else-if="this.userCount>0">
                        <div class="container row">
                            <p>{{$t('pages.members.current_members')}}</p>
                        </div>
                        <div id="group_members_list" class="container row">
                            <div v-for="(member,idx) in members" :key="idx" class="card col-md-3">
                                <p>{{member.text}}
                                    <i href="#" class="button-list-close" @click="addToRemoveUsersList(member,idx)"></i>
                                </p>
                            </div>
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
        addIsAnimated: false,
        delIsAnimated: false,
        removeIsAnimated: false,
        addedUsers: [],
        removedUsers: [],
        members: [],
        userCount: 0
      }
    },
    methods: {
      addSearching () {
        this.addIsAnimated = true
      },
      addSearched () {
        this.addIsAnimated = false
      },
      delSearching () {
        this.delIsAnimated = true
      },
      delSearched () {
        this.delIsAnimated = false
      },
      updateAddedUsers (users) {
        this.addedUsers = users
      },
      updateRemovedUsers (users) {
        this.addedUsers = users
      },
      getInfo (data) {
        this.userCount = data.count
        if (data.hasOwnProperty('users')) {
          this.members = data.users
        }
      },
      addToRemoveUsersList (elem, index) {
        this.members.splice(index, 1)
        this.removedUsers.push(elem)
      },
      returnToUsersList (elem, index) {
        this.removedUsers.splice(index, 1)
        this.members.unshift(elem)
      }
    },
    beforeRouteEnter (to, from, next) {
      axios.get(`/ajax/admin/groups/${to.params.group}/members`).then(({data}) => {
        next(vm => vm.getInfo(data))
      })
    }
  }
</script>