<template>
   <CCardBody>
      <CCardText class="font-weight-bold">Всего ответов: {{ total }}</CCardText>
      <CDataTable hover :items="values" :fields="fields">
         <template #percent="{ item: { count } }">
            <td>{{ Math.floor((count / total) * 100) || 0 }}%</td>
         </template>
         <template #actions="{ item: { value } }">
            <td>
               <CButton
                  v-show="question.comments[value] && question.comments[value].length"
                  @click="openModal(question.comments[value])"
                  color="secondary"
               >
                  Комментарии
               </CButton>
            </td>
         </template>
         <template #no-items-view>
            <p class="mt-2 text-center font-weight-bold">Пока ни одного</p>
         </template>
      </CDataTable>
      <CModal color="dark" centered :show.sync="modal">
         <template #header>
            <h6 class="modal-title">Комментарии</h6>
         </template>
         <CListGroup>
            <CListGroupItem v-for="{ comment, tester, answers, id } in modalData" :key="id">
               <span @click="$inertia.visit(`/admin/testers/${tester.id}`)" class="cursor-pointer font-weight-bold">
                  {{ tester.name }}:
               </span>
               <span v-if="comment">{{ comment }}</span>
               <span v-else class="font-italic text-gray">Без комментария</span>
               <span v-if="answers" class="font-italic font-xs"><br />Ответы тестировщика: {{ answers }}</span>
            </CListGroupItem>
         </CListGroup>
         <template #footer>
            <CButton @click="closeModal" color="dark">Закрыть</CButton>
         </template>
      </CModal>
   </CCardBody>
</template>

<script>
export default {
   name: 'TasksAnswersWithValues',
   props: {
      question: Object,
   },
   data() {
      return {
         values: null,
         total: 0,
         fields: [
            { key: 'value', label: 'Вариант ответа', _classes: 'font-weight-bold' },
            { key: 'count', label: 'Количество ответов' },
            { key: 'percent', label: 'Процент' },
            { key: 'actions', label: ' ' },
         ],
         modal: false,
         modalData: {},
      };
   },
   methods: {
      openModal(data) {
         this.modalData = data;
         this.modal = true;
      },
      closeModal() {
         this.modalData = {};
         this.modal = false;
      },
   },
   beforeMount() {
      this.values = this.question.values.map(({ value }) => {
         this.total += this.question.results[value];
         return {
            value,
            count: this.question.results[value],
         };
      });
   },
};
</script>
