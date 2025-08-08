<template>
  <div class="task-container mb-2"
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
      <div class="input-container">
        <input
          ref="slider"
          type="range"
          data-type="single"
          :data-min="(question.values && question.values.min) || 0"
          :data-max="(question.values && question.values.max) || 10"
          :data-from="average"
          :disabled="close"
        >
      </div>
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
import Input from '@app/elements/Input'
import 'ion-rangeslider'

export default {
  name: 'ModalTaskSliderQuestion',
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
      comment: null,
      selectedAnswer: null,
      selectedAnswerCommentRequired: false,
      sliderInstance: null
    }
  },
  computed: {
    average() {
      let value = 5

      if (this.question.values && this.question.values.min !== undefined && this.question.values.max !== undefined) {
        value = Math.floor((this.question.values.min + this.question.values.max) / 2)
      }

      this.selectedAnswer = this.selectedAnswer ?? value
      this.selectedAnswerCommentRequired = this.checkCommentRule()

      return value
    }
  },
  watch: {
    question: {
      handler: function(newValue) {
        if (newValue.isVisible) {
          this.checkNestedRule()
        } else {
          this.$emit('get-answer', {
            questionId: this.question.values.nestedQuestion,
            isVisible: false,
            sorting: this.question.sorting,
            case: null
          })
        }
      },
      deep: true
    },
    comment() {
      if (this.comment.trim() === '') {
        this.comment = ''
      }
    }
  },
  mounted() {
    $(this.$refs.slider).ionRangeSlider({
      skin: 'round',
      onChange: (data) => {
        this.selectedAnswer = data.from
        this.selectedAnswerCommentRequired = this.checkCommentRule()
        this.checkNestedRule()
      }
    })
    this.sliderInstance = $(this.$refs.slider).data('ionRangeSlider')
  },
  destroyed() {
    if (this.sliderInstance) {
      this.sliderInstance.destroy()
    }
  },
  methods: {
    isImage(filename) {
      const extension = filename.split('.').pop().toLowerCase()

      return (extension === 'jpg' || extension === 'jpeg' || extension === 'png' || extension === 'gif')
    },
    isVideo(filename) {
      const extension = filename.split('.').pop().toLowerCase()

      return (extension === 'mp4' || extension === 'avi' || extension === 'mkv' || extension === 'mov')
    },
    touchComment() {
      this.$refs.comment.$v.$touch()

      return !this.$refs.comment.$v.$invalid
    },
    getData() {
      return {
        task_question_id: this.question.id,
        answer: +this.$refs.slider.value,
        comment: this.comment,
        close: this.close
      }
    },

    checkCommentRule() {
      if (this.question.values.comment) {
        return this.selectedAnswer >= this.question.values.limit_min && this.selectedAnswer <= this.question.values.limit_max
      }

      return false
    },

    checkNestedRule() {
      if (this.question.isTrigger) {
        if (this.question.values.isParentAnswer) {
          if (this.selectedAnswer >= this.question.values.question_limit_min && this.selectedAnswer <= this.question.values.question_limit_max) {
            this.$emit('get-answer', {
              questionId: this.question.values.nestedQuestion,
              isVisible: true,
              sorting: this.question.sorting,
              case: this.index
            })
          } else {
            this.$emit('get-answer', {
              questionId: this.question.values.nestedQuestion,
              isVisible: false,
              sorting: this.question.sorting,
              case: null
            })
          }

        }
      }
    }
  },
  validations: {}
}
</script>
