<template>
   <div>
      <CDataTable
         hover
         :items="versions"
         :fields="fields"
         :items-per-page="itemsPerPage"
         :active-page="activePage"
         :pagination="{ doubleArrows: false, align: 'center' }"
      >
         <template #date="data">
            <td>
               {{ $moment(data.item.created_at).format('DD.MM.YYYY') }}
            </td>
         </template>
         <template #actions="data">
            <td>
               <CButton @click="$inertia.delete(`/admin/products/versions/${data.item.id}`)" color="danger"
                  >Удалить</CButton
               >
            </td>
         </template>
         <template #no-items-view>
            <p class="mt-2 text-center font-weight-bold">Пока ни одной</p>
         </template>
      </CDataTable>
      <CButton @click="addModal = true" color="success" class="float-right mt-3">
         Добавить версию
      </CButton>

      <CModal color="success" centered :show.sync="addModal">
         <template #header>
            <h6 class="modal-title">Добавить версию</h6>
         </template>
         <CInput
            v-model="$v.newVersion.number.$model"
            label="Номер"
            placeholder="1.2.6"
            horizontal
            :isValid="$v.newVersion.number.$dirty ? !$v.newVersion.number.$error : null"
            :invalidFeedback="numberInvalidFeedback"
         />
         <CInput
            v-model="$v.newVersion.description.$model"
            label="Описание"
            placeholder="Исправили ошибки"
            horizontal
            :isValid="$v.newVersion.description.$dirty ? !$v.newVersion.description.$error : null"
            invalidFeedback="Введите описание"
         />
         <template #footer>
            <CButton @click="add(true)" color="success">Добавить</CButton>
            <CButton @click="add(false)" color="light">Отмена</CButton>
         </template>
      </CModal>
   </div>
</template>

<script>
import { required } from 'vuelidate/lib/validators';

export default {
   name: 'ProductVersions',
   props: {
      productId: Number,
      versions: Array,
   },
   data() {
      return {
         fields: [
            { key: 'number', label: 'Номер', _classes: 'font-weight-bold', _style: 'min-width: 100px;' },
            { key: 'description', label: 'Описание', _style: 'min-width: 300px;' },
            { key: 'date', label: 'Дата', _style: 'min-width: 100px;' },
            { key: 'actions', label: 'Действия', _style: 'min-width: 100px;' },
         ],
         activePage: 1,
         itemsPerPage: 5,
         addModal: false,
         newVersion: {
            number: '',
            description: '',
         },
      };
   },
   computed: {
      numberInvalidFeedback() {
         if (!this.$v.newVersion.number.required) {
            return 'Введите номер';
         }
         if (!this.$v.newVersion.number.semVer) {
            return 'Введите номер в формате x.x.x';
         }
         if (!this.$v.newVersion.number.greatest) {
            return 'Номер версии должен быть больше предыдущего';
         }
      },
   },
   methods: {
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
            await this.$inertia.post(
               '/admin/products/new-version',
               {
                  product_id: this.productId,
                  number: this.newVersion.number,
                  description: this.newVersion.description,
               },
               {
                  preserveScroll: true,
               }
            );
            this.clearModal();
         } catch (e) {
            this.$handleError(e);
         }
      },
      clearModal() {
         this.newVersion = {
            number: '',
            description: '',
         };
         this.$v.$reset();
      },
   },
   validations() {
      return {
         newVersion: {
            number: {
               required,
               semVer: function(value) {
                  return /\d+\.\d+\.\d+/.test(value);
               },
               greatest: function(value) {
                  if (!/\d+\.\d+\.\d+/.test(value)) return false;
                  const lastVersion = this.versions[this.versions.length - 1];
                  if (!lastVersion) return true;
                  const [lastFull, lastMajor, lastMinor, lastPatch] = Array.from(
                     lastVersion.number.match(/(\d+)\.(\d+)\.(\d+)/)
                  );
                  const [full, major, minor, patch] = Array.from(value.match(/(\d+)\.(\d+)\.(\d+)/));
                  if (+major > +lastMajor) return true;
                  if (+major === +lastMajor) {
                     if (+minor > +lastMinor) return true;
                     if (+minor === +lastMinor && +patch > +lastPatch) return true;
                  }
                  return false;
               },
            },
            description: { required },
         },
      };
   },
   beforeMount() {
      const lastPage = Math.ceil(this.versions.length / this.itemsPerPage);
      this.activePage = lastPage;
   },
};
</script>
