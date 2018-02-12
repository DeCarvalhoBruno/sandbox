<template>
    <div>
        <navbar/>
        <drawer :menu-items="MenuItems"/>
        <div id="content-wrap" class="content-wrapper">
            <section class="content">
                <transition name="page" mode="out-in">
                    <slot>
                        <router-view/>
                    </slot>
                </transition>
            </section>
        </div>
    </div>

</template>

<script>
  import Navbar from '../components/Navbar'
  import Drawer from '../components/Drawer'
  import MenuItems from '../menu-items'

  export default {
    name: 'BasicLayout',
    components: {
      Navbar,
      Drawer
    },
    data: () => ({
      MenuItems
    })
  }
</script>