<template>
  <div class="flex items-center justify-center gap-4 mt-8">
    <button
      class="pagination-item flex items-center justify-center w-8 h-8 font-medium leading-6 rounded-md cursor-pointer text-17 font-compact"
      v-for="page in totalPagesArray"
      :key="page"
      @click="$emit('page-changed', page)"
      :class="{ active: currentPage === page }"
    >
      {{ page }}
    </button>
  </div>
</template>

<script>
export default {
  props: {
    currentPage: {
      type: Number,
      required: true
    },
    totalPages: {
      type: Number,
      required: true
    }
  },
  computed: {
    totalPagesArray() {
      return Array.from({ length: this.totalPages }, (_, i) => i + 1)
    }
  }
}

</script>

<style scoped>
.pagination {
  display: flex;
  gap: 10px;
  justify-content: center;
  margin: 20px 0;
}
button{
  color: rgb(29 32 35 / var(--tw-text-opacity));
}
button.active {
  box-shadow: 0 4px 16px 0 #00000014;
}
.pagination-item{
  background: #FFFFFF;
}
</style>
