<template>
    <div class="container">
        <div class="row">
            <div class="container">
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
                                    <fa icon="newspaper"/>
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
                        <span class="float-right mt-3">{{total}}&nbsp;{{$tc('db.blog_post',total)}}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <v-table :entity="this.entity" :rows="rows" :total="total" :is-multi-select="true"
                     select-column-name="blog_post_title">
                <th slot="header-action">
                    {{$t('general.actions')}}
                </th>
                <td slot="body-action" slot-scope="props">
                    <div class="inline">
                        <template v-if="props.row.blog_post_slug">
                            <router-link :to="{
                            name: 'admin.blog.edit',
                            params: { slug: props.row.blog_post_slug }
                            }">
                                <button class="btn btn-sm btn-info"
                                        :title="$t('tables.edit_item',{name:props.row[$t('db_raw_inv.blog_post_title')]})">
                                    <fa icon="pencil-alt">
                                    </fa>
                                </button>
                            </router-link>
                        </template>
                    </div>
                </td>
            </v-table>
        </div>
        <div class="row">
            <tree></tree>
        </div>
    </div>
</template>

<script>
  import Vue from 'vue'
  import store from '~/store'
  import Table from '~/components/table/table'
  import TableFilter from '~/components/table/TableFilter'
  import TableMixin from '~/mixins/tables'
  import Tree from '~/components/tree/Tree'
  import axios from 'axios'

  Vue.use(Table)

  export default {
    layout: 'basic',
    middleware: 'check-auth',
    name: 'blog',
    components: {
      'v-table': Table,
      TableFilter,
      Tree
    },
    data () {
      return {
        allSelected: false,
        selectApply: '',
        titleFilter: null,
        filterButtons: {},
        selectionBuffer: {},
        entity: 'blog'
      }
    },
    mixins: [
      TableMixin
    ],
    created () {
      this.setFilterButtons()
      this.$root.$on('modal_confirmed', this.applyMethod)
    },
    methods: {
      setFilterButtons () {
        this.setFilterButton('title')
      },
      async applyToSelected () {
        switch (this.selectApply) {
          case 'del':
            try {
              await axios.post(`/ajax/admin/users/batch/delete`, {users: this.$refs.table.getSelectedRows('username')})
              this.$store.dispatch('session/setAlertMessageSuccess', this.$tc('message.user_delete_ok', 2))
              this.$store.dispatch('table/fetchData', {
                entity: this.entity,
                queryString: this.$route.fullPath
              })
            } catch (e) {}
            break
        }
      },
      filterBlogTitle () {
        this.applyFilter('title')
      }
    },
    beforeRouteEnter (to, from, next) {
      store.dispatch('table/fetchData', {
        entity: 'blog',
        queryString: to.fullPath
      }).then(res => next())
    }
  }
</script>