<template>
  <CRow class="justify-content-center">
    <CCol
      col="12"
      lg="10"
      xl="8"
    >
      <CCard>
        <CCardHeader>
          <h4>{{ user.name }}</h4>
        </CCardHeader>
        <CForm @submit.prevent="submit">
          <CCardBody>
            <CInput
              v-model="$v.user.name.$model"
              type="name"
              autocomplete="name"
              label="Имя"
              horizontal
              :is-valid="$v.user.name.$dirty ? !$v.user.name.$error : null"
              invalid-feedback="Введите имя"
            />
            <CInput
              v-model="$v.user.email.$model"
              type="email"
              autocomplete="email"
              label="Email"
              horizontal
              :is-valid="$v.user.email.$dirty ? !$v.user.email.$error : null"
              invalid-feedback="Введите корректный email"
            />
            <CInput
              v-model="$v.user.phone.$model"
              type="text"
              autocomplete="phone"
              label="Телефон"
              horizontal
              :is-valid="$v.user.phone.$dirty ? !$v.user.phone.$error : null"
              invalid-feedback="Введите корректный номер телефона"
            />
            <CRow
              form
              class="form-group"
            >
              <CCol
                tag="label"
                sm="3"
                class="col-form-label"
              >
                Роли

              </CCol>
              <CCol sm="9">
                <div :class="{'с-inp-validation': $v.userRoles.$dirty ? $v.userRoles.$error : false }" >
                  <CInputCheckbox
                    v-for="role in allRoles"
                    :key="role.value"
                    :label="role.label"
                    :value="role.value"
                    :name="`role_${role.value}`"
                    :checked="userRoles.includes(role.value)"
                    @input="changeRoles(role.value)"
                  />
                  <label v-if="$v.userRoles.$dirty ? $v.userRoles.$error : false" for="userRoles" class="c-inp-valid-lbl">
                    Необходимо выбрать хотя бы один пункт.
                  </label>
                </div>
              </CCol>
            </CRow>
            <CRow
              form
              class="form-group"
            >
              <CCol
                tag="label"
                sm="3"
                class="col-form-label"
              >
                Есть ли телеграм?
              </CCol>
              <CCol sm="9">
                <CInputRadioGroup
                  :checked.sync="telegram_phone_verified"
                  :options="cYesNoEnum" />
              </CCol>
            </CRow>
            <CRow
              form
              class="form-group"
            >
              <CCol
                tag="label"
                sm="3"
                class="col-form-label"
              >
                Есть ли у Вас дети?
              </CCol>
              <CCol sm="9">
                <div :class="{'с-inp-validation': $v.has_children.$dirty ? $v.has_children.$error : false }" >
                  <CInputRadioGroup
                    :checked.sync="has_children"
                    :options="cYesNoEnum" />
                  <label v-if="$v.has_children.$dirty ? $v.has_children.$error : false" for="has_children" class="c-inp-valid-lbl">
                    Укажите, есть ли у Вас дети.
                  </label>
                </div>
              </CCol>
            </CRow>
            <div v-show="inputRadioOptions(has_children, ...[,,], true)">
              <CRow
                form
                class="form-group"
              >
                <CCol
                  tag="label"
                  sm="3"
                  class="col-form-label"
                >
                  Пол ребёнка
                </CCol>
                <CCol sm="9">
                  <CInputRadioGroup
                    :checked.sync="child_gender"
                    :options="cChildGenderEnum" />
                </CCol>
              </CRow>
              <CInput
                v-model="child_age"
                type="number"
                label="Возраст ребёнка"
                horizontal
              />
              <CInput
                v-model="child_count"
                type="number"
                label="Количество детей"
                horizontal
              />
            </div>

            <CRow
              form
              class="form-group"
            >
              <CCol
                tag="label"
                sm="3"
                class="col-form-label"
              >
                Какое у вас образование?
              </CCol>
              <CCol sm="9">
                <CSelect
                  v-model="education_level"
                  :options="cEducationLevelEnum"
                  placeholder="Выберите уровень образования"
                />
              </CCol>
            </CRow>
            
            <CRow
              form
              class="form-group"
            >
              <CCol
                tag="label"
                sm="3"
                class="col-form-label"
              >
                Регион
              </CCol>
              <CCol sm="9">
                <CSelect
                  v-model="regionId"
                  :options="regions"
                  placeholder="Выберите регион"
                  :is-valid="$v.regionId.$dirty ? !$v.regionId.$error : null"
                  invalid-feedback="Выберите регион"
                />
              </CCol>
            </CRow>

            <CRow
              form
              class="form-group"
            >
              <CCol
                tag="label"
                sm="3"
                class="col-form-label"
              >
                Род занятий
              </CCol>
              <CCol sm="9">
                <CSelect
                  v-model="occupation"
                  :options="occupations"
                  placeholder="Выберите род занятий"
                  :is-valid="$v.occupation.$dirty ? !$v.occupation.$error : null"
                  invalid-feedback="Выберите род занятия"
                />
              </CCol>
            </CRow>

            <CRow
              form
              class="form-group"
            >
              <CCol
                tag="label"
                sm="3"
                class="col-form-label"
              >
                Пол?
              </CCol>
              <CCol sm="9">
                <div :class="{'с-inp-validation': $v.sex.$dirty ? $v.sex.$error : false }" >
                  <CInputRadioGroup
                    :checked.sync="sex"
                    :options="[
                      { value: 'муж', label: 'Мужской' },
                      { value: 'жен', label: 'Женский' }
                    ]"/>
                  <label v-if="$v.sex.$dirty ? $v.sex.$error : false" for="sex" class="c-inp-valid-lbl">
                    Укажите свой пол.
                  </label>
                </div>
              </CCol>
            </CRow>

            <CRow
              form
              class="form-group"
            >
              <CCol
                tag="label"
                sm="3"
                class="col-form-label"
              >
                Дата рождения
              </CCol>
              <CCol sm="9">
                <date-picker 
                  id="birthDate"
                  input-class="edit-datepicker-input"
                  v-model="innerDate" 
                  format="DD.MM.YYYY"
                  value-type="format"
                  @change="handleDateChange"
                  style="width: 100%;"
                >
                  <template #input>
                    <CInput
                      :value="formattedDate"
                      @input="handleInputChange"
                      type="text"
                      :is-valid="$v.formattedDate.$dirty ? !$v.formattedDate.$error : null"
                      :invalid="$v.formattedDate.$error"
                      :invalid-feedback="invalidFeedback"
                      ref="datePickerRef"
                      class="dp-input"
                    >
                      <template #prepend>
                        <CButton class="bd-input-icon-container" @click="openDatePicker">
                          <CIcon name="cil-calendar" class="bd-input-icon"/>
                        </CButton>
                      </template>
                    </CInput>
                  </template>
                  <template v-slot:icon-calendar>
                    <svg style="display: none;"></svg>
                  </template>
                  <template v-slot:icon-clear>
                    <svg style="display: none;"></svg>
                  </template>
                </date-picker>
              </CCol>
            </CRow>

            <CRow
              form
              class="form-group"
            >
              <CCol
                tag="label"
                sm="3"
                class="col-form-label"
              >
                Ваше семейное положение
              </CCol>
              <CCol sm="9">
                <CSelect
                  v-model="martial_status"
                  :options="cMartialStatusEnum"
                  placeholder="Выберите ваше семейное положение"
                />
              </CCol>
            </CRow>
            <CRow
              form
              class="form-group"
            >
              <CCol
                tag="label"
                sm="3"
                class="col-form-label"
              >
                Есть у вас автомобиль?
              </CCol>
              <CCol sm="9">
                <div :class="{'с-inp-validation': $v.has_car.$dirty ? $v.has_car.$error : false }" >
                  <CInputRadioGroup
                    :checked.sync="has_car"
                    :options="cYesNoEnum"
                  />
                  <label v-if="$v.has_car.$dirty ? $v.has_car.$error : false" for="has_car" class="c-inp-valid-lbl">
                    Укажите, какой у Вас автомобиль.
                  </label>
                </div>
              </CCol>
            </CRow>
            <div v-show="inputRadioOptions(has_car, ...[,,], true)">
              <CRow
                form
                class="form-group"
              >
                <CCol
                  tag="label"
                  sm="3"
                  class="col-form-label"
                >
                  Марка
                </CCol>
                <CCol sm="9">
                  <div class="input-container">
                    <selectize v-model="car_brand_id">
                      <option
                        v-for="brand in p_list_car_brands"
                        :key="brand.name"
                        :value="brand.id">{{ brand.name }}
                      </option>
                    </selectize>
                  </div>
                </CCol>
              </CRow>
              <CInput
                v-model="car_issuie_year"
                type="number"
                label="Год выпуска автомобили"
                horizontal
              />

              <CRow
                form
                class="form-group"
              >
                <CCol
                  tag="label"
                  sm="3"
                  class="col-form-label"
                >
                  Модель
                </CCol>
                <CCol sm="9">
                  <div class="input-container">
                    <selectize v-model="car_model_id">
                      <option
                        v-for="brand in p_list_car_models"
                        :key="brand.name"
                        :value="brand.id">
                        {{ brand.name }}
                      </option>
                    </selectize>
                  </div>
                </CCol>
              </CRow>

            </div>
            <CRow
              form
              class="form-group"
            >
              <CCol
                tag="label"
                sm="3"
                class="col-form-label"
              >
                Какое у вас жильё?
              </CCol>
              <CCol sm="9">
                <div :class="{'с-inp-validation': $v.household_type.$dirty ? $v.household_type.$error : false }" >
                  <CInputRadioGroup
                    :checked.sync="household_type"
                    @input="$v.household_type.$touch()"
                    :options="cHouseHoldTypeEnum"
                  />
                  <label v-if="$v.household_type.$dirty ? $v.household_type.$error : false" for="household_type" class="c-inp-valid-lbl">
                    Укажите свое жильё.
                  </label>
                </div>
              </CCol>
            </CRow>
            <CRow
              form
              class="form-group"
            >
              <CCol
                tag="label"
                sm="3"
                class="col-form-label"
              >
                Съёмное или своё?
              </CCol>
              <CCol sm="9">
                <CInputRadioGroup
                  :checked.sync="is_household_rental"
                  :options="['Съёмное', 'Своё']"
                />
              </CCol>
            </CRow>
            <CRow
              form
              class="form-group"
            >
              <CCol
                tag="label"
                sm="3"
                class="col-form-label"
              >
                Есть дача или загородное жильё?
              </CCol>
              <CCol sm="9">
                <CInputRadioGroup
                  :checked.sync="has_countryhouse"
                  :options="cYesNoEnum"
                />
              </CCol>
            </CRow>
            <CRow
              form
              class="form-group"
            >
              <CCol
                tag="label"
                sm="3"
                class="col-form-label"
              >
                У вас есть животные?
              </CCol>
              <CCol sm="9">
                <div :class="{'с-inp-validation': $v.has_pet.$dirty ? $v.has_pet.$error : false }" >
                  <CInputRadioGroup
                    :checked.sync="has_pet"
                    :options="cYesNoEnum"
                  />
                <label v-if="$v.has_pet.$dirty ? $v.has_pet.$error : false" for="has_pet" class="c-inp-valid-lbl">
                  Укажите, есть ли животные.
                </label>
                </div>
              </CCol>
            </CRow>
            <div v-show="inputRadioOptions(has_pet, ...[,,], true)">
              <CRow
                form
                class="form-group"
              >
                <CCol
                  tag="label"
                  sm="3"
                  class="col-form-label"
                >
                  Добавить животные
                </CCol>
                <CCol sm="9">
                  <multiselect
                    v-model="pets"
                    :options="x_pets"
                    :multiple="true"
                    :close-on-select="false"
                    :clear-on-select="false"
                    :preserve-search="true"
                    :taggable="true"
                    @tag="addPets"
                    placeholder="Выберите один или несколько пунктов"
                    deselect-label="Нажмите для удаления"
                    selected-label="Выбрано"
                    select-label="Нажмите для выбора"
                  />
                </CCol>
              </CRow>
            </div>
            <CRow
              form
              class="form-group"
            >
              <CCol
                tag="label"
                sm="3"
                class="col-form-label"
              >
                Ваш уровень дохода
              </CCol>
              <CCol sm="9">
                <CSelect
                  v-model="income_level"
                  :options="cIncomeLevelEnum"
                  placeholder="Выберите уровень вашего дохода"
                />
              </CCol>
            </CRow>

            <CRow
              form
              class="form-group"
            >
              <CCol
                tag="label"
                sm="3"
                class="col-form-label"
              >
                Какие у вас интересы?
              </CCol>
              <CCol sm="9">
                <multiselect
                  v-model="intrests"
                  :options="$props.p_list_intrests"
                  :multiple="true"
                  :close-on-select="false"
                  :clear-on-select="false"
                  :preserve-search="true"
                  :taggable="true"
                  @tag="addIntrest"
                  placeholder="Выберите один или несколько пунктов"
                  deselect-label="Нажмите для удаления"
                  selected-label="Выбрано"
                  select-label="Нажмите для выбора"
                  label="name"
                  track-by="id"
                />
              </CCol>
            </CRow>

            <CRow
              v-if="userRoles.includes(2) && $page.props.user.is_moderator"
              form
              class="form-group"
            >
              <CCol
                tag="label"
                sm="3"
                class="col-form-label"
              >
                Доступ к продуктам
              </CCol>
              <CCol sm="9">
                <multiselect
                  v-model="selected"
                  :options="allProducts.products"
                  :multiple="true"
                  :close-on-select="false"
                  :clear-on-select="false"
                  :preserve-search="true"
                  placeholder="Выберите продукт для добавления"
                  deselect-label="Нажмите для удаления"
                  selected-label="Выбрано"
                  select-label="Нажмите для выбора"
                  label="name"
                  track-by="id"
                />
              </CCol>
            </CRow>
          </CCardBody>
          <CCardFooter>
            <CButton
              type="submit"
              color="success"
            >
              Изменить
            </CButton>
            <CButton
              type="reset"
              color="light"
              @click="$inertia.visit('/admin/users')"
            >
              Назад
            </CButton>
            <CButton
              color="danger"
              class="float-right"
              @click="deleteUserModal = true"
            >
              Удалить
            </CButton>
          </CCardFooter>
        </CForm>
      </CCard>
      <CCard>
        <CCardHeader>
               <h4>История обновления персональных данных</h4>
            </CCardHeader>
            <CCardBody>
          <CDataTable
            :items="user.user_logs"
            :fields="fields"
            :no-items-view="{ noResults: 'Нет подходящих вариантов', noItems: 'Пока нет данных' }"
          >
            <template #index="{ index }">
              <td>{{ index + 1 }}</td>
            </template>
            <template #name="data">
              <td>{{ data.item.name }}</td>
            </template>
            <template #email="data">
              <td>{{ data.item.email }}</td>
            </template>
            <template #websso_id="data">
              <td>{{ data.item.websso_id }}</td>
            </template>
            <template #created_at="data">
              <td>{{ formatDateTime(data.item.created_at) }}</td>
            </template>
            <template #user_name="data">
              <td>{{ data.item.user_name }}</td>
            </template>
          </CDataTable>
        </CCardBody>
      </CCard>
    </CCol>
    <CModal
      color="success"
      centered
      :show.sync="editUserModal"
    >
      <template #header>
        <h6 class="modal-title">
          Изменить пользователя?
        </h6>
      </template>
      Вы уверены, что хотите изменить пользователя {{ user.name }}?
      <template #footer>
        <CButton
          color="success"
          @click="editUser(true)"
        >
          Изменить
        </CButton>
        <CButton
          color="light"
          @click="editUser(false)"
        >
          Отмена
        </CButton>
      </template>
    </CModal>
    <CModal
      color="danger"
      centered
      :show.sync="deleteUserModal"
    >
      <template #header>
        <h6 class="modal-title">
          Удалить пользователя?
        </h6>
      </template>
      Вы уверены, что хотите удалить пользователя {{ user.name }}?
      <template #footer>
        <CButton
          color="danger"
          @click="deleteUser(true)"
        >
          Удалить
        </CButton>
        <CButton
          color="light"
          @click="deleteUser(false)"
        >
          Отмена
        </CButton>
      </template>
    </CModal>
  </CRow>
