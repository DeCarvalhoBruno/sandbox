<template>
  <div id="comments-wrapper" class="container">
    <template v-if="scrolledPast">
      <div class="row" v-if="loaded">
        <div id="comments-header" class="col-lg-8 mx-md-auto">
          <div class="w-100">
            <h5 class="bordered"><span>Discussion</span></h5>
            <div class="button-group">
              <submit-button :native-type="'button'"
                             :type="'success'"
                             :loading="refreshLoading"
                             @click="refreshComments"><i class="fa fa-refresh"></i> Refresh
              </submit-button>
              <button type="button"
                      class="btn btn-primary"
                      @click="editMode=true"><i class="fa fa-reply fa-rotate-180"></i> Comment
              </button>
            </div>
          </div>
        </div>
      </div>
      <div v-show="editMode" class="row">
        <div class="col-lg-8 mx-md-auto">
          <comment-editor :is-root-elem="true"
                          :edit-mode="editMode"
                          :slug="slug"
                          @cancelled="editMode = false"
                          @submitted="editMode = false"></comment-editor>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-8 mx-md-auto">
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
  import swal from 'sweetalert2/dist/sweetalert2.min.js'
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
        refreshTriggerDelay: 2000,
        userIsAuthenticated:false
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
      this.$root.$on('commentFavorited', (comment) => {
        axios.patch(`/ajax/forum/blog_posts/${vm.slug}/favorite/${comment.slug}`)
      })
      this.$root.$on('commentUnfavorited', (comment) => {
        axios.delete(`/ajax/forum/blog_posts/${vm.slug}/favorite/${comment.slug}`)
      })
      this.$root.$on('tiptapIsMounted', () => {
        vm.loaded = true
      })
      this.$root.$on('submitComment', async ({comment}) => {
        let response
        if (comment.mode === 'create') {
          response = await axios.post(`/ajax/forum/blog_posts/${vm.slug}/comment`, comment)
        } else {
          response = await axios.patch(`/ajax/forum/blog_posts/${vm.slug}/comment`, comment)
        }
        swal.fire({
          type: response.data.type,
          title: response.data.title,
          position: 'top-end',
          toast: true,
          showConfirmButton: false,
          timer: 8000
        })
      })
      this.$root.$on('deleteComment', async ({slug}) => {
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
      async refreshSetDelay () {
        let vm = this
        setTimeout(async function () {
          vm.refreshIsOK = true
        }, this.refreshTriggerDelay)
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
      this.$root.$off('submitComment')
      this.$root.$off('deleteComment')
      this.$root.$off('commentFavorited')
      this.$root.$off('commentUnfavorited')
    }
  }
</script>