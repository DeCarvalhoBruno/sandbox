<template>
  <div class="container">
    <template v-if="scrolledPast">
      <div class="row" v-if="loaded">
        <div class="col-lg-8 mx-md-auto">
          <header class="pull-left">Discussion</header>
          <button type="button"
                  class="btn btn-primary pull-right"
                  @click="editMode=true"><i class="fa fa-reply"></i> Reply
          </button>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-8 mx-md-auto">
          <div v-show="!loaded" class="fa-3x">
            <i class="fa fa-spinner fa-pulse"></i>
          </div>
          <div id="comments-container" class="card">
            <div v-show="editMode">
              <tiptap @is-mounted="loaded=true" ref="tiptap" :edit-mode="editMode"></tiptap>
              <div class="w-100" v-show="loaded">
                <div class="pull-right p-2">
                  <button type="button" class="btn btn-outline-primary"
                          @click="editMode=false">Cancel</button>
                  <button type="button" class="btn btn-primary" @click="save">Submit</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-8 mx-md-auto">
          <div class="container"></div>
          <div v-for="(comment,idx) in comments" :key="'comment'+idx" class="row card">
            <div class="col">
              <!--slug,txt,username,name,media,updated_at-->
              <div>
                <span v-if="comment.media!==null">
                  <figure>
                    <img :src="comment.media"/>
                  </figure>
                </span>
                <span v-else></span>
                <span><a :href="comment.name">{{comment.username}}</a></span>
                <span>{{comment.updated_at}}</span>

              </div>
              <div>
                <span v-html="comment.txt"></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>
<script>
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
        comments: null
      }
    },
    components: {
      Tiptap: () => import('front_path/components/Tiptap')
    },
    mounted () {
      this.getData()
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
      },
      save () {
        axios.post(`/ajax/forum/blog_posts/${this.slug}/comment`, {
          txt: this.$refs.tiptap.getData()
        })
      }
    }
  }
</script>