</template>

<script>
import 'selectize/dist/css/selectize.default.css'
import { required, email, helpers } from 'vuelidate/lib/validators'
import Layout from '@admin/containers/TheContainer'
import Multiselect from 'vue-multiselect/src/Multiselect.vue'
import { countBy } from 'lodash/collection'
import Selectize from 'vue2-selectize'
import DatePicker from 'vue2-datepicker'
import 'vue2-datepicker/locale/ru'
import 'vue2-datepicker/index.css'
import { format, parse } from 'date-fns';
import moment from 'moment'

export default {
  name: 'EditUser',

  components: {
    Multiselect,
    Selectize,
    DatePicker,
  },

  layout: Layout,

  props: {
    info: {
      type: Object,
      default: () => {}
    },
    user: {
      type: Object,
      default: () => {}
    },
    allProducts: {
      type: Object,
      default: () => {}
    },
    regions: {
      type: Array,
      default: () => [{}]
    },
    occupations: {
      type: Array,
      default: () => [{}]
    },
    p_list_car_brands: {
      type: Array,
      default: () => [{}]
    },
    p_list_car_models: {
      type: Array,
      default: () => [{}]
    },
    p_list_intrests: {
      type: Array,
      default: () => [{}]
    }
  },

  data() {
    return {
      selected: this.user.owned_products.filter(product => product.id),
      userRoles: [],
      allRoles: [],
      editUserModal: false,
      deleteUserModal: false,

      education_level: null,
      martial_status: null,
      telegram_phone_verified: false,
      sex: null,
      innerDate: '',
      formattedDate: '',
      has_children: null,
      is_household_rental: null,
      regionId: null,
      occupation: null,
      intrests: null,
      pets: null,
      x_pets: ['Кошка', 'Собака'],

      household_type: null,
      income_level: null,
      has_pet: null,
      has_car: null,
      has_countryhouse: null,
      child_gender: null,
      child_age: 0,
      child_count: 0,

      car_brand_id: null,
      car_model_id: null,
      car_issuie_year: null,

      fields: [
        { key: 'index', label: '№', sorter: false, _style: 'max-width: 50px;' },
        { key: 'name', label: 'Имя', _style: 'max-width: 110px;' },
        { key: 'email', label: 'Email', _style: 'min-width: 200px;' },
        { key: 'websso_id', label: 'Номер телефона', _style: 'min-width: 200px;' },
        { key: 'created_at', label: 'Дата обновления', _style: 'min-width: 200px;' },
        { key: 'user_name', label: 'Инициатор обновления', _style: 'min-width: 200px;' }
      ],
      activePage: 1,
    }
  },
  watch: {
    innerDate: {
      handler(newVal) {
        this.formattedDate = newVal
      },
      immediate: true
    }
  },
  computed: {
    cEducationLevelEnum() {
      return [
        { value: 'education.basic', label: 'Основное общее' },
        { value: 'education.basic_middle', label: 'Cреднее общее' },
        { value: 'education.professional_middle', label: 'Среднее профессиональное' },
        { value: 'education.professional', label: 'Высшее' }]
    },
    cMartialStatusEnum() {
      return this.sex !== 'female' ? [
        { value: 'martial_status.single', label: 'Холост'},
        { value: 'martial_status.married', label: 'Женат'},
        { value: 'martial_status.widow', label: 'Вдовец' }] : [
        { value: 'martial_status.single', label: 'Не замужем' },
        { value: 'martial_status.married', label: 'Замужем' },
        { value: 'martial_status.widow', label: 'Вдова' }]
    },
    cYesNoEnum() {
      return [{ value: 'Да', label: 'Да' }, { value: 'Нет', label: 'Нет' }]
    },
    cHouseHoldTypeEnum() {
      return [
        { value: 'household.apartment', label: 'Квартира' },
        { value: 'household.house', label: 'Дом' },
        { value: 'household.townhouse', label: 'Таунхаус' }
      ]
    },
    cIncomeLevelEnum() {
      return [
        { value: 'income_level.30000_50000', label: 'От 30000 до 50000' },
        { value: 'income_level.50000_70000', label: 'От 50000 до 70000' },
        { value: 'income_level.70000_100000', label: 'От 70000 до 100000' },
        { value: 'income_level.100000_150000', label: 'От 100000 до 150000' },
        { value: 'income_level.15000_INF', label: 'Больше 150000' }
      ]
    },
    cChildGenderEnum() {
      return [
        { value: 'male', label: 'Мальчик' },
        { value: 'female', label: 'Девочка' },
      ]
    },
    birthDate: {
      get() {
        return this.innerDate
      },
      set(value) {
        this.innerDate = value
        this.$emit('input', value)
      }
    },
    invalidFeedback() {
      if (!this.$v.formattedDate.required) {
        return 'Укажите дату рождения'
      }
      if (!this.$v.formattedDate.validDate) {
        return 'Введите корректную дату в формате ДД.ММ.ГГГГ'
      }
      return ''
    }
  },
  beforeMount() {
    if (this.info) {
      this.education_level = this.info.education_level
      this.martial_status = this.info.martial_status
      this.telegram_phone_verified = this.inputRadioOptions(this.info.telegram_phone_verified)
      this.sex = this.inputRadioOptions(this.info.sex, 'муж', 'жен')
      this.birthDate = this.formatDate(this.info.birthdate)
      this.has_children = this.inputRadioOptions(this.info.has_children)
      this.regionId = this.info.region_id
      this.occupation = this.info.occupation
      this.child_age = this.info.child_age
      this.child_gender = this.info.child_gender
      this.child_count = this.info.child_count

      if (this.user.about) {
        this.household_type = this.user.about.household_type
        this.is_household_rental = this.inputRadioOptions(this.user.about.is_household_rental, 'Съёмное', 'Своё')
        this.has_car = this.inputRadioOptions(this.user.about.has_car)
        this.has_pet = this.inputRadioOptions(this.user.about.has_pet)
        this.has_countryhouse = this.inputRadioOptions(this.user.about.has_countryhouse)
        this.intrests = this.user.about.intrests.map( (val, index) => ({ id: index, name: val }))
        this.pets = this.user.about.pets
        if (Array.isArray(this.pets)) {
          this.pets.forEach(p => this.x_pets.push(p));
        }
        this.x_pets = this.x_pets.filter((v, i, a) => a.indexOf(v) === i)
        this.income_level = this.user.about.income_level
        this.car_brand_id = this.user.about.car_brand_id
        this.car_model_id = this.user.about.car_model_id
        this.car_issuie_year = this.user.about.car_issuie_year
      }
    }
  },
  mounted() {
    console.debug('this.regions', this.regions)
    this.$v.$touch()
    this.setRoles()
  },
  methods: {
    countBy,
    inputRadioOptions(field, trueOption = 'Да', falseOption = 'Нет', reverse = false) {
      if (reverse) {
        return field === trueOption ? true : false
      }
      if (typeof field === 'boolean') {
        return field ? trueOption : falseOption
      } 
      if (trueOption != 'Да' || falseOption != 'Нет') {
        switch(field) {
          case 'male':
            return trueOption
          case  'female':
            return falseOption
        }
      }
      return field;
    },
    setRoles() {
      try {
        this.allRoles = this.$page.props.admin.roles
          .map(role => ({
            value: role.id,
            label: role.label
          }))

        this.userRoles = this.$page.props.admin.roles
          .filter(role => this.user.roles.find(userRole => role.id == userRole.id))
          .map(role => role.id)
      } catch (e) {
        this.$handleError(e)
      }
    },
    changeRoles(id) {
      if (this.userRoles.includes(id)) {
        this.userRoles = this.userRoles.filter(role => role != id)

        return
      }
      this.userRoles.push(id)
    },
    submit() {
      this.$v.$touch()

      if (this.$v.$invalid) return
      if (this.userRoles.length < 1) {
        this.$toast.warning('У пользователя должна быть хотя бы одна роль')

        return
      }
      this.editUserModal = true
    },
    async editUser(modalValue) {
      this.editUserModal = false

      if (!modalValue) return
      try {
        await this.$inertia.put(`/admin/users/${this.user.id}`, {
          name: this.user.name,
          email: this.user.email,
          phone: this.user.phone,
          roles: this.userRoles,
          products: this.selected,
          has_children: this.has_children,
          region_id: +this.regionId,
          occupation: this.occupation,
          has_car: this.has_car,
          has_pet: this.has_pet,
          pets: this.has_pet ? this.pets : null,
          intrests: [...new Set(this.intrests.map((val, i) => val.name))],
          has_countryhouse: this.has_countryhouse,
          income_level: this.income_level,
          sex: this.sex,
          birthdate: new Date(this.formattedDate),
          education_level: this.education_level,
          martial_status: this.martial_status,
          telegram_phone_verified: this.telegram_phone_verified,
          is_household_rental: this.is_household_rental,
          household_type: this.household_type,
          child_gender: this.child_gender,
          child_age: this.child_age,
          child_count: this.child_age,
          car_brand_id: this.car_brand_id,
          car_model_id: this.car_model_id,
          car_issuie_year: this.car_issuie_year
        })
      } catch (e) {
        this.$handleError(e)
      }
    },
    async deleteUser(modalValue) {
      this.deleteUserModal = false
      if (!modalValue) return
      try {
        await this.$inertia.delete(`/admin/users/${this.user.id}`)
      } catch (e) {
        this.$handleError(e)
      }
    },
    formatDateTime(dateTime) {
      const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' }

      return new Date(dateTime).toLocaleString('ru-RU', options)
    },
    handleDateChange(date) {
      this.birthDate = date
      this.formattedDate = date
      this.$v.formattedDate.$touch()
    },
    handleInputChange(value) {  
      this.formattedDate = value
      const momentDate = moment(value, 'DD.MM.YYYY', true)
      if (momentDate.isValid()) {
        this.birthDate = value
      }
      this.$v.formattedDate.$touch()
    },
    openDatePicker() {
      this.$refs.datePickerRef.$el.querySelector('input').focus()
    },
    parseDate(dateString) {
      let parsedDate;
      const patterns = [
        { regex: /^\d{4}-\d{2}-\d{2}$/, format: 'yyyy-MM-dd' }, // 2024-10-12
        { regex: /^\d{2}\/\d{2}\/\d{4}$/, format: 'dd/MM/yyyy' }, // 12/10/2024
      ];
      for (const pattern of patterns) {
        if (pattern.regex.test(dateString)) {
          parsedDate = parse(dateString, pattern.format, new Date());
          break;
        }
      }
      return parsedDate;
    },
    formatDate(rawDate) {
      const parsedDate = this.parseDate(rawDate);
      return parsedDate ? format(parsedDate, 'dd.MM.yyyy') : 'Invalid date';
    },
    addIntrest(newTag) {
      this.intrests.push({id: this.intrests.length - 1, name: newTag})
    },
    addPets(newTag) {
      this.pets.push(newTag)
    }
  },
  validations: {
    user: {
      name: {
        required
      },
      email: {
        required,
        email
      },
      phone: {
        required
      },
    },
    userRoles: {
      required
    },

    regionId: {
      required, 
    },
    occupation: {
      required,
    },
    formattedDate: {
      required,
      validDate: helpers.regex('validDate', /^\d{2}\.\d{2}\.\d{4}$/)
    },

    sex: {
      required
    },
    has_children: {
      required
    },
    household_type: {
      required,
    },
    has_car: {
      required,
    },
    has_pet: {
      required
    },

  },
}
</script>

<style lang="scss" scoped>
.с-inp-validation {
  border: 1px solid #f95721;
}
.c-inp-valid-lbl {
  color: #e55353; font-size: 80%; width: 100%; text-align: right
}

.dp-input {
  margin-bottom: 2px;

  .bd-input-icon-container {
    border-right: none;
    border-left: 1px solid #768192;
    border-top: 1px solid #768192ed;
    border-bottom: 1px solid #768192;
    margin-right: 1px;
    padding: 0 10px;

    :hover {
      cursor: pointer;
      .bd-input-icon {
        color: #666;
      }
    }
    .bd-input-icon {
      color: #666666bd;
    }
  }
}
</style>