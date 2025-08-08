<template>
   <CDataTable
      hover
      :items="questions"
      :fields="fields"
      :items-per-page="5"
      :active-page="activePage"
      :pagination="{ doubleArrows: false, align: 'center' }"
   >
      <template #question="{ item: { question } }">
         <td v-if="isJson(question)">
            <ul>
               <li v-for="item in JSON.parse(question)" v-html="item.value"></li>
            </ul>
         </td>
         <td v-else v-html="question"></td>
      </template>
      <template #actions="{ item: { id } }">
         <td>
            <CButton @click="$inertia.visit(`/admin/tasks/answers/${id}`)" color="info">
               Ответы
            </CButton>
         </td>
      </template>
      <template #no-items-view>
         <p class="font-weight-bold text-center mt-2">Пока ни одного</p>
      </template>
   </CDataTable>
</template>

<script>
export default {
   name: 'TaskResults',
   props: {
      questions: Array,
   },
   data() {
      return {
         fields: [
            {key: 'order', label: 'Порядок', sorter: true},
            {key: 'question', label: 'Вопрос'},
            {key: 'actions', label: ' '},
         ],
         activePage: 1,
      };
   },
   methods: {
      // check string is json
      isJson(str) {
         try {
            JSON.parse(str);
            return true;
         } catch (e) {
            return false;
         }
      },
      getActivityText(status) {
         switch (status) {
            case 0:
               return 'Завершил';
            case 1:
               return 'В ожидании';
            case 2:
               return 'Участвует';
            default:
               return 'Неизвестно';
         }
      },
      getBadgeColor(status) {
         switch (status) {
            case 0:
               return 'danger';
            case 1:
               return 'warning';
            case 2:
               return 'success';
            default:
               return 'secondary';
         }
      },
   },
   beforeMount() {

      // this.questions.sort((a, b) => {
      //    return a.order > b.order ? -1 : 1;
      // });
   },
};
</script>

<style scoped>
.analytics__btn {
   padding: 0;
}
</style>
