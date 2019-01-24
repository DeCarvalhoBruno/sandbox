<template>
    <nav aria-label="Page navigation example">
        <ul class="pagination">
            <template v-if="nav.prev!=null">
                <li class="page-item">
                    <router-link :to="{name: 'admin.media.edit', params: {media: nav.first}}">
                        <a class="page-link" :aria-label="$t('general.first')" :title="$t('general.first')">
                            <span aria-hidden="true" class="font-weight-bold">&laquo;</span>
                            <span class="sr-only">{{$t('general.first')}}</span>
                        </a>
                    </router-link>
                </li>
                <li class="page-item">
                    <router-link :to="{name: 'admin.media.edit', params: {media: nav.prev}}">
                        <a class="page-link" :aria-label="$t('general.prev')" :title="$t('general.prev')">
                            <span aria-hidden="true" class="font-weight-bold">&lsaquo;</span>
                            <span class="sr-only">{$t('general.prev')}</span>
                        </a>
                    </router-link>
                </li>
            </template>
            <template v-else>
                <li class="page-item disabled">
                    <a class="page-link" :aria-label="$t('general.first')" :title="$t('general.first')">
                        <span aria-hidden="true">&laquo;</span>
                        <span class="sr-only">{{$t('general.first')}}</span>
                    </a>
                </li>
                <li class="page-item disabled">
                    <a class="page-link" :aria-label="$t('general.prev')" :title="$t('general.prev')">
                        <span aria-hidden="true">&lsaquo;</span>
                        <span class="sr-only">{$t('general.prev')}</span>
                    </a>
                </li>
            </template>
            <li class="page-item disabled">
                <span class="page-link text-center" style="min-width:4rem">
                    <span v-if="isLoading"><fa class="sync-icon" icon="sync" spin></fa></span>
                    <span v-else>{{nav.idx}} / {{nav.total}}</span>

                </span>
            </li>
            <template v-if="nav.next!=null">
                <li class="page-item">
                    <router-link :to="{name: 'admin.media.edit', params: {media: nav.next}}">
                        <a class="page-link" :aria-label="$t('general.next')" :title="$t('general.next')">
                            <span aria-hidden="true" class="font-weight-bold">&rsaquo;</span>
                            <span class="sr-only">{{$t('general.next')}}</span>
                        </a>
                    </router-link>
                </li>
                <li class="page-item">
                    <router-link :to="{name: 'admin.media.edit', params: {media: nav.last}}">
                        <a class="page-link" :aria-label="$t('general.last')" :title="$t('general.last')">
                            <span aria-hidden="true" class="font-weight-bold">&raquo;</span>
                            <span class="sr-only">{{$t('general.last')}}</span>
                        </a>
                    </router-link>
                </li>
            </template>
            <template v-else>
                <li class="page-item disabled">
                    <a class="page-link" :aria-label="$t('general.next')" :title="$t('general.next')">
                        <span aria-hidden="true">&rsaquo;</span>
                        <span class="sr-only">{{$t('general.next')}}</span>
                    </a>
                </li>
                <li class="page-item disabled">
                    <a class="page-link" :aria-label="$t('general.last')" :title="$t('general.last')">
                        <span aria-hidden="true">&raquo;</span>
                        <span class="sr-only">{{$t('general.last')}}</span>
                    </a>
                </li>
            </template>
        </ul>
    </nav>
</template>

<script>
  export default {
    name: 'record-paginator',
    props: {
      nav: {required: true},
      isLoading: {required: true}
    },
    data () {
      return {}
    }
  }
</script>