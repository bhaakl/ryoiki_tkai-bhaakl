<template>
  <div class="grid w-full grid-cols-12 gap-8">
    <div class="col-span-2">
      <DropdownFilter
        dropdown-type="operation type"
        @checkedFilter="getFilter"
      />
    </div>

    <div
      class="flex items-center col-span-2 col-start-4 gap-1 p-3 text-sm font-medium font-compact text-txt"
    >
      Дата<img
        src="../../../assets/icons/date-icon.svg"
        alt=""
      >
    </div>

    <div class="col-start-7 p-3 text-sm font-medium font-compact text-txt">
      Описание
    </div>
  </div>
</template>

<script>
import DropdownFilter from '@app/components/Shared/DropdownFilter.vue'
export default {
  components: {
    DropdownFilter
  },
  methods: {
    getFilter(data) {
      this.$emit('filters', data)
    }
  }
}

</script>
