<template>
   <CRow class="justify-content-center">
      <CCol col="12">
         <CCard>
            <CCardHeader>
               <CCardTitle class="mb-0">{{ title }}</CCardTitle>
            </CCardHeader>
            <CCardBody>
               <CListGroup>
                  <CCardText class="font-weight-bold">Дата отправки:</CCardText>
                  <CListGroupItem>{{ $moment(mailing.created_at).format('DD MMMM YYYY HH:mm') }}</CListGroupItem>
               </CListGroup>
               <CListGroup class="mt-3">
                  <CCardText class="font-weight-bold">Способ отправки:</CCardText>
                  <CListGroupItem>{{ getMethod(mailing.method) }}</CListGroupItem>
               </CListGroup>
               <CListGroup v-if="isMethodEmail" class="mt-3">
                  <CCardText class="font-weight-bold">Тема письма:</CCardText>
                  <CListGroupItem>{{ mailing.subject }}</CListGroupItem>
               </CListGroup>
               <CListGroup v-if="!isMethodEmail && mailing.sms_template" class="mt-3">
                  <CCardText class="font-weight-bold">ID шаблона:</CCardText>
                  <CListGroupItem>{{ mailing.sms_template.template_id }}</CListGroupItem>
               </CListGroup>
               <CListGroup v-if="!isMethodEmail && mailing.sms_template" class="mt-3">
                  <CCardText class="font-weight-bold">Описание шаблона:</CCardText>
                  <CListGroupItem>{{ mailing.sms_template.description }}</CListGroupItem>
               </CListGroup>
               <CListGroup class="mt-3">
                  <CCardText class="font-weight-bold">Сообщение:</CCardText>
                  <CListGroupItem v-html="mailing.message"></CListGroupItem>
               </CListGroup>
               <CListGroup v-if="isMethodEmail" class="mt-3">
                  <CCardText class="font-weight-bold">Подпись:</CCardText>
                  <CListGroupItem style="white-space: pre;">{{ mailing.signature }}</CListGroupItem>
               </CListGroup>
               <CListGroup class="mt-3">
                  <CCardText class="font-weight-bold">Кому отправлено:</CCardText>
                  <MailingTesters :testers="mailing.testers" :regions="regions" />
               </CListGroup>
            </CCardBody>
            <CCardFooter>
               <CButton @click="$inertia.visit('/admin/mailing/history')" color="light">К истории рассылок</CButton>
            </CCardFooter>
         </CCard>
      </CCol>
   </CRow>
</template>

<script>
import Layout from '@admin/containers/TheContainer';
import MailingTesters from './MailingTesters';

export default {
   name: 'MailingShow',
   layout: Layout,
   props: {
      mailing: Object,
      regions: Array,
   },
   components: {
      MailingTesters,
   },
   data() {
      return {
         methodOptions: [
            { label: 'Email', value: 'email' },
            { label: 'SMS', value: 'sms' },
         ],
      };
   },
   computed: {
      isMethodEmail() {
         return this.mailing.method === 'email';
      },
      title() {
         if (this.isMethodEmail) {
            return `Рассылка "${this.mailing.subject}"`;
         }
         if (this.mailing.sms_template) {
            return `Рассылка "${this.mailing.sms_template.description}"`;
         }
         return 'Рассылка';
      },
   },
   methods: {
      getMethod(value) {
         const option = this.methodOptions.find(option => option.value === value);
         return option.label;
      },
   },
};
</script>
