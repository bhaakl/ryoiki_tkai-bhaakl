<template>
   <base-modal maxWidth="600px">
      <div class="modal__grid">
         <div class="w-full modal__item" style="width: 100%;">
            <div class="input-container">
               <div class="input-container__row">
                  {{ message }}
               </div>
            </div>
         </div>
      </div>
      <div class="modal__button modal__button--small-margin">
         <inertia-link :href="`/products/${id}`" @click="$modal.hideAll()" tabindex="1" class="btn">
            Ок
         </inertia-link>
      </div>
   </base-modal>
</template>

<script>
import BaseModal from './BaseModal';

export default {
   name: 'ReturnToProduct',
   props: {
      message: String
   },
   components: { BaseModal },
};
</script>
