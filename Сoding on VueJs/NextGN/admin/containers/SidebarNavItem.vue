<template>
  <li class="c-sidebar-nav-item">
    <CLink
      :class="'c-sidebar-nav-link'"
      @click.prevent="$inertia.visit(url)"
    >
      <CIcon
        :name="icon"
        custom-classes="c-sidebar-nav-icon"
      />
      {{ name }}
    </CLink>
  </li>
</template>

<script>
export default {
  name: 'SidebarNavItem',
  props: {
    name: String,
    icon: String,
    url: String
  }
}
</script>
