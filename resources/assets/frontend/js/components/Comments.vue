<template>
  <div id="comments-wrapper" class="container">
    <template v-if="scrolledPast">
      <div class="row" v-if="loaded">
        <div id="comments-header" class="col-lg-8 col-sm-12 mx-md-auto">
          <div class="w-100">
            <h5 class="bordered"><span>{{$t('comments.discussion')}}</span></h5>
            <div class="button-group" v-if="userIsAuthenticated">
              <submit-button :native-type="'button'" v-if="userIsAuthenticated"
                             :type="'success'"
                             :loading="refreshLoading"
                             @click="refreshComments"><i class="fa fa-refresh"></i> {{$t('comments.refresh')}}
              </submit-button>
              <button type="button" :disabled="!commentIsOK"
                      class="btn btn-primary"
                      @click="editMode=true"><i class="fa fa-reply fa-rotate-180"></i> {{$t('comments.comment')}}
              </button>
            </div>
            <div class="button-group" v-else>
              <button type="button"
                      class="btn btn-primary"
                      @click="login"><i class="fa fa-reply fa-rotate-180"></i> {{$t('comments.login_comment')}}
              </button>
            </div>
          </div>
        </div>
      </div>
      <transition name="fade">
        <div v-show="editMode" class="row">
          <div class="col-lg-8 col-sm-12 mx-md-auto mb-3">
            <comment-editor :is-root-elem="true"
                            :edit-mode="editMode"
                            :slug="slug"
                            @cancelled="editMode = false"
                            @submitted="editMode = false"></comment-editor>
          </div>
        </div>
      </transition>
      <div class="row">
        <div class="col-lg-8 col-sm-12 mx-md-auto">
          <div v-if="!loaded" class="fa-3x">
            <i class="fa fa-refresh fa-pulse"></i>
          </div>
          <comment-list :comments="comments" :auth-check="userIsAuthenticated"></comment-list>
        </div>
      </div>
    </template>
  </div>
</template>
<script>
  import SubmitButton from 'back_path/components/SubmitButton'
  import initIntersection from 'front_path/plugins/vendor/intersection.js'
  import axios from 'axios'

  export default {
    name: 'comments',
    props: {
      slug: {
        required: true,
        type: String
      }
    },
    data () {
      return {
        scrolledPast: true,
        loaded: false,
        io: null,
        container: null,
        editMode: false,
        comments: null,
        favorites: null,
        refreshLoading: false,
        refreshIsOK: true,
        commentIsOK: true,
        refreshDelay: 5000,
        commentDelay: 120000,
        userIsAuthenticated: false
      }
    },
    components: {
      CommentList: () => import('front_path/components/CommentList'),
      CommentEditor: () => import('front_path/components/CommentEditor'),
      SubmitButton
    },
    mounted () {
      if (window.hasOwnProperty('config')) {
        if (window.config.hasOwnProperty('auth_check')) {
          this.userIsAuthenticated = window.config.auth_check
        }
      }
      this.getData()
      let vm = this
      this.$root.$on('tiptapIsMounted', () => {
        vm.loaded = true
      })
      this.$root.$on('commentFavorited', (comment) => {
        axios.patch(`/ajax/forum/blog_posts/${vm.slug}/favorite/${comment.slug}`)
      })
      this.$root.$on('commentUnfavorited', (comment) => {
        axios.delete(`/ajax/forum/blog_posts/${vm.slug}/favorite/${comment.slug}`)
      })
      this.$root.$on('commentSubmitted', () => {
          this.commentIsOK=false
          this.commentSetDelay()
      })
      this.$root.$on('commentDeleted', async ({slug}) => {
        await axios.delete(`/ajax/forum/blog_posts/${vm.slug}/comment/${slug}`)
      })
      // if (!window.IntersectionObserver) {
      //   initIntersection(window, document)
      // }
      // this.container = this.$el
      // this.io = new IntersectionObserver(([entry]) => {
      //   if (entry.isIntersecting) {
      //     this.scrolledPast = true
      //     this.io.unobserve(this.container)
      //   }
      // })
      // this.io.observe(this.container)
    },
    methods: {
      login(){

      },
      async refreshSetDelay () {
        let vm = this
        setTimeout(async function () {
          vm.refreshIsOK = true
        }, this.refreshDelay)
      },
      async commentSetDelay () {
        let vm = this
        setTimeout(async function () {
          vm.commentIsOK = true
        }, this.commentDelay)
      },
      async refreshComments () {
        if (this.refreshIsOK) {
          this.refreshLoading = true
          await this.getData()
          this.refreshLoading = false
          this.refreshIsOK = false
          this.refreshSetDelay()
        }
      },
      async getData () {
        const {data} = await axios.get(`/ajax/forum/blog_posts/${this.slug}/comment`)
        this.comments = data.posts
      }
    },
    beforeDestroy () {
      this.$root.$off('tiptapIsMounted')
      this.$root.$off('commentSubmitted')
      this.$root.$off('commentDeleted')
      this.$root.$off('commentFavorited')
      this.$root.$off('commentUnfavorited')
    }
  }
</script>