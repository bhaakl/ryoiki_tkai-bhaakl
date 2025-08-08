<template>
   <CCardBody>
      <div class="d-flex justify-content-between font-weight-bold mb-3">
         <CCardText>Всего ответов: {{ total }}</CCardText>
         <CCardText v-if="typeof average == 'number'">Среднее значение: {{ average }}</CCardText>
      </div>
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
            <CListGroupItem v-for="{ comment, tester, id } in modalData" :key="id">
               <span @click="$inertia.visit(`/admin/testers/${tester.id}`)" class="cursor-pointer font-weight-bold">
                  {{ tester.name }}:
               </span>
               <span v-if="comment">{{ comment }}</span>
               <span v-else class="font-italic text-gray">Без комментария</span>
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
   name: 'TasksAnswersWithRange',
   props: {
      question: Object,
   },
   data() {
      return {
         values: [],
         total: 0,
         fields: [
            { key: 'value', label: 'Значение', _classes: 'font-weight-bold' },
            { key: 'count', label: 'Количество ответов' },
            { key: 'percent', label: 'Процент' },
            { key: 'actions', label: ' ' },
         ],
         modal: false,
         modalData: {},
      };
   },
   computed: {
      average() {
         return (
            Number(
               this.values &&
                  this.values.length &&
                  this.values.map(value => value.value * value.count).reduce((sum, value) => sum + value) / this.total
            ).toFixed(1) || null
         );
      },
   },
   methods: {
      openModal(value) {
         this.modalData = value;
         this.modal = true;
      },
      closeModal() {
         this.modalData = {};
         this.modal = false;
      },
   },
   beforeMount() {
      for (let value in this.question.results) {
         this.total += this.question.results[value];
         this.values.push({
            value,
            count: this.question.results[value],
         });
      }
   },
};
</script>
