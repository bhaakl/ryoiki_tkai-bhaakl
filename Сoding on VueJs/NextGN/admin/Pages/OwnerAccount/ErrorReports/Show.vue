<template>
   <CRow class="justify-content-center">
      <CCol col="12">
         <CCard>
            <CCardHeader class="d-flex justify-content-between align-items-end">
               <CCardTitle class="mb-0">
                  Отчёт об ошибке продукта "{{ report.product.name }}" <br />от тестировщика
                  {{ report.tester.name }}
               </CCardTitle>
            </CCardHeader>
            <CCardBody>
               <CListGroup>
                  <CCardText class="font-weight-bold">Описание ошибки:</CCardText>
                  <CListGroupItem>{{ report.description }}</CListGroupItem>
               </CListGroup>
               <CListGroup class="mt-3">
                  <CCardText class="font-weight-bold">Критичность бага:</CCardText>
                  <CListGroupItem>{{ report.criticality_bug || '–' }}</CListGroupItem>
               </CListGroup>
               <CListGroup class="mt-3">
                  <CCardText class="font-weight-bold">Тип бага:</CCardText>
                  <CListGroupItem>{{ report.type_bug || '–' }}</CListGroupItem>
               </CListGroup>
               <CListGroup class="mt-3">
                  <CCardText class="font-weight-bold">Устройство:</CCardText>
                  <CListGroupItem>{{ report.device || '–' }}</CListGroupItem>
               </CListGroup>
              <CListGroup class="mt-3">
                <CCardText class="font-weight-bold">Дата и время фиксации ошибки:</CCardText>
                <CListGroupItem>{{ $moment(report.recorded_at).format('DD.MM.YYYY HH:mm') || '–' }}</CListGroupItem>
              </CListGroup>
              <CListGroup class="mt-3">
                  <CCardText class="font-weight-bold">Браузер:</CCardText>
                  <CListGroupItem>{{ report.browser || '–' }}</CListGroupItem>
               </CListGroup>
               <CListGroup class="mt-3">
                  <CCardText class="font-weight-bold">Пошаговый сценарий воспроизведения ошибки:</CCardText>
                  <CListGroupItem v-for="(step, index) in report.script" :key="step.id">
                     <span class="font-weight-bold">{{ index + 1 }}.</span> {{ step.value }}
                  </CListGroupItem>
               </CListGroup>
               <CListGroup class="mt-3">
                  <CCardText class="font-weight-bold">Ожидаемый результат:</CCardText>
                  <CListGroupItem>{{ report.expectations }}</CListGroupItem>
               </CListGroup>
               <CListGroup class="mt-3">
                  <CCardText class="font-weight-bold">Фактический результат:</CCardText>
                  <CListGroupItem>{{ report.reality }}</CListGroupItem>
               </CListGroup>
               <CListGroup v-if="screenshots.length" class="mt-3 screenshots">
                  <div v-for="({ id, path }, index) in screenshots" :key="id">
                     <CCardText class="font-weight-bold">Скриншот №{{ index + 1 }}:</CCardText>
                     <CImg :src="`/storage/${path}`" alt="Скриншот ошибки" width="100%" />
                  </div>
               </CListGroup>
               <CListGroup v-if="videos.length" class="mt-3">
                  <CCardText v-for="({ id, path }, index) in videos" :key="id" class="font-weight-bold">
                     Видео №{{ index + 1 }}:
                     <CButton @click="$window.open(`/storage/${path}`)" color="info" class="ml-2">
                        Посмотреть
                     </CButton>
                  </CCardText>
               </CListGroup>
               <CListGroup v-if="logs.length" class="mt-3">
                  <CCardText v-for="({ id, path }, index) in logs" :key="id" class="font-weight-bold">
                     Лог-файл №{{ index + 1 }}:
                     <CButton :href="`/storage/${path}`" download color="info" class="ml-2">
                        Скачать
                     </CButton>
                  </CCardText>
               </CListGroup>
            </CCardBody>
            <CCardFooter>
               <CButtonGroup>
                  <CButton @click="$inertia.visit(`/admin/products/${report.product.id}`)" color="light">
                     К продукту
                  </CButton>
                  <CButton
                     @click="$inertia.visit(`/admin/testers/${report.tester.id}`)"
                     color="light"
                     style="border-left:1px solid #ced2d8"
                  >
                     К тестировщику
                  </CButton>
               </CButtonGroup>
               <CDropdown color="dark" toggler-text="Выгрузить" class="float-right">
                  <CDropdownItem :href="`/admin/error-reports/${report.id}/export/excel`">
                     Как XLSX
                  </CDropdownItem>
                  <CDropdownItem :href="`/admin/error-reports/${report.id}/export/csv`">
                     Как CSV
                  </CDropdownItem>
                  <CDropdownItem :href="`/admin/error-reports/${report.id}/export/pdf`">
                     Как PDF
                  </CDropdownItem>
               </CDropdown>
            </CCardFooter>
         </CCard>
      </CCol>
   </CRow>
</template>

<script>
import Layout from '@admin/containers/TheContainer';

export default {
   name: 'ErrorReportsShow',
   layout: Layout,
   props: {
      report: Object,
   },
   data() {
      return {
         screenshots: [],
         videos: [],
         logs: [],
      };
   },
   beforeMount() {
      this.report.files.forEach(({ id, type, path }) => {
         switch (type) {
            case 'screenshot':
               this.screenshots.push({ id, path });
               break;
            case 'video':
               this.videos.push({ id, path });
               break;
            case 'log':
               this.logs.push({ id, path });
               break;
         }
      });
   },
   async mounted() {
      if (this.report.is_new === 1) {
         await this.$inertia.get(`/admin/error-reports/${this.report.id}/clear-new`);
      }
   },
};
</script>

<style scoped>
.screenshots {
   display: flex;
   flex-direction: row;
   flex-wrap: wrap;
}

.screenshots > div {
   width: 30%;
   margin: 1rem;
}

@media screen and (max-width: 900px) {
   .screenshots > div {
      width: 45%;
   }
}

@media screen and (max-width: 600px) {
   .screenshots > div {
      width: 90%;
   }
}
</style>
