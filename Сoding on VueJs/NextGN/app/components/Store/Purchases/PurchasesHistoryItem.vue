<template>
  <div
    class="grid items-start w-full grid-cols-12 gap-6 purchases-content-line border-t border-divider"
  >
    <div class="p-3 text-sm font-normal font-compact text-txt">
      {{ operation.id }}
    </div>
    <div class="flex flex-col col-span-3 gap-3 p-3">
      <div
        v-for="(item, i) in operation.goods"
        :key="i"
      >
        <div class="text-sm font-normal font-compact text-txt">
          {{ item.data.title }}
        </div>
        <div class="flex items-center gap-1 text-xs font-normal font-compact text-greytxt">
          {{ item.quantity }} шт.
          <span
            v-if="item.specification && item.specification.size !== false"
            class="uppercase"
          >, {{ item.specification.size }}, </span>
          <span
            v-if="item.specification && item.specification.color !== false"
            :style="`display: block; background:${item.specification.color}`"
            class="w-2 h-2 rounded-full"
          ></span>
        </div>
      </div>
    </div>

    <div class="col-span-2 p-3 text-sm font-normal font-compact text-txt">
      {{ convertAmount(operation.full_price) }}
    </div>

    <div class="col-span-3 p-3 text-sm font-normal font-compact text-txt">
      {{ operation.address }}
    </div>

    <div
      class="flex items-center gap-1 p-3 text-sm font-normal font-compact text-txt"
    >
      {{ formatDate(operation.created_at) }}
    </div>

    <div
      class="col-span-2 flex items-center justify-center gap-1.5 p-3 text-sm font-normal font-compact text-txt"
    >
      <div
        :class="{
          'w-1.5 h-1.5 rounded-full': true,
          'bg-positive': operation.status === 'delivered',
          'bg-brand': operation.status === 'processing',
          'bg-accent': operation.status === 'sent',
        }"
      />
      {{ getStatus(operation.status) }}
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
    getStatus(status) {
      switch (status) {
      case 'processing':
        return 'В процессе'
      case 'delivered':
        return 'Доставлено'
      case 'sent':
        return 'Отправлено'
      }
    },
    formatDate(dateStr) {
      const date = new Date(dateStr)
      const day = String(date.getDate()).padStart(2, '0')
      const month = String(date.getMonth() + 1).padStart(2, '0')
      const year = date.getFullYear()

      return `${day}.${month}.${year}`
    },
    convertAmount(number) {
      return number.toLocaleString('fr-FR').replace(/\u00A0/g, ' ')
    }
  }
}

</script>
<style>
.purchases-content-line{
  border-top: 1px solid #BCC3D080;
}

</style>
