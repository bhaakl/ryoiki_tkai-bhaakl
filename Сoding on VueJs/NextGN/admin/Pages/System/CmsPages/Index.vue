<template>
   <CRow>
      <CCol col="12">
         <CCard>
            <CCardHeader>
               <h4>CMS</h4>
            </CCardHeader>
            <CCardBody>
               <CDataTable
                  hover
                  striped
                  clickable-rows
                  @row-clicked="item => $inertia.visit(`/admin/cms-pages/${item.id}`)"
                  :items="pages"
                  :fields="fields"
                  :items-per-page="5"
                  :active-page="activePage"
                  :pagination="{ doubleArrows: false, align: 'center' }"
                  :table-filter="{ placeholder: 'Фильтр', label: ' ' }"
                  :noItemsView="{ noResults: 'Нет подходящих вариантов', noItems: 'Страниц нет' }"
               >
                  <template #index="{ index }">
                     <td>{{ index + 1 }}</td>
                  </template>
               </CDataTable>
            </CCardBody>
         </CCard>
      </CCol>
   </CRow>
</template>

<script>
import Layout from '@admin/containers/TheContainer';

export default {
   name: 'TextPagesIndex',
   layout: Layout,
   props: {
      pages: Array,
   },
   data() {
      return {
         fields: [
            { key: 'index', label: '№', sorter: false },
            { key: 'title', label: 'Название страницы' },
            { key: 'slug', label: 'Слаг' },
         ],
         activePage: 1,
      };
   },
};
</script>
