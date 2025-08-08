<template>
  <CHeader
    fixed
    with-subheader
    light
  >
    <CToggler
      in-header
      class="ml-3 d-xl-none"
      @click="$emit('toggle-sidebar-mobile')"
    />
    <CToggler
      in-header
      class="ml-3 d-lg-down-none"
      @click="$emit('toggle-sidebar-desktop')"
    />
    <CHeaderNav class="ml-auto mr-3 my-0">
      <TheHeaderDropdownAccnt />
    </CHeaderNav>
  </CHeader>
</template>

<script>
import TheHeaderDropdownAccnt from './TheHeaderDropdownAccnt'

export default {
  name: 'TheHeader',
  components: {
    TheHeaderDropdownAccnt
  }
}
</script>
