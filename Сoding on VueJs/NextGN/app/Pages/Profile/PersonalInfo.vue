<template>
  <div
    class="cabinet__tab"
    style="display: block;"
  >
    <p class="cabinet__tab-title">
      Личная информация
    </p>
    <p class="mb-10">
      NextGen собирает ваши данные для формирования персональных предложений по тестированию цифровых продуктов и
      услуг и обязуется хранить эти данные в безопасности. С перечнем данных и целями их использования можно ознакомиться по
      <a class="text-accent" href="" target="_blank">ссылке</a>
    </p>
    <div class="cabinet__inputs-grid">
      <div class="cabinet__inputs-item">
        <div
          class="input-container"
          :class="{ 'has-error': $v.email.$dirty ? $v.email.$error : false }"
        >
          <p>Ваш e-mail</p>
          <input
            id="email"
            v-model="$v.email.$model"
            type="email"
            name="email"
          >
          <label
            v-if="$v.email.$dirty ? $v.email.$error : false"
            class="error ui-input__validation"
            for="email"
          >
            Введите валидный Email
          </label>
        </div>
      </div>
      <div class="cabinet__inputs-item">
        <div
          class="input-container"
          :class="{ 'has-error': $v.name.$dirty ? $v.name.$error : false }"
        >
          <p>Имя Фамилия</p>
          <input
            id="name"
            v-model="$v.name.$model"
            name="name"
            type="text"
          >
          <label
            v-if="$v.name.$dirty ? $v.name.$error : false"
            class="error ui-input__validation"
            for="name"
          >
            Это поле необходимо заполнить.
          </label>
        </div>
      </div>
      <div class="cabinet__inputs-item">
        <div
          class="input-container"
          :class="{ 'has-error': $v.regionId.$dirty ? $v.regionId.$error : false }"
        >
          <p>Регион</p>
          <selectize
            id="region"
            v-model="$v.regionId.$model"
            name="region"
            placeholder="Липецкая область, Россия"
          >
            <option
              v-for="region in regions"
              :key="region.id"
              :value="region.id"
            >
              {{ region.name }}
            </option>
          </selectize>
          <label
            v-if="$v.regionId.$dirty ? $v.regionId.$error : false"
            class="error ui-input__validation"
            for="region"
          >
            Выберите регион
          </label>
        </div>
      </div>
      <div class="cabinet__inputs-item">
        <div
          class="input-container"
          :class="{ 'has-error': $v.birthDate.$dirty ? $v.birthDate.$error : false }"
        >
          <p>Род занятий</p>
          <selectize
            id="occupation"
            v-model="$v.occupation.$model"
            placeholder="Выберите один из пунктов"
          >
            <option
              v-for="occupation in occupations"
              :key="occupation.id"
              :value="occupation.name"
            >
              {{ occupation.name }}
            </option>
          </selectize>
          <label
            v-if="$v.occupation.$dirty ? $v.occupation.$error : false"
            class="error ui-input__validation"
            for="occupation"
          >
            Выберите род занятий
          </label>
        </div>
      </div>
      <div class="cabinet__inputs-item">
        <div class="input-container">
          <p>Пол</p>
          <div class="input-container__row">
            <div class="input-radio">
              <label
                tabindex="0"
                @keydown="onSexMaleKeyDown"
              >
                <input
                  ref="inputSexMale"
                  v-model="sex"
                  type="radio"
                  value="male"
                >
                <div class="checkbox" />
                <span>муж</span>
              </label>
            </div>
            <div class="input-radio">
              <label
                tabindex="0"
                @keydown="onSexFemaleKeyDown"
              >
                <input
                  ref="inputSexFemale"
                  v-model="sex"
                  type="radio"
                  value="female"
                >
                <div class="checkbox" />
                <span>жен</span>
              </label>
            </div>
          </div>
        </div>
      </div>
      <div class="cabinet__inputs-item">
        <div
          class="input-container"
          :class="{ 'has-error': $v.birthDate.$dirty ? $v.birthDate.$error : false }"
        >
          <p>Дата рождения</p>
          <date-picker
            id="birthDate"
            v-model="$v.birthDate.$model"
            format="DD.MM.YYYY"
            title-format="DD.MM.YYYY"
            input-class="birth-date-input"
            style="width: 100%"
          />
          <label
            v-if="$v.birthDate.$dirty ? $v.birthDate.$error : false"
            class="error ui-input__validation"
            for="birthDate"
          >
            Укажите дату рождения
          </label>
        </div>
      </div>
      <!-- telegram_phone_verified -->
      <div class="cabinet__inputs-item">
        <div class="input-container">
          <p>Есть Telegram</p>
          <div class="input-container__row">
            <div class="input-radio">
              <label
                tabindex="0"
                @keydown="onSexMaleKeyDown"
              >
                <input
                  ref="inputTelegramPhoneVerified"
                  v-model="telegram_phone_verified"
                  name="telegram_phone_verified"
                  type="radio"
                  :value="true"
                >
                <div class="checkbox" />
                <span>Да</span>
              </label>
            </div>
            <div class="input-radio">
              <label
                tabindex="0"
                @keydown="onSexFemaleKeyDown"
              >
                <input
                  ref="inputTelegramPhoneUnverified"
                  v-model="telegram_phone_verified"
                  name="telegram_phone_verified"
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
      <!-- telegram_phone -->
      <div class="cabinet__inputs-item">
        <div
          v-if="telegram_phone_verified"
          class="input-container"
        >
          <p>Номер телефона telegram</p>
          <input
            id="name"
            v-model="phone"
            name="name"
            type="number"
          >
          <!-- <label v-if="$v.telegram_phone.$dirty ? $v.telegram_phone.$error : false" class="error ui-input__validation" for="name">
                  Это поле необходимо заполнить.
               </label> -->
        </div>
      </div>
      <!-- education_level -->
      <div class="cabinet__inputs-item">
        <div class="input-container">
          <p>Какое у вас образование?</p>
          <selectize
            id="education_level"
            v-model="education_level"
            :settings="{
              plugins : {
                'remove_button' : {
                  label : 'x'
                }
              }
            }"
            placeholder="Выберите один из пунктов"
          >
            <option
              v-for="( value, key ) of cEducationLevelEnum"
              :key="key"
              :value="key"
            >
              {{ value }}
            </option>
          </selectize>
        </div>
      </div>
      <!-- martial_status -->
      <div class="cabinet__inputs-item">
        <div class="input-container">
          <p>Ваше семейное положение</p>
          <selectize
            id="education_level"
            v-model="martial_status"
            :settings="{
              plugins : {
                'remove_button' : {
                  label : 'x'
                }
              }
            }"
            placeholder="Выберите один из пунктов"
          >
            <option
              v-for="( value, key ) of cMartialStatusEnum"
              :key="key"
              :value="key"
            >
              {{ value }}
            </option>
          </selectize>
        </div>
      </div>
      <!-- has_children -->
      <div class="cabinet__inputs-item">
        <div class="input-container">
          <p>Есть ли у вас дети?</p>
          <div class="input-container__row">
            <div class="input-radio">
              <label
                tabindex="0"
                @keydown="onSexMaleKeyDown"
              >
                <input
                  ref="inputHasChildrenYes"
                  v-model="has_children"
                  name="has_children"
                  type="radio"
                  :value="true"
                >
                <div class="checkbox" />
                <span>Да</span>
              </label>
            </div>
            <div class="input-radio">
              <label
                tabindex="0"
                @keydown="onSexFemaleKeyDown"
              >
                <input
                  ref="inputHasChildrenNo"
                  v-model="has_children"
                  name="has_children"
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
    </div>
    <div
      v-show="has_children"
      class="container-fluid"
    >
      <div class="cabinet__tab-title">
        Расскажите о них
      </div>
      <div class="cabinet__inputs-grid">
        <!-- child_gender -->
        <div class="cabinet__inputs-item">
          <div class="input-container">
            <p>Пол ребёнка</p>
            <div class="input-container__row">
              <div class="input-radio">
                <label
                  tabindex="0"
                  @keydown="onSexMaleKeyDown"
                >
                  <input
                    ref="inputChildGenderMale"
                    v-model="child_gender"
                    type="radio"
                    value="male"
                  >
                  <div class="checkbox" />
                  <span>мальчик</span>
                </label>
              </div>
              <div class="input-radio">
                <label
                  tabindex="0"
                  @keydown="onSexFemaleKeyDown"
                >
                  <input
                    ref="inputChildGenderFemale"
                    v-model="child_gender"
                    type="radio"
                    value="female"
                  >
                  <div class="checkbox" />
                  <span>девочка</span>
                </label>
              </div>
            </div>
          </div>
        </div>
        <!-- child_age -->
        <div class="cabinet__inputs-item">
          <div class="input-container">
            <p>Возраст ребёнка</p>
            <input
              id="child_age"
              v-model="child_age"
              name="child_age"
              type="number"
            >
          </div>
        </div>
        <!-- child_count -->
        <div class="cabinet__inputs-item">
          <div class="input-container">
            <p>Количество детей</p>
            <input
              id="child_count"
              v-model="child_count"
              name="child_count"
              type="number"
            >
          </div>
        </div>
      </div>
    </div>
    <div class="cabinet__button">
      <button
        class="btn"
        @click="save"
      >
        Сохранить
      </button>
    </div>
  </div>
