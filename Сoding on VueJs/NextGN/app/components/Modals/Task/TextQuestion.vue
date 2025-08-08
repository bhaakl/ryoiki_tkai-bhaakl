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
        <img :src="`/storage/images/${question.file}`">
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
    <p
      :class="{ 'not_active': close }"
      class="modal__info"
    >
      Ваш ответ
    </p>
    <div class="input-container input-container--no-margin">
      <textarea
        v-model="$v.answer.$model"
        :disabled="close"
        :class="{ invalid__border: invalid }"
      />
      <p
        v-if="invalid && !$v.answer.required"
        class="invalid__text"
      >
        Обязательно к заполнению
      </p>
    </div>
  </div>
</template>

<script>
import { required, requiredIf } from 'vuelidate/lib/validators'

export default {
  name: 'ModalTaskTextQuestion',
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
      answer: ''
    }
  },
  computed: {
    invalid() {
      return this.$v.answer.$dirty && this.$v.answer.$invalid
    }
  },
  watch: {
    close() {
      this.answer = ''
    }
  },
  beforeMount() {
    if (this.question && this.question.answers) {
      let item = this.question.answers.find((item) => item.task_question_id === this.question.id)
      this.answer = item ? item.answer : ''
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
    getData() {
      return {
        task_question_id: this.question.id,
        answer: this.answer,
        close: this.close
      }
    }
  },
  validations: {
    answer: {
      required: requiredIf(function() {
        return !this.close
      })
    }
  }
}
</script>

<style scoped>
.invalid__text {
   color: #ff0032;
   font-size: 0.8rem;
}

.invalid__border {
   border-color: #ff0032;
}
</style>
