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
          style="max-width: 100%;max-height: 400px;margin: 0 auto;display: block;"
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
            class="input-radio"
            tabindex="0"
            @keydown="e => onKeyDown(e, answer.id)"
          >
            <label>
              <input
                :id="`choose-one-${answer.id}`"
                ref="input"
                v-model="$v.selectedAnswer.$model"
                :value="answer.id"
                type="radio"
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
        v-if="invalid && !$v.selectedAnswer.required"
        class="invalid__text"
        style="margin-top: 0.6rem"
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
  name: 'ModalTaskChooseOne',
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
      selectedAnswer: null,
      selectedAnswerCommentRequired: false,
      comment: null
    }
  },
  computed: {
    invalid() {
      return this.$v.selectedAnswer.$dirty && this.$v.selectedAnswer.$invalid
    }
  },
  watch: {
    comment() {
      if (this.comment.trim() === '') {
        this.comment = ''
      }
    },
    close() {
      this.selectedAnswer = {}
    },
    question: {
      handler: function(newValue, oldValue) {
        if (newValue.selectedAnswer !== oldValue.selectedAnswer) {
          this.selectedAnswer = newValue.selectedAnswer
        }
      },
      deep: true
    }
  },
  beforeMount() {
    if (this.question && this.question.answers) {
      let item = this.question.answers.find((item) => item.task_question_id === this.question.id)
      this.selectedAnswer = item ? item.answer : null
      this.comment = item ? item.comment : null
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
        for (let nestedQuestion in question.nestedQuestions) {
          if (question.nestedQuestions[nestedQuestion] !== question.nestedQuestions[answer.id]) {
            this.$emit('get-answer', {
              questionId: question.nestedQuestions[nestedQuestion],
              isVisible: false,
              sorting: question.sorting,
              case: null
            })
          }
        }
        if (answer.isParentAnswer) {
          this.$emit('get-answer', {
            questionId: question.nestedQuestions[answer.id],
            isVisible: true,
            sorting: question.sorting,
            case: this.index
          })
        } else {
          this.$emit('get-answer', {
            questionId: question.nestedQuestions[answer.id],
            isVisible: false,
            sorting: question.sorting,
            case: null
          })
        }
      }
      this.selectedAnswerCommentRequired = answer.comment
      if (answer.error) {

        this.closeModalTask()

        let data = {
          task_id: this.task.id,
          questionId: question.id
        }

        this.$emit('error-report', data)
      }
      this.$emit('updateSelectedAnswer', { questionId: question.id, selectedAnswer: this.selectedAnswer })
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
        const ref = this.$refs.input.find(ref => ref.id === `choose-one-${answerId}`)
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
        answer: this.selectedAnswer,
        comment: this.comment,
        close: this.close
      }
    }
  },
  validations: {
    selectedAnswer: {
      required: requiredIf(function() {
        return !this.close
      })
    }
  }
}
</script>

<style>
.not_active {
   color: #b3b3b3;
}
</style>
