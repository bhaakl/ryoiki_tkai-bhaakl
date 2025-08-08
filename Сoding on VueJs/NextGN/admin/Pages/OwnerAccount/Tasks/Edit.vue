<template>
  <CRow class="justify-content-center">
    <CCol col="12">
      <CCard>
        <CCardHeader>
          <h3 class="mb-0">
            Изменить задание
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
                  :key="`${questionGroup.group}-${index}`"
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
                      :is="question.component"
                      v-for="(question) in questionGroup.questions"
                      v-if="!question.main"
                      :key="question.id"
                      ref="questions"
                      class="drag-component-item"
                      :group_name="hasGroup(questionGroup)"
                      :index="index + 1"
                      :question_list="questions"
                      :question-data="question.question || null"
                      :question-title="question.title || null"
                      :values="question.values || null"
                      :file="question.file || null"
                      :question-id="question.id || null"
                      :comment_required="question.comment_required || null"
                      :child="filteredItems || null"
                      :updatedOptions="updatedOptions"
                      @optionsManaged="updatedOptions = false"
                      @remove-question="removeQuestion(question.id)"
                      @duplicate-question="duplicateQuestion(question.id, questionGroup)"
                    />
                  </div>
                </div>
              </template>
            </draggable>
            <CButton
              color="info"
              @click="addQuestionModal = true"
            >
              Добавить вопрос
            </CButton>
            <div class="clearfix" />
            <CButton
              type="submit"
              color="success"
              class="mt-4"
            >
              Изменить задание
            </CButton>
            <CButton
              v-if="!task.is_archived"
              color="danger"
              class="float-right mt-4"
              @click="deleteModal = true"
            >
              Архивировать задание
            </CButton>
            <CButton
              v-else
              color="success"
              class="float-right mt-4"
              @click.prevent="restore"
            >
              Восстановить задание
            </CButton>
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
          name="isGroupingEnabled"
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
      :show.sync="editModal"
    >
      <template #header>
        <h6 class="modal-title">
          Изменить задание?
        </h6>
      </template>
      Вы уверены, что хотите изменить задание "{{ title }}"?
      <template #footer>
        <CButton
          color="success"
          @click="edit(true)"
        >
          Изменить
        </CButton>
        <CButton
          color="light"
          @click="edit(false)"
        >
          Отмена
        </CButton>
      </template>
    </CModal>
    <CModal
      color="danger"
      centered
      :show.sync="deleteModal"
    >
      <template #header>
        <h6 class="modal-title">
          Архивировать задание?
        </h6>
      </template>
      Вы уверены, что хотите архивировать задание "{{ title }}"?
      <template #footer>
        <CButton
          color="danger"
          @click="deleteTask(true)"
        >
          Да
        </CButton>
        <CButton
          color="light"
          @click="deleteTask(false)"
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
import CommonMethods from './Create.vue'

export default {
  name: 'TasksEdit',
  components: {
    ChooseOne,
    ChooseMany,
    TextQuestion,
    SliderQuestion,
    ScreenshotQuestion,
    CloseQuestion,
    ManyInManyQuestion,
    Draggable,
    CommonMethods
  },
  layout: Layout,
  props: {
    task: Object,
    products: Array
  },
  data() {
    return {
      groupedQuestions: [],
      isGroupingEnabled: false,
      title: '',
      isLocation: false,
      closeTask: false,
      questions: [],
      selectedProduct: null,
      productsOptions: [],
      editModal: false,
      deleteModal: false,
      addQuestionModal: false,
      updatedOptions: false
    }
  },
  computed: {
    filteredItems() {
      return this.questions.map(item => {
        return {
          ...item,
          type: 'ManyQuestion',
          component: undefined
        }
      }).filter(item => !!item.main)
    }
  },
  watch: {
    groupedQuestions(val) {
      console.log(val)
    }
  },
  mounted() {
    this.groupQuestions()
  },
  beforeMount() {
    this.title = this.task.title
    this.isLocation = this.task.is_location
    this.closeTask = this.task.is_close
    this.questions = this.task.questions.map(question => {
      question.component = {
        radio: 'ChooseOne',
        close: 'CloseQuestion',
        checkbox: 'ChooseMany',
        text: 'TextQuestion',
        range: 'SliderQuestion',
        file: 'ScreenshotQuestion',
        many: 'ManyInManyQuestion'
      }[question.type]
      question.values = JSON.parse(question.values)

      return question
    })
    this.productsOptions = this.products.map(({ id, name }) => {
      return {
        value: id,
        label: name
      }
    })
    this.selectedProduct = +this.productsOptions.find(option => option.value == this.task.product_id).value
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
    async restore() {
      await this.$inertia.post(`/admin/tasks/${this.task.id}/restore`, {
        preserveScroll: true
      })
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
    addQuestion(component = null) {
      this.addQuestionModal = false
      if (!component) return
      const id = this.questions.length ? this.questions[this.questions.length - 1].id + 1 : 1
      const index = this.questions.length + 1

      const question = { id, component, index }
      if (this.isGroupingEnabled) {
        this.questions.push({ ...question, group_name: component + '_' + id + '_' + index })
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
      this.updatedOptions = true
    },
    submit() {
      this.$v.$touch()
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
      this.editModal = true
    },
    async edit(modalValue) {
      this.editModal = false
      if (!modalValue) return
      let formattedQuestions = this.$refs.questions?.map(question => question.getData())

      if (formattedQuestions) {
        this.questions.forEach((item, index) => {
          const value = formattedQuestions.find((val) => item.id === val.id)
          if (value) {
            value.group_name = item.group_name
            value.index = index + 1
          }
        })
      }
      console.log(formattedQuestions, 'formattedQuestions')
      try {
        await this.$inertia.post(`/admin/tasks/${this.task.id}/edit`, {
          product_id: +this.selectedProduct,
          title: this.title,
          is_location: this.isLocation,
          is_close: this.closeTask,
          questions: formattedQuestions
        })
      } catch (e) {
        this.$handleError(e)
      }
    },
    async deleteTask(modalValue) {
      this.deleteModal = false
      if (!modalValue) return
      try {
        await this.$inertia.delete(`/admin/tasks/${this.task.id}`)
      } catch (e) {
        this.$handleError(e)
      }
    }
  },
  validations: {
    title: {
      required
    }
  }
}
</script>
