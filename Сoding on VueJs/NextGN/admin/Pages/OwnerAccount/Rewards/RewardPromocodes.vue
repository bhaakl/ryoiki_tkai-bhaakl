<template>
   <CDataTable
      hover
      striped
      column-filter
      :table-filter="{ placeholder: 'Фильтр', label: ' ' }"
      :sorter="{ external: true, resetable: true }"
      :items="privatePromocodes"
      :fields="fields"
      :items-per-page="15"
      :active-page="activePage"
      :pagination="{ doubleArrows: false, align: 'center' }"
   >
      <template #tester="{ item } ">
         <td>{{ tester(item) }}</td>
      </template>
      <template #given_at="{ item } ">
         <td>{{ givenAt(item) }}</td>
      </template>
      <template #no-items-view>
         <p class="font-weight-bold text-center mt-2">Пока ни одного</p>
      </template>
   </CDataTable>
</template>

<script>
export default {
   name: 'RewardPromocodes',
   props: {
      promocodes: Array,
   },
   data() {
      return {
         fields: [
            { key: 'code', label: 'Код', _style: 'min-width: 200px;' },
            { key: 'tester', label: 'Кому выдан', _style: 'min-width: 200px;' },
            { key: 'given_at', label: 'Дата выдачи', _style: 'min-width: 200px;' },
         ],
         activePage: 1,
         privatePromocodes: [],
      };
   },
   methods: {
      tester({ tester }) {
         return tester ? tester.name : '–';
      },
      givenAt({ tester, given_at }) {
         return tester && given_at ? this.$moment(given_at).format('DD MMMM YYYY') : '–';
      },
      calculatePrivatePromocodes() {
         this.privatePromocodes = this.promocodes.filter(promocode => promocode.is_public === 0);
      },
   },
   watch: {
      promocodes() {
         this.calculatePrivatePromocodes();
      },
   },
   beforeMount() {
      this.calculatePrivatePromocodes();
   },
};
</script>
