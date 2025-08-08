<template>
  <div class="relative flex items-center gap-4 group">
    <slot name="title" />
    <div
      :class="['absolute left-0 hidden pt-4 top-full  md:group-hover:block', dropdownClass]"
    >
      <div
        class="flex flex-col gap-[1px] w-full bg-white rounded-2xl p-1.5 shadow-shadow"
      >
        <slot name="content" />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DropdownMenu',
  props: {
    dropdownClass: {
      type: String,
      default: '',
    },
  }
}
</script>
