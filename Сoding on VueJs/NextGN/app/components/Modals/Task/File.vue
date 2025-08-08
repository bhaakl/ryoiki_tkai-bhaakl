<template>
  <div class="task-container mb-2"
       :style="`display:${question.isNested && !question.isVisible ? 'none' : 'block'}`"
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
      :class="{ 'not_active': close }"
      class="modal__info modal__info--line-break ql-editor"
      v-html="question.question"
    />
    <file-input
      v-model="files"
      :is-disabled="close"
      :max-items="3"
      :max-file-size="10"
      :formats="['image/jpeg', 'image/png']"
      show-list
    />
    <p
      v-if="invalid && !$v.files.required"
      class="invalid__text"
      style="margin-top: 0.6rem;"
    >
      Обязательно к заполнению
    </p>
    <Input
      ref="comment"
      v-model="comment"
      title="Комментарий"
      :is-required="question.comment_required === 1 && !close"
      no-margin-bottom
      :disabled="close"
      :is-disabled="close"
    />
  </div>
</template>

<script>
import { required, requiredIf } from 'vuelidate/lib/validators'
import FileInput from '@app/elements/FileInput'
import Input from '@app/elements/Input'

export default {
  name: 'ModalTaskFile',
  components: {
    FileInput,
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
      files: [],
      comment: null
    }
  },
  computed: {
    invalid() {
      return this.$v.files.$dirty && this.$v.files.$invalid
    }
  },
  watch: {
    comment() {
      if (this.comment.trim() === '') {
        this.comment = ''
      }
    },
    close() {
      this.files = []
    }
  },
  methods: {
    touchComment() {
      this.$refs.comment.$v.$touch()

      return !this.$refs.comment.$v.$invalid
    },
    getFiles(files) {
      this.files = files
    },
    getData() {
      return {
        task_question_id: this.question.id,
        answer: this.files.length ? this.files : null,
        comment: this.comment
      }
    }
  },
  validations: {
    files: {
      required: requiredIf(function() {
        return !this.close
      })
    }
  }
}
</script>
