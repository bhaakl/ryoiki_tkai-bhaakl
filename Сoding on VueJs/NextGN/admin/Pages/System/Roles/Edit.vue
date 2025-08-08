<template>
   <CRow class="justify-content-center">
      <CCol col="12" lg="10" xl="8">
         <CCard>
            <CCardHeader>
               <h4>{{ role.label }}</h4>
            </CCardHeader>
            <CForm @submit.prevent="submit">
               <CCardBody>
                  <CInput
                     v-model="$v.role.label.$model"
                     label="Роль"
                     horizontal
                     :isValid="$v.role.label.$dirty ? !$v.role.label.$error : null"
                     invalidFeedback="Введите роль"
                  />
                  <CInput v-model="$v.role.name.$model" label="Название" horizontal disabled />
               </CCardBody>
               <CCardFooter>
                  <CButton type="submit" color="success">Изменить</CButton>
                  <CButton @click="$inertia.visit('/admin/roles')" type="reset" color="light">Назад</CButton>
                  <CButton @click="deleteRoleModal = true" color="danger" class="float-right">Удалить</CButton>
               </CCardFooter>
            </CForm>
         </CCard>
      </CCol>
      <CModal color="success" centered :show.sync="editRoleModal">
         <template #header>
            <h6 class="modal-title">Изменить роль?</h6>
         </template>
         Вы уверены, что хотите изменить роль {{ role.label }}?
         <template #footer>
            <CButton @click="editRole(true)" color="success">Изменить</CButton>
            <CButton @click="editRole(false)" color="light">Отмена</CButton>
         </template>
      </CModal>
      <CModal color="danger" centered :show.sync="deleteRoleModal">
         <template #header>
            <h6 class="modal-title">Удалить роль?</h6>
         </template>
         Вы уверены, что хотите удалить роль {{ role.label }}?
         <template #footer>
            <CButton @click="deleteRole(true)" color="danger">Удалить</CButton>
            <CButton @click="deleteRole(false)" color="light">Отмена</CButton>
         </template>
      </CModal>
   </CRow>
</template>

<script>
import { required } from 'vuelidate/lib/validators';
import Layout from '@admin/containers/TheContainer';

export default {
   name: 'EditRole',
   layout: Layout,
   props: {
      role: Object,
   },
   data() {
      return {
         editRoleModal: false,
         deleteRoleModal: false,
      };
   },
   methods: {
      submit() {
         this.$v.$touch();
         if (this.$v.$invalid) return;
         this.editRoleModal = true;
      },
      async editRole(modalValue) {
         this.editRoleModal = false;
         if (!modalValue) return;
         try {
            await this.$inertia.put(`/admin/roles/${this.role.id}`, {
               label: this.role.label,
               //    name: this.role.name,
            });
         } catch (e) {
            this.$handleError(e);
         }
      },
      async deleteRole(modalValue) {
         this.deleteRoleModal = false;
         if (!modalValue) return;
         try {
            await this.$inertia.delete(`/admin/roles/${this.role.id}`);
         } catch (e) {
            this.$handleError(e);
         }
      },
   },
   validations: {
      role: {
         label: {
            required,
         },
         name: {
            required,
         },
      },
   },
};
</script>
