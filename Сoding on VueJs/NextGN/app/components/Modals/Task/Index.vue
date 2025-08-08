<template>
  <div
    :id="`task-modal-${task.id}`"
    class="modal mfp-with-anim mfp-hide"
  >
    <p class="modal__title">
      {{ task.title }}
    </p>
    <div class="modal__tasks">
      <div class="device_block">
        <p
          ref="device_id"
          class="modal__sub-title modal__sub-title--no-padding"
        >
          Выберите устройство
        </p>
        <selectize v-model="device_id">
          <option
            v-for="deviceItem in deviceOptions"
            :key="deviceItem.id"
            :value="deviceItem.id"
          >
            {{ deviceItem.title }}
          </option>
        </selectize>
        <p
          v-if="!is_valid && $v.device_id.$invalid"
          class="invalid__text"
        >
          Обязательно к заполнению
        </p>
      </div>
      <div
        v-if="isWebPhone"
        style="margin-bottom: 40px;"
      >
        <p class="modal__sub-title modal__sub-title--no-padding">
          Выберите браузер<span class="red"> *</span>:
        </p>
        <selectize v-model="newBrowser">
          <option
            v-for="browser in browserOptions"
            :key="browser"
            :value="browser"
          >
            {{ browser }}
          </option>
        </selectize>
        <p
          v-if="!is_valid && $v.device_id.$invalid"
          class="invalid__text"
        >
          Обязательно к заполнению
        </p>
      </div>
      <div
        v-if="isWeb"
        class="device_block"
      >
        <p class="modal__sub-title modal__sub-title--no-padding">
          Версия браузера<span class="red"> *</span>
        </p>
        <Input
          ref="comment"
          v-model="versionBrowser"
          no-margin-bottom
        />
        <p
          v-if="!is_valid && $v.versionBrowser.$invalid"
          class="invalid__text"
        >
          Обязательно к заполнению
        </p>
      </div>
      <div
        v-if="task.is_location"
        class="device_block"
      >
        <p class="modal__sub-title modal__sub-title--no-padding">
          Регион<span class="red"> *</span>
        </p>
        <selectize v-model="location">
          <option
            v-for="region in regions"
            :key="region.id"
            :value="region.id"
          >
            {{ region.name }}
          </option>
        </selectize>
        <p
          v-if="!is_valid && $v.location.$invalid"
          class="invalid__text"
        >
          Обязательно к заполнению
        </p>
      </div>
      <div class="flex flex-col">
        <div
          v-for="(question, index) in questions"
          :key="question.order"
          :style="`order:${question.sorting}`"
        >
          <component
            :is="question.component"
            ref="questions"
            :index="index + 1"
            :question="question"
            :task="task"
            :close="question.close"
            @updateSelectedAnswer="updateSelectedAnswer"
            @get-answer="getAnswer"
            @close-event="closeFollowingQuestions($event)"
            @error-report="setErrorReport($event)"
          />
        </div>
      </div>
    </div>

    <div class="modal__button">
      <pulse-loader
        v-if="isLoading"
        color="#ffffff"
        class="btn_loading"
      />
      <button
        v-else
        class="btn"
        @click="send(true)"
      >
        Завершить задание
      </button>
    </div>
  </div>
</template>

<script>
import ChooseOne from './ChooseOne'
import ChooseMany from './ChooseMany'
import TextQuestion from './TextQuestion'
import SliderQuestion from './SliderQuestion'
import CloseQuestion from './CloseQuestion'
import ManyInManyQuestion from './ManyInManyQuestion'
import File from './File'
import Selectize from 'vue2-selectize'
import { required, requiredIf } from 'vuelidate/lib/validators'
import Input from '@app/elements/Input'
import SessionExpired from '../SessionExpired.vue'
import PulseLoader from 'vue-spinner/src/PulseLoader.vue'

