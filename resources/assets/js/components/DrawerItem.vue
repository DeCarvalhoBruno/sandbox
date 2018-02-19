<template>
    <router-link tag="li" v-if="router && router.name" :to="router">
        <a href="#">
            <fa :icon="icon"/>
            <span>{{ name }}</span>
            <span class="pull-right-container" v-show="badge">
                <small class="label pull-right"
                   :class="[badge.type==='String'?'bg-green':'label-primary']">{{ badge.data }}
                </small>
            </span>
        </a>
    </router-link>
    <li :class="getMenuClass" v-else>
        {{ isHeader ? name : '' }}
        <a href="#" v-if="!isHeader">
            <fa :icon="icon"/>
            <span>{{ name }}</span>
            <span class="pull-right-container">
                <small v-if="badge && badge.data" class="label pull-right"
                       :class="[badge.type==='String'?'bg-green':'label-primary']">{{ badge.data }}
                </small>
                <fa v-else icon="angle-left"/>
            </span>
        </a>
        <ul class="treeview-menu" v-if="items.length > 0">
            <router-link tag="li" v-for="(item,index) in items" :data="item" :key="index" :to="item.router"
                         v-if="item.type=='item'" :active-class="'current-view'">
                <a v-if="item.router && item.router.name">
                    <fa :icon="item.icon"/>{{ item.name }}
                </a>
                <a v-else>
                    <fa :icon="item.icon"/>{{ item.name }}
                </a>
            </router-link>
            <li class="treeview" v-else>
                <a href="#">
                    <fa :icon="item.icon"/>{{ item.name }}
                </a>
                <ul class="treeview-menu">
                    <drawer-item
                            v-for="(i,index) in item.items"
                            :data="i"
                            :key="index"
                            :type="i.type"
                            :isHeader="i.isHeader"
                            :icon="i.icon"
                            :name="i.name"
                            :badge="i.badge"
                            :items="i.items"
                            :router="i.router"
                            :link="i.link">
                    </drawer-item>
                </ul>
            </li>
        </ul>
    </li>
</template>
<script>
  export default {
    name: 'drawer-item',
    data () {
      return {
        open: false
      }
    },
    props: {
      type: {
        type: String,
        default: 'item'
      },
      isHeader: {
        type: Boolean,
        default: false
      },
      icon: {
        type: String,
        default: ''
      },
      name: {
        type: String
      },
      badge: {
        type: Object,
        default () {
          return {}
        }
      },
      items: {
        type: Array,
        default () {
          return []
        }
      },
      router: {
        type: Object,
        default () {
          return {
            name: ''
          }
        }
      },
      link: {
        type: String,
        default: ''
      }
    },
    computed: {
      getMenuClass: function () {
        return {
          header: this.isHeader,
          treeview: this.type !== 'item'
        }
      }
    },
    methods: {
      toggleOpenMenu () {
        if (this.isHeader) {
          return
        }
        this.open = !this.open
      }
    }
  }
</script>
