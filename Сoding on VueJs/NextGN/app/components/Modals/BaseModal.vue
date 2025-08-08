<template>
  <div>
    <div class="mfp-bg mfp-zoom-in mfp-ready" />
    <div class="mfp-wrap mfp-close-btn-in mfp-auto-cursor mfp-zoom-in mfp-ready">
      <div class="mfp-container mfp-inline-holder">
        <div class="mfp-content">
          <div
            class="modal mfp-with-anim"
            :style="{ maxWidth }"
          >
            <p
              v-if="title"
              class="modal__title"
            >
              {{ title }}
            </p>

            <slot />

            <button
              ref="closeModalBtn"
              title="Закрыть (Esc)"
              type="button"
              class="mfp-close"
              tabindex="0"
              @click="$modal.hideAll()"
              @keydown="onKeyDown"
            >
              ×
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'BaseModal',

  props: {
    title: {
      type: String,
      default: ''
    },
    maxWidth: {
      type: String,
      default: ''
    }
  },

  methods: {
    onKeyDown(e) {
      if (e.keyCode === 27) {
        e.preventDefault()
        this.$refs.closeModalBtn.click()
      }
    }
  }
}
</script>
