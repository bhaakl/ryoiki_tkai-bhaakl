<template>
   <CRow class="justify-content-center">
      <CCol col="12">
         <CCard>
            <CCardHeader class="d-flex justify-content-between align-items-end">
               <CCardTitle class="mb-0">
                  <div v-if="isJson(question.question)">
                     Ответы на кейс №{{ question.order }}
                  </div>
                  <div v-else v-html="question.question"></div>
               </CCardTitle>
            </CCardHeader>
            <WithValues v-if="question.values && question.values.length && question.type !== 'many'" :question="question" />
            <WithRange v-else-if="question.type === 'range'" :question="question" />
            <WithFile v-else-if="question.type === 'file'" :question="question" />
            <WithMany v-else-if="question.type === 'many'" :question="question" />
            <WithText v-else :question="question" />
            <CCardFooter>
               <CButton @click="$inertia.visit(`/admin/tasks/${question.task_id}`)" color="light">К заданию</CButton>
            </CCardFooter>
         </CCard>
      </CCol>
   </CRow>
</template>

<script>
import Layout from '@admin/containers/TheContainer';
import WithText from './WithText';
import WithRange from './WithRange';
import WithFile from './WithFile';
import WithMany from './WithMany';
import WithValues from './WithValues';

export default {
   name: 'TasksAnswers',
   layout: Layout,
   props: {
      question: Object,
   },
   components: {
      WithText,
      WithRange,
      WithFile,
      WithValues,
      WithMany,
   },
   methods: {
      isJson(str) {
         try {
            JSON.parse(str);
            return true;
         }catch (e) {
            return false;
         }
      }
   }
};
</script>
