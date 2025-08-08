<template>
   <CCardBody>
      <div class="scrollable">
         <table>
            <thead>
            <tr>
               <th class="split-head-cell">
                  <div>Ответы</div>
                  <div>Ответы</div>
                  <hr>
                  <div>Вопросы</div>
               </th>
               <th v-for="answer in question.values" :key="answer.id">
                  <span>{{ answer.value }}</span>
               </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(q, index) in JSON.parse(question.question)" :key="index">
               <td>
                  <span class="" v-html="q.value"></span>
               </td>
               <td v-for="(result, resultIndex) in question.results[index]" :key="resultIndex">
                  {{ Math.floor(( result / testerCount) * 100) || 0 }}%
               </td>
            </tr>
            </tbody>
         </table>
      </div>
      <CCardText class="font-weight-bold mt-3">Всего ответов: {{ testerCount }}</CCardText>

      <div class="modal-body">
         <CCardText class="font-weight-bold mt-3">Комментарии:</CCardText>
            <ul v-for="(result, resultIndex) in question.answers" :key="resultIndex" role="list-items" class="list-group mb-2">
               <li class="list-group-item">
                  <span class="cursor-pointer font-weight-bold">{{ result.reply.tester.name }}:</span>
                  <span>{{result.comment}}</span>
               </li>
            </ul>
      </div>

   </CCardBody>
</template>

<script>
export default {
   name: 'TasksAnswersWithMany',
   props: {
      question: Object,
   },
   data() {
      return {
         values: null,
         total: 0,
         testerCount: 0,
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
         this.testerCount = this.question.answers.length;
         return {
            value,
            count: this.question.results[value],
         };
      });
   },
};
</script>

<style lang="scss" scoped>
table {
   border-collapse: separate;
   border-spacing: 0;
   border-top: 1px solid grey;

   tr {
      td {
         text-align: center;
      }
   }

   .split-head-cell {
      div:first-child {
         display: flex;
         flex-direction: column;
         align-items: flex-end;
      }

      div:last-child {
         display: flex;
         flex-direction: column;
         align-items: flex-start;
      }

      div {
         padding: 0 5px;
         font-size: 12px;
      }

      padding: 0;

      hr {
         transform: rotate3d(1, 1, 1, 45deg);
      }
   }
}

th, td {
   padding: 14px;
   border: 1px solid black;
   white-space: normal;
   overflow: hidden;
   font-size: 12px;
   width: 150px;
   max-width: 150px;
   min-width: 150px;
}

th:first-child, td:first-child {
   position: sticky;
   left: 0;
   background-color: #f2f2f2;
   z-index: 1;
   font-size: 12px;
   white-space: normal;
}

.scrollable {
   overflow-x: auto;
}


</style>
