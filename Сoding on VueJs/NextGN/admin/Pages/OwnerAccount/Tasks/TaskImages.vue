<template>
   <div v-if="file" class="images mt-2">
      <CImg
         :src="`/storage/images/${file}`"
         thumbnail
         class="images__img"
         style="max-height: 300px;"
      />

      <div v-if="preview">
         <img :src="preview" alt="Выбранное изображение">
      </div>

      <div class="buttons ml-2">
         <CButton @click="deleteImage(questionId)" color="danger">Удалить</CButton>
      </div>

   </div>
</template>

<script>
export default {
   name: 'TaskImages',
   props: {
      questionId: Number,
      file: String,
      preview: null,
   },
   methods: {
      async deleteImage(taskId) {
         this.preview = null
         this.file = null
         // try {
         //    await this.$inertia.post(`/admin/task/file/${this.questionId}`, {
         //       preserveScroll: true,
         //    });
         //
         //    this.file = ''
         //
         // } catch (e) {
         //    this.$handleError(e);
         // }
      },
   },
};
</script>

<style scoped>
.images {
   display: flex;
   flex-wrap: wrap;
}

.images__root {
   max-width: 33%;
   position: relative;
   transition: 0.5s ease;
}

.images__buttons {
   position: absolute;
   z-index: 999;
   top: 50%;
   width: 100%;
   transform: translate(0%, -50%);
   text-align: center;
}

.images__btn {
   display: none;
   margin: auto;
   transition: 0.5s ease;
}

.images__root:hover > .images__img,
.images__img:hover {
   opacity: 0.3;
   transition: 0.5s ease;
}

.images__img:hover + .images__buttons .images__btn,
.images__buttons:hover .images__btn,
.images__btn:hover {
   display: block;
   transition: 0.5s ease;
}

.images__img--default {
   opacity: 0.3 !important;
}

.images__btn--default {
   display: block !important;
   cursor: initial;
}
</style>
