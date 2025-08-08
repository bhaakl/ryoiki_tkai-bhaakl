<template>
  <div class="card">
    <div class="card-header d-flex flex-row justify-content-between">
      <p class="font-weight-bold">
        Вопрос {{ index }}<span> (с несколькими вариантами ответа)</span>
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

      <p style="margin-top: 1rem;">
        Варианты ответов:
      </p>
      <draggable
        v-model="$v.answers.$model"
        handle=".answer-drag-btn"
      >
        <template v-for="(answer, answerKey) in $v.answers.$each.$iter">
          <CInput
            :key="answer.id.$model"
            v-model="answer.value.$model"
            placeholder="Вариант"
            :prepend="`${Number.parseInt(answerKey) + 1}.`"
            :is-valid="answer.value.$dirty ? !answer.value.$error : null"
            invalid-feedback="Введите вариант"
          >
            <!-- <template #append>
                     <CButton color="light" class="answer-drag-btn">
                        <CIcon size="sm" name="cil-line-spacing"/>
                     </CButton>
                  </template> -->
          </CInput>
          <CInputCheckbox
            :label="`Открыть окно сообщения об ошибке`"
            name="stop_tasks"
            style="margin-bottom: 10px; margin-top: -8px; margin-left: 5px;"
            :checked.sync="answer.error.$model"
          />
          <CInputCheckbox
            :label="`Обязательное комментирование данного ответа`"
            name="stop_tasks"
            style="margin-bottom: 10px; margin-top: -8px; margin-left: 5px;"
            :checked.sync="answer.comment.$model"
          />
          <CInputCheckbox
            :label="`Перейти к вопросу при выборе этого ответа`"
            name="stop_tasks"
            style="margin-bottom: 10px; margin-top: -8px; margin-left: 5px;"
            :checked.sync="answer.isParentAnswer.$model"
            @change="!answer.isParentAnswer.$model ? answer.nestedQuestion.$model = null : ''"
          />
          <CSelect
            v-if="answer.isParentAnswer.$model"
            :value.sync="answer.nestedQuestion.$model"
            :options="questionListItems[questionId]['optionList']"
            label="Вопросы"
            :disabled="!questionListItems[questionId]['optionList'] || questionListItems[questionId]['optionList'].length <= 1"
          />
        </template>
      </draggable>
      <CButton
        color="light"
        @click="addAnswer"
      >
        Добавить вариант ответа
      </CButton>
      <CButton
        color="danger"
        class="float-right"
        @click="$emit('remove-question')"
      >
        Удалить вопрос
      </CButton>
    </div>
  </div>
</template>

<script>
import { required } from 'vuelidate/lib/validators'
import Draggable from 'vuedraggable'
import TaskImages from '../TaskImages'

export default {
  name: 'TasksCreateChooseMany',
  components: {
    Draggable,
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
      type: Array,
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
      answers: [
        { id: 1, value: '', error: false, comment: false, isParentAnswer: false, nestedQuestion: null },
        { id: 2, value: '', error: false, comment: false, isParentAnswer: false, nestedQuestion: null }
      ],
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
  watch: {
    answers: {
      handler: function() {
        this.manageOptions()
      }, deep: true
    }
  },
  beforeMount() {
    this.selectedFile = this.file

    if (this.questionData) {
      this.question = this.questionData
    }
    if (this.values) {
      this.answers.map((item, index) => {
        const newItem = this.values[index] || {}

        for (const key in item) {
          if (Object.prototype.hasOwnProperty.call(newItem, key)) {
            item[key] = newItem[key]
          }
        }

        return item
      })
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

          if (this.questionId === questionItem.id && questionItem.values) {
            questionItem.values = this.answers
          }

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
                if (question.values.isParentAnswer) {
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
    addAnswer() {
      this.answers.push({ id: this.answers.length + 1, value: '', error: false, comment: false, isParentAnswer: false, nestedQuestion: null })
    },
    getData() {
      return {
        index: this.index,
        type: 'checkbox',
        question: this.question,
        values: this.answers,
        comment_required: this.isCommentRequired ? 1 : 0,
        id: this.questionId,
        images: this.image ? this.image : this.selectedFile
      }
    }
  },
  validations: {
    question: {
      required
    },
    answers: {
      $each: {
        id: {},
        value: {
          required
        },
        error: {},
        comment: {},
        isParentAnswer: {},
        nestedQuestion: {}
      }
    }
  }
}
</script>
