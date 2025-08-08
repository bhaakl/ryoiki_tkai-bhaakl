<template>
  <div class="page__content">
    <section class="toptest">
      <div class="container">
        <div class="error">
          {{ description || defaultDescription }}
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import Layout from '@admin/containers/TheContainer'

export default {
  name: 'TextPage',
  layout: Layout,
  props: {
    status: Number,
    title: String,
    description: String
  },
  computed: {
    defaultTitle() {
      return {
        503: '503: Сервис недоступен',
        500: '500: Ошибка сервера',
        404: '404: Запрошенная вами страница не найдена',
        403: '403: Доступ запрещен'
      }[this.status]
    },
    defaultDescription() {
      return {
        503: 'Извините, мы проводим техническое обслуживание. Пожалуйста, зайдите в ближайшее время.',
        500: 'Упс, на наших серверах что-то пошло не так.',
        404: 'Возможно, вы указали неправильный адрес или страница, на которую вы хотели зайти, устарела и была удалена.',
        403: 'Извините, вам запрещен доступ к этой странице.'
      }[this.status]
    }
  }
}
</script>

<style>
.error {
   font-size: 44px;
   font-family: NGEN Compact Medium,Helvetica,Arial,sans-serif;
}
</style>

