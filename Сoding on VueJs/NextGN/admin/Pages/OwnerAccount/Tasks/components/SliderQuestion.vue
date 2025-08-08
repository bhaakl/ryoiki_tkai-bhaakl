<template>
  <div class="card">
    <div class="card-header d-flex flex-row justify-content-between">
      <p class="font-weight-bold">
        Вопрос {{ index }}<span> (ползунок)</span>
      </p>
      <CButton
        v-if="!group_name"
        color="light"
        size="sm"
        class="question-drag-btn"
      >
        <CIcon
          size="sm"
          name="cil-line-spacing"
        />
      </CButton>
      <CButton
        v-if="group_name"
        color="info"
        @click="$emit('duplicate-question')"
      >
        Дублировать вопрос
      </CButton>
    </div>
    <div class="card-body">
      <CInputCheckbox
        label="Обязательное комментирование ответа"
        :checked.sync="isCommentRequired"
        name="comment_required"
        style="margin: 1rem 0;"
      />
      <p>Описание вопроса:</p>
      <VueTextEditor v-model="$v.question.$model" />
      <small
        v-if="$v.question.$dirty ? $v.question.$error : false"
        style="color:red"
      >Введите описание</small>

      <div class="mt-4">
        <CInputFile
          accept="image/*,video/*"
          label="Прикрепите видео или картинку:"
          class="mb-4"
          horizontal
          @change="onChangeImage"
        >
          <template #description>
            <small class="form-text text-muted w-100">
              Допустимые форматы для фото: все типы файлов изображений, такие как JPEG, PNG и др.<br>
              Допустимые форматы для видео: все типы файлов видео, такие как MP4, AVI, MOV и др.<br>
              Максимальный размер файла: 1.5 Гб<br>
            </small>
          </template>
        </CInputFile>
        <span
          v-if="errorMessage"
          class="text-danger"
        >{{ errorMessage }}</span>
        <div v-if="selectedFile">
          <img
            v-if="fileExtension == 'img'"
            style="width: 300px"
            :src="`/storage/images/${selectedFile}`"
          >
          <video
            v-if="fileExtension == 'video'"
            class="mb-4 mt-4"
            style="width: 300px"
            :src="`/storage/images/${selectedFile}`"
          />
        </div>
        <div class="buttons mt-4">
          <CButton
            v-if="selectedFile"
            color="danger"
            @click="deleteImage()"
          >
            Удалить
          </CButton>
        </div>
        <img
          v-if="getFileType == 'img'"
          class="mb-4 mt-4"
          style="width: 300px"
          :src="previewImageUrl"
        >
        <video
          v-if="getFileType == 'video'"
          class="mb-4 mt-4"
          style="width: 300px"
          :src="previewImageUrl"
        />
        <div class="buttons ml-2">
          <CButton
            v-if="previewImageUrl"
            color="danger"
            @click="deletePreviewImage()"
          >
            Удалить
          </CButton>
        </div>
      </div>

      <p
        style="margin-top: 1rem;"
        class="modal__info"
      >
        Значения ползунка:
      </p>
      <CInput
        v-model="$v.min.$model"
        label="Минимальное"
        horizontal
        :is-valid="$v.min.$dirty ? !$v.min.$error : null"
        invalid-feedback="Введите минимум"
      />
      <CInput
        v-model="$v.max.$model"
        label="Максимальное"
        horizontal
        :is-valid="$v.max.$dirty ? !$v.max.$error : null"
        invalid-feedback="Введите максимум"
      />
      <CInputCheckbox
        :label="`Обязательное комментирование выбранного диапазона`"
        name="stop_tasks"
        style="margin-bottom: 10px; margin-left: 5px;"
        :checked.sync="isRangeCommentRequired"
      />
      <div v-if="isRangeCommentRequired">
        <CInput
          v-model="$v.limits.min.$model"
          label="Минимальное"
          horizontal
          :is-valid="$v.limits.min.$dirty ? !$v.limits.min.$error : null"
          invalid-feedback="Введите минимум"
        />
        <CInput
          v-model="$v.limits.max.$model"
          label="Максимальное"
          horizontal
          :is-valid="$v.limits.max.$dirty ? !$v.limits.max.$error : null"
          invalid-feedback="Введите максимум"
        />
      </div>
      <CInputCheckbox
        :label="`Перейти к вопросу при выборе этого диапазона`"
        name="stop_tasks"
        style="margin-bottom: 10px; margin-left: 5px;"
        :checked.sync="$v.isParentAnswer.$model"
      />
      <div v-if="$v.isParentAnswer.$model">
        <CInput
          v-model="$v.nestedQuestionLimits.min.$model"
          label="Минимальное"
          horizontal
          :is-valid="$v.nestedQuestionLimits.min.$dirty ? !$v.nestedQuestionLimits.min.$error : null"
          invalid-feedback="Введите минимум"
        />
        <CInput
          v-model="$v.nestedQuestionLimits.max.$model"
          label="Максимальное"
          horizontal
          :is-valid="$v.nestedQuestionLimits.max.$dirty ? !$v.nestedQuestionLimits.max.$error : null"
          invalid-feedback="Введите максимум"
        />
        <CSelect
          :value.sync="$v.nestedQuestion.$model"
          :options="questionListItems[questionId]['optionList']"
          label="Вопросы"
          :disabled="!questionListItems[questionId]['optionList'] || questionListItems[questionId]['optionList'].length <= 1"
        />
      </div>
      <CButton
        color="danger"
        class="float-right"
        @click="$emit('remove-question')"
      >
        Удалить вопрос
      </CButton>
      <div class="clearfix" />
    </div>
  </div>
