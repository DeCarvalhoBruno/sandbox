<template>
    <aside id="slider" class="main-sidebar">
        <section class="sidebar">
            <ul data-widget="tree" class="sidebar-menu">
                <drawer-item
                        v-for="(item,index) in MenuItems"
                        :data="item"
                        :key="index"
                        :type="item.type"
                        :isHeader="item.isHeader"
                        :icon="item.icon"
                        :name="item.name"
                        :badge="item.badge"
                        :items="item.items"
                        :router="item.router"
                        :link="item.link"
                >
                </drawer-item>
            </ul>
        </section>
    </aside>
</template>

<script>
  import DrawerItem from './DrawerItem'

  export default {
    name: 'drawer',
    props: {
      MenuItems: {
        type: Array,
        default () { return []}
      }
    },
    components: {
      'drawer-item': DrawerItem
    },
    mounted () {
      $('.treeview>a').each(function () {
        $(this).tree()
        // tree.call($(this))
      })

    }
  }
</script>
