<template>
    <div class="container">
        <div class="row">
            <div class="container">
                <div class="row mb-3">
                    <router-link :to="{
                            name: 'admin.blog.add',
                            }">
                        <button class="btn btn-add" type="button">{{$t('pages.blog.add_post')}}</button>
                    </router-link>
                </div>
                <table-filter :filterButtons="filterButtons" :entity="this.entity"
                              @filter-removed="removeFilter"
                              @filter-reset="resetFilters"/>
                <div class="row pb-1">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" class="form-control"
                                   :placeholder="$t('pages.blog.filter_title')"
                                   :aria-label="$t('pages.blog.filter_title')"
                                   v-model="titleFilter"
                                   @keyup.enter="filterBlogTitle">
                            <div class="input-group-append">
                                <label class="input-group-text"
                                       :title="$t('general.search')"
                                       @click="filterBlogTitle">
                                    <fa icon="newspaper"></fa>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row pb-1">
                    <div class="col-md-8">
                        <div class="form-row align-items-center">
                            <div class="col my-1">
                                <select class="custom-select mr-sm-2" id="select_apply_to_all" v-model="selectApply">
                                    <option disabled value="">{{$t('tables.grouped_actions')}}</option>
                                    <option value="del">{{$t('tables.option_del_blog')}}</option>
                                </select>
                            </div>
                            <div class="col my-1">
                                <button type="button" class="btn btn-primary"
                                        :title="$t('tables.btn_apply_title')"
                                        @click="applyToSelected">
                                    {{$t('general.apply')}}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <span class="float-right mt-3">{{data.total}}&nbsp;{{$tc('db.blog_post',data.total)}}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <v-table ref="table"
                     :entity="entity" :data="computedTable"
                     :is-multi-select="true" select-column-name="blog_post_title">
                <th slot="header-action">
                    {{$t('general.actions')}}
                </th>
                <td slot="body-action" slot-scope="props">
                    <div class="inline">
                        <template v-if="props.row.blog_post_slug">
                            <router-link :to="{
                            name: 'admin.blog.edit',
                            params: { slug: props.row.blog_post_slug }}">
                                <button
                                        class="btn btn-sm btn-info"
                                        :title="$t(
                                        'tables.edit_item',{
                                        name:props.row[$t('db_raw_inv.blog_post_title')]
                                        })">
                                    <fa icon="pencil-alt"></fa>
                                </button>
                            </router-link>
                            <button type="button" class="btn btn-sm btn-danger"
                                    :title="$t('tables.delete_item',{name:props.row[$t('db_raw_inv.blog_post_title')]})"
                                    @click="deleteRow(
                                        props.row,
                                        'blog_post',
                                        'blog_post_slug',
                                        'blog_post_title',
                                        '/ajax/admin/blog/post'
                                    )">
                                <fa icon="trash-alt"></fa>
                            </button>
                        </template>
                    </div>
                </td>
            </v-table>
        </div>
    </div>
</template>

<script>
  import Vue from 'vue'
  import Swal from '~/mixins/sweet-alert'
  import Table from '~/components/table/table'
  import TableFilter from '~/components/table/TableFilter'
  import TableMixin from '~/mixins/tables'
  import axios from 'axios'

  Vue.use(Table)

  export default {
    layout: 'basic',
    middleware: 'check-auth',
    name: 'blog',
    components: {
      'v-table': Table,
      TableFilter
    },
    data () {
      return {
        allSelected: false,
        selectApply: '',
        titleFilter: null,
        filterButtons: {},
        selectionBuffer: {},
        entity: 'blog',
        data: {
          total: 0
        }
      }
    },
    mixins: [
      TableMixin,
      Swal
    ],
    methods: {
      setFilterButtons () {
        this.setFilterButton('title')
      },
      async applyToSelected () {
        let posts = this.$refs.table.getSelectedRows('blog_post_slug')
        if (posts.length > 0) {
          switch (this.selectApply) {
            case 'del':
              this.swalDeleteWarning(
                this.$t('modal.blog_delete.h'),
                this.$tc('modal.blog_delete.t', 2, {number: posts.length}),
                this.$t('general.delete')
              ).then(async (result) => {
                if (result.value) {
                  await axios.post('/ajax/admin/blog/post/batch/delete', {posts: posts})
                  this.refreshTableData()
                  this.swalNotification('success', this.$tc('message.blog_delete_ok', 2))
                }
              })
              break
          }
        }
      },
      filterBlogTitle () {
        this.applyFilter('title')
      }
    }
  }
</script>