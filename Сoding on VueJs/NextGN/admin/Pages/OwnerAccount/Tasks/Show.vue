dd<template>
   <CRow class="justify-content-center">
      <CCol col="12">
         <CCard>
            <CCardHeader class="d-flex justify-content-between align-items-end">
               <CCardTitle class="mb-0">Задание "{{ task.title }}"</CCardTitle>
               <CButton v-if="task.is_close"
                        size="sm"
                        color="success"
                        @click.prevent="$inertia.visit(`/admin/tasks/${task.id}/closed`)">Участники закрытого задания
               </CButton>
            </CCardHeader>
            <CCardBody>
               <CListGroup>
                  <CCardText class="font-weight-bold">Продукт:</CCardText>
                  <CListGroupItem @click="$inertia.visit(`/admin/products/${task.product.id}`)" class="cursor-pointer">
                     {{ task.product.name }}
                  </CListGroupItem>
               </CListGroup>
               <CListGroup class="mt-3">
                  <CCardText class="font-weight-bold">Платформа:</CCardText>
                  <CListGroupItem>
                     {{ task.product.platformTitle }}
                  </CListGroupItem>
               </CListGroup>
               <CListGroup class="mt-3">
                  <CCardText class="font-weight-bold">Количество ответов:</CCardText>
                  <CListGroupItem>
                     {{ task.replies ? task.replies.length : 0 }}
                  </CListGroupItem>
               </CListGroup>
               <CListGroup class="mt-3">
                  <TaskResults :questions="task.questions" />
               </CListGroup>
               <CButtonGroup>
                  <CButton @click="$inertia.visit(`/admin/tasks/${task.id}/edit`)" color="success">Изменить</CButton>
                  <CButton @click="copyModal = true" color="warning">Копировать</CButton>
                  <CDropdown
                     color="dark"
                     toggler-text="Выгрузить"
                     addTogglerClasses="border-left-radius-none"
                     style="width: 200px;"
                  >
                     <CDropdownItem :href="`/admin/tasks/export/excel/${task.id}`">Как XLSX</CDropdownItem>
                     <CDropdownItem :href="`/admin/tasks/export/csv/${task.id}`">Как CSV</CDropdownItem>
                     <CDropdownItem :href="`/admin/tasks/export/pdf/${task.id}`">Как PDF</CDropdownItem>
                  </CDropdown>
               </CButtonGroup>
            </CCardBody>
            <CCardFooter>
               <CButton @click="$inertia.visit('/admin/tasks')" color="light">К заданиям</CButton>
            </CCardFooter>
         </CCard>
      </CCol>
      <CModal color="warning" centered :show.sync="copyModal">
         <template #header>
            <h6 class="modal-title">На какой продукт скопировать задание?</h6>
         </template>
         <CSelect :value.sync="productToCopy" :options="productsOptions" label="Продукт" horizontal />
         <template #footer>
            <CButton @click="copy(true)" color="warning">Копировать</CButton>
            <CButton @click="copy(false)" color="light">Отмена</CButton>
         </template>
      </CModal>
   </CRow>
</template>

<script>
import Layout from '@admin/containers/TheContainer';
import TaskResults from './TaskResults';

export default {
   name: 'TasksShow',
   layout: Layout,
   props: {
      task: Object,
      products: Array,
   },
   components: {
      TaskResults,
   },
   data() {
      return {
         copyModal: false,
         productsOptions: [],
         productToCopy: null,
      };
   },
   methods: {
      async copy(modalValue) {
         this.copyModal = false;
         if (!modalValue) return;
         try {
            await this.$inertia.post(`/admin/tasks/${this.task.id}/copy`, {
               product_id: +this.productToCopy,
            });
         } catch (e) {
            this.$handleError(e);
         }
      },
   },
   beforeMount() {
      this.productsOptions = this.products
         .map(product => ({
            value: product.id,
            label: `${product.name} - ${product.platformTitle}`,
         }));

      if (this.productsOptions && this.productsOptions.length) {
         this.productToCopy = this.productsOptions[0]?.value || null
      }
   },
};
</script>

<style>
.border-left-radius-none {
   border-top-left-radius: 0;
   border-bottom-left-radius: 0;
}
</style>
