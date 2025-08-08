<template>
   <CRow class="justify-content-center">
      <CCol md="8" xl="6">
         <CCard class="mx-4 mb-0">
            <CCardBody class="p-4">
               <CForm @submit.prevent="submit">
                  <h3 class="mb-4">Добавить роль</h3>
                  <CInput
                     v-model.trim="$v.label.$model"
                     placeholder="Роль"
                     :isValid="$v.label.$dirty ? !$v.label.$error : null"
                     invalidFeedback="Введите роль"
                  />
                  <CInput
                     v-model.trim="$v.name.$model"
                     placeholder="Название"
                     :isValid="$v.name.$dirty ? !$v.name.$error : null"
                     invalidFeedback="Введите название"
                  />
                  <CButton type="submit" color="success" block>Добавить</CButton>
                  <CButton @click="$inertia.visit('/admin/roles')" color="light" block>Назад</CButton>
               </CForm>
            </CCardBody>
         </CCard>
      </CCol>
      <CModal color="success" centered :show.sync="addRoleModal">
         <template #header>
            <h6 class="modal-title">Добавить роль?</h6>
         </template>
         Вы уверены, что хотите добавить роль {{ label }}?
         <template #footer>
            <CButton @click="addRole(true)" color="success">Добавить</CButton>
            <CButton @click="addRole(false)" color="light">Отмена</CButton>
         </template>
      </CModal>
   </CRow>
</template>

<script>
import { required } from 'vuelidate/lib/validators';
import Layout from '@admin/containers/TheContainer';

export default {
   name: 'AddRole',
   layout: Layout,
   data() {
      return {
         label: '',
         name: '',
         addRoleModal: false,
      };
   },
   methods: {
      submit() {
         this.$v.$touch();
         if (this.$v.$invalid) return;
         this.addRoleModal = true;
      },
      async addRole(modalValue) {
         this.addRoleModal = false;
         if (!modalValue) return;
         try {
            await this.$inertia.post('/admin/roles', {
               label: this.label,
               name: this.name,
            });
         } catch (e) {
            this.$handleError(e);
         }
      },
   },
   validations: {
      label: {
         required,
      },
      name: {
         required,
      },
   },
};
</script>
