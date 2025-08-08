<template>
   <CRow>
      <CCol col="12">
         <CCard>
            <CCardHeader>
               <h4>Пользователи</h4>
            </CCardHeader>
            <CCardBody>
               <CDataTable
                  hover
                  striped
                  columnFilter
                  :items="items"
                  :fields="fields"
                  :items-per-page="5"
                  :active-page="activePage"
                  :pagination="{ doubleArrows: false, align: 'center' }"
                  :itemsPerPageSelect="{ label: 'Количество:', values: [5, 15, 20] }"
                  :table-filter="{ placeholder: 'Фильтр', label: ' ' }"
                  :noItemsView="{ noResults: 'Нет подходящих вариантов', noItems: 'Пользователей пока нет' }"
               >
                  <template #id="{ item: { id } }">
                     <td>{{ id }}</td>
                  </template>
                  <template #roles_labels="data">
                     <td>
                        <CBadge
                           v-for="role in data.item.roles"
                           :key="role.id"
                           :color="getBadge(role.name)"
                           class="mb-1 mr-1"
                        >
                           {{ role.label }}
                        </CBadge>
                     </td>
                  </template>
                  <template #actions="data">
                     <td style="width:125px">
                        <CButton @click="$inertia.visit(`/admin/users/${data.item.id}/edit`)" color="info">
                           Изменить
                        </CButton>
                     </td>
                  </template>
               </CDataTable>

               <CCol col="6" sm="4" md="2" class="mt-4 mb-3 ml-auto mb-xl-0">
                  <CButton @click="$inertia.visit('/admin/users/create')" block color="success" style="min-width: 90px">
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
   name: 'UsersIndex',
   layout: Layout,
   props: {
      users: Array,
   },
   data() {
      return {
         fields: [
            { key: 'id', label: 'ID', sorter: false, filter: false },
            { key: 'name', label: 'Имя', _classes: 'font-weight-bold' },
            { key: 'roles_labels', label: 'Роли' },
            { key: 'actions', label: 'Действия', filter: false },
         ],
         activePage: 1,
         items: [],
      };
   },
   methods: {
      getBadge(role) {
         switch (role) {
            case 'admin':
               return 'success';
            case 'moderator':
               return 'warning';
            case 'product_owner':
               return 'danger';
            // case 'alpha':
            //    return 'primary';
            // case 'beta':
            //    return 'info';
            default:
               return 'secondary';
         }
      },
   },
   beforeMount() {
      this.items = this.users.map(user => ({
         ...user,
         roles_labels: user.roles.map(role => role.label).join(', '),
      }));
   },
};
</script>
