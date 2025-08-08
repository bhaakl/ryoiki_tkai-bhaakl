<template>
   <CCardBody>
      <CCardText class="mb-3 font-weight-bold">Всего ответов: {{ items.length }}</CCardText>
      <CButton :href="`/admin/tasks/answers/${this.question.id}/download`" type="a" color="dark">
         Скачать все
      </CButton>
      <CListGroup class="mt-3">
         <CListGroupItem v-for="{ id, paths, comment, tester } in items" :key="id">
            <CCardText class="font-weight-bold">Тестировщик:</CCardText>
            <CCardText @click="$inertia.visit(`/admin/testers/${tester.id}`)" class="cursor-pointer mb-3">{{
               tester.name
            }}</CCardText>
            <CImg
               v-for="path in paths"
               :key="path"
               :src="`/storage/${path}`"
               alt="Скриншот ошибки"
               class="d-block my-2 images"
            />
            <CCardText class="font-weight-bold mt-3">Комментарий:</CCardText>
            <CCardText v-if="comment">{{ comment }}</CCardText>
            <CCardText v-else class="font-italic text-gray">Без комментария</CCardText>
         </CListGroupItem>
      </CListGroup>
   </CCardBody>
</template>

<script>
export default {
   name: 'TasksAnswersWithFile',
   props: {
      question: Object,
   },
   data() {
      return {
         items: [],
      };
   },
   beforeMount() {
      this.items = this.question.answers.map(({ id, answer, comment, reply: { tester } }) => ({
         id,
         paths: JSON.parse(answer),
         comment,
         tester: {
            id: (tester && tester.id) || '–',
            name: (tester && tester.name) || '–',
         },
      }));
   },
};
</script>

<style scoped>
.images {
   max-width: 80%;
   max-height: 500px;
}

@media screen and (max-width: 480px) {
   .images {
      max-width: 100%;
   }
}
</style>
