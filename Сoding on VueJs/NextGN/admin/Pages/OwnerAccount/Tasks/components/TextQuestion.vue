<template>
  <div class="card">
    <div class="card-header d-flex flex-row justify-content-between">
      <p class="font-weight-bold">
        Вопрос {{ index }}<span> (с текстовым ответом)</span>
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
        />
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
        <div class="buttons ml-2">
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
        <div class="buttons mt-4">
          <CButton
            v-if="previewImageUrl"
            color="danger"
            @click="deletePreviewImage()"
          >
            Удалить
          </CButton>
        </div>
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
  name: 'TasksCreateTextQuestion',

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
      type: Array,
      default: null
    },
    comment_required: {
      type: Number,
      default: null
    },
    file: {
      type: String,
      default: null
    },
    group_name: Boolean
  },

  data() {
    return {
      errorMessage: null,
      selectedFile: null,
      previewImageUrl: null,
      image: null,
      question: ''
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
  },

  methods: {
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
        type: 'text',
        question: this.question,
        id: this.questionId,
        images: this.image ? this.image : this.selectedFile
      }
    }
  },
  validations: {
    question: {
      required
    }
  }
}
</script>