export default {
  name: 'ModalTaskIndex',
  components: {
    ChooseOne,
    ChooseMany,
    TextQuestion,
    SliderQuestion,
    CloseQuestion,
    File,
    ManyInManyQuestion,
    Selectize,
    Input,
    PulseLoader
  },
  props: {
    task: Object,
    userDevices: Array,
    deviceType: String,
    product: Object,
    regions: Array
  },
  data() {
    return {
      questions: [],
      replyId: null,
      fileQuestionsData: [],
      device_id: null,
      newBrowser: '',
      versionBrowser: null,
      location: null,
      is_valid: true,
      deviceOptions: [],
      browserOptions: ['Chrome', 'Safari', 'Firefox', 'Opera', 'Edge'],
      errorReport: {},
      isLoading: false,
      nestedQuestionIds: []
    }
  },
  computed: {
    isWebPhone() {
      const platform = this.product.platform

      return (platform === 'web_ios' || platform === 'web_android')
    },
    isWeb() {
      const platform = this.product.platform

      return (platform === 'web_ios' || platform === 'web_android' || platform === 'web')
    }
  },
  mounted() {
    this.getLocation()
  },
  validations: {
    device_id: {
      required
    },
    newBrowser: {
      required: requiredIf(function() {
        return ['web_ios', 'web_android'].includes(this.product.platform)
      })
    },
    versionBrowser: {
      required: requiredIf(function() {
        return this.product.platform.includes('web')
      })
    },
    location: {
      required: requiredIf(function() {
        return this.task.is_location === 1
      })
    }
  },
  async beforeMount() {
    await this.getNestedQuestions()

    this.questions = this.task.questions.map((question, key) => {
      question.isTrigger = false
      question.isNested = false
      question.isVisible = false
      question.selectedAnswer = null
      question.nestedQuestions = {}
      question.sorting = (key + 1) * 100
      question.component = {
        radio: 'ChooseOne',
        close: 'CloseQuestion',
        checkbox: 'ChooseMany',
        text: 'TextQuestion',
        range: 'SliderQuestion',
        file: 'File',
        many: 'ManyInManyQuestion'
      }[question.type]

      try {
        question.values = JSON.parse(question.values)
      } catch (e) {
        // пробуем парсить. Если не вышло, просто игнорируем
      }

      if (question.type === 'many') {
        try {
          question.question = JSON.parse(question.question)
        } catch (e) {
          // пробуем парсить. Если не вышло, просто игнорируем
        }
      }

      switch (question.component) {
      case 'SliderQuestion':
        if (question.values.nestedQuestion) {
          question.isTrigger = true
        }
        break
      default:
        if (question.values) {
          question.values.map((val) => {
            if (val.nestedQuestion) {
              question.isTrigger = true
              question.nestedQuestions[val.id] = val.nestedQuestion
            }
          })
        }
      }

      if (this.nestedQuestionIds.includes(question.id)) {
        question.isNested = true
      }

      return question

    })

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
      this.device_id = this.deviceOptions[0]?.id || null
    }
  },
  methods: {
    getNestedQuestions() {
      this.task.questions.map((question) => {
        question.component = {
          radio: 'ChooseOne',
          close: 'CloseQuestion',
          checkbox: 'ChooseMany',
          text: 'TextQuestion',
          range: 'SliderQuestion',
          file: 'File',
          many: 'ManyInManyQuestion'
        }[question.type]

        switch (question.component) {
        case 'ChooseOne':
        case 'CloseQuestion':
        case 'ChooseMany':
          if (question.values) {
            question.values = JSON.parse(question.values)
            question.values.map((val) => {
              if (val.nestedQuestion) {
                this.nestedQuestionIds.push(val.nestedQuestion)
              }
            })
          }
          break
        case 'SliderQuestion':
          question.values = JSON.parse(question.values)
          if (question.values.nestedQuestion) {
            this.nestedQuestionIds.push(question.values.nestedQuestion)
          }
          break
        }
      })

      this.nestedQuestionIds = [...new Set(this.nestedQuestionIds)]
    },
    getAnswer(data) {
      this.questions.map((question, index) => {
        if (question.id === data.questionId) {
          this.$set(this.questions, index, { ...question,
            isVisible: data.isVisible,
            selectedAnswer: data.isVisible ? question.selectedAnswer : null,
            sorting: data.sorting + 1,
            triggerTitle: data.case ? 'Дополнительный вопрос для Кейс ' + data.case : null })

          if (!data.visible && question.isTrigger) {
            for (let item in question.nestedQuestions) {
              this.closeNestedQuestions(question.nestedQuestions[item])
            }
          }
        }
      })
    },
    closeNestedQuestions(questionId) {
      this.questions.map((question, index) => {
        if (question.id === questionId) {
          this.$set(this.questions, index, { ...question, isVisible: false, selectedAnswer: null })
          if (question.isTrigger) {
            question.nestedQuestions.map((item) => {
              this.closeNestedQuestions(item)
            })
          }
        }
      })
    },
    updateSelectedAnswer(data) {
      this.questions.map((question, index) => {
        if (question.id === data.questionId) {
          this.$set(this.questions, index, { ...question, selectedAnswer: data.selectedAnswer })
        }
      })
    },
    getLocation() {
      if (this.$page.props.profile.info.region_id) {
        this.location = this.$page.props.profile.info.region_id
      }
    },
    closeFollowingQuestions(payload) {
      const { answer, q } = payload
      this.questions.forEach(question => {
        if (question.id > q.id) {
          question.close = answer.close
        }
      })
    },
    setErrorReport(payload) {
      this.$emit('set-error-report', payload)
    },
    validateFields() {
      const commentsValidation = []
      this.$refs.questions.forEach(q => {
        if (!q.question.isNested || (q.question.isNested && q.question.isVisible)) {
          if (typeof q.touchComment == 'function') {
            const isCommentValid = q.touchComment()
            commentsValidation.push(isCommentValid)

            return q.$v && q.$v.$touch() && isCommentValid
          }

          return q.$v && q.$v.$touch()
        }
      })

      let checkQuestionsValidation = this.$refs.questions.some(q => {
        if ( q.question.isNested && !q.question.isVisible ) {

          return false
        } else {

          return q.$v.$invalid
        }
      })

      return !checkQuestionsValidation && commentsValidation.every(i => i)
            && !this.$v.location.$invalid && !this.$v.versionBrowser.$invalid && this.$v.device_id.$invalid === false
    },
    async participate() {
      try {
        await this.$inertia.post('/products/participate', {
          product_id: this.product.id
        })
      } catch (e) {
        this.$handleError(e)
      }
    },
    async send(isFinished) {
      const { data } = await axios.get('/api/check-session')
      if (!data) {
        this.$modal.show(SessionExpired)

        return
      }

      this.is_valid = this.validateFields()

      if (!this.is_valid) {
        if (this.$v.device_id.$invalid === true) {
          this.$refs.device_id.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'nearest' })

          return
        }

        for (let question of this.$refs.questions) {
          if (!question.question.isNested || (question.question.isNested && question.question.isVisible)) {
            if (question.$v.$invalid || (typeof question.touchComment == 'function' && !question.touchComment())) {
              question.$el.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'nearest' })

              return
            }
          }
        }

        return
      }
      try {
        this.isLoading = true
        this.fileQuestionsData = this.$refs.questions
          .filter(q => q.question.component === 'File')
          .map(question => question.getData())

        const device = this.deviceOptions.find(item => item.id === parseInt(this.device_id))

        await this.$inertia.post('/task-answer', {
          task_id: this.task.id,
          is_finished: isFinished ? 1 : 0,
          answers:
                     this.$refs.questions
                       .filter(q => q.question.component !== 'File')
                       .filter(q => q.question.close === false)
                       .map(question => question.getData()) || null,
          device_id: this.device_id,
          device_type: this.deviceType === 'smart_tv' ? 'device' : this.deviceType,
          os_id: device.os_id ? device.os_id : null,
          browser: this.newBrowser,
          browser_version: this.versionBrowser,
          location: this.regions.find(region => region.id === this.location)?.name
        },
        {
          preserveScroll: true,
          onSuccess: async event => {
            this.replyId = event.props.responseData.reply.id
            await this.sendFiles()
            await this.participate()
            this.$window.$.magnificPopup.close()
            this.$inertia.visit(this.$page.url)
            this.isLoading = false
          },
          onError: () => {
            this.isLoading = false
          }
        }
        )
      } catch (e) {
        this.$handleError(e)
      }
    },
    async sendFiles() {
      if (this.fileQuestionsData.length > 0) {
        for (const data of this.fileQuestionsData) {
          if (!data.answer || !data.answer.length) {
            continue
          }

          const formData = new FormData()
          formData.append('task_reply_id', this.replyId)
          formData.append('task_question_id', data.task_question_id)
          data.answer.forEach(file => {
            formData.append('answer[]', file)
          })
          formData.append('comment', data.comment)

          await this.$inertia.post('/task-answer-file', formData)
        }
      }
    },
    setMobileDeviceOptions() {
      this.deviceOptions = this.userDevices.filter(device => {
        const os = device.os_version.os.toLowerCase()
        const platform = this.product.platform.toLowerCase()

        if ((os === 'ios' || os === 'android') && (platform.includes('web_ios') || platform.includes('ios'))) {
          return os === 'ios'
        }

        if ((os === 'ios' || os === 'android') && (platform.includes('web_android') || platform.includes('android'))) {
          return os === 'android'
        }

        return false
      })
        .map(device => {
          return {
            id: device.id,
            title: `${device.brand} ${device.model} (${device.os_version.os} ${device.os_version.version})`
          }
        })
    },
    setWebDeviceOptions() {
      this.deviceOptions = this.userDevices.map(device => {
        return {
          id: device.id,
          title: `${device.brand} (${device.os_version.os} ${device.os_version.version}) – ${device.browser}`
        }
      })
    },
    setSmartTvDeviceOptions() {
      this.deviceOptions = this.userDevices.map(device => {
        return {
          id: device.device_id,
          title: `${device.device_smart_tv.brand} – ${device.os_version.os}`,
          os_id: device.os_version.id
        }
      })
    }
  }
}
</script>

<style>
.answer__counter {
   display: none !important;
}

.invalid__text {
   color: #ff0032;
   font-size: 0.8rem;
}

.invalid__border {
   border-color: #ff0032;
}

.device_block {
   margin-bottom: 20px;
}

.padding_y {
   padding: 10px;
}
.btn:hover {
   background: rgba(0,0,0,0);
   border: 1px solid rgba(188,195,208,.5);
   color: #ff0032;
}
.modal__button .btn {
   width: 100%;
}

.modal__button .btn_loading {
   width: 100%;
}

.btn_loading:hover {

}

.btn_loading {
   display: inline-block;
   position: relative;
   cursor: not-allowed;
   background: #ff0032;
   border-radius: 8px;
   color: #fff;
   font-size: 17px;
   font-family: NGEN Compact Medium,Helvetica,Arial,sans-serif;
   text-align: center;
   line-height: 1.3;
   padding: 13px 25px;
   transition: all .25s ease-in-out;
   box-sizing: border-box;
   border: 1px solid #ff0032;
}

</style>
