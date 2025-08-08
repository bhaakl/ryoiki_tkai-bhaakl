<template>
   <CRow class="justify-content-center">
      <CCol col="12">
         <CCard class="mx-4 mb-0">
            <CCardBody class="p-4">
               <CForm @submit.prevent="submit">
                  <h3 class="mb-4">Добавить пользователя</h3>
                  <CInput
                     v-model="$v.name.$model"
                     placeholder="Имя"
                     autocomplete="username"
                     :isValid="$v.name.$dirty ? !$v.name.$error : null"
                     invalidFeedback="Введите имя"
                  >
                     <template #prepend-content><CIcon name="cil-user"/></template>
                  </CInput>
                  <CInput
                     v-model="$v.email.$model"
                     placeholder="Email"
                     autocomplete="email"
                     prepend="@"
                     :isValid="$v.email.$dirty ? !$v.email.$error : null"
                     invalidFeedback="Введите корректный email"
                  />
                  <CInput
                     v-model="$v.password.$model"
                     placeholder="Пароль"
                     type="password"
                     autocomplete="new-password"
                     :isValid="$v.password.$dirty ? !$v.password.$error : null"
                     invalidFeedback="Введите пароль"
                  >
                     <template #prepend-content><CIcon name="cil-lock-locked"/></template>
                  </CInput>
                  <CInput
                     v-model="$v.confirmPassword.$model"
                     placeholder="Повторите пароль"
                     type="password"
                     autocomplete="new-password"
                     :isValid="$v.confirmPassword.$dirty ? !$v.confirmPassword.$error : null"
                     invalidFeedback="Не совпадает с паролем"
                  >
                     <template #prepend-content><CIcon name="cil-lock-locked"/></template>
                  </CInput>
                  <CRow form class="form-group mb-4">
                     <CCol col="12" tag="label" class="col-form-label">
                        Роли:
                     </CCol>
                     <CCol col="12" class="pl-5">
                        <CInputCheckbox
                           v-for="role in allRoles"
                           :key="role.value"
                           :label="role.label"
                           :value="role.value"
                           :name="`role_${role.value}`"
                           :checked="userRoles.includes(role.value)"
                           @input="changeRoles(role.value)"
                        />
                     </CCol>
                  </CRow>
                  <CButton type="submit" color="success" block>Добавить</CButton>
                  <CButton @click="$inertia.visit('/admin/users')" color="light" block>Назад</CButton>
               </CForm>
            </CCardBody>
         </CCard>
      </CCol>
      <CModal color="success" centered :show.sync="addUserModal">
         <template #header>
            <h6 class="modal-title">Добавить пользователя?</h6>
         </template>
         Вы уверены, что хотите добавить пользователя {{ name }}?
         <template #footer>
            <CButton @click="addUser(true)" color="success">Добавить</CButton>
            <CButton @click="addUser(false)" color="light">Отмена</CButton>
         </template>
      </CModal>
   </CRow>
</template>

<script>
import { required, email, sameAs } from 'vuelidate/lib/validators';
import Layout from '@admin/containers/TheContainer';

export default {
   name: 'UsersCreate',
   layout: Layout,
   data() {
      return {
         name: '',
         email: '',
         password: '',
         confirmPassword: '',
         userRoles: [],
         allRoles: [],
         addUserModal: false,
      };
   },
   methods: {
      changeRoles(id) {
         if (this.userRoles.includes(id)) {
            this.userRoles = this.userRoles.filter(role => role != id);
            return;
         }
         this.userRoles.push(id);
      },
      submit() {
         this.$v.$touch();
         if (this.$v.$invalid) return;
         if (this.userRoles.length < 1) {
            this.$toast.warning('У пользователя должна быть хотя бы одна роль');
            return;
         }
         if (!this.email.endsWith('@nextgen.ru')) {
            this.$toast.warning('Доспустимо использование только внутренних адресов на домене xxx.ru');
            return;
         }
         this.addUserModal = true;
      },
      async addUser(modalValue) {
         this.addUserModal = false;
         if (!modalValue) return;
         try {
            await this.$inertia.post('/admin/users', {
               name: this.name,
               email: this.email,
               password: this.password,
               roles: this.userRoles,
            });
         } catch (e) {
            this.$handleError(e);
         }
      },
   },
   validations: {
      name: {
         required,
      },
      email: {
         required,
         email,
      },
      password: {
         required,
      },
      confirmPassword: {
         sameAsPassword: sameAs('password'),
      },
   },
   async beforeMount() {
      this.allRoles = this.$page.props.admin.roles.map(role => ({
         value: role.id,
         label: role.label,
      }));
   },
};
</script>
