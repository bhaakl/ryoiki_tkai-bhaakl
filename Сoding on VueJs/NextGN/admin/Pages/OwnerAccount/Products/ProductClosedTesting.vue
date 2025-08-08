<template>
   <CRow>
      <add-new-alpha-testers :productId="product.id" ref="reportModal"/>
      <CCol col="12" v-if="product.closed_testing">
         <CCard :key="`update-counter-${forceUpdateCounter}`">
            <CCardHeader class="d-flex justify-content-between align-items-end">
               <CButton @click="$inertia.visit('/admin/products/' + product.id)" color="light">Назад</CButton>
               <CButton size="sm" color="success" @click.prevent="$refs.reportModal.show()">Добавить тестировщиков</CButton>
            </CCardHeader>
            <CCardHeader class="d-flex justify-content-between">
               <h4>Участники закрытого тестирования продукта {{ product.name }}</h4>
            </CCardHeader>
            <CCardBody>
               <CCard>
                  <CCardBody>
                     <CInputCheckbox class="ml-2" :checked="only_closed" @change="toggleOnlyClosed" label="Показать только участников закрытого тестирования"/>
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
                  <template #role="{ item: { id, closed } }">
                     <td>
                        <CInputCheckbox :checked="closed" @change="sendCheck(id)" />
                     </td>
                  </template>

                  <template #network="{ item: { id, closed, network_type } }">
                     <td :key="`product-${id}`">
                        <div class="form-check form-check-inline">
                           <input
                              class="form-check-input"
                              type="radio"
                              :name="'network_type_' + id"
                              :id="'network_type_ngen_' + id"
                              value="ngen"
                              v-model="network_type"
                              :disabled="!closed"
                              @change="sendNetworkType(id, 'ngen')">
                           <label class="form-check-label" :for="'network_type_ngen_' + id">Только сеть NextGen</label>
                        </div>
                        <div class="form-check form-check-inline">
                           <input
                              class="form-check-input"
                              type="radio"
                              :name="'network_type_' + id"
                              :id="'network_type_internet_' + id"
                              value="internet"
                              v-model="network_type"
                              :disabled="!closed"
                              @change="sendNetworkType(id, 'internet')">
                           <label class="form-check-label" :for="'network_type_internet_' + id">Интернет (глобальная сеть)</label>
                        </div>
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
import AddNewAlphaTesters from "./AddNewAlphaTesters";

export default {
   name: 'TestersIndex',
   layout: Layout,
   components: {
      AddNewAlphaTesters

   },
   props: {
      data: Object,
      product: Object,
      current_page: Number,
      from: Number,
      to: Number,
      last_page: Number,
      per_page: Number,
   },

   data() {
      return {
         only_closed: false,
         forceUpdateCounter: 0,
         fields: [
            {key: 'id', label: 'ID', sorter: true},
            {key: 'name', label: 'ФИО', _classes: 'font-weight-bold'},
            {key: 'phone', label: 'Номер телефона', _classes: 'font-weight-bold', sorter: false},
            {key: 'role', label: 'Участник', sorter: false},
            {key: 'network', label: 'Сеть', sorter: false},
         ],
         filterQueryParams: {
            search: '',
            sort_mode: 'desc',
            sort_column: 'id',
            only_closed: false,
            page: this.current_page
         },
         isShow: false,
      };
   },
   methods: {
      show() {
         this.isShow = true;
      },

      toggleOnlyClosed() {
         this.only_closed = !this.only_closed;
         this.$nextTick(() => {
            this.filterQueryParams = { ...this.filterQueryParams };
            this.filterQueryParams = { ...this.filterQueryParams, only_closed: this.only_closed };
         });
      },

      async sendCheck(userId) {
         try {
            await this.$inertia.post('/admin/testers/sendClosedAlphaTester', {
               userId: userId,
               productId: this.product.id,
            },{
               preserveScroll: true
            });
         } catch (e) {
            this.$handleError(e);
         }
      },

      async sendNetworkType(userId, type) {
         try {
            await this.$inertia.post('/admin/testers/sendNetworkdAlphaTester', {
               userId: userId,
               productId: this.product.id,
               network_type: type,
            },{
               preserveScroll: true,
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
            this.$inertia.get(`closed-testing`,this.filterQueryParams,{
               preserveState : true,
               replace       : true,
               preserveScroll: true,
            });
         },
         deep: true
      },
      data:{
         handler(){
            this.forceUpdateCounter++;
         },
         deep: true
      }
   },

};
</script>
