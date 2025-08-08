<template>
   <CRow class="justify-content-center">
      <CCol col="12">
         <CCard>
            <CCardHeader>
               <h3 class="mb-0">Редактирование страницы</h3>
            </CCardHeader>
            <CCardBody>
               <CForm @submit.prevent="submit">
                  <CInput
                     v-model="$v.title.$model"
                     type="text"
                     label="Заголовок"
                     horizontal
                     :isValid="$v.title.$dirty ? !$v.title.$error : null"
                     invalidFeedback="Заполните это поле"
                  />
                  <CCard>
                     <CCardHeader>Первый блок</CCardHeader>
                     <CCardBody>
                        <div role="group" class="form-group">
                           <label>Заголовок</label>
                           <VueTextEditor v-model="data.hero_block.title" />
                           <div v-show="!!!data.hero_block.title" class="invalid-feedback invalid-feedback-block">Введите текст</div>
                        </div>
                        <div role="group" class="form-group">
                           <label>Подзаголовок</label>
                           <VueTextEditor v-model="data.hero_block.sub_title" />
                           <div v-show="!!!data.hero_block.sub_title" class="invalid-feedback invalid-feedback-block">Введите текст</div>
                        </div>
                        <div role="group" class="form-group">
                           <label>Текст</label>
                           <VueTextEditor v-model="data.hero_block.text" />
                           <div v-show="!!!data.hero_block.text" class="invalid-feedback invalid-feedback-block">Введите текст</div>
                        </div>
                     </CCardBody>
                  </CCard>
                  <CButtonGroup class="mt-3">
                     <CButton type="submit" color="success">Изменить</CButton>
                     <CButton @click="$inertia.visit('/admin/cms-pages')" color="light">Назад</CButton>
                  </CButtonGroup>
               </CForm>
            </CCardBody>
         </CCard>
      </CCol>
      <CModal color="success" centered :show.sync="editModal">
         <template #header>
            <h6 class="modal-title">Изменить страницу?</h6>
         </template>
         Вы уверены, что хотите изменить страницу "{{ page.title }}"?
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
            await this.$inertia.put(`/admin/cms-pages/${this.page.id}`, {
               title: this.title,
               data: this.data,
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
   },
   beforeMount() {
      this.title = this.page.title;
      this.data = this.page.data;
   },
};
</script>
<style>
.invalid-feedback-block {
   display: block;
}
</style>
