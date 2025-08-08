<template>
  <inertia-link :href="hrefLink" class="product__back">← {{title}}</inertia-link>
</template>

<script>
import Layout from '@admin/containers/TheContainer'
export default {
  name: 'ProfileBack',

  layout: Layout,

  props: {
    title: {
      type: String,
      default: 'на Главную'
    },
    hrefLink: {
      type: String,
      default: '/'
    },
  }
}
</script>

<style scoped>
.product__back {
  position: unset;
}
</style>
