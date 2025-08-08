<template>
  <div class="c-app">
    <TheSidebar
      :sidebar-show="sidebarShow"
      :sidebar-minimize="sidebarMinimize"
      @change-sidebar-show="value => (sidebarShow = value)"
      @toggle-sidebar-minimize="sidebarMinimize = !sidebarMinimize"
    />
    <CWrapper>
      <TheHeader
        @toggle-sidebar-desktop="toggleSidebarDesktop"
        @toggle-sidebar-mobile="toggleSidebarMobile"
      />
      <div class="c-body">
        <main class="c-main">
          <CContainer fluid>
            <slot />
          </CContainer>
        </main>
      </div>
    </CWrapper>
  </div>
</template>

<script>
import TheSidebar from './TheSidebar'
import TheHeader from './TheHeader'

export default {
  name: 'TheContainer',
  components: {
    TheSidebar,
    TheHeader
  },
  data() {
    return {
      sidebarShow: 'responsive',
      sidebarMinimize: false
    }
  },
  updated() {

  },
  methods: {
    toggleSidebarDesktop() {
      const sidebarOpened = [true, 'responsive'].includes(this.sidebarShow)
      this.sidebarShow = sidebarOpened ? false : 'responsive'
    },
    toggleSidebarMobile(state) {
      const sidebarClosed = [false, 'responsive'].includes(this.sidebarShow)
      this.sidebarShow = sidebarClosed ? true : 'responsive'
    }
  }
}
</script>
