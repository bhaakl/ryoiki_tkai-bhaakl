<template>
   <CDataTable
      hover
      clickable-rows
      @row-clicked="tester => $inertia.visit(`/admin/testers/${tester.id}`)"
      :items="testers"
      :fields="fields"
      :items-per-page="5"
      :active-page="activePage"
      :pagination="{ doubleArrows: false, align: 'center' }"
   >
      <template #given_at="{ item: {pivot: {created_at}} } ">
         <td>{{ givenAt(created_at) }}</td>
      </template>
      <template #no-items-view>
         <p class="font-weight-bold text-center mt-2">Пока ни одного</p>
      </template>
   </CDataTable>
</template>

<script>
export default {
   name: 'RewardTesters',
   props: {
      testers: Array,
   },
   data() {
      return {
         fields: [
            { key: 'name', label: 'Кому', _style: 'min-width: 200px;' },
            { key: 'given_at', label: 'Дата', _style: 'min-width: 200px;' },
         ],
         activePage: 1,
      };
   },
   methods: {
      givenAt(date) {
         return this.$moment(date).format('DD MMMM YYYY');
      },
   },
};
</script>
