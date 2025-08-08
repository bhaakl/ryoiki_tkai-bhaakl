<template>
  <div class="fixed inset-0 z-50 w-full h-full pb-6 bg-white pt-15 md:pt-6">
    <div
      class="absolute w-8 h-8 cursor-pointer md:top-5 top-3 right-5 z-110"
      @click="$emit('close-details')"
    >
      <CloseCross />
    </div>
    <div class="w-full h-full overflow-hidden">
      <div
        class="flex flex-col items-center w-full h-full grid-cols-12 gap-8 px-5 mx-auto md:px-20 md:grid max-w-388 2xl:px-9"
      >
        <div
          class="col-span-2 flex order-2 md:order-none md:flex-col items-start justify-center gap-2.5 mr-auto ml-0 md:mr-0 md:ml-0"
        >
          <div
            v-for="(slide, index) in slides"
            :key="index"
            data-role="thumb-btn"
            :class="[
              'relative md:w-40 w-11 md:h-40 h-11 overflow-hidden rounded-lg cursor-pointer border-2 border-solid',
              index === shownSlide ? 'border-brand' : 'border-transparent',
            ]"
            @click="handleThumb(index)"
          >
            <img
              v-if="slide"
              class="absolute z-10 object-cover w-full h-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2"
              :src="`/storage/images/${slide.image_url}`"
            >
          </div>
        </div>

        <div
          data-role="prev-btn"
          class="hidden ml-auto mr-3 rounded-full cursor-pointer md:block md:order-none shadow-shadow w-11 h-11"
          @click="handlePrev"
        >
          <Arrow />
        </div>

        <div
          data-role="slider"
          class="relative order-1 w-full h-full col-span-6 overflow-hidden rounded-lg md:order-none"
        >
          <img
            v-if="slides[shownSlide]"
            class="absolute object-cover w-full h-auto -translate-x-1/2 -translate-y-1/2 md:h-full top-1/2 left-1/2"
            :src="`/storage/images/${slides[shownSlide]['image_url']}`"
            alt=""
          >
        </div>

        <div
          data-role="next-btn"
          class="hidden ml-3 mr-auto rounded-full cursor-pointer md:block md:order-none shadow-shadow w-11 h-11"
          @click="handleNext"
        >
          <Arrow class="rotate-180" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Arrow from '../../assets/img/icons/Arrow.vue'
import CloseCross from '../../assets/img/icons/CloseCross.vue'
import getGoodsImage from '@app/mixins/getGoodsImage'

export default {
  components: {
    Arrow,
    CloseCross
  },
  mixins: [getGoodsImage],
  props: {
    item: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      slides: [],
      shownSlide: 0
    }
  },
  mounted() {
    this.slides = this.item.images
  },
  methods: {
    handleThumb(index) {
      this.shownSlide = index
    },
    handlePrev() {
      this.shownSlide = (this.shownSlide - 1 + this.slides.length) % this.slides.length
    },
    handleNext() {
      this.shownSlide = (this.shownSlide + 1) % this.slides.length
    }
  }
}

</script>
