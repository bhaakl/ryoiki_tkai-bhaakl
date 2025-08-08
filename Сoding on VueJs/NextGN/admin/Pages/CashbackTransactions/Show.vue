<template>
  <CRow>
     <CCol col="12">
        <CCard>
           <CCardHeader>
              <h4>Транзакция {{ data.id }}</h4>
           </CCardHeader>
           <CCardBody>
              <CListGroup class="mt-3">
                    <CCardText class="font-weight-bold">
                      Проект:
                      <span class="font-weight-normal">
                        {{ data.product.name }}
                      </span>
                    </CCardText>
                </CListGroup>
                <CListGroup class="mt-3">
                    <CCardText class="font-weight-bold">
                      Тестировщик:
                      <span class="font-weight-normal">
                        {{ data.user.name }}
                      </span>
                    </CCardText>
                </CListGroup>
                <CListGroup class="mt-3">
                    <CCardText class="font-weight-bold">
                      E-mail тестировщика:
                      <span class="font-weight-normal">
                        {{ data.user.email }}
                      </span>
                    </CCardText>
                </CListGroup>
                <CListGroup class="mt-3">
                    <CCardText class="font-weight-bold">
                      Номер телефона тестировщика:
                      <span class="font-weight-normal">
                        {{ data.user.phone }}
                      </span>
                    </CCardText>
                </CListGroup>
                <CListGroup class="mt-3">
                    <CCardText class="font-weight-bold">
                      Награда:
                      <span class="font-weight-normal">
                        {{ $props.data.value }} {{ data.reward_type }} - {{ $props.data.reward_for }}
                      </span>
                    </CCardText>
                </CListGroup>
                <CListGroup class="mt-3">
                    <CCardText class="font-weight-bold">
                      Статус транзакции:
                      <span class="font-weight-normal">
                        {{ data.reward_payment_status }}
                      </span>
                    </CCardText>
                </CListGroup>
              <template v-if="data.rewards && data.rewards.length > 0">
                <h4 class="mt-4">Найденные ошибки:</h4>
                <CDataTable
                  :items="$props.data.rewards"
                  :fields="fields"
                >
                <template #id="{item}">
                  <td>
                    <a :href="`/admin/error-reports/${item.error_report.id}`" target="_blank">{{ item.error_report.id }}</a>
                  </td>
                </template>
                <template #description="{item}">
                  <td>{{ item.error_report.description }}</td>
                </template>
                </CDataTable>
              </template>
           </CCardBody>
        </CCard>
     </CCol>
  </CRow>
</template>

<script>
import Layout from '@admin/containers/TheContainer'

export default {
  name: 'TransactionShow',
  layout: Layout,
  props: {
     data: Object,
  },
  data() {
     return {
      fields: [
         { key: 'id', label: '№', sorter: false },
         { key: 'description', label: 'Описание', sorter: false },
         { key: 'reward', label: 'Награда', sorter: false }
        ]
     }
  }
}
</script>
