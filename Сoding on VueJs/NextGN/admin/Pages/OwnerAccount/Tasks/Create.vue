<template>
  <CRow class="justify-content-center">
    <CCol col="12">
      <CCard>
        <CCardHeader>
          <h3 class="mb-0">
            Добавить задание
          </h3>
        </CCardHeader>
        <CCardBody>
          <CForm @submit.prevent="submit">
            <CSelect
              :value.sync="selectedProduct"
              :options="productsOptions"
              label="Продукт"
              horizontal
            />
            <CInput
              v-model="$v.title.$model"
              label="Название"
              horizontal
              :is-valid="$v.title.$dirty ? !$v.title.$error : null"
              invalid-feedback="Введите название"
            />
            <CInputCheckbox
              label="Запрашивать локацию тестировщика"
              :checked.sync="isLocation"
              name="comment_required"
              style="margin: 1rem 0;"
            />
            <CInputCheckbox
              label="Закрытое задание"
              :checked.sync="closeTask"
              name="close_task"
              style="margin: 1rem 0;"
            />
            <draggable
              v-model="groupedQuestions"
              handle=".question-drag-btn"
              draggable=".drag-group-item"
            >
              <template v-for="(questionGroup, index) in groupedQuestions">
                <div

                  :key="`${questionGroup.group_name}-${index}`"
                  :class="{card: hasGroup(questionGroup)}"
                  class="drag-group-item"
                >
                  <div
                    v-if="hasGroup(questionGroup)"
                    class="card-header d-flex justify-content-between"
                  >
                    <h4>Группа</h4>
                    <CButton
                      color="light"
                      size="sm"
                      class="question-drag-btn"
                    >
                      <CIcon
                        size="sm"
                        name="cil-line-spacing"
                      />
                    </CButton>
                  </div>
                  <div :class="{'card-body': hasGroup(questionGroup) }">
                    <component
                      :is="question.type"
                      v-for="(question, subIndex) in questionGroup.questions"
                      :key="question.id"
                      ref="questions"
                      :group_name="hasGroup(questionGroup)"
                      :question-id="question.id || null"
                      class="drag-component-item"
                      :index="subIndex + 1"
                      :question_list="questions"
                      @remove-question="removeQuestion(question.id)"
                      @duplicate-question="duplicateQuestion(question.id, questionGroup)"
                    />
                  </div>
                </div>
              </template>
            </draggable>
            <CCardFooter>
              <CButton
                color="info"
                @click="addQuestionModal = true"
              >
                Добавить вопрос
              </CButton>
              <CButton
                color="success"
                @click="submit('newTask')"
              >
                Создать задание
              </CButton>
              <CButton
                color="info"
                @click="submit('import')"
              >
                Импорт заданий
              </CButton>
              <CButton
                color="danger"
                class="float-right"
                @click="$inertia.visit('/admin/tasks')"
              >
                Отмена
              </CButton>
            </CCardFooter>
          </CForm>
        </CCardBody>
      </CCard>
    </CCol>
    <CModal
      color="info"
      centered
      :show.sync="addQuestionModal"
    >
      <template #header>
        <h6 class="modal-title">
          Добавить вопрос?
        </h6>
        <CInputCheckbox
          label="Добавить в группу"
          :checked.sync="isGroupingEnabled"
          name="group_name"
        />
      </template>
      Выберите тип вопроса:
      <template #footer-wrapper>
        <footer class="modal-footer justify-content-center">
          <CButtonGroup
            vertical
            style="width: 80%"
          >
            <CButton
              color="light"
              @click="addQuestion('ChooseOne')"
            >
              С одним вариантом ответа
            </CButton>
            <CButton
              color="light"
              @click="addQuestion('ChooseMany')"
            >
              С несколькими вариантами ответа
            </CButton>
            <CButton
              color="light"
              @click="addQuestion('TextQuestion')"
            >
              С текстовым ответом
            </CButton>
            <CButton
              color="light"
              @click="addQuestion('SliderQuestion')"
            >
              С ползунком
            </CButton>
            <CButton
              color="light"
              @click="addQuestion('ScreenshotQuestion')"
            >
              Со скриншотом
            </CButton>
            <CButton
              color="light"
              @click="addQuestion('CloseQuestion')"
            >
              Закрывающий вопрос
            </CButton>
            <CButton
              color="light"
              @click="addQuestion('ManyInManyQuestion')"
            >
              Множество вопросов с одинаковыми ответами
            </CButton>
            <CButton
              color="secondary"
              @click="addQuestion()"
            >
              Отмена
            </CButton>
          </CButtonGroup>
        </footer>
      </template>
    </CModal>
    <CModal
      color="success"
      centered
      :show.sync="addModal"
    >
      <template #header>
        <h6 class="modal-title">
          Добавить задание?
        </h6>
      </template>
      Вы уверены, что хотите добавить задание "{{ title }}"?
      <template #footer>
        <CButton
          color="success"
          @click="add(true)"
        >
          Добавить
        </CButton>
        <CButton
          color="light"
          @click="add(false)"
        >
          Отмена
        </CButton>
      </template>
    </CModal>
    <CModal
      color="success"
      centered
      :show.sync="addModalImportTask"
    >
      <template #header>
        <h6 class="modal-title">
          Импорт вопросов к заданию
        </h6>
      </template>
      <p>
        Файл должен быть формата xlsx или xls, строго соответствовать шаблону, скачать его можно по ссылке
        <a href="/storage/import.xlsx">Шаблон импорта заданий</a>
      </p>
      <CInputFile
        label="Файлы"
        class="mb-1"
        horizontal
        accept=".xlsx,.xls"
        @change="addImage"
      />
      <p
        v-if="!title"
        style="color: red; font-size: 12px"
      >
        Чтоб продолжить импорт введите название Задания
      </p>
      <template #footer>
        <CButton
          v-if="ifImport && newVersion.path"
          color="success"
          @click="add(true, false)"
        >
          Загрузить
        </CButton>
        <CButton
          color="light"
          @click="add(false)"
        >
          Отмена
        </CButton>
      </template>
    </CModal>
  </CRow>
