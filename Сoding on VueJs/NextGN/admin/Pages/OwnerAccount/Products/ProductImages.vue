<template>
   <div class="images">
      <div v-for="image in images" :key="image.id" class="images__root">
         <CImg
            :src="`/storage/images/${image.name}`"
            thumbnail
            class="images__img"
            :class="{ 'images__img--default': image.default }"
         />
         <div class="images__buttons">
            <CButton v-if="image.default" color="dark" disabled class="images__btn images__btn--default">
               Главное
            </CButton>
            <CButton v-else @click="setAsDefault(image.id)" color="dark" class="images__btn">
               Установить главным
            </CButton>
            <CButton @click="deleteImage(image.id)" color="danger" class="images__btn mt-1">Удалить</CButton>
         </div>
      </div>
   </div>
</template>

<script>
export default {
   name: 'ProductImages',
   props: {
      productId: Number,
      images: Array,
   },
   methods: {
      async setAsDefault(imageId) {
         try {
            await this.$inertia.post(
               `/admin/products/images/set-default`,
               {
                  product_id: this.productId,
                  image_id: imageId,
               },
               {
                  preserveScroll: true,
               }
            );
         } catch (e) {
            this.$handleError(e);
         }
      },
      async deleteImage(imageId) {
         try {
            await this.$inertia.delete(`/admin/products/${this.productId}/images/${imageId}`, {
               preserveScroll: true,
            });

         } catch (e) {
            this.$handleError(e);
         }
      },
   },
};
</script>

<style scoped>
.images {
   display: flex;
   flex-wrap: wrap;
   gap: 10px;
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

.images__img {
  min-width: 300px;
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
