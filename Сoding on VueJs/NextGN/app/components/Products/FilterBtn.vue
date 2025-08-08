<template>
  <a
    :class="{ 'is-active': isActive }"
    class="filter__button"
    href="#"
    v-on="$listeners"
  >
    <div class="filter__check">
      <svg class="ico ico-mono-check">
        <use :xlink:href="imagePath(`sprite-mono.svg`) + '#ico-mono-check'" />
      </svg>
    </div>

    <div
      v-if="$slots['prepend-icon']"
      class="filter__icon"
    >
      <slot name="prepend-icon" />
    </div>

    <slot />
  </a>
</template>

<script>
import imagePath from '@app/mixins/imagePath.vue'

export default {
  props: {
    isActive: {
      type: Boolean,
      default: false
    }
  },

  mixin: [imagePath]
}
</script>
