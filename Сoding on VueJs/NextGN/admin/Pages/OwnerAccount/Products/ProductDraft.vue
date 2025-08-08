<template>
   <CRow>
      <add-new-alpha-testers :productId="product.id" ref="reportModal"/>
      <CCol col="12" v-if="product.draft">
         <CCard>
            <CCardHeader class="d-flex justify-content-between align-items-end">
               <CButton @click="$inertia.visit('/admin/products/' + product.id)" color="light">Назад</CButton>
            </CCardHeader>
            <CCardHeader class="d-flex justify-content-between">
               <h4>Доступ к черновику продукта {{ product.name }}</h4>
            </CCardHeader>
            <CCardBody>
               <CCard>
                  <CCardBody>
                     <CInputCheckbox class="ml-2" :checked="only_draft" @change="toggleOnlyDraft" label="Показать только тестировщиков с доступом"/>
                  </CCardBody>
               </CCard>
               <CDataTable
                  striped
                  :table-filter="{ placeholder: 'Поиск', label: ' ', external: true }"
                  :table-filter-value.sync="filterQueryParams.search"
                  :sorter="{ external: true, resetable: false }"
                  :sorter-value="{ column: filterQueryParams.sort_column, asc: filterQueryParams.sort_mode === 'asc' }"
                  @update:sorter-value="sort"
                  :items="data.data"
                  :fields="fields"
                  :noItemsView="{ noResults: 'Нет подходящих вариантов', noItems: 'Нет результатов по вашему запросу' }"
               >
                  <template #id="{ item: { id } }">
                     <td>{{ id }}</td>
                  </template>
                  <template #role="{ item: { id, draft } }">
                     <td>
                        <CInputCheckbox :checked="draft" @change="sendCheck(id)" />
                     </td>
                  </template>
                  <template #phone="{ item: { phone } }">
                     <td>
                        {{ phone || '-' }}
                     </td>
                  </template>
               </CDataTable>
               <CPagination :active-page.sync="filterQueryParams.page"
                            :limit="per_page"
                            :pages="data.last_page"
                            arrows dots align="center"/>
            </CCardBody>
         </CCard>
      </CCol>
   </CRow>
</template>

<script>
import Layout from '@admin/containers/TheContainer';

export default {
   name: 'TestersIndex',
   layout: Layout,
   props: {
      data: Object,
      product: Object,
      current_page: Number,
      from: Number,
      to: Number,
      last_page: Number,
      per_page: Number,
   },
   created() {
      const params = new URLSearchParams(window.location.search);
      this.filterQueryParams.search = params.get('search');
   },
   data() {
      return {
         draft: false,
         fields: [
            {key: 'id', label: 'ID', sorter: true},
            {key: 'name', label: 'ФИО', _classes: 'font-weight-bold'},
            {key: 'phone', label: 'Номер телефона', _classes: 'font-weight-bold', sorter: false},
            {key: 'role', label: 'Участник', sorter: false},
         ],
         filterQueryParams: {
            search: '',
            sort_mode: 'desc',
            sort_column: 'id',
            draft: true,
            page: this.current_page
         },
         isShow: false,
      };
   },
   methods: {
      show() {
         this.isShow = true;
      },

      toggleOnlyDraft() {
         this.draft = !this.draft;
         this.$nextTick(() => {
            this.filterQueryParams = { ...this.filterQueryParams };
            this.filterQueryParams = { ...this.filterQueryParams, only_draft: this.draft };
         });
      },

      async sendCheck(userId) {
         try {
            await this.$inertia.post('/admin/testers/sendDraftAlphaTester', {
               userId: userId,
               productId: this.product.id,
            });
         } catch (e) {
            this.$handleError(e);
         }
      },

      sort({asc, column}) {
         this.filterQueryParams.sort_mode = asc === true ? 'asc' : 'desc';
         this.filterQueryParams.sort_column = column || 'id';
      },
   },
   watch: {
      filterQueryParams: {
         handler() {
            this.$inertia.get(`draft`,this.filterQueryParams,{
               preserveState : true,
               replace       : true,
               preserveScroll: true,
            });
         },
         deep: true
      },
   },
};
</script>
