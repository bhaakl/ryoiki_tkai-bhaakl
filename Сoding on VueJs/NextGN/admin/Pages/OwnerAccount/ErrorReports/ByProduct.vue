<template>
   <CRow>
      <CCol col="12">
         <CCard>
            <CCardHeader>
               <h4>Отчёты об ошибках продукта "{{ product.name }}"</h4>
            </CCardHeader>
            <CCardBody>
               <CDataTable
                  hover
                  striped
                  clickable-rows
                  :table-filter="{ placeholder: 'Фильтр', label: ' ' }"
                  :sorter="{ external: true, resetable: true }"
                  @row-clicked="item => $inertia.visit(`/admin/error-reports/${item.id}`)"
                  @update:sorter-value="sort"
                  :items="items"
                  :fields="fields"
                  :items-per-page="5"
                  :active-page="activePage"
                  :pagination="{ doubleArrows: false, align: 'center' }"
                  :noItemsView="{ noResults: 'Нет подходящих вариантов', noItems: 'Отчетов пока нет' }"
               >
                  <template #index="{ index }">
                     <td>{{ index + 1 }}</td>
                  </template>
                  <template #tester="{ item: { tester } }">
                     <td>{{ tester.name }}</td>
                  </template>
                  <template #is_new="{ item: { is_new } }">
                     <td>
                        <CBadge v-if="is_new" color="danger" class="mb-1 mr-1">
                           Новый
                        </CBadge>
                     </td>
                  </template>
               </CDataTable>
            </CCardBody>
            <CCardFooter>
               <CButton @click="$inertia.visit(`/admin/products/${product.id}`)" color="light">К продукту</CButton>
               <CDropdown color="dark" toggler-text="Выгрузить все" class="float-right">
                  <CDropdownItem :href="`/admin/error-reports/by-product/${product.id}/export/excel`">
                     Как XLSX
                  </CDropdownItem>
                  <CDropdownItem :href="`/admin/error-reports/by-product/${product.id}/export/csv`">
                     Как CSV
                  </CDropdownItem>
                  <CDropdownItem :href="`/admin/error-reports/by-product/${product.id}/export/pdf`">
                     Как PDF
                  </CDropdownItem>
               </CDropdown>
            </CCardFooter>
         </CCard>
      </CCol>
   </CRow>
</template>

<script>
import Layout from '@admin/containers/TheContainer';

export default {
   name: 'ErrorReportsByProduct',
   layout: Layout,
   props: {
      reports: Array,
      product: Object,
   },
   data() {
      return {
         fields: [
            { key: 'index', label: '№', sorter: false },
            { key: 'description', label: 'Описание', sorter: false },
            { key: 'tester', label: 'Тестировщик', _style: 'min-width: 150px;' },
            { key: 'is_new', label: '', _style: 'min-width: 150px;', sorter: false },
         ],
         activePage: 1,
         items: [],
      };
   },
   methods: {
      sort({ asc, column }) {
         if (column === null) {
            // this.items.sort((a, b) => a.id - b.id);
            this.items.sort((a, b) => b.is_new - a.is_new);
         }

         if (column === 'tester') {
            return this.sortByTester(asc);
         }
      },
      sortByTester(asc) {
         this.items.sort((a, b) => {
            if (asc) {
               return a.tester.name > b.tester.name ? -1 : 1;
            }
            return a.tester.name > b.tester.name ? 1 : -1;
         });
      },
   },
   beforeMount() {
      this.items = [...this.reports].sort((a, b) => b.is_new - a.is_new);
   },
};
</script>
