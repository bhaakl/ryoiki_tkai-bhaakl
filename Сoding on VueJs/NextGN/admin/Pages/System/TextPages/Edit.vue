<template>
   <CRow class="justify-content-center">
      <CCol col="12">
         <CCard>
            <CCardHeader>
               <h3 class="mb-0">Изменить страницу</h3>
            </CCardHeader>
            <CCardBody>
               <CForm @submit.prevent="submit">
                  <div role="group" class="form-group">
                     <label>Заголовок</label>
                     <VueTextEditor v-model="$v.title.$model" />
                     <div v-show="$v.title.$error" class="invalid-validation">Введите заголовок</div>
                  </div>

                  <div role="group" class="form-group">
                     <label>Текст</label>
                     <VueTextEditor v-model="$v.text.$model" />
                     <div v-show="$v.text.$error" class="invalid-validation">Введите текст</div>
                  </div>
                  <CButtonGroup class="mt-3">
                     <CButton type="submit" color="success">Изменить</CButton>
                     <CButton @click="$inertia.visit('/admin/text-pages')" color="light">Назад</CButton>
                  </CButtonGroup>
               </CForm>
            </CCardBody>
         </CCard>
      </CCol>
      <CModal color="success" centered :show.sync="editModal">
         <template #header>
            <h6 class="modal-title">Изменить страницу?</h6>
         </template>
         Вы уверены, что хотите изменить страницу "{{ page.title | stripHtml }}"?
         <template #footer>
            <CButton @click="edit(true)" color="success">Изменить</CButton>
            <CButton @click="edit(false)" color="light">Отмена</CButton>
         </template>
      </CModal>
   </CRow>
</template>

<script>
import {required} from 'vuelidate/lib/validators';
import Layout from '@admin/containers/TheContainer';

export default {
   name: 'TextPagesEdit',
   layout: Layout,
   props: {
      page: Object,
   },
   data() {
      return {
         title: '',
         text: '',
         editModal: false,
      };
   },
   methods: {
      submit() {
         this.$v.$touch();
         if (this.$v.$invalid) return;
         this.editModal = true;
      },
      async edit(modalValue) {
         this.editModal = false;
         if (!modalValue) return;
         try {
            await this.$inertia.put(`/admin/text-pages/${this.page.id}`, {
               title: this.title,
               text: this.text,
            });
         } catch (e) {
            this.$handleError(e);
         }
      },
   },
   validations: {
      title: {
         required,
      },
      text: {
         required,
      },
   },
   beforeMount() {
      this.title = this.page.title;
      this.text = this.page.text;
   },
};
</script>
