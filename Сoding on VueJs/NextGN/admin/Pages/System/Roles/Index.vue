<template>
   <CRow>
      <CCol col="12">
         <CCard>
            <CCardHeader>
               <h4>Роли</h4>
            </CCardHeader>
            <CCardBody>
               <CDataTable
                  hover
                  striped
                  :items="$page.props.admin.roles"
                  :fields="fields"
                  :items-per-page="5"
                  :active-page="activePage"
                  :table-filter="{ placeholder: 'Фильтр', label: ' ' }"
                  :pagination="{ doubleArrows: false, align: 'center' }"
                  :noItemsView="{ noResults: 'Нет подходящих вариантов', noItems: 'Ролей пока нет' }"
               >
                  <template #index="{ index }">
                     <td>{{ index + 1 }}</td>
                  </template>
                  <template #actions="data">
                     <td style="width:125px">
                        <CButton @click="$inertia.visit(`/admin/roles/${data.item.id}`)" color="info">
                           Изменить
                        </CButton>
                     </td>
                  </template>
               </CDataTable>

               <CCol col="6" sm="4" md="2" class="ml-auto mt-4 mb-3 mb-xl-0">
                  <CButton @click="$inertia.visit('/admin/roles/create')" block color="success" style="min-width: 90px">
                     Добавить
                  </CButton>
               </CCol>
            </CCardBody>
         </CCard>
      </CCol>
   </CRow>
</template>

<script>
import Layout from '@admin/containers/TheContainer';

export default {
   name: 'RolesIndex',
   layout: Layout,
   data() {
      return {
         fields: [
            { key: 'index', label: '№', sorter: false },
            { key: 'label', label: 'Роль', _classes: 'font-weight-bold' },
            { key: 'name', label: 'Название' },
            { key: 'actions', label: 'Действия' },
         ],
         activePage: 1,
      };
   },
};
</script>
