<template>
  <nav>
    <ul class="flex p-0 items-center gap-1 mb-5 md:mb-8">
      <li>
        <a
          class="text-sm font-normal font-compact text-greyfooter"
          href="/store"
        >Магазин вознаграждений</a>
      </li>
      <li>
        <img
          src="../../assets/icons/breadcrumb-chevron.svg"
          alt=""
        >
      </li>
      <li>
        <a class="text-sm font-normal font-compact text-txt">
          {{ pageName }}
        </a>
      </li>
    </ul>
  </nav>
</template>

<script>

export default {
  props: {
    pageName: {
      type: String,
      required: true
    }
  }
}
</script>
<style scoped>
  ul li{
    display: flex;
    align-items: center;
  }
</style>
