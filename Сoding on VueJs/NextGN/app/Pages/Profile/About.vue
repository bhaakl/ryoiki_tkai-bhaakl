<script>

// make migrations for has_pets and pets_list ( json colunn )

import Layout from '@app/components/Layout/Index'
import ProfileLayout from './ProfileLayout'
import Selectize from 'vue2-selectize'
import { minValue, required } from 'vuelidate/lib/validators'
import axios from 'axios'

import $ from 'jquery'

if (!$().selectize) {
  require('selectize')
}

export default {
  name: 'ProfileAbout',
  components: {
    Selectize
  },
  layout: [Layout, ProfileLayout],
  props: {
    user: Object,
    // list of car brands for combo
    p_list_car_brands: Array,
    // car models ( if there is selected brand avaliable ) - combo fill
    p_list_car_models: Array,
    // list of intrests for me
    p_list_intrests: Array,
    about: Object,
    disableSave: {
      type: Boolean,
      default: false
    },
    disableTitle: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      ...this.$props.about,
      x_car_models: this.$props.p_list_car_models,
      x_pets: ['Кошка', 'Собака', 'Хомяки', 'Морские свинки', 'Крысы', 'Кролики', 'Попугаи', 'Канарейки', 'Амадины', 'Рыбы', 'Черепахи', 'Ящерицы', 'Змеи', 'Ежики', 'Шиншиллы', 'Карликовые свинки'],
      selected_brand: undefined,
      newInterests: []
    }
  },
  watch: {
    selected_brand: {
      handler: async function(n) {
        let response = await axios.get(`/profile/about/car-models/${n}`)
        console.debug(response, n)
        this.x_car_models = response.data
      }
    },
    has_pet(n) {
      if (n)
        this.$nextTick(() => (function($, self) {
          $(self.$refs.petsSelector).selectize({
            showAddOptionOnCreate: false,
            createOnBlur: true,
            plugins: ['remove_button'],
            create: function(input) {
              return { value: input, text: input }
            },
            onChange: (v) => self.pets = v
          })
        })($, this))
    }
  },
  beforeMount() {
    this.selected_brand = this.car_brand_id
    if (Array.isArray(this.pets)) {
      this.pets.forEach(p => this.x_pets.push(p))
    }
    this.x_pets = this.x_pets.filter((v, i, a) => a.indexOf(v) === i)
    console.debug('x_pets', this.x_pets, 'pets', this.pets)
    console.debug('intrests', this.intrests)
  },
  mounted() {
    console.debug('ps', this.$refs.petsSelector);
    // make it dirty as selectize mlugin fucks off the input createOnBlur
    (function($, self) {
      $(self.$refs.petsSelector).selectize({
        showAddOptionOnCreate: false,
        createOnBlur: true,
        plugins: ['remove_button'],
        create: function(input) {
          if (!input || input.length < 2 || input.length > 50) {
            alert('Введите минимум 3 символа и максимум 50')

            return false
          }

          return { value: input, text: input }
        },
        onChange: (v) => {
          self.pets = v
        }
      })
      $(self.$refs.intrestSelector).selectize({
        showAddOptionOnCreate: false,
        createOnBlur: true,
        plugins: ['remove_button'],

        create: function(input) {
          if (!input || input.length < 3 || input.length > 50) {
            alert('Введите минимум 3 символа и максимум 50')

            return false
          }
          console.debug('intrests input', input)

          return { value: input, text: input }
        },
        onChange: function(v) {
          self.intrests = v
        }
      })

    })($, this)
  },
  methods: {
    getPostData() {
      let storeKeys = [
        'household_type',
        'has_car',
        'income_level',
        'has_pet',
        'intrests'
      ]
      let store = {}
      for (let storeKey of storeKeys) {
        store[storeKey] = this[storeKey]
      }
      // household
      if (store['household_type']) {
        for (let storeKey of ['is_household_rental', 'has_countryhouse']) {
          store[storeKey] = this[storeKey]
        }
      }
      // car
      if (store['has_car']) {
        for (let storeKey of ['car_brand_id', 'car_model_id', 'car_issuie_year']) {
          store[storeKey] = this[storeKey] ?? null
        }
      } else {
        for (let storeKey of ['car_brand_id', 'car_model_id', 'car_issuie_year']) {
          store[storeKey] = null
        }
      }
      // pet
      if (store['has_pet']) {
        store['pets'] = this.pets
      } else {
        store['pets'] = null
      }

      store['intrests'] = this.intrests

      store['newInterests'] = this.newInterests
      store['lastInterests'] = this.lastInterests

      console.debug('ent to store is', store)

      return store
    },
    async save() {
      this.$v.$touch()
      if (this.$v.$invalid) return
      console.debug('pass')
      let store = this.getPostData()
      await this.$inertia.post('/profile/about/' + this.$props.about.id, store)
    }
  },
  validations: {
    household_type: {
      required
    },
    has_car: {
      required
    },
    has_pet: {
      required
    }
  }
}
</script>
<template>
  <div
    class="cabinet__tab"
    style="display: block;"
  >
    <p
      v-if="!$props.disableTitle"
      class="cabinet__tab-title"
    >
      О себе
    </p>
    <p class="mb-10">
      NextGen собирает ваши данные для формирования персональных предложений по тестированию цифровых продуктов
      и услуг и обязуется хранить эти данные в безопасности. С перечнем данных и целями их использования можно
      ознакомиться по <a class="text-accent" href="" target="_blank">ссылке</a>
    </p>
    <div class="cabinet__inputs-grid">
      <div class="cabinet__inputs-item row-container">
        <div class="input-container">
          <p>Какое у вас жильё?</p>
          <div
            class="input-container__row"
            :class="{ 'has-error': $v.household_type.$dirty ? $v.household_type.$error : false }"
          >
            <div class="input-radio">
              <label tabindex="0">
                <input
                  v-model="$v.household_type.$model"
                  type="radio"
                  value="household.apartment"
                >
                <div class="checkbox" />
                <span>Квартира</span>
              </label>
            </div>
            <div class="input-radio">
              <label tabindex="0">
                <input
                  v-model="$v.household_type.$model"
                  type="radio"
                  value="household.house"
                >
                <div class="checkbox" />
                <span>Дом</span>
              </label>
            </div>
            <div class="input-radio">
              <label tabindex="0">
                <input
                  v-model="$v.household_type.$model"
                  type="radio"
                  value="household.townhouse"
                >
                <div class="checkbox" />
                <span>Таунхаус</span>
              </label>
            </div>
          </div>
          <label
            v-if="$v.household_type.$dirty ? $v.household_type.$error : false"
            class="error ui-input__validation w-full"
            style="text-align: right;"
            for="household_type"
          >
            Это поле необходимо заполнить
          </label>
        </div>
      </div>
      <template v-if="household_type">
        <div class="cabinet__inputs-item row-container">
          <div class="input-container">
            <p>Съёмное или своё?</p>
            <div class="input-container__row">
              <div class="input-radio">
                <label tabindex="0">
                  <input
                    v-model="is_household_rental"
                    type="radio"
                    :value="true"
                  >
                  <div class="checkbox" />
                  <span>Съёмное</span>
                </label>
              </div>
              <div class="input-radio">
                <label tabindex="0">
                  <input
                    v-model="is_household_rental"
                    type="radio"
                    :value="false"
                  >
                  <div class="checkbox" />
                  <span>Своё</span>
                </label>
              </div>
            </div>
          </div>
        </div>
        <div class="cabinet__inputs-item row-container">
          <div class="input-container">
            <p>Есть дача или загородное жильё?</p>
            <div class="input-container__row">
              <div class="input-radio">
                <label tabindex="0">
                  <input
                    v-model="has_countryhouse"
                    type="radio"
                    :value="true"
                  >
                  <div class="checkbox" />
                  <span>Есть</span>
                </label>
              </div>
              <div class="input-radio">
                <label tabindex="0">
                  <input
                    v-model="has_countryhouse"
                    type="radio"
                    :value="false"
                  >
                  <div class="checkbox" />
                  <span>Нет</span>
                </label>
              </div>
            </div>
          </div>
        </div>
      </template>
      <div class="cabinet__inputs-item row-container">
        <div class="input-container">
          <p>Есть у вас автомобиль?</p>
          <div
            class="input-container__row"
            :class="{ 'has-error': $v.has_car.$dirty ? $v.has_car.$error : false }"
          >
            <div class="input-radio">
              <label tabindex="0">
                <input
                  v-model="$v.has_car.$model"
                  type="radio"
                  :value="true"
                >
                <div class="checkbox" />
                <span>Есть</span>
              </label>
            </div>
            <div class="input-radio">
              <label tabindex="0">
                <input
                  v-model="$v.has_car.$model"
                  type="radio"
                  :value="false"
                >
                <div class="checkbox" />
                <span>Нет</span>
              </label>
            </div>
          </div>
          <label
            v-if="$v.has_car.$dirty ? $v.has_car.$error : false"
            class="error ui-input__validation w-full"
            style="text-align: right;"
            for="has_car"
          >
            Это поле необходимо заполнить
          </label>
        </div>
      </div>
      <!-- car extended info -->
      <template v-if="has_car">
        <div class="cabinet__inputs-item w-33">
          <div class="input-container">
            <p>Марка</p>
            <selectize
              v-model="car_brand_id"
              @input="selected_brand = car_brand_id"
            >
              <option
                v-for="brand of $props.p_list_car_brands"
                :key="brand.id"
                :value="Number( brand.id )"
              >
                {{ brand.name }}
              </option>
            </selectize>
          </div>
        </div>
        <div class="cabinet__inputs-item w-33">
          <div class="input-container">
            <p>Модель</p>
            <selectize v-model="car_model_id">
              <option
                v-for="model of x_car_models"
                :key="model.id"
                :value="Number( model.id )"
              >
                {{ model.name }}
              </option>
            </selectize>
          </div>
        </div>
        <div class="cabinet__inputs-item w-33">
          <div class="input-container">
            <p>Год выпуска</p>
            <input
              v-model="car_issuie_year"
              type="number"
            >
          </div>
        </div>
      </template>
      <!-- income level -->
      <div class="cabinet__inputs-item row-container">
        <div class="input-container">
          <p>Ваш уровень дохода</p>
          <selectize
            v-model="income_level"
            placeholder="Выберите один из пунктов"
            style="width: 50%;"
          >
            <option value="income_level.30000_50000">
              От 30000 ₽ до 50000 ₽
            </option>
            <option value="income_level.50000_70000">
              От 50000 ₽ до 70000 ₽
            </option>
            <option value="income_level.70000_100000">
              От 70000 ₽ до 100000 ₽
            </option>
            <option value="income_level.100000_150000">
              От 100000 ₽ до 150000 ₽
            </option>
            <option value="income_level.15000_INF">
              Больше 150000 ₽
            </option>
          </selectize>
        </div>
      </div>
      <!-- pets info -->
      <div class="cabinet__inputs-item row-container">
        <div
          class="input-container"
          :class="{ 'has-error': $v.has_pet.$dirty ? $v.has_pet.$error : false }"
        >
          <p>У вас есть животные?</p>
          <div class="input-container__row">
            <div class="input-radio">
              <label tabindex="0">
                <input
                  v-model="$v.has_pet.$model"
                  type="radio"
                  :value="true"
                >
                <div class="checkbox" />
                <span>Есть</span>
              </label>
            </div>
            <div class="input-radio">
              <label tabindex="0">
                <input
                  v-model="$v.has_pet.$model"
                  type="radio"
                  :value="false"
                >
                <div class="checkbox" />
                <span>Нет</span>
              </label>
            </div>
          </div>
          <label
            v-if="$v.has_pet.$dirty ? $v.has_pet.$error : false"
            class="error ui-input__validation w-full"
            style="text-align: right;"
            for="has_pet"
          >
            Это поле необходимо заполнить
          </label>
        </div>
      </div>
      <template v-if="has_pet">
        <div
          class="cabinet__inputs-item w-full"
          style="margin-bottom: 22px;"
        >
          <select
            ref="petsSelector"
            v-model="pets"
            multiple
            :class="{ 'custom-select': true }"
          >
            <option value="Кошка">
              Кошка
            </option>
            <option value="Собака">
              Собака
            </option>
            <template v-for="pet of x_pets">
              <option :value="pet">
                {{ pet }}
              </option>
            </template>
          </select>
          <!-- <selectize v-model="pets" :settings="{
              showAddOptionOnCreate : false,
              createOnBlur : true,
              create : function ( input ){
                  return { value : input, text : input }
              },
          }" multiple> -->
          <!-- required options -->
          <!-- <template v-for="pet of x_pets">
              <option :value="pet">
                  {{ pet }}
              </option>
          </template>
          <option value="Кошка">
              Кошка
          </option>
          <option value="Собака">
              Собака
          </option>
      </selectize> -->
        </div>
      </template>
      <!-- intrests -->
      <div class="cabinet__inputs-item w-full">
        <div class="input-container">
          <p>Какие у вас интересы?</p>
          <select
            ref="intrestSelector"
            v-model="intrests"
            placeholder="Выберите один или несколько пунктов"
            multiple
            :class="{ 'custom-select': true }"
          >
            <template v-for="intrest of $props.p_list_intrests">
              <option :value="intrest.name">
                {{ intrest.name }}
              </option>
            </template>
          </select>
        </div>
      </div>
    </div>
    <div
      v-if="!$props.disableSave"
      class="cabinet__button"
    >
      <button
        class="btn"
        @click="save"
      >
        Сохранить
      </button>
    </div>
  </div>
