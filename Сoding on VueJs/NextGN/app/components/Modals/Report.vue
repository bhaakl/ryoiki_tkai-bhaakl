<template>
  <div
    :id="`report-modal-${product.id}`"
    class="modal mfp-with-anim mfp-hide"
  >
    <p class="modal__title">
      Отчёт об ошибке продукта «{{ product.name }}»
    </p>
    <div v-if="deviceOptions.length">
      <p
        id="device"
        class="modal__small-title"
      >
        Выберите устройство<span class="red"> *</span>:
      </p>
      <div
        class="input-container input-container--os"
        style="margin-top: 1rem;"
      >
        <selectize v-model="device">
          <option
            v-for="deviceItem in deviceOptions"
            :key="deviceItem.id"
            :value="deviceItem.title"
          >
            {{ deviceItem.title }}
          </option>
        </selectize>
      </div>

      <div v-if="isWebPhone">
        <p class="modal__small-title">
          Выберите браузер<span class="red"> *</span>:
        </p>
        <CIcon
          name="cil-check-circle"
          style="color: #2eb85c;"
        />
        <selectize v-model="newBrowser">
          <option
            v-for="browser in browserOptions"
            :key="browser"
            :value="browser"
          >
            {{ browser }}
          </option>
        </selectize>
      </div>
    </div>

    <div>
      <p class="modal__small-title">
        Дата и время фиксации ошибки<span class="red"> *</span>:
      </p>
      <div
        class="input-container input-container--os"
        style="margin-top: 1rem;"
      >
        <date-picker
          id="recorded_at"
          ref="plainDatePicker"
          v-model="recorded_at"
          type="datetime"
          :lang="lang"
          format="DD.MM.YYYY HH:mm"
          style="width: 100%"
          popup-class="plain-date-popup"
          time-title-format="DD.MM.YYYY"
          :disabled-date="disabledDate"
          :disabled-time="disabledTime"
          :time-picker-options="{ start: '00:00', step: '00:15', end: '23:45', format: 'HH:mm' }"
        >
          <template #footer>
            <div class="date-picker-footer">
              <button
                type="button"
                class="date-picker-footer-cancel"
                @click="clearDate()"
              >
                Отменить
              </button>
              <button
                type="button"
                class="date-picker-footer-confirm"
                @click="confirmDate()"
              >
                Подтвердить
              </button>
            </div>
          </template>
        </date-picker>
        <div v-if="recorded_at_error">
          <p class="red">
            Обязательное поле
          </p>
        </div>
      </div>
    </div>

    <div v-if="product.criticality_bug">
      <p class="modal__small-title">
        Критичность бага<span class="red"> *</span>:
        <img
          class="cursor-pointer"
          src="@app/assets/img/question.svg"
          :title="selectedCriticality"
          js-tooltip
        >
      </p>
      <div
        class="input-container input-container--os"
        style="margin-top: 1rem;"
      >
        <selectize v-model="criticality_bug">
          <option
            v-for="criticality in criticalityBugs"
            :key="criticality.id"
            :value="criticality.title"
          >
            {{ criticality.title }}
          </option>
        </selectize>
        <div v-if="criticality_bug_error">
          <p class="red">
            Обязательное поле
          </p>
        </div>
      </div>
    </div>

    <div
      v-if="product.type_bug"
      class="task-container modal__about-error"
    >
      <p class="modal__small-title">
        Тип бага<span class="red"> *</span>:
        <img
          class="cursor-pointer"
          src="@app/assets/img/question.svg"
          :title="selectedTypeBugs"
          js-tooltip
        >
      </p>
      <div
        class="input-container input-container--os"
        style="margin-top: 1rem;"
      >
        <selectize v-model="type_bug">
          <option
            v-for="bugType in typeBugs"
            :key="bugType.id"
            :value="bugType.title"
          >
            {{ bugType.title }}
          </option>
        </selectize>
        <div v-if="type_bug_error">
          <p class="red">
            Обязательное поле
          </p>
        </div>
      </div>
    </div>

    <div class="task-container modal__about-error">
      <p class="modal__small-title">
        Общее описание ошибки<span class="red"> *</span>:
      </p>
      <p class="modal__info">
        Например, «Приложение падает при попытке сохранить адрес доставки»
      </p>
      <e-input
        :key="inputReset"
        ref="description"
        v-model="description"
        is-required
        is-textarea
      />
    </div>
    <div class="task-container modal__about-error">
      <p class="modal__small-title">
        Пошаговый сценарий для воспроизведения ошибки<span class="red"> *</span>:
      </p>
      <p class="modal__info">
        Здесь нужно написать пошаговый сценарий по которому можно воспроизвести ошибку начиная от запуска приложения
      </p>

      <div class="modal__answer-container">
        <div
          v-for="step in script"
          :key="step.id"
          class="modal__answer"
        >
          <span class="answer__counter">{{ step.id }}.</span>
          <e-input
            :key="inputReset"
            :ref="step.isNew ? '' : 'script_step'"
            v-model="step.value"
            is-required
          />
        </div>
      </div>
      <div class="modal__button modal__button--task-answer">
        <a
          class="btn"
          href="#"
          @click="addStep"
        >Добавить шаг</a>
      </div>
    </div>
    <div class="task-container modal__about-error">
      <p class="modal__small-title">
        Ожидаемый результат<span class="red"> *</span>:
      </p>
      <p class="modal__info">
        Опишите, какого поведения вы ожидали от приложения
      </p>
      <e-input
        :key="inputReset"
        ref="expectations"
        v-model="expectations"
        is-required
        is-textarea
      />
    </div>
    <div class="task-container modal__about-error">
      <p class="modal__small-title">
        Фактический результат<span class="red"> *</span>:
      </p>
      <p class="modal__info">
        Опишите, как повело себя приложение
      </p>
      <e-input
        :key="inputReset"
        ref="reality"
        v-model="reality"
        is-required
        is-textarea
      />
    </div>
    <div class="task-container modal__about-error">
      <p class="modal__small-title">
        Скриншот:
      </p>
      <p class="modal__info">
        В случае если ошибка отображается на скриншоте - приложить скриншот
      </p>
      <file-input
        :key="inputReset"
        v-model="screenshots"
        :max-items="3"
        :max-file-size="10"
        :formats="['image/jpeg', 'image/png']"
        show-list
      />
    </div>
    <div class="task-container modal__about-error">
      <p class="modal__small-title">
        Видео:
      </p>
      <p class="modal__info">
        В случае если ошибка не статична — приложить видео
      </p>
      <file-input
        :key="inputReset"
        v-model="videos"
        show-list
        :max-items="1"
        :max-file-size="150"
        :formats="['video/mp4']"
      />
    </div>
    <div class="task-container modal__about-error">
      <p class="modal__small-title">
        Лог:
      </p>
      <p class="modal__info">
        В случае если ошибка отображается в логах устройства или браузера — приложить лог с ошибкой
      </p>
      <file-input
        :key="inputReset"
        v-model="logs"
        show-list
        :max-items="1"
        :max-file-size="20"
        :formats="['custom/log', 'custom/har']"
      />
    </div>
    <!-- <div class="modal__ready-status">
         <div class="modal__ready-bg"><span style="width: 30%"></span></div>
         <p class="modal__info">
            <span>30% выполнено.</span> Вы заполнили «Описание», осталось загрузить видео и скриншоты
         </p>
      </div> -->
    <p class="modal__info">
      <span class="red">*</span> — поля, обязательные к заполнению
    </p>
    <div class="modal__button modal__button--small-margin">
      <a
        class="btn"
        href="#"
        @click="send"
      >Отправить отчёт</a>
    </div>
  </div>