</template>

<script>
import Selectize from 'vue2-selectize'
import DatePicker from 'vue2-datepicker'
import 'vue2-datepicker/locale/ru'
import { email, required } from 'vuelidate/lib/validators'
import Layout from '@app/components/Layout/Index'
import ProfileLayout from './ProfileLayout'
export default {
  name: 'ProfilePersonalInfo',
  components: {
    Selectize,
    DatePicker
  },
  layout: [Layout, ProfileLayout],
  props: {
    info: Object,
    regions: Array,
    occupations: Array,
    educationLevelEnum: Object,
    martialStatusEnum: Object
  },
  data() {
    return {
      email: '',
      name: '',
      regionId: null,
      occupation: '',
      sex: null,
      birthDate: null,
      phone: '',
      telegram_phone_verified: false,
      education_level: null,
      martial_status: null,
      has_children: false,
      child_gender: null,
      child_age: null,
      child_count: null
    }
  },
  computed: {
    cEducationLevelEnum() {
      return {
        'education.basic': 'Основное общее',
        'education.basic_middle': 'Cреднее общее',
        'education.professional_middle': 'Среднее профессиональное',
        'education.professional': 'Высшее'
      }
    },
    cMartialStatusEnum() {
      return            this.sex != 'female' ? {
        'martial_status.single': 'Холост',
        'martial_status.married': 'Женат',
        'martial_status.widow': 'Вдовец'
      } : {
        'martial_status.single': 'Не замужем',
        'martial_status.married': 'Замужем',
        'martial_status.widow': 'Вдова'
      }
    }
  },
  beforeMount() {
    this.email = this.$page.props.user.email
    this.name = this.$page.props.user.name

    if (this.info) {
      this.regionId = this.info.region_id || null
      this.occupation = this.info.occupation || ''
      this.sex = this.info.sex || null
      this.birthDate = new Date(this.info.birthdate) || null
      this.phone = this.info.telegram_phone || '',
      this.telegram_phone_verified = typeof this.info.telegram_phone_verified === 'boolean' ? this.info.telegram_phone_verified : null,
      this.education_level = this.info.education_level || null,
      this.martial_status = this.info.martial_status || null,
      this.has_children = this.info.has_children || false,
      this.child_gender = this.info.child_gender || null,
      this.child_age = this.info.child_age || null,
      this.child_count = this.info.child_count || null
    }
  },
  methods: {
    onSexMaleKeyDown(e) {
      if (e.keyCode === 13) {
        e.preventDefault()
        this.$refs.inputSexMale.click()
      }
    },
    onSexFemaleKeyDown(e) {
      if (e.keyCode === 13) {
        e.preventDefault()
        this.$refs.inputSexFemale.click()
      }
    },
    async save() {
      this.$v.$touch()
      if (this.$v.$invalid) return

      await this.$inertia.post('/profile/edit-personal-info', {
        email: this.email,
        name: this.name,
        region_id: +this.regionId,
        occupation: this.occupation,
        sex: this.sex,
        birthdate: this.birthDate,
        phone: this.phone,
        telegram_phone_verified: this.telegram_phone_verified,
        education_level: this.education_level,
        martial_status: this.martial_status,
        has_children: this.has_children,
        child_gender: this.has_children ? this.child_gender : null,
        child_age: this.has_children ? Number( this.child_age ) : null,
        child_count: this.has_children ? Number( this.child_count ) : null
      } )
    }
  },
  validations: {
    email: {
      required,
      email
    },
    name: {
      required
    },
    regionId: {
      required
    },
    occupation: {
      required
    },
    birthDate: {
      required
    }
  }
}
</script>

<style>
.cabinet__inputs-item .selectize-input .item {
  width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>
