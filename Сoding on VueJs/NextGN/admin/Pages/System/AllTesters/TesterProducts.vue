<template>
   <CDataTable
      hover
      clickable-rows
      @row-clicked="({ id }) => $inertia.visit(`/admin/products/${id}`)"
      :items="products"
      :fields="fields"
      :items-per-page="5"
      :active-page="activePage"
      :pagination="{ doubleArrows: false, align: 'center' }"
      :noItemsView="{ noResults: 'Нет подходящих вариантов', noItems: 'Пока ни одного' }"
   >
      <template #status="{ item: { is_active } }">
         <td>
            <CBadge :color="getBadgeColor(is_active)" class="mr-1 mb-1">
               {{ getStatusText(is_active) }}
            </CBadge>
         </td>
      </template>
      <template #is_tester_active="{ item: { pivot: { is_active } } }">
         <td>
            <CBadge :color="getBadgeColor(is_active)" class="mr-1 mb-1">
               {{ getActivityText(is_active) }}
            </CBadge>
         </td>
      </template>
      <template #no-items-view>
         <p class="font-weight-bold text-center mt-2">Пока ни одного</p>
      </template>
   </CDataTable>
</template>

<script>
export default {
   name: 'TesterProducts',
   props: {
      products: Array,
   },
   data() {
      return {
         fields: [
            { key: 'name', label: 'Название', sorter: false },
            { key: 'status', label: 'Статус проекта', sorter: false },
            // { key: 'is_tester_active', label: 'Активность' },
         ],
         activePage: 1,
      };
   },
   methods: {
      getStatusText(status) {
         switch (status) {
            case 0:
               return 'Неактивно';
            case 1:
               return 'В ожидании';
            case 2:
               return 'Активно';
            default:
               return 'Неизвестно';
         }
      },
      getActivityText(status) {
         switch (status) {
            case 0:
               return 'Завершил';
            case 1:
               return 'В ожидании';
            case 2:
               return 'Участвует';
            default:
               return 'Неизвестно';
         }
      },
      getBadgeColor(status) {
         switch (status) {
            case 0:
               return 'danger';
            case 1:
               return 'warning';
            case 2:
               return 'success';
            default:
               return 'secondary';
         }
      },
   },
   beforeMount() {
      this.products.sort((a, b) => {
         return a.pivot.is_active > b.pivot.is_active ? -1 : 1;
      });
   },
};
</script>
