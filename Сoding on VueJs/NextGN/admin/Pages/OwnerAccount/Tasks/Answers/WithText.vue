<template>
   <CCardBody>
      <CCardText class="mb-3 font-weight-bold">Всего ответов: {{ question.answers.length }}</CCardText>
      <CDataTable
         hover
         :items="items"
         :fields="fields"
         :header="false"
         :items-per-page="10"
         :active-page="activePage"
         :pagination="{ doubleArrows: false, align: 'center' }"
      >
         <template #actions="{ item }">
            <td style="width:125px;">
               <CButton @click="openModal(item)" color="secondary">
                  Комментарий
               </CButton>
            </td>
         </template>
      </CDataTable>
      <CModal color="dark" centered :show.sync="modal">
         <template #header>
            <h6 class="modal-title">Комментарий к "{{ modalData.value }}"</h6>
         </template>
         <span @click="$inertia.visit(`/admin/testers/${modalData.tester.id}`)" class="font-weight-bold cursor-pointer">
            {{ modalData.tester.name }}:
         </span>
         <span v-if="modalData.comment">{{ modalData.comment }}</span>
         <span v-else class="font-italic text-gray">Без комментария</span>
         <template #footer>
            <CButton @click="closeModal" color="dark">Закрыть</CButton>
         </template>
      </CModal>
   </CCardBody>
</template>

<script>
export default {
   name: 'TasksAnswersWithText',
   props: {
      question: Object,
   },
   data() {
      return {
         items: null,
         fields: [{ key: 'value' }, { key: 'actions' }],
         activePage: 1,
         modal: false,
         modalData: {
            tester: {},
         },
      };
   },
   methods: {
      openModal(data) {
         this.modalData = data;
         this.modal = true;
      },
      closeModal() {
         this.modalData = {
            tester: {},
         };
         this.modal = false;
      },
   },
   beforeMount() {
      this.items = this.question.answers.map(({ answer, comment, reply: { tester } }) => ({
         value: answer,
         comment,
         tester: {
            id: (tester && tester.id) || '–',
            name: (tester && tester.name) || '–',
         },
      }));
   },
};
</script>
