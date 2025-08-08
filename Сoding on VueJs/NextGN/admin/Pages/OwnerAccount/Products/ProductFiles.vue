<template>
   <div>
      <CDataTable
         hover
         :items="files"
         :fields="fields"
         :items-per-page="itemsPerPage"
         :active-page="activePage"
         :pagination="{ doubleArrows: false, align: 'center' }"
      >
         <template #actions="data">
            <td>
               <CButton @click="$inertia.delete(`/admin/products/files/${data.item.id}`,{
                  preserveScroll: true
               })" color="danger">Удалить</CButton>
            </td>
         </template>
         <template #no-items-view>
            <p class="mt-2 text-center font-weight-bold">Пока ни одной</p>
         </template>
      </CDataTable>
      <CButton @click="addModal = true" color="success" class="float-right mt-3">
         Загрузить файлы
      </CButton>

      <CModal color="success" centered :show.sync="addModal">
         <template #header>
            <h6 class="modal-title">Добавить файлы</h6>
         </template>
         <CInput
            v-model="$v.newVersion.name.$model"
            label="Название файла"
            placeholder=""
            horizontal
            :isValid="$v.newVersion.name.$dirty ? !$v.newVersion.name.$error : null"
            :invalidFeedback="numberInvalidFeedback"
         />
         <CInput
            v-model="$v.newVersion.button_name.$model"
            label="Название кнопки для скачивания"
            placeholder=""
            horizontal
            :isValid="$v.newVersion.button_name.$dirty ? !$v.newVersion.button_name.$error : null"
            invalidFeedback="Введите название кнопки для скачивания"
         />
         <CInputFile
            @change="addImage"
            label="Файлы"
            class="mb-1"
            horizontal
            :isValid="$v.newVersion.button_name.$dirty ? !$v.newVersion.button_name.$error : null">
         </CInputFile>
         <p v-if="$v.newVersion.path.$error" style="color: red">Выберите файл</p>
         <template #footer>
            <CButton @click="add(true)" color="success">Загрузить</CButton>
            <CButton @click="add(false)" color="light">Отмена</CButton>
         </template>
      </CModal>
   </div>
</template>

<script>
import { required } from 'vuelidate/lib/validators';
import { Cropper } from 'vue-advanced-cropper';

export default {
   name: 'ProductFiles',
   components: {
      Cropper
   },
   props: {
      productId: Number,
      files: Array,
   },
   data() {
      return {
         fields: [
            { key: 'name', label: 'Фаил', _classes: 'font-weight-bold', _style: 'min-width: 100px;' },
            { key: 'button_name', label: 'Название кнопки для скачивания', _style: 'min-width: 300px;' },
            { key: 'actions', label: 'Действия', _style: 'min-width: 100px;' },
         ],
         activePage: 1,
         itemsPerPage: 5,
         addModal: false,
         newVersion: {
            name: '',
            path: [],
            button_name: '',
         },
      };
   },
   computed: {
      numberInvalidFeedback() {
         if (!this.$v.newVersion.name.required) {
            return 'Введите название файла';
         }
      },
   },
   methods: {
      addImage(files) {
         this.newVersion.path = Array.from(files).slice(0, 3);
      },
      async add(modalValue) {
         if (!modalValue) {
            this.addModal = false;
            this.clearModal();
            return;
         }
         this.$v.$touch();
         if (this.$v.$invalid) return;
         this.addModal = false;

         try {
            const formData = new FormData();
            formData.append('product_id', this.productId);
            formData.append('name', this.newVersion.name);
            formData.append('button_name', this.newVersion.button_name);
            if (this.newVersion.path && this.newVersion.path.length) {
               this.newVersion.path.forEach(file => {
                  formData.append('files[]', file);
               });
            }
            await this.$inertia.post('/admin/products/addFile', formData, {
               preserveScroll: true
            });
            this.clearModal();
         } catch (e) {
            this.$handleError(e);
         }
      },
      clearModal() {
         this.newVersion = {
            name: '',
            path: [],
            button_name: '',
         };
         this.$v.$reset();
      },
   },
   validations() {
      return {
         newVersion: {
            name: { required },
            path: { required },
            button_name: { required },
         },
      };
   },
   beforeMount() {
      const lastPage = Math.ceil(this.newVersion.path.length / this.itemsPerPage);
      this.activePage = lastPage;
   },
};
</script>
