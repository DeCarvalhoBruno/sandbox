<template>
    <div>
        <v-table :entity="'blog'" :rows="rows" :total="total" :is-multi-select="true"
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
</template>

<script>
  import Vue from 'vue'
  import store from '~/store'
  import Table from '~/components/table/table'
  import { mapGetters } from 'vuex'
  import axios from 'axios'

  Vue.use(Table)

  export default {
    layout: 'basic',
    middleware: 'check-auth',
    name: 'blog',
    components: {
      'v-table': Table
    },
    computed: {
      ...mapGetters({
        rows: 'table/rows',
        total: 'table/total',
        extras: 'table/extras'
      })
    },
    data () {
      return {}
    },
    beforeRouteEnter (to, from, next) {
      store.dispatch('table/fetchData', {
        entity: 'blog',
        queryString: to.fullPath
      }).then(res => next())
    }
  }
</script>