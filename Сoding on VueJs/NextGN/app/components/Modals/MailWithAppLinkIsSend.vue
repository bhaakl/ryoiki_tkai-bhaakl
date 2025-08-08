<template>
   <base-modal maxWidth="600px">
      <p class="modal__sub-title">Письмо отправлено</p>
      <div class="modal__grid">
         <div class="w-full modal__item" style="width: 100%;">
            <div class="input-container">
               <div class="input-container__row">Установите приложение по ссылке, направленной на {{ email }}.</div>
            </div>
         </div>
      </div>
   </base-modal>
</template>

<script>
import BaseModal from './BaseModal';

export default {
   name: 'MailWithAppLinkIsSend',
   props: {
      email: String,
   },
   components: { BaseModal },
};
</script>
