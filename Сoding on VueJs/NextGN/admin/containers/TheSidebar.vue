<template>
  <CSidebar
    fixed
    :minimize="sidebarMinimize"
    :show="sidebarShow"
    breakpoint="xl"
    @update:show="value => $emit('change-sidebar-show', value)"
  >
    <CRenderFunction
      v-if="currentPanel === 'admin'"
      flat
      :content-to-render="navAdmin"
    />
    <CRenderFunction
      v-else-if="currentPanel === 'product_owner'"
      flat
      :content-to-render="navOwnerAccount"
    />
    <CRenderFunction
      v-else
      flat
      :content-to-render="$options.navModerAcc"
    />
    <CSidebarMinimizer
      class="d-md-down-none"
      @click.native="$emit('toggle-sidebar-minimize')"
    />
  </CSidebar>
</template>

<script>
import navAdmin from './_navAdmin'
import navOwnerAccount from './_navOwnerAccount'
import navModerAcc from './_navModerAcc'
import currentPanel from '@admin/mixins/currentPanel.vue'

export default {
  name: 'TheSidebar',
  mixins: [currentPanel],
  props: {
    sidebarShow: [String, Boolean],
    sidebarMinimize: Boolean
  },
  computed: {
    navOwnerAccount() {
      return navOwnerAccount.map(item => {
        item._children = item._children.filter(menuItem => {
          return this.$page.props.user.permissions.some(userPermission => {
            return menuItem.permissions === undefined ? true :  menuItem.permissions.includes(userPermission)
          })
        })

        return item
      })
    },
    navAdmin() {
      return                        navAdmin
      // return navAdmin.map( item => {
      //    item._children =  item._children.filter( child => {
      //       if ( child.url === '/admin/top-testers' ){
      //          return               !this.$page.props.disableTopTestersPage && this.$page.props.internal_visitor;
      //       } else {
      //          return               true;
      //       }
      //    } );
      //    return                     item;
      // } );
    }
  },
  navAdmin,
  navOwnerAccount,
  navModerAcc
}
</script>