</template>

<script>
import { required } from 'vuelidate/lib/validators'
import TaskImages from '../TaskImages'

export default {
  name: 'TasksCreateSliderQuestion',
  components: {
    TaskImages
  },
  props: {
    index: Number,
    questionId: Number,
    questionData: {
      type: String,
      default: null
    },
    values: {
      type: Object,
      default: null
    },
    file: {
      type: String,
      default: null
    },
    comment_required: {
      type: Number,
      default: null
    },
    group_name: Boolean,
    question_list: Array
  },
  data() {
    return {
      errorMessage: null,
      selectedFile: null,
      previewImageUrl: null,
      image: null,
      question: '',
      min: 0,
      max: 10,
      isParentAnswer: false,
      nestedQuestion: null,
      nestedQuestionLimits: {
        min: 0,
        max: 10
      },
      isRangeCommentRequired: false,
      limits: {
        min: 0,
        max: 10
      },
      isCommentRequired: false,
      questionListItems: {},
      componentTypeList: ['ChooseOne', 'CloseQuestion', 'ChooseMany', 'TextQuestion', 'SliderQuestion', 'File', 'ManyInManyQuestion']
    }
  },
  computed: {
    getFileType() {
      if (this.image) {
        const fileType = this.image.type

        if (fileType.includes('image')) {
          return  'img'
        } else if (fileType.includes('video')) {
          return 'video'
        } else {
          console.log('Это неизвестный тип файла')
        }
      }
    },
    fileExtension() {
      if (this.selectedFile) {
        const fileExtension = this.selectedFile.split('.').pop()

        if (fileExtension === 'jpg' || fileExtension === 'jpeg' || fileExtension === 'png') {
          return  'img'
        } else if (fileExtension === 'mp4' || fileExtension === 'avi' || fileExtension === 'mov') {
          return 'video'
        } else {
          console.log('Это неизвестный тип файла')
        }
      }
    }
  },
  beforeMount() {
    this.selectedFile = this.file

    if (this.questionData) {
      this.question = this.questionData
    }
    if (this.values) {
      this.min = this.values.min || 0
      this.max = this.values.max || 10
      this.isParentAnswer = this.values.isParentAnswer || false
      this.nestedQuestion = this.values.nestedQuestion || null
      this.isRangeCommentRequired = this.values.comment || false
      this.limits.min = this.values.limit_min || 0
      this.limits.max = this.values.limit_max || 10
      this.nestedQuestionLimits.min = this.values.question_limit_min || 0
      this.nestedQuestionLimits.max = this.values.question_limit_max || 10
    }
    if (this.comment_required) {
      this.isCommentRequired = !!this.comment_required
    }
    this.manageOptions()
  },
  methods: {
    stripHtml(htmlString) {
      return new DOMParser().parseFromString(htmlString, 'text/html').body.textContent || ''
    },
    manageOptions() {
      this.question_list.map((questionItem, elementKey) => {
        if (!questionItem.group_name) {
          Object.assign(this.questionListItems, { [questionItem.id]: { blackList: [], optionList: [] } })

          this.questionListItems[questionItem.id]['blackList'].push(questionItem.id)

          this.question_list.map((question, optionKey) => {
            let switchArg = question.component ?? null
            if (this.componentTypeList.includes(question.type)) {
              switchArg = question.type
            }

            if (optionKey < elementKey) {
              this.questionListItems[questionItem.id]['blackList'].push(question.id)
            }
            if (!question.group_name) {
              switch (switchArg) {
              case 'ChooseOne' :
              case 'ChooseMany' :
              case 'CloseQuestion' :
                if (question.values) {
                  question.values.map((item) => {
                    if (item.isParentAnswer) {
                      this.questionListItems[questionItem.id]['blackList'].push(question.id)
                    }
                  })
                }
                if (!this.questionListItems[questionItem.id]['blackList'].includes(question.id)) {
                  this.questionListItems[questionItem.id]['optionList'].push({
                    value: question.id,
                    label: question.question ? this.stripHtml(question.question) : `Новый вопрос - ${optionKey}`
                  })
                }
                break
              case 'ManyInManyQuestion' :
                this.questionListItems[questionItem.id]['optionList'].push({
                  value: question.id,
                  label: question.title ? this.stripHtml(question.title) : `Новый вопрос - ${optionKey}`
                })
                break
              case 'SliderQuestion' :
                if (question.values && question.values.isParentAnswer) {
                  this.questionListItems[questionItem.id]['blackList'].push(question.id)
                }
                if (!this.questionListItems[questionItem.id]['blackList'].includes(question.id)) {
                  this.questionListItems[questionItem.id]['optionList'].push({
                    value: question.id,
                    label: question.question ? this.stripHtml(question.question) : `Новый вопрос - ${optionKey}`
                  })
                }
                break
              default:
                this.questionListItems[questionItem.id]['optionList'].push({
                  value: question.id,
                  label: question.question ? this.stripHtml(question.question) : `Новый вопрос - ${optionKey}`
                })
              }
            }
          })
        }
      })

      this.setDefaultOptionValues()

      return this.questionListItems
    },
    setDefaultOptionValues() {
      this.question_list.map((item) => {
        this.questionListItems[item.id]['optionList'].unshift({
          value: null,
          label: 'Выберите вопрос'
        })
      })
    },
    deleteImage() {
      this.selectedFile = null
      this.image = null
    },
    deletePreviewImage() {
      this.previewImageUrl = null
    },
    onChangeImage(files) {
      const maxFileSize = 1.5 * 1024 * 1024 * 1024

      const file = files[0]
      if (file.size > maxFileSize) {
        this.errorMessage = 'Файл слишком большой. Максимальный размер: 1.5 ГБ'

        return
      }

      this.image = file
      this.previewImageUrl = URL.createObjectURL(this.image)
      this.selectedFile = null
      this.errorMessage = null

      const formData = new FormData()
      formData.append('image', this.image)
    },
    getData() {
      return {
        index: this.index,
        type: 'range',
        question: this.question,
        values: {
          min: +this.min,
          max: +this.max,
          comment: this.isRangeCommentRequired,
          limit_min: +this.limits.min,
          limit_max: +this.limits.max,
          question_limit_min: +this.nestedQuestionLimits.min,
          question_limit_max: +this.nestedQuestionLimits.max,
          isParentAnswer: this.isParentAnswer,
          nestedQuestion: this.nestedQuestion
        },
        comment_required: this.isCommentRequired,
        id: this.questionId,
        images: this.image ? this.image : this.selectedFile
      }
    }
  },
  validations: {
    question: {
      required
    },
    limits: {
      min: {
        required
      },
      max: {
        required
      }
    },
    nestedQuestionLimits: {
      min: {
        required
      },
      max: {
        required
      }
    },
    min: {
      required
    },
    max: {
      required
    },
    isParentAnswer: {},
    nestedQuestion: {}
  }
}
</script>
