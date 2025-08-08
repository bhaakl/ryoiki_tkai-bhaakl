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
    <div v-if="question.file">
      <div v-if="isImage(question.file)">
        <img
          :src="`/storage/images/${question.file}`"
          style="max-width: 100%;"
        >
      </div>
      <div v-else-if="isVideo(question.file)">
        <video
          controls
          style="width: 100%;height:300px;"
        >
          <source
            :src="`/storage/images/${question.file}`"
            type="video/mp4"
          >
        </video>
      </div>
    </div>
    <p
      :class="{ 'not_active': close }"
      class="modal__info modal__info--line-break ql-editor"
      v-html="question.question"
    />
    <div class="modal__task-fields">
      <div class="modal__task-grid">
        <div
          v-for="answer in question.values"
          :key="answer.id"
          class="modal__task-item"
        >
          <div
            class="input-checkbox"
            tabindex="0"
            @keydown="e => onKeyDown(e, answer.id)"
          >
            <label>
              <input
                :id="`choose-many-${answer.id}`"
                ref="input"
                v-model="$v.selectedAnswers.$model"
                :value="answer.id"
                type="checkbox"
                :disabled="close"
                @change="getEvent(answer, question)"
              >
              <div class="checkbox" />
              <span :style="{ color: close ? '#b3b3b3' : '' }">{{ answer.value }}</span>
            </label>
          </div>
        </div>
      </div>
      <p
        v-if="invalid && !$v.selectedAnswers.required"
        class="invalid__text"
        style="margin-top: 0.6rem;"
      >
        Обязательно к заполнению
      </p>
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
import { requiredIf } from 'vuelidate/lib/validators'
import Input from '@app/elements/Input'

export default {
  name: 'ModalTaskChooseMany',
  components: {
    Input
  },
  props: {
    index: Number,
    close: {
      type: Boolean,
      default: false
    },
    task: Object,
    question: Object
  },
  data() {
    return {
      selectedAnswers: [],
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
      this.selectedAnswers = []
    },
    question: {
      handler: function(newValue, oldValue) {
        if (newValue.selectedAnswer !== oldValue.selectedAnswer) {
          this.selectedAnswers = newValue.selectedAnswer ?? []
        }
      },
      deep: true
    }
  },
  methods: {
    closeModalTask() {
      this.$window.$.magnificPopup.close({
        items: {
          src: `#task-modal-${this.task.id}`
        }
      })

      setTimeout(()=>{
        this.$window.$.magnificPopup.open({
          items: {
            src: `#report-modal-${this.task.product_id}`

          },
          callbacks: {
            close: () => {
              this.reopenTask()
            }
          }
        })
      },500)

      this.goCloseButton()
    },
    reopenTask() {
      setTimeout(()=>{
        this.$window.$.magnificPopup.open({
          items: {
            src: `#task-modal-${this.task.id}`
          }
        })
      },500)

      this.goCloseButton()
    },
    getEvent(answer, question) {
      if (question.isTrigger) {
        if (this.selectedAnswers.includes(answer.id) && answer.isParentAnswer) {
          this.$emit('get-answer', {
            questionId: question.nestedQuestions[answer.id],
            isVisible: true,
            sorting: question.sorting,
            type: question.component,
            case: this.index
          })
        } else {
          this.$emit('get-answer', {
            questionId: question.nestedQuestions[answer.id],
            isVisible: false,
            sorting: question.sorting,
            type: question.component,
            case: null })
        }
      }

      if (this.selectedAnswersCommentRules[answer.id] !== undefined) {
        delete this.selectedAnswersCommentRules[answer.id]
      } else {
        this.selectedAnswersCommentRules[answer.id] = answer.comment
      }

      this.selectedAnswerCommentRequired = Object.values(this.selectedAnswersCommentRules).includes(true)

      if (answer.error && this.selectedAnswers.includes(answer.id)) {
        this.closeModalTask()

        let data = {
          task_id: this.task.id,
          questionId: question.id
        }

        this.$emit('error-report', data)
      }

      this.$emit('updateSelectedAnswer', { questionId: question.id, selectedAnswer: this.selectedAnswers })
    },
    goCloseButton() {
      setTimeout(()=>{
        const element = document.querySelector('.mfp-wrap.mfp-close-btn-in.mfp-auto-cursor.mfp-ready')
        if (element) {
          element.classList.remove('mfp-close-btn-in', 'mfp-auto-cursor')
          element.classList.add('mfp-zoom-in')
          element.classList.add('mfp-ready')
        }
      },600)
    },
    isImage(filename) {
      const extension = filename.split('.').pop().toLowerCase()

      return (extension === 'jpg' || extension === 'jpeg' || extension === 'png' || extension === 'gif')
    },
    isVideo(filename) {
      const extension = filename.split('.').pop().toLowerCase()

      return (extension === 'mp4' || extension === 'avi' || extension === 'mkv' || extension === 'mov')
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
        answer: this.selectedAnswers.length ? this.selectedAnswers : null,
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
