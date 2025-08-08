<template>
  <CDropdown
    in-nav
    class="c-header-nav-items"
    placement="bottom-end"
    add-menu-classes="pt-0"
  >
    <template #toggler>
      <CHeaderNavLink>
        <div class="c-avatar">
          <img
            v-if="!avatar"
            src="@app/assets/img/avatar-placeholder.png"
            class="c-avatar-img"
          >
          <img
            v-else
            :src="`/storage/images/${avatar.path}`"
            class="c-avatar-img"
            style="border-radius: 100%"
          >
        </div>
      </CHeaderNavLink>
    </template>
    <CDropdownHeader
      tag="div"
      class="text-center"
      color="light"
    >
      <strong class="font-sm">{{ username }}</strong>
    </CDropdownHeader>
    <CDropdownItem
      v-if="isUserAdminAndProductOwner"
      @click="$inertia.visit(anotherPanelLink)"
    >
      <CIcon name="cil-swap-horizontal" /><span class="ml-1">В панель {{ anotherPanelLabel }}</span>
    </CDropdownItem>
    <CDropdownItem @click="$inertia.visit('/admin/edit-avatar')">
      <CIcon name="cil-happy" /><span class="ml-1">Изменить аватар</span>
    </CDropdownItem>
    <CDropdownItem href="/admin/logout">
      <CIcon name="cil-lock-locked" /><span class="ml-1">Выйти</span>
    </CDropdownItem>
  </CDropdown>
</template>

<script>
import currentPanel from '@admin/mixins/currentPanel.vue'

export default {
  name: 'TheHeaderDropdownAccnt',
  mixins: [currentPanel],
  computed: {
    avatar() {
      if (this.$page.props && this.$page.props.admin && this.$page.props.admin.avatar) {
        return this.$page.props.admin.avatar
      }

      return null
    },
    username() {
      return this.$page.props.user && this.$page.props.user.name
    },
    isUserAdminAndProductOwner() {
      return (
        this.$page.props.admin &&
            this.$page.props.admin.userRoles &&
            this.$page.props.admin.userRoles.includes('admin') &&
            this.$page.props.admin.userRoles.includes('product_owner') &&
            this.currentPanel !== null
      )
    },
    anotherPanelLink() {
      return this.currentPanel === 'admin' ? '/admin/testers' : '/admin/users'
    },
    anotherPanelLabel() {
      return this.currentPanel === 'admin' ? 'владельца' : 'администратора'
    }
  }
}
</script>

<style scoped>
.c-icon {
   margin-right: 0.3rem;
}
</style>
