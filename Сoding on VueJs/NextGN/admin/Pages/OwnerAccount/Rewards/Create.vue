<template>
   <CRow class="justify-content-center">
      <CCol col="12">
         <CCard>
            <CCardHeader>
               <h3 class="mb-0">Добавить вознаграждение</h3>
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
                  <div role="group" class="form-group">
                     <label>Описание</label>
                     <VueTextEditor v-model="$v.description.$model" />
                     <div v-show="$v.description.$dirty ? !$v.description.$error : null" class="invalid-feedback invalid-feedback-block">Введите описание</div>
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
                     :isValid="$v.image.$dirty ? ($v.image.$error ? false : null) : null"
                     invalidFeedback="Добавьте изображение"
                  />
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
                     <CButton type="submit" color="success">Добавить</CButton>
                     <CButton @click="$inertia.visit('/admin/rewards')" color="light">К вознаграждениям</CButton>
                  </CButtonGroup>
               </CForm>
            </CCardBody>
         </CCard>
      </CCol>
      <CModal color="success" centered :show.sync="addModal">
         <template #header>
            <h6 class="modal-title">Добавить вознаграждение?</h6>
         </template>
         Вы уверены, что хотите добавить вознаграждение "{{ title }}"?
         <template #footer>
            <CButton @click="add(true)" color="success">Добавить</CButton>
            <CButton @click="add(false)" color="light">Отмена</CButton>
         </template>
      </CModal>
   </CRow>
</template>

<script>
import { required, requiredIf, between } from 'vuelidate/lib/validators';
import { Cropper } from 'vue-advanced-cropper';
import Layout from '@admin/containers/TheContainer';

export default {
   name: 'RewardsCreate',
   layout: Layout,
   components: {
      Cropper
   },
   data() {
      return {
         title: '',
         description: '',
         type: 'public',
         promocode: '',
         image: null,
         addModal: false,
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
         this.addModal = true;
      },
      async add(modalValue) {
         this.addModal = false;
         if (!modalValue) return;
         try {
            const formData = new FormData();
            formData.append('title', this.title);
            formData.append('type', this.type);
            formData.append('description', this.description);
            if (this.type === 'public') {
               formData.append('promocode', this.promocode);
            }
            formData.append('image', this.image);
            const { coordinates } = this.$refs.cropper.getResult();
            for (let value in coordinates) {
               formData.append('сoordinates[]', coordinates[value]);
            }
            await this.$inertia.post('/admin/rewards', formData, {
               preserveScroll: true,
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
      description: {
         required,
      },
      promocode: {
         required: requiredIf(function() {
            return this.type === 'public';
         }),
      },
      image: {
         required,
      },
   },
};
</script>
