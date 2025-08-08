<template>
  <div
    class="md:w-[440px] max-w-[320px] md:max-w-[440px] px-5 pt-8 pb-5 bg-white shadow-lg rounded-4xl flex flex-col gap-8"
  >
    <div class="mx-auto">
      <component :is="modalData.statusIcon" />
    </div>

    <div class="px-4">
      <h2
        class="mb-2 text-xl font-medium leading-6 text-center font-wide text-txt"
      >
        {{ modalData.heading }}
      </h2>
      <p
        class="font-normal leading-6 text-center text-17 font-compact text-greytxt"
        v-html="formattedMessage"
      />
    </div>

    <button
      class="store-menu-btn flex items-center justify-center w-full py-[18px] text-xs font-bold tracking-wider uppercase font-wide text-txt rounded-2xl bg-tertiary"
      @click="$emit('close')"
    >
      Хорошо
    </button>
  </div>
</template>

<script>
import Error from '../../assets/img/icons/Error.vue'
import Success from '../../assets/img/icons/Success.vue'

export default {
  data() {
    return {
      success: true
    }
  },
  computed: {
    modalData() {
      return {
        heading: this.success ? 'Успешно' : 'Что-то пошло не так',
        message: this.success
          ? 'Товары были успешно оформлены. Мы отправим их вам в ближайшее время. Ждём вас в новых тестированиях'
          : 'При оплате товара произошла ошибка.\nПроверьте баланс или повторите попытку позднее',
        statusIcon: this.success ? Success : Error
      }
    },
    formattedMessage() {
      return this.modalData.message.replace(/\n/g, '<br>')
    }
  }
}

</script>
<style>
.store-menu-btn:hover{
  background-color: #e2e5eb;
}
</style>
