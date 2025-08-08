<template>
   <CRow>
      <CCol col="12">
         <CCard>
            <CCardHeader>
               <h4>Шаблоны для SMS</h4>
            </CCardHeader>
            <CCardBody>
               <CDataTable
                  hover
                  striped
                  :items="templates"
                  :fields="fields"
                  :items-per-page="5"
                  :active-page="activePage"
                  :pagination="{ doubleArrows: false, align: 'center' }"
                  :table-filter="{ placeholder: 'Фильтр', label: ' ' }"
                  :noItemsView="{ noResults: 'Нет подходящих вариантов', noItems: 'Шаблонов пока нет' }"
               >
                  <template #actions="data">
                     <td style="width:125px">
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
                     </td>
                  </template>
                  <!-- <template #example="{ item: { example } }">
                     <td>
                        {{ example || '–' }}
                     </td>
                  </template> -->
               </CDataTable>
               <CCol class="ml-auto mt-4 mb-3 mb-xl-0">
                  <CRow class="justify-content-end">
                     <CButton @click="addModal = true" color="success" class="mr-3 mw-90px h-max-content">
                        Добавить
                     </CButton>
                  </CRow>
               </CCol>
            </CCardBody>
         </CCard>
      </CCol>
      <CModal color="success" centered :show.sync="addModal">
         <template #header>
            <h6 class="modal-title">Добавить шаблон</h6>
         </template>
         <CInput
            v-model="$v.newTemplate.id.$model"
            label="ID шаблона"
            horizontal
            :isValid="$v.newTemplate.id.$dirty ? !$v.newTemplate.id.$error : null"
            invalidFeedback="Введите корректный id шаблона"
         />
         <CInput
            v-model="$v.newTemplate.description.$model"
            label="Описание"
            horizontal
            :isValid="$v.newTemplate.description.$dirty ? !$v.newTemplate.description.$error : null"
            invalidFeedback="Введите описание"
         />
         <!-- <CInput
            v-model="$v.newTemplate.example.$model"
            label="Пример сообщения"
            placeholder="необязательно"
            horizontal
         /> -->
         <template #footer>
            <CButton @click="add(true)" color="success">Добавить</CButton>
            <CButton @click="add(false)" color="light">Отмена</CButton>
         </template>
      </CModal>
      <CModal color="danger" centered :show.sync="removeModal">
         <template #header>
            <h6 class="modal-title">Удалить шаблон?</h6>
         </template>
         Вы уверены, что хотите удалить этот шаблон?
         <template #footer>
            <CButton @click="remove(true)" color="danger">Удалить</CButton>
            <CButton @click="remove(false)" color="light">Отмена</CButton>
         </template>
      </CModal>
   </CRow>
</template>

<script>
import { required, integer } from 'vuelidate/lib/validators';
import Layout from '@admin/containers/TheContainer';

export default {
   name: 'MailingSmsTemplates',
   layout: Layout,
   props: {
      templates: Array,
   },
   data() {
      return {
         newTemplate: {
            id: '',
            description: '',
            // example: '',
         },
         fields: [
            { key: 'template_id', label: 'ID шаблона' },
            { key: 'description', label: 'Описание', _classes: 'font-weight-bold' },
            // { key: 'example', label: 'Пример сообщения' },
            { key: 'actions', label: 'Действия', filter: false },
         ],
         activePage: 1,
         addModal: false,
         removeModal: false,
         removeId: null,
      };
   },
   methods: {
      async add(modalValue) {
         if (!modalValue) {
            this.addModal = false;
            this.clearAddModal();
            return;
         }
         this.$v.$touch();
         if (this.$v.$invalid) return;
         this.addModal = false;
         try {
            await this.$inertia.post(`/admin/mailing/sms-templates`, {
               template_id: +this.newTemplate.id,
               description: this.newTemplate.description,
               // example: this.newTemplate.example,
            });
         } catch (e) {
            this.$handleError(e);
         } finally {
            this.clearAddModal();
         }
      },
      async remove(modalValue) {
         this.removeModal = false;
         if (!modalValue) return;
         try {
            await this.$inertia.delete(`/admin/mailing/sms-templates/${this.removeId}`);
         } catch (e) {
            this.$handleError(e);
         } finally {
            this.removeId = null;
         }
      },
      clearAddModal() {
         this.newTemplate = {
            id: '',
            description: '',
            // example: '',
         };
         this.$v.$reset();
      },
   },
   validations: {
      newTemplate: {
         id: {
            required,
            integer,
         },
         description: {
            required,
         },
         // example: {},
      },
   },
};
</script>
