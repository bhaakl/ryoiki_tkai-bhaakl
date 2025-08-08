<template>
  <div class="relative flex items-center gap-1 p-3 text-sm font-medium font-compact text-txt group">
    <span class="whitespace-nowrap">{{ dropdownLabel }}</span><img
      src="../../assets/icons/filter-icon.svg"
      alt=""
    >
    <ul
      class="absolute z-10 bg-white top-full translate-y-[1px] -translate-x-3 rounded-2xl p-1.5 shadow-shadow max-w-[164px] m-0 group-hover:block hidden"
    >
      <li
        v-for="item in dropdownItems"
        :key="item.value"
        class="w-full"
      >
        <input
          :id="item.value"
          v-model="selectedFilters"
          type="checkbox"
          :checked="item.checked"
          class="option custom-checkbox"
          :value="item.value"
        >
        <label
          :for="item.value"
          class="flex items-end w-full gap-3 p-1.5 font-compact font-normal text-sm text-txt rounded-xl hover:bg-divider/25"
        >{{
          item.label }}</label>
      </li>
    </ul>
  </div>
</template>

<script>

export default {
  props: {
    dropdownType: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      selectedFilters: []
    }
  },
  computed: {
    dropdownItems() {
      if (this.dropdownType === 'operation type') {
        return [
          { value: 'accrual', label: 'Начисление', checked: true },
          { value: 'replenishment', label: 'Расходы', checked: true }
        ]
      } else if (this.dropdownType === 'status') {
        return [
          { value: 'processing', label: 'В процессе', checked: true },
          { value: 'sent', label: 'Отправлено', checked: true },
          { value: 'delivered', label: 'Доставлено', checked: true }
        ]
      } else if (this.dropdownType === 'cashback status') {
        return [
          { value: 'processing', label: 'Обрабатывается', checked: true },
          { value: 'accrued', label: 'Начислено', checked: true }
        ]
      }

      return []
    },
    dropdownLabel() {
      if (this.dropdownType === 'operation type') {
        return 'Тип операции'
      } else if (this.dropdownType === 'status' || this.dropdownType === 'cashback status') {
        return 'Статус'
      }

      return ''
    }
  },
  watch: {
    selectedFilters: {
      handler() {
        this.$emit('checkedFilter', this.selectedFilters)
      }, deep: true
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
  width: 16px;
  height: 16px;
}

.custom-checkbox:checked + label::before {
  background-image: url("/icons/Checkbox-checked.svg");
}
</style>
