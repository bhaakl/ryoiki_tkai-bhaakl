<template>
   <CRow>
      <CCol col="12">
         <CCard>
            <CCardHeader>
               <h4>Типы занятости</h4>
            </CCardHeader>
            <CCardBody>
               <CDataTable
                  hover
                  striped
                  columnFilter
                  :items="occupations"
                  :fields="fields"
                  :items-per-page="5"
                  :active-page="activePage"
                  :pagination="{ doubleArrows: false, align: 'center' }"
                  :table-filter="{ placeholder: 'Фильтр', label: ' ' }"
                  :noItemsView="{ noResults: 'Нет подходящих вариантов', noItems: 'Типов занятости пока нет' }"
               >
                  <template #index="{ index }">
                     <td>{{ index + 1 }}</td>
                  </template>
                  <template #actions="data">
                     <td style="width:125px">
                        <CButtonGroup>
                           <CButton
                              @click="
                                 editModal = true;
                                 editId = data.item.id;
                                 editName = data.item.name;
                              "
                              color="info"
                              class="mw-90px"
                           >
                              Изменить
                           </CButton>
                           <CButton
                              @click="
                                 removeModal = true;
                                 removeId = data.item.id;
                              "
                              color="danger"
                              class="mw-90px"
                           >
                              Удалить
                           </CButton>
                        </CButtonGroup>
                     </td>
                  </template>
               </CDataTable>

               <CCol class="ml-auto mt-4 mb-3 mb-xl-0">
                  <CRow class="justify-content-end">
                     <CCol sm="6" lg="5" xl="4" class="pr-2">
                        <CInput
                           v-model="$v.newOccupation.$model"
                           type="text"
                           placeholder="Новый тип занятости"
                           :isValid="$v.newOccupation.$dirty ? !$v.newOccupation.$error : null"
                           invalidFeedback="Введите тип занятости"
                        />
                     </CCol>
                     <CButton @click="submitAdd" color="success" class="mr-3 mw-90px h-max-content">
                        Добавить
                     </CButton>
                  </CRow>
               </CCol>
            </CCardBody>
         </CCard>
      </CCol>
      <CModal color="success" centered :show.sync="addModal">
         <template #header>
            <h6 class="modal-title">Добавить новый тип занятости?</h6>
         </template>
         Вы уверены, что хотите добавить тип занятости {{ newOccupation }}?
         <template #footer>
            <CButton @click="add(true)" color="success">Добавить</CButton>
            <CButton @click="add(false)" color="light">Отмена</CButton>
         </template>
      </CModal>
      <CModal color="info" centered :show.sync="editModal">
         <template #header>
            <h6 class="modal-title">Изменить тип занятости</h6>
         </template>
         <CInput
            v-model="$v.editName.$model"
            :isValid="$v.editName.$dirty ? !$v.editName.$error : null"
            invalidFeedback="Введите тип занятости"
         />
         <template #footer>
            <CButton @click="submitEdit" color="info">Изменить</CButton>
            <CButton @click="cancelEdit" color="light">Отмена</CButton>
         </template>
      </CModal>
      <CModal color="danger" centered :show.sync="removeModal">
         <template #header>
            <h6 class="modal-title">Удалить тип занятости?</h6>
         </template>
         Вы уверены, что хотите удалить этот тип занятости?
         <template #footer>
            <CButton @click="remove(true)" color="danger">Удалить</CButton>
            <CButton @click="remove(false)" color="light">Отмена</CButton>
         </template>
      </CModal>
   </CRow>
</template>

<script>
import { required } from 'vuelidate/lib/validators';
import Layout from '@admin/containers/TheContainer';

export default {
   name: 'Occupations',
   layout: Layout,
   props: {
      occupations: Array,
   },
   data() {
      return {
         newOccupation: '',
         fields: [
            // { key: 'index', label: '№', sorter: false, filter: false },
            { key: 'name', label: 'Тип занятости', _classes: 'font-weight-bold' },
            { key: 'actions', label: 'Действия', filter: false },
         ],
         activePage: 1,
         addModal: false,
         editModal: false,
         editId: null,
         editName: '',
         removeModal: false,
         removeId: null,
      };
   },
   methods: {
      submitAdd() {
         this.$v.newOccupation.$touch();
         if (this.$v.newOccupation.$invalid) return;
         this.addModal = true;
      },
      submitEdit() {
         this.$v.editName.$touch();
         if (this.$v.editName.$invalid) return;
         this.edit();
      },
      cancelEdit() {
         this.editModal = false;
      },
      async add(modalValue) {
         this.addModal = false;
         if (!modalValue) return;
         try {
            await this.$inertia.post(`/admin/occupations`, {
               name: this.newOccupation,
            });
         } catch (e) {
            this.$handleError(e);
         } finally {
            this.newOccupation = '';
            this.$v.$reset();
         }
      },
      async edit() {
         this.editModal = false;
         try {
            await this.$inertia.put(`/admin/occupations/${this.editId}`, {
               name: this.editName,
            });
         } catch (e) {
            this.$handleError(e);
         } finally {
            this.editId = null;
            this.editName = '';
         }
      },
      async remove(modalValue) {
         this.removeModal = false;
         if (!modalValue) return;
         try {
            await this.$inertia.delete(`/admin/occupations/${this.removeId}`);
         } catch (e) {
            this.$handleError(e);
         } finally {
            this.removeId = null;
         }
      },
   },
   validations: {
      newOccupation: {
         required,
      },
      editName: {
         required,
      },
   },
};
</script>
