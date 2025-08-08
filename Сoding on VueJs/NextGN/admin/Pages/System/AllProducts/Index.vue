<template>
   <CRow>
      <CCol col="12">
         <CCard>
            <CCardHeader>
               <h4>Активные мобильные продукты</h4>
            </CCardHeader>
            <CCardBody>
               <Table @set-product-owner="setProductOwner" :products="productsMobile" />
            </CCardBody>
         </CCard>
         <CCard>
            <CCardHeader>
               <h4>Активные веб-продукты</h4>
            </CCardHeader>
            <CCardBody>
               <Table @set-product-owner="setProductOwner" :products="productsWeb" />
            </CCardBody>
         </CCard>
         <CCard>
            <CCardHeader>
               <h4>Неактивные продукты</h4>
            </CCardHeader>
            <CCardBody>
               <Table @set-product-owner="setProductOwner" :products="productsNonActive" :sorter="false" />
            </CCardBody>
         </CCard>
      </CCol>
      <CModal color="info" centered :show.sync="editModal">
         <template #header>
            <h6 class="modal-name">Назначить владельцев продукта</h6>
         </template>
         <CInputCheckbox
            v-for="{ id, name } in productOwners"
            :key="id"
            :label="name"
            :value="id"
            :name="`owner_${id}`"
            :checked="currentProductOwners.includes(id)"
            @input="toggleCurrentProductOwners(id)"
         />
         <template #footer>
            <CButton @click="edit(true)" color="info">Назначить</CButton>
            <CButton @click="edit(false)" color="light">Отмена</CButton>
         </template>
      </CModal>
   </CRow>
</template>

<script>
import Layout from '@admin/containers/TheContainer';
import Table from './Table';

export default {
   name: 'AllProducts',
   layout: Layout,
   props: {
      products: Array,
      productOwners: Array,
   },
   components: {
      Table,
   },
   data() {
      return {
         currentProductId: null,
         currentProductOwners: [],
         editModal: false,
         fields: [
            { key: 'sort', label: 'Сортировка', sorter: false, filter: false },
            { key: 'index', label: '№', sorter: false },
            { key: 'name', label: 'Название', _classes: 'font-weight-bold', _style: 'min-width: 250px;' },
            { key: 'description', label: 'Описание', sorter: false },
            { key: 'is_active', label: 'Статус', _style: 'min-width: 115px;' },
            { key: 'users', label: 'Пользователи', _style: 'min-width: 115px;' },
            { key: 'actions', label: 'Действия', _style: 'min-width: 115px;' },
         ],
         activePage: 1,
      };
   },
   computed: {
      productsMobile() {
         return this.products.filter(product => product.is_active !== 0 && product.platform !== 'web');
      },
      productsWeb() {
         return this.products.filter(product => product.is_active !== 0 && product.platform === 'web');
      },
      productsNonActive() {
         return this.products.filter(product => product.is_active === 0);
      },
   },
   methods: {
      setProductOwner(id) {
         this.currentProductId = id;
         this.currentProductOwners = this.productOwners
            .filter(owner => {
               return owner.products.map(product => product.id).includes(this.currentProductId);
            })
            .map(owner => owner.id);
         this.editModal = true;
      },
      toggleCurrentProductOwners(id) {
         if (this.currentProductOwners.includes(id)) {
            this.currentProductOwners = this.currentProductOwners.filter(role => role != id);
            return;
         }
         this.currentProductOwners.push(id);
      },
      async edit(modalValue) {
         this.editModal = false;
         if (!modalValue) return;
         try {
            await this.$inertia.post(`/admin/all-products/${this.currentProductId}/owners`, {
               users: this.currentProductOwners,
            });
         } catch (e) {
            this.$handleError(e);
         } finally {
            this.currentProductId = null;
            this.currentProductOwners = [];
         }
      },
   },
};
</script>