</template>
<style lang="scss">
.custom-select .selectize-input {
  display: flex; 
  flex-wrap: wrap; 
  white-space: normal;
  word-break: break-all;
}
.selectize-control.multi .item {
  width: fit-content!important;
  padding-left: 10px !important;
  padding-right: 10px !important;
  background: red !important;
  border-radius: 10px !important;
  color: white !important;
  margin-right: 5px !important;
  margin-bottom: 5px !important;
  margin-top: 5px!important;
}

.selectize-dropdown-content .create {
  display: none !important;
  // display: block !important;
  // padding: 7px 10px !important;
  // font-size: 14px !important;
  // line-height: 1.4 !important;
}

.cabinet__inputs-item {
  &.row-container {
    width: 100%;

    .input-container {
      flex-wrap: wrap;
      flex-direction: row;
      justify-content: space-between;
      align-items: center;

      > p {
        margin-bottom: 0;
      }

      .input-container__row {
        width: 50%;
        justify-content: space-between;

        > .input-radio {
          padding-right: 0;
        }
      }
    }
  }

  &.w-33 {
    width: 33.3%;
    
    @media screen and (max-width: 768px) {
      width: 100%;
    }
  }
}

.cabinet__content {
  max-width: 1131px;
}

.remove {
  position: static !important;
  display: inline !important;
  padding: 0 !important;
  border: unset !important;
  font-size: 19px !important;
  font-weight: 100 !important;
  margin-left: 5px;
}
</style>
