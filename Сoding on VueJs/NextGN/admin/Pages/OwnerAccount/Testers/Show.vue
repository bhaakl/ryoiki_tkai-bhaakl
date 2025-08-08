<template>
   <CRow class="justify-content-center">

      <CCol col="12" sm="10" md="9" xl="7">
         <CCard>
            <CCardHeader class="d-flex justify-content-between align-items-end">
               <CCardTitle class="mb-0">{{ tester.name }}</CCardTitle>
               <CBadge :color="getBadge(tester.role.name)" class="mr-1 mb-1">
                  {{ tester.role.label }}
               </CBadge>
            </CCardHeader>
            <CCardBody>
               <CListGroup v-if="tester.email || tester.websso_id">
                  <CCardText class="font-weight-bold">Контакты:</CCardText>
                  <CListGroupItem v-if="tester.email">
                     <span class="font-weight-bold">E-mail: </span>{{ tester.email }}
                  </CListGroupItem>
                  <CListGroupItem v-if="tester.websso_id">
                     <span class="font-weight-bold">Телефон: </span>+7{{ tester.websso_id }}
                  </CListGroupItem>
               </CListGroup>
               <CListGroup v-if="tester.personal_info" class="mt-3">
                  <CCardText class="font-weight-bold">Личные данные:</CCardText>
                  <CListGroupItem v-if="tester.personal_info.birthdate">
                     <span class="font-weight-bold">Дата рождения: </span>
                     {{ $moment(tester.personal_info.birthdate).format('DD MMMM YYYY') }}
                  </CListGroupItem>
                  <CListGroupItem v-if="tester.personal_info.occupation">
                     <span class="font-weight-bold">Род занятий: </span>{{ tester.personal_info.occupation }}
                  </CListGroupItem>
                  <CListGroupItem v-if="tester.personal_info.sex">
                     <span class="font-weight-bold">Пол: </span>
                     {{ tester.personal_info.sex === 'male' ? 'Мужской' : 'Женский' }}
                  </CListGroupItem>
                  <CListGroupItem v-if="tester.personal_info.region && tester.personal_info.region.name">
                     <span class="font-weight-bold">Регион: </span>
                     {{ tester.personal_info.region.name }}
                  </CListGroupItem>
               </CListGroup>
               <CListGroup class="mt-3">
                  <CCardText class="font-weight-bold">Устройства:</CCardText>
                  <TesterDevices :devices="tester.devices" :osVersions="osVersions" />
               </CListGroup>
               <CListGroup class="mt-3">
                  <CCardText class="font-weight-bold">Проекты:</CCardText>
                  <TesterProducts :products="tester.products" />
               </CListGroup>
               <CListGroup class="mt-3">
                  <CCardText class="font-weight-bold">Вознаграждения:</CCardText>
                  <TesterRewards :testerId="tester.id" :rewards="tester.rewards" :userRewards="userRewards" />
               </CListGroup>
            </CCardBody>
            <CCardFooter>
               <CButton @click="$inertia.visit('/admin/testers')" color="light">К тестировщикам</CButton>
               <CButton
                  @click="$inertia.visit(`/admin/error-reports/by-tester/${tester.id}`)"
                  color="info"
                  class="float-right"
               >
                  Посмотреть отчеты
               </CButton>
            </CCardFooter>
         </CCard>
      </CCol>
   </CRow>
</template>

<script>
import Layout from '@admin/containers/TheContainer';
import TesterDevices from './TesterDevices';
import TesterProducts from './TesterProducts';
import TesterRewards from './TesterRewards';

export default {
   name: 'TestersShow',
   layout: Layout,
   props: {
      tester: Object,
      userRewards: Array,
      osVersions: Array,
   },
   components: {
      TesterDevices,
      TesterProducts,
      TesterRewards,
   },
   methods: {
      getBadge(role) {
         switch (role) {
            case 'alpha':
               return 'danger';
            case 'beta':
               return 'success';
            default:
               return 'secondary';
         }
      },
   },
};
</script>

<style scoped>
.no-device {
   padding-left: 1.25rem;
   padding-right: 1.25rem;
}
</style>
