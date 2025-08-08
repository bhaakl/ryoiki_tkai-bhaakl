<template>
  <div class="grid w-full grid-cols-12 gap-8 balance-content-line border-t border-divider">
    <div
      class="flex items-center col-span-3 gap-1 p-3 text-sm font-normal font-compact text-txt"
    >
      {{ operation.operation_type }} {{ operation.operation_value }}
    </div>
    <div
      class="flex items-center gap-1 p-3 text-sm font-normal font-compact text-txt"
    >
      {{ formatDate(operation.created_at) }}
    </div>
    <div class="col-start-7 p-3 text-sm font-normal font-compact text-txt">
      {{ operation.description }}
    </div>
  </div>
</template>

<script>
export default {
  props: {
    operation: {
      type: Object,
      required: true
    }
  },
  methods: {
    formatDate(dateStr) {
      const date = new Date(dateStr)
      const day = String(date.getDate()).padStart(2, '0')
      const month = String(date.getMonth() + 1).padStart(2, '0')
      const year = date.getFullYear()

      return `${day}.${month}.${year}`
    }
  }
}

</script>
<style>
.balance-content-line{
  border-top: 1px solid #BCC3D080;
}

</style>
