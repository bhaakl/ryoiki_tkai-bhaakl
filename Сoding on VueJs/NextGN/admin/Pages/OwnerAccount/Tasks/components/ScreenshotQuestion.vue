<template>
   <div class="card">
      <div class="card-header d-flex flex-row justify-content-between">
         <p class="font-weight-bold">Вопрос {{ index }}<span> (скриншот)</span></p>
        <CButton color="light" size="sm" class="question-drag-btn" v-if="!group_name">
          <CIcon size="sm" name="cil-line-spacing" />
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
         <small style="color:red" v-if="$v.question.$dirty ? $v.question.$error : false">Введите описание</small>

         <div class="mt-4">
            <CInputFile accept="image/*,video/*" @change="onChangeImage" label="Прикрепите видео или картинку:" class="mb-4" horizontal>
               <template #description>
                  <small class="form-text text-muted w-100">
                     Допустимые форматы для фото: все типы файлов изображений, такие как JPEG, PNG и др.<br />
                     Допустимые форматы для видео: все типы файлов видео, такие как MP4, AVI, MOV и др.<br />
                     Максимальный размер файла: 1.5 Гб<br />
                  </small>
               </template>
            </CInputFile>
            <span v-if="errorMessage" class="text-danger">{{ errorMessage }}</span>
            <div v-if="selectedFile">
               <img v-if="fileExtension == 'img'" style="width: 300px" :src="`/storage/images/${selectedFile}`"/>
               <video v-if="fileExtension == 'video'" class="mb-4 mt-4" style="width: 300px" :src="`/storage/images/${selectedFile}`"></video>
            </div>
            <div class="buttons mt-4">
               <CButton v-if="selectedFile" @click="deleteImage()" color="danger">Удалить</CButton>
            </div>
            <img v-if="getFileType == 'img'" class="mb-4 mt-4" style="width: 300px" :src="previewImageUrl">
            <video v-if="getFileType == 'video'" class="mb-4 mt-4" style="width: 300px" :src="previewImageUrl"></video>
            <div class="buttons ml-2">
               <CButton v-if="previewImageUrl" @click="deletePreviewImage()" color="danger">Удалить</CButton>
            </div>
         </div>

         <CButton @click="$emit('remove-question')" color="danger" class="float-right">Удалить вопрос</CButton>
         <div class="clearfix"></div>
      </div>
   </div>
</template>

<script>
import { required } from 'vuelidate/lib/validators';
import TaskImages from "../TaskImages";

export default {
   name: 'TasksCreateScreenshotQuestion',
   props: {
      index: Number,
      questionId: Number,
      questionData: {
         type: String,
         default: null,
      },
      values: {
         type: Array,
         default: null,
      },
      comment_required: {
         type: Number,
         default: null,
      },
      file: {
         type: String,
         default: null,
      },
      group_name: Boolean,
   },
   components: {
      TaskImages
   },
   data() {
      return {
         errorMessage: null,
         selectedFile: null,
         previewImageUrl: null,
         image: null,
         question: '',
         isCommentRequired: false,
      };
   },
   computed: {
      getFileType(){
         if (this.image) {
            const fileType = this.image.type;

            if (fileType.includes('image')) {
               return  'img'
            } else if (fileType.includes('video')) {
               return 'video'
            } else {
               console.log('Это неизвестный тип файла');
            }
         }
      },
      fileExtension(){
         if (this.selectedFile) {
            const fileExtension = this.selectedFile.split('.').pop();

            if (fileExtension === 'jpg' || fileExtension === 'jpeg' || fileExtension === 'png') {
               return  'img'
            } else if (fileExtension === 'mp4' || fileExtension === 'avi' || fileExtension === 'mov') {
               return 'video'
            } else {
               console.log('Это неизвестный тип файла');
            }
         }
      }
   },
   methods: {
      deleteImage(){
         this.selectedFile = null
         this.image = null
      },
      deletePreviewImage(){
         this.previewImageUrl = null
      },
      onChangeImage(files) {
         const maxFileSize = 1.5 * 1024 * 1024 * 1024;

         const file = files[0];
         if (file.size > maxFileSize) {
            this.errorMessage = "Файл слишком большой. Максимальный размер: 1.5 ГБ";
            return;
         }

         this.image = file;
         this.previewImageUrl = URL.createObjectURL(this.image);
         this.selectedFile = null;
         this.errorMessage = null;

         const formData = new FormData();
         formData.append('image', this.image);
      },
      getData() {
         return {
            index: this.index,
            type: 'file',
            question: this.question,
            comment_required: this.isCommentRequired ? 1 : 0,
            id: this.questionId,
            images: this.image ? this.image : this.selectedFile,
         };
      },
   },
   validations: {
      question: {
         required,
      },
   },
   beforeMount() {
      this.selectedFile = this.file

      if (this.questionData) {
         this.question = this.questionData;
      }
      if (this.comment_required) {
         this.isCommentRequired = !!this.comment_required;
      }
   },
};
</script>
