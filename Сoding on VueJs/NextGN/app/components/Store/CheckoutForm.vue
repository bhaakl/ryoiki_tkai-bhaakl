<template>
  <div
    class="md:w-[440px] max-w-[320px] md:max-w-[440px] px-5 pt-8 pb-5 bg-white shadow-lg rounded-4xl flex flex-col gap-8"
  >
    <div>
      <h2
        class="px-4 mb-2 text-xl font-medium leading-6 text-center font-wide text-txt"
      >
        Оформление заказа
      </h2>
      <p
        class="px-4 font-normal leading-6 text-center text-17 font-compact text-greytxt"
      >
        Пожалуйста, заполните поля
      </p>
    </div>

    <form @submit.prevent="submitForm">
      <div
        :class="{ 'has-error': $v.form.city.$dirty ? $v.form.city.$error : false }"
        class="shopping-card-form mb-4"
      >
        <p class="block text-sm font-normal font-compact text-greytxt">Город<span class="red"> *</span></p>
        <input
          v-model="$v.form.city.$model"
          id="city"
          type="text"
          class="block w-full px-3 py-3.5 mt-1 border border-divider/50 font-compact font-normal text-17 leading-6 rounded-2xl text-greytxt bg-tertiary"
        >
        <label v-if="$v.form.city.$dirty ? $v.form.city.$error : false" class="error ui-input__validation" for="city">
          Город не может быть пустым
        </label>
      </div>
      <div
        :class="{ 'has-error': $v.form.street.$dirty ? $v.form.street.$error : false }"
        class="shopping-card-form mb-4"
      >
        <p class="block text-sm font-normal font-compact text-greytxt">Улица<span class="red"> *</span></p>
        <input
          id="street"
          v-model="$v.form.street.$model"
          type="text"
          class="block w-full px-3 py-3.5 mt-1 border border-divider/50 font-compact font-normal text-17 leading-6 rounded-2xl text-greytxt bg-tertiary"
        >
        <label v-if="$v.form.street.$dirty ? $v.form.street.$error : false" class="error ui-input__validation" for="street">
          Улица не может быть пустым
        </label>
      </div>
      <div class="flex items-start gap-4 mb-6">
        <div
          :class="{ 'has-error': $v.form.building.$dirty ? $v.form.building.$error : false }"
          class="shopping-card-form"
        >
          <p class="block text-sm font-normal font-compact text-greytxt">Дом<span class="red"> *</span></p>
          <input
            id="building"
            v-model="$v.form.building.$model"
            type="text"
            class="block w-full px-3 py-3.5 mt-1 border border-divider/50 font-compact font-normal text-17 leading-6 rounded-2xl text-greytxt bg-tertiary"
          >
          <label v-if="$v.form.building.$dirty ? $v.form.building.$error : false" class="error ui-input__validation" for="building">
            Дом не может быть пустым
          </label>
        </div>

        <div
          :class="{ 'has-error': $v.form.apartment.$dirty ? $v.form.apartment.$error : false }"
          class="shopping-card-form"
        >
          <p class="block text-sm font-normal font-compact text-greytxt">Квартира/Офис<span class="red"> *</span></p>
          <input
            id="apartment"
            v-model="$v.form.apartment.$model"
            type="text"
            class="block w-full px-3 py-3.5 mt-1 border border-divider/50 font-compact font-normal text-17 leading-6 rounded-2xl text-greytxt bg-tertiary"
          >
          <label v-if="$v.form.apartment.$dirty ? $v.form.apartment.$error : false" class="error ui-input__validation" for="apartment">
            Квартира/Офис не может быть пустым
          </label>
        </div>
      </div>

      <p class="mb-3 text-sm font-normal font-compact text-txt">
        Продолжая, вы соглашаетесь с
        <a
          class="text-accent"
          href="https://betatesting.skycorp.it/pages/agreement"
          target="_blank"
        >условиями обработки персональных данных.</a>
      </p>

      <div class="flex gap-3">
        <button
          class="store-menu-btn flex items-center justify-center w-full py-[18px] text-xs font-bold tracking-wider uppercase font-wide text-txt rounded-2xl bg-tertiary"
          @click="$emit('returnToCart')"
        >
          Отменить
        </button>
        <button
          class="store-btn flex items-center justify-center w-full py-[18px] text-xs font-bold tracking-wider uppercase font-wide text-white rounded-2xl"
        >
          Купить
        </button>
      </div>
    </form>
  </div>
</template>

<script>

import axios from 'axios'
import cartStore from '../../store/modules/cart'
import { required } from 'vuelidate/lib/validators'

export default {
  data() {
    return {
      form: {
        city: null,
        street: '',
        building: '',
        apartment: ''
      },
      address: String
    }
  },
  methods: {
    clearFields() {
      this.form.city = ''
      this.form.street = ''
      this.form.building = ''
      this.form.apartment = ''
    },
    submitForm() {
      this.$v.$touch()
      if (this.$v.$invalid) return
      let goods = []
      let cartItems = cartStore.methods.getCart()
      cartItems.map((item) => {
        goods.push({
          id: item.product.id,
          specification_id: item.product.specificationId,
          quantity: item.quantity
        })
      })
      let address = Object.values(this.form)
      this.address = address.join(', ')
      let param = {
        goods: goods,
        address: this.address
      }
      axios.post('/purchase', param).then(() => {
        cartStore.methods.clearCart()
        this.$emit('completeCheckout')
      }).catch((e) => {
        this.$handleError(e)
      }).finally (() => {
        this.clearFields()
      })
    }
  },
  validations: {
    form: {
      city: {
        required
      },
      street: {
        required
      },
      building: {
        required
      },
      apartment: {
        required
      }
    }
  }
}

</script>
<style>
.store-btn{
  background-color: #ff0032;
}
.store-btn:hover{
  background-color: #EE0730;
}
.red {
  color: #ff0032;
}
.shopping-card-form label {
  margin-top: 7px;
  display: inline-block;
  color: #D8400C;
  font-size: 12px;
  line-height: 16px;
}
.store-menu-btn:hover{
  background-color: #e2e5eb;
}

</style>