</template>

<script>
import { required } from 'vuelidate/lib/validators'
import Selectize from 'vue2-selectize'
import EInput from '@app/elements/Input'
import FileInput from '@app/elements/FileInput'
import SessionExpired from './SessionExpired.vue'
import DatePicker from 'vue2-datepicker'
import ru from 'vue2-datepicker/locale/ru'
export default {
  name: 'ModalReport',
  components: {
    EInput,
    FileInput,
    Selectize,
    DatePicker
  },
  props: {
    product: Object,
    userDevices: [Array, Object],
    deviceType: String,
    errorReport: Object,
    criticalityBugs: Array,
    typeBugs: Array
  },
  data() {
    return {
      device: null,
      criticality_bug: null,
      type_bug: null,
      lang: ru,
      script: [
        { id: 1, value: '', isNew: false },
        { id: 2, value: '', isNew: false }
      ],
      description: '',
      expectations: '',
      reality: '',
      recorded_at: null,
      screenshots: [],
      videos: [],
      logs: [],
      inputReset: 0,
      deviceOptions: [],
      criticalityBugsOptions: [],
      bugTypesOptions: [],
      newBrowser: '',
      browserOptions: ['Chrome', 'Safari', 'Firefox', 'Opera', 'Edge'],
      localErrorReport: {},
      criticality_bug_error: false,
      type_bug_error: false,
      device_error: false,
      recorded_at_error: false
    }
  },

  computed: {
    isWebPhone() {
      const platform = this.product.platform

      return (platform === 'web_ios' || platform === 'web_android')
    },

    selectedCriticality() {
      return this.criticalityBugs.find(criticality => criticality.title === this.criticality_bug)?.description || 'Выберите для описания критичности бага'
    },

    selectedTypeBugs() {
      return this.typeBugs.find(type => type.title === this.type_bug)?.description || 'Выберите для описания типа бага'
    }
  },
  watch: {
    criticality_bug(newVal) {
      this.criticality_bug_error = !newVal
    },
    type_bug(newVal) {
      this.type_bug_error = !newVal
    },
    recorded_at(newVal) {
      this.recorded_at_error = !newVal
    }
  },
  beforeMount() {

    const platform = this.product.platform
    const isMobile = (platform === 'android' || platform === 'ios' || platform === 'web_ios' || platform === 'web_android')
    const isSmartTv = (platform === 'smart_tv')

    if (isMobile) {
      this.setMobileDeviceOptions()
    } else if (isSmartTv) {
      this.setSmartTvDeviceOptions()
    } else {
      this.setWebDeviceOptions()
    }

    if (this.deviceOptions.length) {
      this.device = this.deviceOptions[0].title
    }

    this.getCriticalityBugs()
  },

  methods: {
    disabledDate(date) {
      return date > new Date()
    },
    disabledTime(time) {
      const now = new Date()
      const hour = time.getHours()
      const minute = time.getMinutes()

      return hour > now.getHours() || (hour === now.getHours() && minute > now.getMinutes())
    },
    addStep() {
      this.script.push({ id: this.script.length + 1, value: '', isNew: true })
    },
    scroll() {
      const element = document.getElementById('device')
      if (element) {
        element.scrollIntoView({
          behavior: 'smooth',
          block: 'start',
          inline: 'nearest'
        })
      }

      return
    },
    validateFields() {
      this.$refs.description.$v.$touch()
      this.$refs.expectations.$v.$touch()
      this.$refs.reality.$v.$touch()
      this.$refs.script_step.forEach(step => {
        if (!step.isNew) {
          step.$v.$touch()
        }
      })

      return (
        !this.$refs.script_step.some(step => step.$v.$invalid) &&
        !this.$refs.description.$v.$invalid &&
        !this.$refs.expectations.$v.$invalid &&
        !this.$refs.reality.$v.$invalid
      )
    },
    clearFields() {
      this.script = [
        { id: 1, value: '' },
        { id: 2, value: '' }
      ]
      this.description = ''
      this.expectations = ''
      this.reality = ''
      this.recorded_at = ''
      this.screenshots = []
      this.videos = []
      this.logs = []
      this.$refs.description.$v.$reset()
      this.$refs.expectations.$v.$reset()
      this.$refs.reality.$v.$reset()
      this.$refs.script_step.forEach(step => step.$v.$reset())
      this.localErrorReport = {}
      this.criticality_bug_error = false
      this.type_bug_error = false
      this.recorded_at_error = false
      this.criticality_bug = ''
      this.type_bug = ''
    },
    clearDate() {
      this.$refs.plainDatePicker.clear()
    },
    confirmDate() {
      this.$refs.plainDatePicker.closePopup()
    },
    async send() {
      const isValid = this.validateFields()

      if (!this.recorded_at) {
        this.recorded_at_error = true
        this.scroll()

        return
      }

      if (this.product.criticality_bug && !this.criticality_bug) {
        this.criticality_bug_error = true
        this.scroll()
      }

      if (this.product.type_bug && !this.type_bug) {
        this.type_bug_error = true
        this.scroll()

        return
      }

      if (!isValid) {
        for (let step of this.$refs.script_step) {
          if (step.$v.$invalid) {
            step.$el.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'nearest' })

            return
          }
        }
        if (this.$refs.description.$v.$invalid) {
          this.$refs.description.$el.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'nearest' })

          return
        }
        if (this.$refs.expectations.$v.$invalid) {
          this.$refs.expectations.$el.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'nearest' })

          return
        }
        if (this.$refs.reality.$v.$invalid) {
          this.$refs.reality.$el.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'nearest' })

          return
        }

        return
      }
      try {

        const { data } = await axios.get('/api/check-session')
        if (!data) {
          this.$modal.show(SessionExpired)

          return
        }

        const formData = new FormData()
        const formattedDate = this.$moment(this.recorded_at).format('YYYY-MM-DD HH:mm')

        formData.append('product_id', this.product.id)
        if (this.device) {
          formData.append('device', this.device)
          formData.append('device_type', this.deviceType)
        }
        formData.append('recorded_at', formattedDate || null)
        formData.append('description', this.description)
        formData.append('script', JSON.stringify(this.script))
        formData.append('expectations', this.expectations)
        formData.append('reality', this.reality)
        formData.append('browser', this.newBrowser)
        formData.append('task_id', this.errorReport.task_id || null)
        formData.append('question_id', this.errorReport.questionId || null)
        formData.append('criticality_bug', this.criticality_bug || null)
        formData.append('type_bug', this.type_bug || null)
        this.screenshots.forEach(file => {
          formData.append('screenshots[]', file)
        })
        this.videos.forEach(file => {
          formData.append('videos[]', file)
        })
        this.logs.forEach(file => {
          formData.append('logs[]', file)
        })
        await this.$inertia.post('/error-reports', formData)
        this.clearFields()
        this.$window.$.magnificPopup.close()
        if (this.errorReport.task_id) {
          this.$emit('open-task-modal', this.errorReport.task_id)
        }
        this.inputReset++
      } catch (e) {
        this.inputReset++
        this.$handleError(e)
      }
    },
    setMobileDeviceOptions() {
      if (Array.isArray(this.userDevices)) {
        return this.userDevices
          .filter(device => this.isValidDevice(device))
          .map(device => this.formatDevice(device))
      }

      if (typeof this.userDevices === 'object' && this.userDevices !== null) {
        return Object.values(this.userDevices.userDevicesMobile)
          .filter(device => this.isValidDevice(device))
          .map(device => this.formatDevice(device))
      }

      return []
    },
    setWebDeviceOptions() {
      if (Array.isArray(this.userDevices)) {
        this.deviceOptions = this.userDevices
          .map(device => {
            return {
              id: device.id,
              title: `${device.brand} (${device.os_version.os} ${device.os_version.version}) – ${device.browser}`
            }
          })
      }

      if (typeof this.userDevices === 'object' && this.userDevices !== null) {
        this.deviceOptions = this.userDevices.userDevicesWeb
          ? this.userDevices.userDevicesWeb.map(device => {
            return {
              id: device.id,
              title: `${device.brand} (${device.os_version.os} ${device.os_version.version}) – ${device.browser}`
            }
          })
          : []
      }
    },
    setSmartTvDeviceOptions() {
      if (Array.isArray(this.userDevices)) {
        this.deviceOptions = this.userDevices
          .map(device => {
            return {
              id: device.id,
              title: `${device.device_smart_tv.brand} – ${device.os_version.os}`
            }
          })
      }

      if (typeof this.userDevices === 'object' && this.userDevices !== null) {
        this.deviceOptions = this.userDevices.userDevicesSmartTv
          ? this.userDevices.userDevicesSmartTv.map(device => {
            return {
              id: device.id,
              title: `${device.device_smart_tv.brand} – ${device.os_version.os}`
            }
          })
          : []
      }
    },

    getCriticalityBugs() {

    },

    // Метод для проверки устройства
    isValidDevice(device) {
      const os = device.os_version.os.toLowerCase()
      const platform = this.product.platform.toLowerCase()

      if ((os === 'ios' || os === 'android') && (platform.includes('web_ios') || platform.includes('ios'))) {
        return os === 'ios'
      }

      if ((os === 'ios' || os === 'android') && (platform.includes('web_android') || platform.includes('android'))) {
        return os === 'android'
      }

      return false
    },
    // Метод для форматирования устройства
    formatDevice(device) {
      return {
        id: device.id,
        title: `${device.brand} ${device.model} (${device.os_version.os} ${device.os_version.version})`
      }
    }
  }
}
</script>

<style>
   .btn:hover {
     background: rgba(0,0,0,0);
     border: 1px solid rgba(188,195,208,.5);
     color: #ff0032;
   }

   #tooltip {
      z-index: 10000;
   }
</style>
