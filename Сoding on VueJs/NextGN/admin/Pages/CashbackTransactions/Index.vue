<template>
  <CRow>
    <transaction-export-modal ref="transactionExportModal"/>
    <CCol col="12">
      <CCard>

        <CCardHeader class="d-flex justify-content-between align-items-center">
          <h4>Cashback</h4>
          <div>
            <CButton size="sm" color="dark" @click.prevent="$refs.transactionExportModal.show()">Экспорт всех транзакций</CButton>
            <CButton size="sm" @click="handleTransactions" :disabled="!selectedItems.length" color="success">Обработать выбранные</CButton>
          </div>
        </CCardHeader>
        <CCardBody>
          <CDataTable
            hover
            striped
            :items="data"
            :fields="fields"
            :items-per-page="30"
            :active-page="activePage"
            :table-filter="{ placeholder: 'Фильтр', label: ' ' }"
            :pagination="{ doubleArrows: false, align: 'center' }"
            :noItemsView="{ noResults: 'Нет подходящих вариантов', noItems: 'Пока нет данных' }"
          >
            <template #checked-header>
              <CInputCheckbox class="d-flex justify-content-center align-items-center" @input="selectAllItems"></CInputCheckbox>
            </template>
            <template #product="{item}">
              <td>
                <a :href="`/admin/products/${item.product.id}`" target="_blank">{{ item.product.name }}</a>
              </td>
            </template>
            <template #tester="{item}">
              <td>
                <a :href="`/admin/testers/${item.user.id}`" target="_blank">{{ item.user.name }}</a>
              </td>
            </template>
            <template #phone="{item}">
              <td>
                {{ item.user.phone }}
              </td>
            </template>
            <template #checked="{item}">
              <td>
                <CInputCheckbox @input="selectCheckboxItem(item.id)"
                                :checked="selectedItems.includes(item.id)"></CInputCheckbox>
              </td>
            </template>
            <template #type="{item}">
              <td>
                {{ item.reward_type === 'coins' ? 'Коины' : 'Кэшбэк' }}
              </td>
            </template>
            <template #created_at="{item}">
              <td>
                {{ $moment(item.created_at).format('DD-MM-YYYY HH:mm:ss') }}
              </td>
            </template>
            <template #show_details="{ item: { id } }">
              <td>
                <CButton
                  @click="$inertia.visit(`/admin/transactions/${id}`)"
                  color="primary"
                  variant="outline"
                  shape="square"
                  size="sm">
                  Посмотреть
                </CButton>
              </td>
            </template>
          </CDataTable>
        </CCardBody>
      </CCard>
    </CCol>
  </CRow>
</template>

<script>
import Layout from '@admin/containers/TheContainer';
import TransactionExportModal from './TransactionExportModal.vue';

export default {
  name: 'TransactionIndex',
  layout: Layout,
  components: {
    TransactionExportModal
  },
  props: {
    data: Array,
  },
  data() {
    return {
      fields: [
        {key: 'checked', label: '', _style: 'width: 10px;', filter: true, sorter: false},
        {key: 'id', label: '№', _style: 'max-width: 20px;', filter: true, sorter: true},
        {key: 'tester', label: 'Тестировщик', _style: 'min-width: 100px;', filter: true, sorter: true},
        {key: 'phone', label: 'Номер телефона', _style: 'min-width: 100px;', filter: true, sorter: true},
        {key: 'product', label: 'Проект', _style: 'min-width: 100px;', filter: true, sorter: true},
        {key: 'value', label: 'Размер выплаты', _style: 'max-width: 110px;', filter: true, sorter: true},
        {key: 'type', label: 'Валюта', _style: 'min-width: 100px;', filter: true, sorter: true},
        {key: 'created_at', label: 'Время', _style: 'min-width: 100px;', filter: true, sorter: true},
        {key: 'reward_payment_status', label: 'Статус', _style: 'min-width: 100px;', filter: true, sorter: true},
        {key: 'show_details', label: '', _style: {width: '1%'}, sorter: false, filter: false},
      ],
      selectedItems: [],
      activePage: 1,
    };
  },
  methods: {
    selectCheckboxItem(id) {
      if (this.selectedItems.includes(id)) {
        this.selectedItems.splice(this.selectedItems.indexOf(id), 1)
      } else {
        this.selectedItems.push(id)
      }
    },
    selectAllItems() {
      if (this.selectedItems.length === this.data.length) {
        this.selectedItems = []
      } else {
        this.selectedItems = this.data.map((el) => {
          return el.id
        })
      }
    },
    async handleTransactions() {
      try {
        await this.$inertia.post(`/admin/transactions/cashback/handle`, {
          transactions: this.selectedItems.map((el) => {
            return {id: el}
          }),
        });

        this.selectedItems = [];
      } catch (e) {
        this.$handleError(e);
      }
    }
  }
}
</script>
