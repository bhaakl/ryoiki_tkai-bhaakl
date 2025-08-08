<template>
  <div
    v-if="!isMobile()"
    class="filter-card-btn px-5 pt-5 pb-2 bg-white border border-divider/50 rounded-3xl"
  >
    <h2
      class="mt-1 mb-2 font-medium leading-6 font-compact text-17 text-greytxt"
    >
      Тип товара
    </h2>
    <ul class="flex flex-col p-0">
      <li
        v-for="item in categories"
        :key="item.id"
      >
        <input
          :id="`option-one-${item.id}`"
          v-model="filteredCategories"
          type="checkbox"
          class="option custom-checkbox"
          :value="item.id"
          @change="$emit('filtered', filteredCategories)"
        >
        <label
          :for="`option-one-${item.id}`"
          class="flex items-center w-full gap-[9px] py-2.5 font-compact font-normal text-17 leading-6 text-txt"
        >{{ item.name }}</label>
        <ul class="ml-4 flex flex-col p-0">
          <li
            v-for="subcategory in item.categories"
            :key="subcategory.id"
          >
            <input
              :id="`subOption-one-${subcategory.id}`"
              v-model="filteredCategories"
              type="checkbox"
              class="subOption custom-checkbox"
              :value="subcategory.id"
              @change="$emit('filtered', filteredCategories)"
            >
            <label
              :for="`subOption-one-${subcategory.id}`"
              class="flex items-center w-full gap-[9px] py-2.5 font-compact font-normal text-17 leading-6 text-txt"
            >{{ subcategory.name }}</label>
          </li>
        </ul>
      </li>
    </ul>
  </div>
  <div
    v-else
    class=""
  >
    <div
      class="filter-card-btn flex items-center justify-between px-3 py-[14px] bg-white border border-divider/50 rounded-2xl md:rounded-3xl"
      @click="toggleModal"
    >
      <p class="font-normal leading-6 font-compact text-17 text-txt">
        Тип товара
      </p>
      <ChevronDown />
    </div>

    <div
      v-if="isModalOpen === true"
      class="fixed inset-0 z-50 w-full h-full bg-txt/40"
    >
      <div
        class="absolute bottom-0 w-full bg-white rounded-t-[20px] pt-5 pb-[58px]"
      >
        <div class="relative flex items-center justify-between px-5 mb-5">
          <div
            @touchmove="isModalOpen = false"
            class="absolute w-8 h-1 -translate-x-1/2 -top-7 left-1/2 bg-white/35 z-60"
          />
          <h2 class="my-1 text-xl font-medium leading-6 font-wide text-txt">
            Тип товара
          </h2>
          <div
            class="w-8 h-8"
            @click="toggleModal"
          >
            <CloseCross />
          </div>
        </div>
        <div class="px-[18px] mb-2 max-h-[250px] overflow-y-scroll">
          <ul class="flex flex-col p-0">
            <li
              v-for="item in categories"
              :key="item.id"
            >
              <input
                :id="`option-one-${item.id}`"
                v-model="filteredCategories"
                type="checkbox"
                class="option custom-checkbox"
                :value="item.id"
                @change="$emit('filtered', filteredCategories)"
              >
              <label
                :for="`option-one-${item.id}`"
                class="flex items-center w-full gap-[9px] py-2.5 font-compact font-normal text-17 leading-6 text-txt"
              >{{ item.name }}</label>
              <ul class="ml-4 flex flex-col p-0">
                <li
                  v-for="subcategory in item.categories"
                  :key="subcategory.id"
                >
                  <input
                    :id="`subOption-one-${subcategory.id}`"
                    v-model="filteredCategories"
                    type="checkbox"
                    class="subOption custom-checkbox"
                    :value="subcategory.id"
                    @change="$emit('filtered', filteredCategories)"
                  >
                  <label
                    :for="`subOption-one-${subcategory.id}`"
                    class="flex items-center w-full gap-[9px] py-2.5 font-compact font-normal text-17 leading-6 text-txt"
                  >{{ subcategory.name }}</label>
                </li>
              </ul>
            </li>
          </ul>
        </div>

        <div class="flex justify-center gap-3 mx-auto px-[12.5px]">
          <button
            @click="clearFilter"
            class="flex items-center justify-center w-full py-[14px] text-xs font-bold tracking-wider uppercase font-wide text-txt rounded-2xl bg-tertiary"
          >
            Сбросить
          </button>
          <button
            @click="toggleModal"
            class="flex items-center justify-center w-full py-[14px] text-xs font-bold tracking-wider uppercase font-wide text-white rounded-2xl bg-brand"
          >
            Применить
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ChevronDown from '../../assets/img/icons/ChevronDown.vue'
import CloseCross from '../../assets/img/icons/CloseCross.vue'

export default {
  components: {
    ChevronDown,
    CloseCross
  },
  inject: ['isMobile'],
  props: {
    categories: Array
  },
  data() {
    return {
      isModalOpen: false,
      toggleMenu: false,
      filteredCategories: []
    }
  },
  methods: {
    toggleModal() {
      this.isModalOpen = !this.isModalOpen
    },
    clearFilter() {
      this.filteredCategories = []
      this.$emit('filtered', this.filteredCategories)
    }
  }
}
</script>

<style scoped>
.custom-checkbox {
  position: absolute;
  z-index: -1;
  opacity: 0;
}

.custom-checkbox + label {
  display: inline-flex;
  align-items: center;
  user-select: none;
  cursor: pointer;
}
.custom-checkbox + label::before {
  content: "";
  background-image: url("/icons/Checkbox-unchecked.svg");
  display: inline-block;
  width: 24px;
  height: 24px;
}

.custom-checkbox:checked + label::before {
  background-image: url("/icons/Checkbox-checked.svg");
}
.filter-card-btn{
  border: 1px solid #BCC3D080;
}
ul{
  padding: 0;
}
</style>
