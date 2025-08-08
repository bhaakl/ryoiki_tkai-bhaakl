<template>
   <div>
      <button
         title="Закрыть (Esc)"
         type="button"
         @click="$parent.close()"
         class="mfp-close"
         tabindex="0"
      >
         ×
      </button>
      <iframe frameborder="no" scrolling="no" ref="pollIframe" :style="{ height: iframeHeight + 'px' }" :src="src"></iframe>
   </div>
</template>

<script>
export default {
   name: 'PollModal',
   props: {
      src: String,
   },
  data() {
    return {
      iframeHeight: 0
    };
  },
  mounted() {
    this.updateWindowHeight();

    window.addEventListener('resize', this.updateWindowHeight);
  },
  beforeDestroy() {
    window.removeEventListener('resize', this.updateWindowHeight);
  },
  methods: {
    updateWindowHeight() {
      this.iframeHeight = window.innerHeight * 0.8;
    },
  },
};
</script>

<style scoped>
iframe {
  width: 100%;
}
</style>
