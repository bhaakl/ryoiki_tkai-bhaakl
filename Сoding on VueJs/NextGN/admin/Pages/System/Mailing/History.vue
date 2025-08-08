<template>
   <CRow>
      <CCol col="12">
         <CCard>
            <CCardHeader>
               <h4>История рассылок</h4>
            </CCardHeader>
            <CCardBody>
               <CDataTable
                  hover
                  striped
                  clickable-rows
                  column-filter
                  :table-filter="{ placeholder: 'Фильтр', label: ' ' }"
                  :sorter="{ external: false, resetable: true }"
                  @row-clicked="item => $inertia.visit(`/admin/mailing/history/${item.id}`)"
                  :items="items"
                  :fields="fields"
                  :items-per-page="15"
                  :active-page="activePage"
                  :pagination="{ doubleArrows: false, align: 'center' }"
                  :noItemsView="{ noResults: 'Нет подходящих вариантов', noItems: 'Рассылок пока нет' }"
               >
                  <template #title="{ item: {method, subject, sms_template} }">
                     <td>{{ method == 'email' ? subject : (sms_template && sms_template.description) || '–' }}</td>
                  </template>
                  <template #method="{ item: {method} }">
                     <td>{{ getMethod(method) }}</td>
                  </template>
               </CDataTable>
            </CCardBody>
         </CCard>
      </CCol>
   </CRow>
</template>

<script>
import Layout from '@admin/containers/TheContainer';

export default {
   name: 'MailingsHistory',
   layout: Layout,
   props: {
      mailings: Array,
   },
   data() {
      return {
         fields: [
            { key: 'title', label: 'Тема/Описание', _classes: 'font-weight-bold', sorter: false },
            { key: 'method', label: 'Способ', _style: 'min-width: 250px;' },
            { key: 'date', label: 'Дата отправки' },
         ],
         activePage: 1,
         items: [],
         methodOptions: [
            { label: 'Email', value: 'email' },
            { label: 'SMS', value: 'sms' },
         ],
      };
   },
   methods: {
      getMethod(value) {
         const option = this.methodOptions.find(option => option.value === value);
         return option.label;
      },
   },
   beforeMount() {
      this.items = this.mailings.map(mailing => ({
         ...mailing,
         date: this.$moment(mailing.created_at).format('DD MMMM YYYY HH:mm'),
      }));
   },
};
</script>
