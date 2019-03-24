<template>
  <div id="comments-container" class="container" v-if="comments!==null&&comments.length">
    <div v-for="comment in comments" :key="comment.slug"
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
              <div><i class="fa fa-star-o" aria-label="recommend" title="recommend"></i></div>
              <div class="fav-count">10</div>
            </div>
          </div>
        </div>
        <div class="comment-body">
          <span v-if="comment.edit_mode!==2" v-html="comment.txt"></span>
          <comment-editor v-else :edit-mode="comment.edit_mode"
                          @cancelled="comment.edit_mode=false"
                          @submitted="comment.edit_mode=false"
                          :contents="comment.txt"
                          :slug="comment.slug"></comment-editor>
        </div>
        <div class="comment-footer">
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
          <comment-editor v-if="comment.edit_mode===1&&comment.owns" :edit-mode="comment.edit_mode"
                          @cancelled="comment.edit_mode=false"
                          @submitted="comment.edit_mode=false"
                          :slug="comment.slug"></comment-editor>
        </div>
        <comment-list :comments="comment.children"></comment-list>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'comment-list',
    props: {
      comments: {required: true}
    },
    components: {
      CommentEditor: () => import('front_path/components/CommentEditor')
    },
    data () {
      return {}
    },
    methods: {
      deleteComment (slug) {
        this.$root.$emit('deleteComment',{slug:slug})
      }
    }
  }
</script>