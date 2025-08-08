<template>
   <CRow>
      <CCol col="12">
         <CCard>
            <CCardHeader>
               <h4>Версии {{ os == 'android' ? 'Android' : 'iOS' }}</h4>
            </CCardHeader>
            <CCardBody>
               <CDataTable
                  hover
                  striped
                  :items="versions"
                  :fields="fields"
                  :items-per-page="5"
                  :active-page="activePage"
                  :pagination="{ doubleArrows: false, align: 'center' }"
                  :noItemsView="{ noResults: 'Нет подходящих вариантов', noItems: 'Версий ОС пока нет' }"
               >
                  <template #actions="data">
                     <td style="width:125px">
                        <CButton
                           @click="
                              deleteVersionModal = true;
                              deleteVersionId = data.item.id;
                           "
                           color="danger"
                           class="mw-90px"
                        >
                           Удалить
                        </CButton>
                     </td>
                  </template>
               </CDataTable>

               <CCol class="ml-auto mt-4 mb-3 mb-xl-0">
                  <CRow class="justify-content-end">
                     <CCol sm="6" lg="5" xl="4" class="pr-2">
                        <CInput
                           v-model="$v.newVersion.$model"
                           type="text"
                           placeholder="Новая версия"
                           description="В формате x.x.x (1.0.1)"
                           :isValid="$v.newVersion.$dirty ? !$v.newVersion.$error : null"
                           invalidFeedback="Введите корректную версию ОС"
                        />
                     </CCol>
                     <CButton @click="submitAddVersion" color="success" class="mr-4 mw-90px h-max-content">
                        Добавить
                     </CButton>
                  </CRow>
               </CCol>
            </CCardBody>
         </CCard>
      </CCol>
      <CModal color="success" centered :show.sync="addVersionModal">
         <template #header>
            <h6 class="modal-title">Добавить новую версию?</h6>
         </template>
         Вы уверены, что хотите добавить версию {{ newVersion }}?
         <template #footer>
            <CButton @click="addVersion(true)" color="success">Добавить</CButton>
            <CButton @click="addVersion(false)" color="light">Отмена</CButton>
         </template>
      </CModal>
      <CModal color="danger" centered :show.sync="deleteVersionModal">
         <template #header>
            <h6 class="modal-title">Удалить версию?</h6>
         </template>
         Вы уверены, что хотите удалить версию?
         <template #footer>
            <CButton @click="deleteVersion(true)" color="danger">Удалить</CButton>
            <CButton @click="deleteVersion(false)" color="light">Отмена</CButton>
         </template>
      </CModal>
   </CRow>
</template>

<script>
import Layout from '@admin/containers/TheContainer';
import { required } from 'vuelidate/lib/validators';

export default {
   name: 'OSVersions',
   layout: Layout,
   props: {
      os: String,
      versions: Array,
   },
   data() {
      return {
         newVersion: '',
         fields: [
            { key: 'version', label: 'Версия', _classes: 'font-weight-bold' },
            { key: 'actions', label: 'Действия' },
         ],
         activePage: 1,
         addVersionModal: false,
         deleteVersionModal: false,
         deleteVersionId: null,
      };
   },
   methods: {
      submitAddVersion() {
         this.$v.$touch();
         if (this.$v.$invalid) return;
         this.addVersionModal = true;
      },
      async addVersion(modalValue) {
         this.addVersionModal = false;
         if (!modalValue) return;
         try {
            await this.$inertia.post(`/admin/os-versions/${this.os}`, {
               version: this.newVersion,
            });
         } catch (e) {
            this.$handleError(e);
         } finally {
            this.newVersion = '';
            this.$v.$reset();
         }
      },
      async deleteVersion(modalValue) {
         this.deleteVersionModal = false;
         if (!modalValue) return;
         try {
            await this.$inertia.delete(`/admin/os-versions/${this.deleteVersionId}`);
         } catch (e) {
            this.$handleError(e);
         } finally {
            this.deleteVersionId = null;
         }
      },
   },
   validations: {
      newVersion: {
         required,
         osVersion: value => /^\d+\.\d+\.\d+$/.test(value),
      },
   },
};
</script>