</template>

<script>
import { required } from 'vuelidate/lib/validators'
import Layout from '@admin/containers/TheContainer'
import ChooseOne from './components/ChooseOne'
import ChooseMany from './components/ChooseMany'
import TextQuestion from './components/TextQuestion'
import SliderQuestion from './components/SliderQuestion'
import ScreenshotQuestion from './components/ScreenshotQuestion'
import CloseQuestion from './components/CloseQuestion'
import ManyInManyQuestion from './components/ManyInManyQuestion'
import Draggable from 'vuedraggable'

export default {
  name: 'TasksCreate',
  components: {
    ChooseOne,
    ChooseMany,
    TextQuestion,
    SliderQuestion,
    ScreenshotQuestion,
    CloseQuestion,
    ManyInManyQuestion,
    Draggable
  },
  layout: Layout,
  props: {
    products: Array
  },
  data() {
    return {
      isGroupingEnabled: false,
      isLocation: false,
      closeTask: false,
      title: '',
      questions: [],
      groupedQuestions: [],
      selectedProduct: null,
      productsOptions: [],
      addModal: false,
      addQuestionModal: false,
      addModalImportTask: false,
      newVersion: {
        path: []
      }
    }
  },
  computed: {
    ifImport() {
      return this.title && Array.isArray(this.newVersion.path) && this.newVersion.path.length > 0
    }
  },
  watch: {
    groupedQuestions(v) {
      console.log(v)
    }
  },
  mounted() {
    this.groupQuestions()
  },
  beforeMount() {
    this.productsOptions = this.products.map(({ id, name }) => {
      return {
        value: id,
        label: name
      }
    })
    this.selectedProduct = +this.productsOptions[0].value
    const urlParams = new URLSearchParams(this.$window.location.search)
    if (urlParams.has('product_id')) {
      this.fromProductId = +urlParams.get('product_id')
      this.selectedProduct = +urlParams.get('product_id')
    }
  },
  methods: {
    groupQuestions() {
      const grouped = []

      this.questions.forEach(question => {
        if (question.group_name) {
          let existingGroup = grouped.find(group => group.group === question.group_name)

          if (!existingGroup) {
            existingGroup = {
              group: question.group_name,
              questions: []
            }
            grouped.push(existingGroup)
          }
          existingGroup.questions.push(question)
        } else {
          grouped.push({
            group: null,
            questions: [question]
          })
        }
      })
      this.groupedQuestions = grouped
    },
    hasGroup(item) {
      return !!item.group
    },
    duplicateQuestion(questionId, questionGroup) {
      const questionToDuplicate = this.questions.find(question => question.id === questionId)
      if (questionToDuplicate) {
        const newQuestion = { ...questionToDuplicate }
        newQuestion.id = this.questions.length + 1
        newQuestion.index = this.questions.length + 1

        this.questions.push(newQuestion)
        questionGroup.questions.push(newQuestion)

        this.$nextTick(() => {
          let currentComponentQuestion = this.$refs.questions.find((question) => question.questionId === questionToDuplicate.id)
          let lastComponentQuestion = this.$refs.questions.find((question) => question.questionId === newQuestion.id)

          Object.assign(lastComponentQuestion, {
            question: currentComponentQuestion.question,
            answers: currentComponentQuestion.answers,
            file: currentComponentQuestion?.file,
            comment_required: currentComponentQuestion?.comment_required,
            values: currentComponentQuestion?.values
          })

        })
      }
    },
    addQuestion(type = null) {
      this.addQuestionModal = false
      if (!type) return
      const id = this.questions.length ? this.questions[this.questions.length - 1].id + 1 : 1
      const index = this.questions.length + 1
      const question = { id, type, index }
      if (this.isGroupingEnabled) {
        this.questions.push({ ...question, group_name: type + '_' + id + '_' + index })
      } else {
        this.questions.push({ ...question, group_name: null })
      }
      this.groupQuestions()
      this.isGroupingEnabled = false
    },
    removeQuestion(id) {
      this.questions = this.questions.filter(question => question.id !== id)
      this.questions.forEach((question, index) => (question.index = index + 1))
      this.groupQuestions()
    },
    submit($type) {
      this.$v.$touch()

      if ($type == 'import') {
        this.addModalImportTask = true
      }

      if (this.$refs.questions) {
        this.$refs.questions.forEach(question => question.$v.$touch())
      }
      if (
        this.$v.$invalid ||
        (this.$refs.questions && this.$refs.questions.some(question => question.$v.$invalid))
      ) {
        this.$window.scrollTo({
          top: 0,
          behavior: 'smooth'
        })

        return
      }
      this.addModal = true
    },
    async add(modalValue, onlyTask = true) {
      this.addModal = false
      this.addModalImportTask = false
      if (!modalValue) return

      let formattedQuestions = onlyTask ? this.$refs.questions?.map(question => question.getData()) : null

      if (formattedQuestions) {
        this.questions.forEach((item, index) => {
          const value = formattedQuestions.find((val) => item.id === val.id)
          if (value) {
            value.group_name = item.group_name
            value.index = index + 1
          }
        })
      }

      try {
        await this.$inertia.post('/admin/tasks', {
          product_id: +this.selectedProduct,
          title: this.title,
          is_location: this.isLocation,
          is_close: this.closeTask,
          questions: formattedQuestions,
          import: this.newVersion.path
        })

      } catch (e) {
        this.$handleError(e)
      }
    },
    addImage(files) {
      this.newVersion.path = Array.from(files).slice(0, 3)
    },
    clearModal() {
      this.newVersion = {
        path: []
      }
      this.$v.$reset()
    }
  },
  validations: {
    title: {
      required
    }
  }
}
</script>
