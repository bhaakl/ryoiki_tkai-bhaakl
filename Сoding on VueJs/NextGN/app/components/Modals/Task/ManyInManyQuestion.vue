<template>
  <div
    class="task-container mb-2"
    :style="`display:${(question.isNested && !question.isVisible) ? 'none' : 'block'}`"
  >
    <p
      :class="{ 'not_active': close }"
      class="modal__sub-title modal__sub-title--no-padding"
    >
      Кейс {{ index }}
    </p>
    <p v-if="question.isNested">
      {{ question.triggerTitle }}
    </p>
    <p
      v-if="question.title"
      :class="{ 'not_active': close }"
      class="modal__info modal__info--line-break ql-editor"
      v-html="question.title"
    />
    <div class="scrollable">
      <table>
        <thead>
          <tr>
            <th class="split-head-cell">
              <div>Ответы</div>
              <hr>
              <div>Вопросы</div>
            </th>
            <th
              v-for="answer in question.values"
              :key="answer.id"
            >
              <span>{{ answer.value }}</span>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(q, index) in question.question"
            :key="index"
          >
            <td>
              <span
                class=""
                v-html="q.value"
              />
            </td>
            <td
              v-for="answer in q.values"
              :key="answer.id"
            >
              <div class="input-checkbox">
                <label>
                  <input
                    :id="'choose-many-' + q.id + '-' + answer.id"
                    ref="input"
                    v-model="selectedAnswers[q.id] && selectedAnswers[q.id][answer.id]"
                    type="checkbox"
                    :value="answer.id"
                    :disabled="answer.disabled || close"
                    @change="handleCheckboxChange($event, q, q.id, answer)"
                  >
                  <div
                    class="checkbox"
                    :disabled="answer.disabled"
                  />
                </label>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <Input
      ref="comment"
      v-model="comment"
      title="Комментарий"
      :is-required="(question.comment_required === 1 || selectedAnswerCommentRequired) && !close"
      no-margin-bottom
      :is-disabled="close"
    />
  </div>
</template>

<script>
import { required, requiredIf } from 'vuelidate/lib/validators'
import Input from '@app/elements/Input'

export default {
  name: 'ModalTaskManyInManyQuestion',
  components: {
    Input
  },
  props: {
    index: Number,
    close: {
      type: Boolean,
      default: false
    },
    question: Object
  },
  data() {
    return {
      selectedAnswers: {},
      selectedAnswersCommentRules: {},
      selectedAnswerCommentRequired: false,
      comment: null
    }
  },
  computed: {
    invalid() {
      return this.$v.selectedAnswers.$dirty && this.$v.selectedAnswers.$invalid
    }
  },
  watch: {
    comment() {
      if (this.comment.trim() === '') {
        this.comment = ''
      }
    },
    close() {
      this.selectedAnswers = {}
    }
  },
  created() {
    this.question.question.forEach(question => {
      question.values = this.question.values.map(answer => ({
        ...answer,
        disabled: false
      }))
    })
  },
  mounted() {
    for (const q of this.question.question) {
      const id = String(q.id)
      if (!this.selectedAnswers[id]) {
        this.$set(this.selectedAnswers, id, {})
      }
    }
  },
  methods: {
    handleCheckboxChange(e, question, questionId, answer) {
      if (this.selectedAnswersCommentRules[answer.id] !== undefined) {
        delete this.selectedAnswersCommentRules[answer.id]
      } else {
        this.selectedAnswersCommentRules[answer.id] = answer.comment
      }

      this.selectedAnswerCommentRequired = Object.values(this.selectedAnswersCommentRules).includes(true)

      let answerId = answer.id
      if (question.multipleAnswer == 'single') {
        for (const q of this.question.question) {
          if (q.id === questionId && q.values !== answerId) {
            for (const value of q.values) {
              if (value.id !== answerId) {
                value.disabled = e.target.checked
              }
            }
          }
        }
      }

      if (!this.selectedAnswers[questionId]) {
        this.$set(this.selectedAnswers, questionId, {})
      }

      const selectedQuestion = this.selectedAnswers[questionId]
      if (selectedQuestion[answerId]) {
        selectedQuestion[answerId] = this.question.values.find((answer) => answer.id === answerId)
      } else {
        delete selectedQuestion[answerId]
      }
    },

    onKeyDown(e, answerId) {
      if (e.keyCode === 13) {
        e.preventDefault()
        const ref = this.$refs.input.find(ref => ref.id === `choose-many-${answerId}`)
        ref.click()
      }
    },
    touchComment() {
      this.$refs.comment.$v.$touch()

      return !this.$refs.comment.$v.$invalid
    },
    getData() {
      return {
        task_question_id: this.question.id,
        answer: this.selectedAnswers,
        comment: this.comment,
        close: this.close
      }
    }
  },
  validations: {
    selectedAnswers: {
      required: requiredIf(function() {
        return !this.close
      })
    }
  }
}
</script>

<style lang="scss" scoped>
table {
   border-collapse: separate;
   border-spacing: 0;
   border-top: 1px solid grey;

   tr {
      td {
         text-align: center;
      }
   }

   .split-head-cell {
      div:first-child {
         display: flex;
         flex-direction: column;
         align-items: center;
         text-transform: uppercase;
         margin-left: 50px;
      }

      div:last-child {
         display: flex;
         flex-direction: column;
         align-items: center;
         text-transform: uppercase;
         margin-right: 50px;
      }

      div {
         padding: 0 5px;
         font-size: 10px;
      }

      padding: 0;

      hr {
         transform: rotate3d(1, 1, 1, 20deg);
      }
   }
}

th, td {
   padding: 14px;
   border: 1px solid black;
   white-space: normal;
   overflow: hidden;
   font-size: 12px;
   width: 150px;
   max-width: 150px;
   min-width: 150px;
}

th:first-child, td:first-child {
   position: sticky;
   left: 0;
   background-color: #f2f2f2;
   z-index: 1;
   font-size: 12px;
   white-space: normal;
   text-align: left;
}

.scrollable {
   overflow-x: auto;
   margin-top: 10px;
}

.input-checkbox {
   padding: 0;
   display: flex;
   justify-content: center;
   align-items: center;
   .checkbox[disabled] {
      background-color: #b6b6b6;
   }
}

</style>
