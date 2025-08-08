<template>
   <div>
      <CDataTable
         :items="items"
         :fields="fields"
         :items-per-page="itemsPerPage"
         :active-page="activePage"
         :pagination="{ doubleArrows: false, align: 'center' }"
      >
         <template #os="{item: {os}}">
            <td>{{ getOsName(os) }}</td>
         </template>
         <template #version="{item: {version, pivot: {from}}}">
            <td>{{ from ? `От ${version} и выше` : version }}</td>
         </template>
         <template #no-items-view>
            <p class="mt-2 text-center font-weight-bold">Пока ни одной</p>
         </template>
      </CDataTable>
      <CButton @click="showEditModal" color="success" class="float-right mt-3">
         Изменить
      </CButton>

      <CModal size="lg" color="success" centered :show.sync="editModal">
         <template #header>
            <h6 class="modal-title">Выбрать версии</h6>
         </template>
         <!-- <CSelect :value.sync="selectedOs" :options="osOptions" label="ОС" horizontal /> -->
         <CRow form class="form-group">
            <CCol tag="label" sm="3" class="col-form-label">
               Конкретная версия
            </CCol>
            <CCol sm="9">
               <CInputCheckbox
                  v-for="{ id, version } in versionOptions"
                  :key="id"
                  :label="version"
                  :value="id"
                  :name="`version_${id}`"
                  :checked="selectedVersions.includes(id)"
                  @input="toggleVersion(id)"
                  style="display:inline-block; width: 80px; margin-bottom: 15px;"
               />
            </CCol>
         </CRow>
         <CRow form class="form-group">
            <CCol tag="label" sm="3" class="col-form-label">
               От и выше
            </CCol>
            <CCol sm="9">
               <CInputCheckbox
                  v-for="{ id, version } in versionOptions"
                  :key="id"
                  :label="version"
                  :value="id"
                  :name="`version_${id}`"
                  :checked="selectedVersionsFrom.includes(id)"
                  @input="toggleVersionFrom(id)"
                  style="display:inline-block; width: 80px; margin-bottom: 15px;"
               />
            </CCol>
         </CRow>
         <template #footer>
            <!-- <CButton @click="selectAll" color="light" class="mr-auto">Выбрать все</CButton> -->
            <CButton @click="edit(true)" color="success">Изменить</CButton>
            <CButton @click="edit(false)" color="light">Отмена</CButton>
         </template>
      </CModal>
   </div>
</template>

<script>
import { required } from 'vuelidate/lib/validators';

export default {
   name: 'ProductOSVersions',
   props: {
      productId: Number,
      versions: Array,
      versionOptions: Array,
   },
   data() {
      return {
         fields: [
            { key: 'os', label: 'ОС', _style: 'min-width: 100px;' },
            { key: 'version', label: 'Версия', _style: 'min-width: 100px;' },
         ],
         items: [],
         activePage: 1,
         itemsPerPage: 5,
         editModal: false,
         selectedOs: null,
         selectedVersions: [],
         selectedVersionsFrom: [],
         osOptions: [
            { value: 'android', label: 'Android' },
            { value: 'ios', label: 'iOS' },
         ],
      };
   },
   methods: {
      showEditModal() {
         this.editModal = true;
         this.$inertia.reload({
            only: ['osVersions']
         })
      },
      getOsName(value) {
         const option = this.osOptions.find(option => option.value === value);
         return option.label;
      },
      toggleVersion(id) {
         const isChecked = this.selectedVersions.includes(id);
         if (isChecked) {
            this.selectedVersions = this.selectedVersions.filter(v => v !== id);
         } else {
            this.selectedVersions.push(id);
         }
      },
      toggleVersionFrom(id) {
         const isChecked = this.selectedVersionsFrom.includes(id);
         if (isChecked) {
            this.selectedVersionsFrom = this.selectedVersionsFrom.filter(v => v !== id);
         } else {
            this.selectedVersionsFrom.push(id);
         }
      },
      selectAll() {
         this.versionOptions
            .map(version => version.id)
            .forEach(id => {
               const isChecked = this.selectedVersions.includes(id);
               if (isChecked) return;
               this.selectedVersions.push(id);
            });
      },
      setItems() {
         const versions = [...this.versions.filter(v => v.pivot && v.pivot.from === 0)];
         const versionsFrom = [...this.versions.filter(v => v.pivot && v.pivot.from === 1)];
         versions.sort((a, b) => a.id - b.id);
         versionsFrom.sort((a, b) => a.id - b.id);
         this.items = [...versions, ...versionsFrom];
         this.selectedVersions = versions.map(v => v.id);
         this.selectedVersionsFrom = versionsFrom.map(v => v.id);
      },
      async edit(modalValue) {
         this.editModal = false;
         if (!modalValue) {
            // this.clearModal();
            return;
         }
         try {
            await this.$inertia.post(
               '/admin/products/os-versions',
               {
                  product_id: this.productId,
                  versions: this.selectedVersions,
                  versions_from: this.selectedVersionsFrom,
               },
               {
                  preserveScroll: true,
               }
            );
            // this.clearModal();
         } catch (e) {
            this.$handleError(e);
         }
      },
      // clearModal() {
      //    this.selectedOs = 'android';
      // },
   },
   watch: {
      versions(value) {
         this.setItems();
      },
      //    selectedOs(value) {
      //       this.versionOptions = this.allVersions.filter(v => v.os === value);
      //    },
   },
   beforeMount() {
      this.setItems();
   },
};
</script>
