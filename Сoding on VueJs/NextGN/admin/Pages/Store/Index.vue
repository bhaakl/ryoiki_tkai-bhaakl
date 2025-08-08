<template>
  <CRow>
    <CCol col="12">
      <CCard>
        <CCardHeader>
          <h4>Магазин</h4>
        </CCardHeader>
        <div class="row mx-3 my-2">
          <div class="col-2">
            <p class="font-weight-bold">
              Тип товара:
            </p>
            <ul style="list-style-type: none; margin-left: -23px">
              <li
                v-for="item in categories"
                :key="item.id"
              >
                <label>
                  <input
                    :id="`option-one-${item.id}`"
                    v-model="filteredCategories"
                    type="checkbox"
                    :value="item.id"
                    @change="filtered"
                  >
                  {{ item.name }}
                </label>
              </li>
            </ul>
          </div>
        </div>
        <CDataTable
          hover
          striped
          column-filter
          :table-filter="{ placeholder: 'Фильтр', label: ' ' }"
          :items="items"
          :fields="fields"
          :items-per-page="5"
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
          <CButton class="mb-3 mr-3" @click="$inertia.visit('/admin/goods/create')" block color="success">
            Добавить
          </CButton>
        </CButtonGroup>
      </CCard>
    </CCol>
  </CRow>
</template>
<script>
import Layout from '@admin/containers/TheContainer'
export default {
  layout: Layout,
  props: {
    goods: Array,
    categories: Array,
    pages: Number
  },
  data() {
    return {
      reportArray: [],
      filteredCategories: [],
      fields: [
        { key: 'input', label: '', _style: { width: '1%' }, sorter: false, filter: false },
        { key: 'id', label: 'ID', sorter: false },
        { key: 'title', label: 'Продукт', _style: 'min-width: 125px;' },
        { key: 'categoryTitle', label: 'Категория', _style: 'min-width: 125px;', filter: false },
        { key: 'description', label: 'Описание', _style: 'min-width: 125px;', filter: false },
        { key: 'default_price', label: 'Цена по умолчанию', _style: 'min-width: 125px;', filter: false },
        { key: 'default_residual', label: 'Остаток по умолчанию', _style: 'min-width: 125px;', filter: false },
        { key: 'show_details', label: '', _style: { width: '1%' }, sorter: false, filter: false }
      ],
      items: [],
      activePage: 1,
      isAllSelected: false
    }
  },
  beforeMount() {
    this.initItems()
  },
  methods: {
    initItems() {
      this.items = this.goods
        .map(good => ({
          ...good,
          categoryTitle: good.categories.map(category => category.name).join(', ')
        }))
    },
    selectAllRows() {
      if (this.isAllSelected) {
        this.reportArray = []
      } else {
        this.reportArray = this.items.map(item => item.id)
      }
      this.isAllSelected = !this.isAllSelected
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
    filtered() {
      axios.post('/admin/get-goods', {categories: this.filteredCategories}).then((data) => {
        this.goods = data.data.data
        this.initItems()
      })
    }
  }
}
</script>
<style scoped>
</style>
