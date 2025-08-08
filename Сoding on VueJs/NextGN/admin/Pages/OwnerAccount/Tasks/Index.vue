<template>
  <CRow>
    <CCol col="12">
      <CCard>
        <CCardHeader>
          <h4>Задания</h4>
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
              В архиве:
            </p>
            <ul style="list-style-type: none; margin-left: -23px">
              <li>
                <label>
                  <input
                    v-model="is_archived"
                    type="radio"
                    value=""
                    @change="filtered"
                  >
                  Не важно
                </label>
              </li>
              <li>
                <label>
                  <input
                    v-model="is_archived"
                    type="radio"
                    :value="true"
                    @change="filtered"
                  >
                  Да
                </label>
              </li>
              <li>
                <label>
                  <input
                    v-model="is_archived"
                    type="radio"
                    :value="false"
                    @change="filtered"
                  >
                  Нет
                </label>
              </li>
            </ul>
          </div>
          <div class="col-2">
            <p class="font-weight-bold">
              Вопросов от {{ getMinValue }} до {{ getMaxValue }}
            </p>
            <vue-slider
              v-model="selectedValue"
              :min="minValue"
              :max="maxValue"
              @change="filtered"
            />
          </div>
        </div>

        <CCardBody>
          <CButtonGroup class="mb-3">
            <CButton
              color="primary"
              @click="selectAllRows"
            >
              {{ isAllSelected ? 'Снять выделение' : 'Выделить все' }}
            </CButton>
          </CButtonGroup>
          <CDataTable
            hover
            striped
            column-filter
            :table-filter="{ placeholder: 'Фильтр', label: ' ' }"
            :items="items"
            :fields="fields"
            :items-per-page="30"
            :active-page="activePage"
            :pagination="{ doubleArrows: false, align: 'center' }"
            :no-items-view="{ noResults: 'Нет подходящих вариантов', noItems: 'Заданий пока нет' }"
          >
            <template #input="{ item: { id } }">
              <td>
                <CInputCheckbox
                  :checked="include(id)"
                  @change="addRow($event.target.checked, id)"
                />
              </td>
            </template>
            <template #is_active="{ item: { product: { is_active } } }">
              <td>
                <CBadge
                  :color="getBadgeColor(is_active)"
                  class="mb-1 mr-1"
                >
                  {{ getStatusText(is_active) }}
                </CBadge>
              </td>
            </template>
            <template #stage="{ item: { product: { stage } } }">
              <td>
                {{ stage ? 'Beta' : 'Alpha' }}
              </td>
            </template>
            <template #id="{ item: { id } }">
              <td>
                <a
                  :href="`/admin/tasks/${id}`"
                  target="_blank"
                >{{ id }}</a>
              </td>
            </template>
            <template #show_details="{ item: { id } }">
              <td>
                <CButton
                  color="primary"
                  variant="outline"
                  shape="square"
                  size="sm"
                  @click="$inertia.visit(`/admin/tasks/${id}`)"
                >
                  Посмотреть
                </CButton>
              </td>
            </template>
          </CDataTable>
          <CButtonGroup class="mt-4 mb-3 ml-auto mb-xl-0 float-right">
            <CButton
              v-if="reportArray.length > 0"
              color="success"
              @click="copyModal = true"
            >
              Копировать
            </CButton>
            <CButton
              v-if="reportArray.length > 0"
              color="warning"
              @click="clearModal = true"
            >
              Очистить
            </CButton>
            <CButton
              block
              color="success"
              @click="$inertia.visit('/admin/tasks/create')"
            >
              Добавить
            </CButton>
            <CDropdown
              color="dark"
              toggler-text="Выгрузить все"
              add-toggler-classes="border-left-radius-none"
              style="width: 200px;"
            >
              <CDropdownItem :href="`/admin/tasks/export/excel`">
                Как XLSX
              </CDropdownItem>
              <CDropdownItem :href="`/admin/tasks/export/csv`">
                Как CSV
              </CDropdownItem>
              <CDropdownItem :href="`/admin/tasks/export/pdf`">
                Как PDF
              </CDropdownItem>
            </CDropdown>
          </CButtonGroup>
        </CCardBody>
      </CCard>
    </CCol>

    <CModal
      color="danger"
      centered
      :show.sync="clearModal"
    >
      <template #header>
        <h6 class="modal-title">
          Удаление отчетов
        </h6>
      </template>
      Вы уверены, что хотите удалить отчеты по выбранным заданиям?
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

    <CModal
      color="warning"
      centered
      :show.sync="copyModal"
    >
      <template #header>
        <h6 class="modal-title">
          На какой продукт скопировать задания?
        </h6>
      </template>
      <CSelect
        :value.sync="productToCopy"
        :options="productsOptions"
        label="Продукт"
        horizontal
      />
      <template #footer>
        <CButton
          color="warning"
          @click="copy(true)"
        >
          Копировать
        </CButton>
        <CButton
          color="light"
          @click="copy(false)"
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
  name: 'TasksIndex',
  components: {
    VueSlider
  },
  layout: Layout,
  props: {
    tasks: Array,
    products: Array
  },
  data() {
    return {
      selectedValue: [0, 100],
      minValue: 0,
      maxValue: 100,
      clearModal: false,
      copyModal: false,
      reportArray: [],
      productsOptions: [],
      productToCopy: null,
      fields: [
        { key: 'input', label: '', _style: { width: '1%' }, sorter: false, filter: false },
        { key: 'id', label: 'ID', sorter: false },
        { key: 'productName', label: 'Продукт', _style: 'min-width: 125px;' },
        { key: 'platformTitle', label: 'Платформа', _style: 'min-width: 125px;', filter: false },
        { key: 'stage', label: 'Стадия', _style: 'min-width: 125px;', filter: false },
        { key: 'is_active', label: 'Статус', _style: 'min-width: 125px;', filter: false },
        { key: 'title', label: 'Название', _style: 'min-width: 125px;' },
        { key: 'questionsLength', label: 'Вопросов', filter: false },
        { key: 'is_archived_label', label: 'В архиве', filter: false },
        { key: 'show_details', label: '', _style: { width: '1%' }, sorter: false, filter: false }
      ],
      platformOptions: [
        { label: 'Android', value: 'android' },
        { label: 'iOS', value: 'ios' },
        { label: 'Web Desktop', value: 'web' },
        { label: 'Web Android', value: 'web_android' },
        { label: 'Web iOS', value: 'web_ios' },
        { label: 'Smart TV', value: 'smart_tv' }
      ],
      activePage: 1,
      items: [],
      selectedItems: [],
      is_archived: '',
      isAllSelected: false
    }
  },
  computed: {
    getMinValue() {
      return this.selectedValue[0]
    },

    getMaxValue() {
      return this.selectedValue[1]
    }
  },
  beforeMount() {
    this.items = this.tasks
      .map(task => ({
        ...task,
        productName: task.product.name,
        platformTitle: task.product.platformTitle,
        questionsLength: task.questions.length
      }))

    this.productsOptions = this.products
      .map(product => ({
        value: product.id,
        label: `${product.name} - ${product.platformTitle}`
      }))

    if (this.productsOptions && this.productsOptions.length) {
      this.productToCopy = this.productsOptions[0]?.value || null
    }
  },
  methods: {
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
    selectAllRows() {
      if (this.isAllSelected) {
        this.reportArray = []
      } else {
        this.reportArray = this.items.map(item => item.id)
      }
      this.isAllSelected = !this.isAllSelected
    },
    filtered() {
      this.items = this.tasks
        .filter(task => {
          return (
            (this.selectedItems.length === 0 || this.selectedItems.includes(task.product.platform)) &&
                  task.questions.length >= this.selectedValue[0] &&
                  task.questions.length <= this.selectedValue[1] &&
                  (this.is_archived === '' || task.is_archived == this.is_archived)
          )
        })
        .map(task => {
          return {
            ...task,
            productName: task.product.name,
            platformTitle: task.product.platformTitle,
            questionsLength: task.questions.length
          }
        })
    },
    async clearTasks(modalValue) {
      this.clearModal = false
      if (!modalValue) return
      try {
        await this.$inertia.post('/admin/tasks/clearReport/', {
          reportArray: this.reportArray
        })
      } catch (e) {
        this.$handleError(e)
      }

      this.reportArray = []
    },
    async copy(modalValue) {
      this.copyModal = false
      if (!modalValue) return
      try {
        await this.$inertia.post('/admin/tasks/copyTasks/', {
          reportArray: this.reportArray,
          product_id: +this.productToCopy
        })
      } catch (e) {
        this.$handleError(e)
      } finally {
        setTimeout(() => {
          window.location.reload()
        }, 1000)
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
    }
  }
}
</script>

<style>
.border-left-radius-none {
   border-top-left-radius: 0;
   border-bottom-left-radius: 0;
}
</style>
