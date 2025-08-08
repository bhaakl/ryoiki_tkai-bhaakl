<template>
      <CModal color="success" :closeOnBackdrop="false" centered :show.sync="isShow">
         <template #header>
            <h6 class="modal-title">Добавить по номеру телефона</h6>
         </template>
         <CRow>
            <CCol col="4" class="d-flex align-items-center">
               <label class="mb-0">Введите один или несколько номеров (каждый с новой строки)</label>
            </CCol>
            <CCol col="8">
               <CTextarea
                  v-model="$v.phone.$model"
                  rows="6"
                  :horizontal="{ input: 'col-sm-12' }"
                  :isValid="$v.phone.$dirty ? !$v.phone.$error : null"
                  invalidFeedback="Необходимо ввести один или несколько номеров"
               />
            </CCol>
         </CRow>
         <CRow style="margin-top: 10px">
            <CCol col="4" class="d-flex align-items-center">
               <label class="mb-0">Сеть</label>
            </CCol>
            <CCol col="8">
               <div class="form-check form-check-inline">
                  <input
                     v-model="network_type"
                     class="form-check-input"
                     name="network_type"
                     id="network_type_ngen"
                     type="radio"
                     checked
                     value="ngen">
                  <label class="form-check-label" for="network_type_ngen">Только сеть NextGen</label>
               </div>
               <div class="form-check form-check-inline">
                  <input
                     v-model="network_type"
                     class="form-check-input"
                     name="network_type"
                     id="network_type_internet"
                     type="radio"
                     value="internet">
                  <label class="form-check-label" for="network_type_internet">Интернет (глобальная сеть)</label>
               </div>
            </CCol>
         </CRow>
         <template #footer>
            <CButton @click="send" :disabled="isBtnDisabled" color="success">Добавить</CButton>
            <CButton @click="hide" color="light">Отмена</CButton>
         </template>
      </CModal>
</template>
<script>

import { required, maxLength, url } from 'vuelidate/lib/validators';

export default {
   name: 'AddNewAlphaTesters',
   props: {
      productId: Number
   },
   data() {
      return {
         isShow: false,
         phone: '',
         email: '',
         network_type: 'ngen',
      }
   },
   computed: {
      isBtnDisabled() {
         return this.email.length === 0 || !this.email.includes("@");
      },
   },
   methods: {
      show() {
         this.isShow = true;
      },
      hide() {
         this.isShow = false;
      },
      init() {
         this.email = this.$page.props.user.email;
      },
      async send() {
         if (this.isBtnDisabled) {
            return;
         }

         this.$v.$touch();
         if (this.$v.$invalid) {
            return;
         }

         try {
            await this.$inertia.post(
               `/admin/products/${this.productId}/add-new-alpha-testers`,
               {
                  phone: this.phone,
                  network_type: this.network_type,
                  productId: this.productId,
               },
               { preserveScroll: true }
            );
            this.hide();
         } catch (e) {
            this.$handleError(e);
         }
      }
   },
   watch: {
      isShow() {
         this.init();
      }
   },
   mounted() {
      this.init();
   },

   validations() {
      return {
         phone: {
            required,
         },
      };
   },
}
</script>
