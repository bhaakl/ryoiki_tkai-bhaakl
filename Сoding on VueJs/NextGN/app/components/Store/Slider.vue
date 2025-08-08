<template>
  <section
    class="rounded-[20px] md:rounded-4xl bg-[#e7eafa] px-4 pt-4 pb-4 lg:px-10 lg:pt-6 lg:pb-8 w-full h-[300px] flex flex-col"
  >
    <div class="w-full h-1" />
    <div class="my-3 lg:my-6">
      <h1
        class="mb-4 text-2xl font-medium leading-7 text-black lg:leading-none font-wide lg:text-heading white"
      >
        Магазин вознаграждений
      </h1>
      <p
        class="text-sm font-normal text-black h-30 lg:h-20 lg:text-xl font-compact"
      >
        Description
      </p>
    </div>
    <div class="flex items-center justify-between mt-auto mb-0 align-bottom">
      <inertia-link
        class="store-btn flex items-center p-[14px] text-xs font-bold tracking-wider uppercase font-wide text-white rounded-2xl"
        href="/store"
      >
        <span class="lg:px-2 lg:py-1 px-1 py-0.5">Подробнее</span>
      </inertia-link>
      <div class="items-center hidden gap-4 lg:flex">
        <div class="rounded-full cursor-pointer shadow-shadow">
          <Arrow />
        </div>
        <div class="rounded-full cursor-pointer shadow-shadow">
          <Arrow class="rotate-180" />
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import Arrow from '../../assets/img/icons/Arrow.vue'

export default {
  components: {
    Arrow
  }
}
</script>
<style scoped>
.store-btn{
  background-color: #ff0032;
  transition: 0.3s;
}
.store-btn:hover{
  background-color: #EE0730;
}
</style>
