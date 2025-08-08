<template>
   <CDataTable
      hover
      striped
      :table-filter="{ placeholder: 'Фильтр', label: ' ' }"
      :items="products"
      :fields="fields"
      :items-per-page="30"
      :active-page="activePage"
      :pagination="{ doubleArrows: false, align: 'center' }"
      :noItemsView="{ noResults: 'Нет подходящих вариантов', noItems: 'Продуктов пока нет' }"
   >
      <template #sort="{ item: { id } }">
         <td class="d-flex justify-content-center align-items-center">
            <CButtonGroup vertical size="sm">
               <inertia-link
                  :href="`/admin/all-products/move-order-up/${id}`"
                  method="post"
                  preserve-scroll
                  preserve-state
                  as="CButton"
                  color="light"
                  class="row-drag-btn"
               >
                  <CIcon size="sm" name="cil-arrow-thick-top" />
               </inertia-link>
               <inertia-link
                  :href="`/admin/all-products/move-order-down/${id}`"
                  method="post"
                  preserve-scroll
                  preserve-state
                  as="CButton"
                  color="light"
                  class="row-drag-btn"
               >
                  <CIcon size="sm" name="cil-arrow-thick-bottom" />
               </inertia-link>
            </CButtonGroup>
         </td>
      </template>
      <template #index="{ index }">
         <td>{{ index + 1 }}</td>
      </template>
      <template #is_active="{ item: { is_active } }">
         <td>
            <CBadge :color="getBadgeColor(is_active)" class="mb-1 mr-1">
               {{ getStatusText(is_active) }}
            </CBadge>
         </td>
      </template>
      <template #users="{ item: { id } }">
         <td>
            <CButton @click="$emit('set-product-owner', id)" color="info">
               Назначить
            </CButton>
         </td>
      </template>
      <template #actions="{ item: { id } }">
         <td>
            <CButton @click="$inertia.visit(`/admin/all-products/${id}/edit`)" color="success">
               Изменить
            </CButton>
         </td>
      </template>
      <template #description="{ item: { description } }">
         <td><small v-html="description"></small></td>
      </template>
   </CDataTable>
</template>

<script>
export default {
   name: 'AllProductsTable',
   props: {
      products: Array,
      sorter: {
         type: Boolean,
         default: true,
      },
   },
   data() {
      return {
         fieldValues: [
            { key: 'sort', label: 'Сортировка', sorter: false, filter: false },
            { key: 'index', label: '№', sorter: false },
            { key: 'name', label: 'Название', _classes: 'font-weight-bold', _style: 'min-width: 250px;' },
            { key: 'description', label: 'Описание', sorter: false },
            { key: 'is_active', label: 'Статус', _style: 'min-width: 115px;' },
            { key: 'users', label: 'Пользователи', _style: 'min-width: 115px;' },
            { key: 'actions', label: 'Действия', _style: 'min-width: 115px;' },
         ],
         activePage: 1,
      };
   },
   computed: {
      fields() {
         if (!this.sorter) {
            return this.fieldValues.filter(field => field.key !== 'sort');
         }
         return this.fieldValues;
      },
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
};
</script>
