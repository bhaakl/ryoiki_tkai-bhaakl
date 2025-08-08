<template>
   <CRow class="justify-content-center">
      <CCol col="12">
         <CCard>
            <CCardHeader>
               <h3 class="mb-0">Изменить вознаграждение</h3>
            </CCardHeader>
            <CCardBody>
               <CForm @submit.prevent="submit">
                  <CInput
                     v-model="$v.title.$model"
                     label="Название"
                     horizontal
                     :isValid="$v.title.$dirty ? !$v.title.$error : null"
                     invalidFeedback="Введите название"
                  />
                  <div role="group" class="form-group form-row">
                     <label class="col-form-label col-sm-3">
                        Описание
                     </label>
                     <div class="col-sm-9">
                        <VueTextEditor v-model="$v.description.$model" />
                        <div v-show="$v.description.$error" class="invalid-validation">Введите описание</div>
                     </div>
                  </div>
                  <CSelect :value.sync="type" :options="typeOptions" label="Тип" horizontal />
                  <CInput
                     v-if="type === 'public'"
                     v-model="$v.promocode.$model"
                     label="Промокод"
                     horizontal
                     :isValid="$v.promocode.$dirty ? !$v.promocode.$error : null"
                     invalidFeedback="Введите промокод"
                  />
                  <CInputFile
                     @change="onChangeImage"
                     label="Изображение"
                     class="mb-4"
                     horizontal
                  >
                     <template #description>
                        <small class="form-text text-muted w-100">
                           Если оставить пустым, изображение останется прежним.<br />
                           Допустимые форматы: jpg, png.<br />
                           Размер изображения: 640 x 280.<br />
                        </small>
                     </template>
                  </CInputFile>
                  <cropper
                     v-if="previewImageUrl"
                     :src="previewImageUrl"
                     :stencil-props="{
                        aspectRatio: 25 / 11,
                     }"
                     class="cropper mb-4"
                     style="max-height: 500px;"
                     ref="cropper"
                  />
                  <CButtonGroup>
                     <CButton type="submit" color="success">Изменить</CButton>
                     <CButton @click="$inertia.visit(`/admin/rewards/${reward.id}`)" color="light">
                        К вознаграждению
                     </CButton>
                  </CButtonGroup>
                  <CButton @click="removeModal = true" color="danger" class="float-right">Удалить</CButton>
               </CForm>
            </CCardBody>
         </CCard>
      </CCol>
      <CModal color="success" centered :show.sync="editModal">
         <template #header>
            <h6 class="modal-title">Изменить вознаграждение?</h6>
         </template>
         Вы уверены, что хотите изменить вознаграждение "{{ title }}"?
         <template #footer>
            <CButton @click="edit(true)" color="success">Изменить</CButton>
            <CButton @click="edit(false)" color="light">Отмена</CButton>
         </template>
      </CModal>
      <CModal color="danger" centered :show.sync="removeModal">
         <template #header>
            <h6 class="modal-title">Удалить вознаграждение?</h6>
         </template>
         Вы уверены, что хотите удалить вознаграждение "{{ title }}"?
         <template #footer>
            <CButton @click="remove(true)" color="danger">Удалить</CButton>
            <CButton @click="remove(false)" color="light">Отмена</CButton>
         </template>
      </CModal>
   </CRow>
</template>

<script>
import { required, requiredIf, between } from 'vuelidate/lib/validators';
import { Cropper } from 'vue-advanced-cropper';
import Layout from '@admin/containers/TheContainer';

export default {
   name: 'RewardsEdit',
   layout: Layout,
   props: {
      reward: Object,
   },
   components: {
      Cropper
   },
   data() {
      return {
         title: '',
         description: '',
         type: null,
         promocode: '',
         image: null,
         editModal: false,
         removeModal: false,
         typeOptions: [
            { value: 'public', label: 'Общий промокод' },
            { value: 'private', label: 'Личный промокод' },
            { value: 'gift', label: 'Ценные призы' },
         ],
         previewImageUrl: null,
      };
   },
   methods: {
      onChangeImage(files) {
         this.image = files[0];
         this.previewImageUrl = URL.createObjectURL(this.image);
      },
      submit() {
         this.$v.$touch();
         if (this.$v.$invalid) return;
         this.editModal = true;
      },
      async edit(modalValue) {
         this.editModal = false;
         if (!modalValue) return;
         try {
            const formData = new FormData();
            formData.append('title', this.title);
            formData.append('type', this.type);
            formData.append('description', this.description);
            if (this.type === 'public') {
               formData.append('promocode', this.promocode);
            }
            if (this.image) {
               formData.append('image', this.image);
               const { coordinates } = this.$refs.cropper.getResult();
               for (let value in coordinates) {
                  formData.append('сoordinates[]', coordinates[value]);
               }
            }
            await this.$inertia.post(`/admin/rewards/${this.reward.id}`, formData, {
               preserveScroll: true,
            });
         } catch (e) {
            this.$handleError(e);
         }
      },
      async remove(modalValue) {
         this.removeModal = false;
         if (!modalValue) return;
         try {
            await this.$inertia.delete(`/admin/rewards/${this.reward.id}`, {
               preserveScroll: true,
            });
         } catch (e) {
            this.$handleError(e);
         }
      },
      setPublicPromocode() {
         if (this.type !== 'public' || !this.reward.promocodes) return;
         const promocode = this.reward.promocodes.find(promocode => promocode.is_public);
         if (!promocode) return;
         this.promocode = promocode.code;
      },
   },
   watch: {
      type(value) {
         if (value !== 'public') return;
         this.setPublicPromocode();
      },
   },
   validations: {
      title: {
         required,
      },
      description: {
         required,
      },
      promocode: {
         required: requiredIf(function() {
            return this.type === 'public';
         }),
      },
   },
   beforeMount() {
      this.title = this.reward.title;
      this.type = this.reward.type;
      this.setPublicPromocode();
      this.description = this.reward.description;
   },
};
</script>

<style scoped>
.invalid-validation {
   width: 100%;
   margin-top: 0.25rem;
   font-size: 80%;
   color: #e55353;
}
</style>
