<template>
  <CRow>
    <CCol col="12">
      <CCard>
        <CCardHeader>
          <h4>Продукты</h4>
        </CCardHeader>
        <div class="row mx-3 my-2">
          <div class="col-2">
            <p class="font-weight-bold">
              Платформа:
            </p>
            <ul style="list-style-type: none; margin-left: -23px">
              <li
                v-for="(item, index) in platformOptions"
                :key="index"
              >
                <label>
                  <input
                    v-model="selectedItems"
                    type="checkbox"
                    :value="item.value"
                    @change="filtered"
                  >
                  {{ item.label }}
                </label>
              </li>
            </ul>
          </div>
          <div class="col-2">
            <p class="font-weight-bold">
              Статус:
            </p>
            <ul style="list-style-type: none; margin-left: -23px">
              <li>
                <label>
                  <input
                    v-model="status"
                    type="radio"
                    value="10"
                    @change="updateSelectedItemsStatus"
                  >
                  Не важно
                </label>
              </li>
              <li>
                <label>
                  <input
                    v-model="status"
                    type="radio"
                    value="2"
                    @change="updateSelectedItemsStatus"
                  >
                  Активно
                </label>
              </li>
              <li>
                <label>
                  <input
                    v-model="status"
                    type="radio"
                    value="0"
                    @change="updateSelectedItemsStatus"
                  >
                  Неактивно
                </label>
              </li>
            </ul>
          </div>
          <div class="col-2">
            <p class="font-weight-bold">
              Черновик:
            </p>
            <ul style="list-style-type: none; margin-left: -23px">
              <li>
                <label>
                  <input
                    v-model="draftStatus"
                    type="radio"
                    :value="true"
                    @change="updateSelectedItemsDraft"
                  >
                  Да
                </label>
              </li>
              <li>
                <label>
                  <input
                    v-model="draftStatus"
                    type="radio"
                    :value="false"
                    @change="updateSelectedItemsDraft"
                  >
                  Нет
                </label>
              </li>
            </ul>
          </div>
          <div class="col-2">
            <p class="font-weight-bold">
              Новых отчетов от {{ getMinValue }} до {{ getMaxValue }}
            </p>
            <vue-slider
              v-model="selectedValue"
              :min="minValue"
              :max="maxValue"
              @change="filtered"
            />
          </div>
          <div class="col-4">
            <p class="font-weight-bold">
              Дата начала и дата окончания:
            </p>
            <CInput
              v-model="startDateFilter"
              type="date"
              horizontal
              @change="filtered"
            />
            <CInput
              v-model="endDateFilter"
              type="date"
              horizontal
              @change="filtered"
            />
            <span
              v-if="checkDate"
              class="text-danger"
            >Дата начала не может быть больше даты окончания</span>
          </div>
        </div>

        <CCardBody>
          <CDataTable
            hover
            striped
            :table-filter="{ placeholder: 'Фильтр', label: ' ' }"
            :sorter="{ external: false, resetable: true }"
            CInputCheckbox
            :items="items"
            :fields="fields"
            :items-per-page="30"
            :active-page="activePage"
            :pagination="{ doubleArrows: false, align: 'center' }"
            :no-items-view="{ noResults: 'Нет подходящих вариантов', noItems: 'Продуктов пока нет' }"
          >
            <template #input="{ item: { id } }">
              <td>
                <CInputCheckbox
                  :checked="include(id)"
                  @change="addRow($event.target.checked, id)"
                />
              </td>
            </template>
            <template #id="{ item: { id } }">
              <td>
                <a
                  :href="`/admin/products/${id}`"
                  target="_blank"
                >{{ id }}</a>
              </td>
            </template>
            <template #stage="{ item: { stage } }">
              <td>
                {{ stage ? 'Beta' : 'Alpha' }}
              </td>
            </template>
            <template #date_start="{ item: { date_start } }">
              <td>
                {{ startDate(date_start) }}
              </td>
            </template>
            <template #date_end="{ item: { date_end } }">
              <td>
                {{ endDate(date_end) }}
              </td>
            </template>
            <template #is_active="{ item: { is_active } }">
              <td>
                <CBadge
                  :color="getBadgeColor(is_active)"
                  class="mb-1 mr-1"
                >
                  {{ getStatusText(is_active) }}
                </CBadge>
              </td>
            </template>
            <template #draft="{ item: { draft } }">
              <td>
                <CBadge
                  v-if="draft"
                  color="info"
                >
                  Черновик
                </CBadge>
              </td>
            </template>
            <template #new_reports="{ item: { new_reports } }">
              <td style="text-align: center;">
                <CBadge
                  v-if="new_reports"
                  color="danger"
                  class="mb-1 mr-1 px-2"
                >
                  {{ new_reports }}
                </CBadge>
              </td>
            </template>
            <template #show_details="{ item: { id } }">
              <td>
                <CButton
                  color="primary"
                  variant="outline"
                  shape="square"
                  size="sm"
                  @click="$inertia.visit(`/admin/products/${id}`)"
                >
                  Посмотреть
                </CButton>
              </td>
            </template>
          </CDataTable>

          <CButtonGroup class="mt-4 mb-3 ml-auto mb-xl-0 float-right">
            <CButton
              v-if="reportArray.length > 0"
              color="warning"
              @click="clearModalErrors = true"
            >
              Очистить ошибки
            </CButton>
            <CButton
              v-if="reportArray.length > 0"
              color="warning"
              style="min-width: 90px"
              @click="clearModalTask = true"
            >
              Очистить ответы
            </CButton>
            <CButton
              color="success"
              @click="$inertia.visit('/admin/products/create')"
            >
              Добавить
            </CButton>
          </CButtonGroup>
        </CCardBody>
      </CCard>
    </CCol>

    <CModal
      color="danger"
      centered
      :show.sync="clearModalErrors"
    >
      <template #header>
        <h6 class="modal-title">
          Удаление отчетов по ошибкам
        </h6>
      </template>
      Вы уверены, что хотите удалить отчеты по выбранным продуктам?
      <template #footer>
        <CButton
          color="danger"
          @click="clearErrors(true)"
        >
          Да
        </CButton>
        <CButton
          color="light"
          @click="clearErrors(false)"
        >
          Отмена
        </CButton>
      </template>
    </CModal>

    <CModal
      color="danger"
      centered
      :show.sync="clearModalTask"
    >
      <template #header>
        <h6 class="modal-title">
          Удаление ответов по заданиям
        </h6>
      </template>
      Вы уверены, что хотите удалить ответы заданий по выбранным продуктам?
      <template #footer>
        <CButton
          color="danger"
          @click="clearTasks(true)"
        >
          Да
        </CButton>
        <CButton
          color="light"
          @click="clearTasks(false)"
        >
          Отмена
        </CButton>
      </template>
    </CModal>
  </CRow>
