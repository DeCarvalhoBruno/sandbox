<template>
  <div id="comments-wrapper" class="container">
    <template v-if="scrolledPast">
      <div class="row" v-if="loaded">
        <div id="comments-header" class="col-lg-8 mx-md-auto">
          <div class="w-100">
            <h5 class="bordered"><span>Discussion</span></h5>
            <button type="button"
                    class="btn btn-primary"
                    @click="editMode=true"><i class="fa fa-reply fa-rotate-180"></i> Comment
            </button>
          </div>
        </div>
      </div>
      <div v-if="!loaded" class="fa-3x">
        <i class="fa fa-spinner fa-pulse"></i>
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
          <comment-list :comments="comments"></comment-list>
        </div>
      </div>
    </template>
  </div>
</template>
<script>
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
        comments: null
      }
    },
    components: {
      CommentList: () => import('front_path/components/CommentList'),
      CommentEditor: () => import('front_path/components/CommentEditor')
    },
    mounted () {
      this.getData()
      let vm = this
      this.$root.$on('tiptapIsMounted', () => {
        vm.loaded = true
      })
      this.$root.$on('submitComment', async ({comment}) => {
        console.log(comment)
        // const {data} = await axios.post(`/ajax/forum/blog_posts/${vm.slug}/comment`, comment)
        // swal.fire({
        //   type: data.type,
        //   title: data.title,
        //   position: 'top-end',
        //   toast: true,
        //   showConfirmButton: false,
        //   timer: 8000
        // })
      })
      this.$root.$on('deleteComment',async ({slug}) => {
        console.log(slug)
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
      async getData () {
        const {data} = await axios.get(`/ajax/forum/blog_posts/${this.slug}/comment`)
        this.comments = data.posts
      }
    },
    beforeDestroy () {
      this.$root.$off('tiptapIsMounted')
      this.$root.$off('submitComment')
      this.$root.$off('deleteComment')
    }
  }
</script>