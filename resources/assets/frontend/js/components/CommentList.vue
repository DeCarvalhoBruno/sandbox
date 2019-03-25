<template>
  <div id="comments-container" class="container" v-if="comments!==null&&comments.length">
    <div v-for="(comment,idx) in comments" :key="comment.slug"
         class="row comment-item"
         :class="[comment.lvl==0?'card':'']">
      <div class="col">
        <div class="comment-header">
          <figure class="comment-header-item">
            <img v-if="comment.media!==null" :src="comment.media"/>
            <img v-else src="/media/img/site/placeholder_tb.png"/>
          </figure>
          <div class="comment-header-item">
            <div class="d-block">
              <div class="username"><a :href="comment.name">{{comment.username}}</a></div>
              <div class="date">{{comment.updated_at}}</div>
            </div>
          </div>
          <div class="comment-header-item favorite">
            <div class="d-block">
              <div><i class="fa" :class="{
              'fa-star':comment.fav,
              'fa-star-o':!comment.fav,
              auth:authCheck,
              flip:comment.fav,animate:cssAnimationsActivated}"
                      :aria-label="comment.fav?'cancel recommendation':'recommend'"
                      :title="comment.fav?'cancel recommendation':'recommend'"
                      @click="recommend(comment,idx)"></i></div>
              <div class="fav-count">{{(comment.cnt>0?comment.cnt:'')}}</div>
            </div>
          </div>
        </div>
        <div class="comment-body">
          <span v-if="comment.edit_mode!==2" v-html="comment.txt"></span>
          <comment-editor v-else :edit-mode="comment.edit_mode"
                          @cancelled="updateEditMode(idx)"
                          @submitted="updateComment(idx,$event)"
                          :contents="comment.txt"
                          :slug="comment.slug"></comment-editor>
        </div>
        <div class="comment-footer" v-if="authCheck">
          <button v-if="!comment.edit_mode" type="button"
                  class="btn btn-outline-info pull-left"
                  @click="comment.edit_mode=1"><i class="fa fa-reply fa-rotate-180"></i> Reply
          </button>
          <div class="pull-right">
            <button v-if="!comment.edit_mode&&comment.owns" type="button"
                    class="btn btn-sm btn-outline-info mr-2" title="edit comment" aria-label="edit comment"
                    @click="comment.edit_mode=2"><i class="fa fa-pencil"></i>
            </button>
            <button v-if="!comment.edit_mode&&comment.owns" type="button"
                    class="btn btn-sm btn-outline-danger" title="delete comment" aria-label="delete comment"
                    @click="deleteComment(comment.slug)"><i class="fa fa-trash"></i>
            </button>
          </div>
          <comment-editor v-if="comment.edit_mode===1" :edit-mode="comment.edit_mode"
                          @cancelled="updateEditMode(idx)"
                          @submitted="updateEditMode(idx)"
                          :slug="comment.slug"></comment-editor>
        </div>
        <comment-list :comments="comment.children" :auth-check="authCheck"></comment-list>
      </div>
    </div>
  </div>
</template>

<script>
  import swal from 'sweetalert2/dist/sweetalert2.min.js'

  export default {
    name: 'comment-list',
    props: {
      comments: {required: true},
      authCheck: {required: true, type: Boolean}
    },
    components: {
      CommentEditor: () => import('front_path/components/CommentEditor')
    },
    data () {
      return {
        cssAnimationsActivated: false
      }
    },
    mounted () {
      let vm = this
      //making sure css animations don't trigger on load
      setTimeout(() => {
        vm.cssAnimationsActivated = true
      }, 2000)

    },
    methods: {
      recommend (comment, index) {
        if (!this.authCheck) {
          return
        }
        if (this.comments[index].fav === true) {
          this.comments[index].fav = false
          this.comments[index].cnt--
          this.$root.$emit('commentUnfavorited', comment)
        } else {
          this.comments[index].fav = true
          this.comments[index].cnt++
          this.$root.$emit('commentFavorited', comment)
        }
      },
      deleteComment (slug) {
        let vm=this
        swal.fire({
          title: 'title',
          text: 'text',
          type: 'question',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'confirmationButtonText'
        }).then(async (result) => {
          if (result.value) {
            vm.$root.$emit('deleteComment', {slug: slug})
          }
        })
      },
      updateComment (index, event) {
        this.comments[index].edit_mode = false
        this.comments[index].txt = event.txt
      },
      updateEditMode (index) {
        this.comments[index].edit_mode = false
      }
    }
  }
</script>