</template>

<script>
import Layout from '@admin/containers/TheContainer'
import VueSlider from 'vue-slider-component'
import 'vue-slider-component/theme/default.css'

export default {
  name: 'ProductsIndex',
  components: {
    VueSlider
  },
  layout: Layout,
  props: {
    products: Array
  },
  data() {
    return {
      selectedValue: [0, 1000],
      draftStatus: false,
      minValue: 0,
      maxValue: 1000,
      clearModalErrors: false,
      clearModalTask: false,
      reportArray: [],
      fields: [
        { key: 'input', label: '', _style: { width: '1%' }, sorter: false, filter: false },
        { key: 'id', label: 'ID', sorter: true },
        { key: 'name', label: 'Название', _classes: 'font-weight-bold' },
        { key: 'stage', label: 'Стадия' },
        { key: 'platformLabel', label: 'Платформа' },
        { key: 'date_start', label: 'Дата начала' },
        { key: 'date_end', label: 'Дата окончания' },
        // { key: 'description', label: 'Описание', sorter: false },
        { key: 'is_active', label: 'Статус', _style: 'min-width: 100px;' },
        { key: 'new_reports', label: 'Новых отчетов', _style: 'min-width: 100px;' },
        { key: 'draft', label: 'Черновик', _style: 'min-width: 30px;' },
        { key: 'show_details', label: '', _style: { width: '1%' }, sorter: false, filter: false }
      ],
      activePage: 1,
      items: [],
      newItems: [],
      selectedItems: [],
      selectedItemsStatus: [],
      platformOptions: [
        { label: 'Android', value: 'android' },
        { label: 'iOS', value: 'ios' },
        { label: 'Web Desktop', value: 'web' },
        { label: 'Web Android', value: 'web_android' },
        { label: 'Web iOS', value: 'web_ios' },
        { label: 'Smart TV', value: 'smart_tv' }
      ],
      status: 10,
      startDateFilter: '',
      endDateFilter: '',
      checkDate: false
    }
  },
  computed: {
    getMinValue() {
      return this.selectedValue[0]
    },

    getMaxValue() {
      return this.selectedValue[1]
    },
    isUserCreateDraft() {
      return this.$page.props.roles.includes('create_draft')
    }
  },
  beforeMount() {

    if (this.isUserCreateDraft) {
      this.draftStatus = true
    }

    this.items = this.products
      .map(product => {
        const platform = this.platformOptions.find(p => p.value === product.platform)

        return {
          ...product,
          platformLabel: platform.label,
          new_reports: product.error_reports.filter(report => report.is_new === 1).length,
          date_start: this.$moment(product.date_start).format('YYYY-MM-DD'),
          date_end: this.$moment(product.date_end).format('YYYY-MM-DD')
        }
      })
      .sort((a, b) => {
        return a.is_active > b.is_active ? -1 : 1
      })

    const minDateStart = this.products.reduce((min, product) => {
      const dateStart = new Date(product.date_start)

      return dateStart < min ? dateStart : min
    }, new Date('9999-12-31'))

    const maxDateEnd = this.products.reduce((max, product) => {
      const dateEnd = new Date(product.date_end)

      return dateEnd > max ? dateEnd : max
    }, new Date('0000-01-01'))

    this.startDateFilter = this.$moment(minDateStart).format('YYYY-MM-DD')
    this.endDateFilter = this.$moment(maxDateEnd).format('YYYY-MM-DD')

    this.filtered()
  },
  methods: {
    updateSelectedItemsStatus() {
      this.selectedItemsStatus = []

      if (this.status === '2') {
        this.selectedItemsStatus = [2]
      }

      if (this.status === '0') {
        this.selectedItemsStatus = [0]
      }
      this.filtered()
    },

    updateSelectedItemsDraft() {
      this.filtered()
    },

    filtered() {
      if (this.startDateFilter > this.endDateFilter) {
        this.checkDate = true

        return
      }

      this.checkDate = false

      this.items = this.products
        .map(product => {
          const platform = this.platformOptions.find(p => p.value === product.platform)

          return {
            ...product,
            platformLabel: platform.label,
            new_reports: product.error_reports.filter(report => report.is_new === 1).length,
            date_start: this.$moment(product.date_start).format('YYYY-MM-DD'),
            date_end: this.$moment(product.date_end).format('YYYY-MM-DD')
          }
        })
        .filter(product => {
          return (
            (this.selectedItemsStatus.length === 0 || this.selectedItemsStatus.includes(product.is_active)) &&
                  (this.selectedItems.length === 0 || this.selectedItems.includes(product.platform)) &&
                  product.new_reports >= this.selectedValue[0] &&
                  product.new_reports <= this.selectedValue[1] &&
                  (this.startDateFilter > 0 || product.date_start >= this.startDateFilter) &&
                  (this.endDateFilter > 0 || product.date_end <= this.endDateFilter) &&
                  product.draft == this.draftStatus
          )
        })
        .sort((a, b) => {
          return a.is_active > b.is_active ? -1 : 1
        })
    },
    async clearErrors(modalValue) {
      this.clearModalErrors = false
      if (!modalValue) return
      try {
        await this.$inertia.post('/admin/tasks/clearErrors', {
          reportArray: this.reportArray
        })
        this.removeNewReports()
      } catch (e) {
        this.$handleError(e)
      }

      this.reportArray = []
    },
    async clearTasks(modalValue) {
      this.clearModalTask = false
      if (!modalValue) return
      try {
        await this.$inertia.post('/admin/tasks/clearTasksInProducts', {
          reportArray: this.reportArray
        })
      } catch (e) {
        this.$handleError(e)
      }

      this.reportArray = []
    },
    include(id) {
      if (this.reportArray.includes(id)) {
        return true
      }
    },
    addRow(isChecked, id) {
      if (isChecked) {
        this.reportArray.push(id)
      } else {
        this.reportArray = this.reportArray.filter(item => item !== id)
      }
    },
    getStatusText(status) {
      switch (status) {
      case 0:
        return 'Неактивно'
      case 1:
        return 'В ожидании'
      case 2:
        return 'Активно'
      default:
        return 'Неизвестно'
      }
    },
    getBadgeColor(status) {
      switch (status) {
      case 0:
        return 'danger'
      case 1:
        return 'warning'
      case 2:
        return 'success'
      default:
        return 'secondary'
      }
    },
    startDate(date) {
      return this.$moment(date).format('DD.MM.YYYY')
    },
    endDate(date) {
      return this.$moment(date).format('DD.MM.YYYY')
    },
    removeNewReports() {
      this.items = this.items.filter(item => !this.reportArray.includes(item.id))
    }
  }
}
</script>
