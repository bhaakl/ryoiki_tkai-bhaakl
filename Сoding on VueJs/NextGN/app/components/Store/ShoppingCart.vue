<template>
  <div
    v-if="!isMobile()"
    class="w-full md:w-[440px]  max-w-[440px] px-5 pt-8 pb-5 bg-white shadow-lg rounded-[32px] md:rounded-4xl flex flex-col gap-8 relative"
  >
    <button
      class="shopping-cart-close mfp-close"
      @click="$emit('close')"
    >x
    </button>
    <h2
      class="px-4 text-xl font-medium leading-6 text-center font-wide text-txt mb-0"
    >
      Корзина
    </h2>
    <ul class="w-full overflow-y-auto max-h-100 p-0">
      <li
        v-for="(item, key) in cardItems"
        :key="key"
        class="w-full shopping-cart-item flex flex-col items-start justify-between py-4 border-b  border-divider"
      >
        <div class="w-full flex items-start justify-between">
          <div class="flex flex-row items-start justify-start mb-4">
            <div class="relative overflow-hidden rounded-lg w-11 h-11 order-1 md:order-[0]">
              <img :src="item.product.image" alt="" class="absolute z-10 object-cover w-full h-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2" />
            </div>
          </div>
          <div class="w-full flex flex-col items-start justify-start ml-3">
            <div class="w-full flex flex-row justify-between items-start gap-3  md:order-[0]">
              <h3 class="w-[137px] mb-1 font-medium leading-6 text-17 font-compact text-left">
                {{ item.product.title }}
              </h3>
              <div class="flex w-[70px] justify-end items-center gap-2  md:order-[0]">
                <button @click="decreaseQuantity(item)" class="p-1 flex items-center bg-transparent">
                  <img src="@app/assets/icons/minus.svg" alt="Minus button" />
                </button>
                <div class="flex items-center font-normal leading-6 font-compact text-17 text-txt">
                  {{ item.quantity }}
                </div>
                <button @click="increaseQuantity(item)" class="p-1 flex items-center bg-transparent">
                  <img src="@app/assets/icons/plus.svg" alt="Plus button" />
                </button>
              </div>
              <div class="flex w-[75px] items-center justify-end gap-1 font-medium leading-6 font-compact text-txt text-17 md:order-[0]">
                {{ convertAmount(item.product.total) }}
                <div class="w-6 h-6"><Coin /></div>
              </div>
              <button class="w-[24px] flex justify-end order-3 md:order-[0] bg-transparent" @click="removeFromCart(item.product)">
                <img src="@app/assets/icons/delete.svg" alt="Delete button" />
              </button>
            </div>
            <div class="flex gap-2 items-center">
              <p v-if="item.product.size !== false" class="flex items-center justify-center w-6 h-6 text-xs font-normal leading-6 uppercase rounded-lg cursor-pointer bg-tertiary font-compact text-txt">
                {{ item.product.size }}
              </p>
              <p v-if="item.product.color !== false" :style="`background:${item.product.color}`" class="flex items-center justify-center w-6 h-6 text-xs font-normal leading-6 uppercase rounded-lg cursor-pointer font-compact text-txt">
              </p>
            </div>
          </div>
        </div>
      </li>
    </ul>
    <div
      v-if="cartDetails.total !== 0"
      class="flex items-center gap-1 font-medium leading-6 text-center text-17 font-compact text-txt"
    >
      К списанию: {{ convertAmount(cartDetails.total) }}
      <div class="w-6 h-6"><Coin /></div>
    </div>
    <button
      :disabled="!($page.props.user.coins >= cartDetails.total) || cartDetails.total === 0"
      @click="$emit('proceedToCheckout')"
      class="store-btn flex items-center justify-center w-full py-[18px] text-xs font-bold tracking-wider uppercase font-wide text-white rounded-2xl"
    >
      Далее
    </button>
  </div>
  <div
    v-else
    class="w-full  max-w-[440px] md:w-[440px] px-5 pt-8 pb-5 bg-white shadow-lg rounded-[32px] md:rounded-4xl flex flex-col gap-8 relative"
  >
    <button
      class="shopping-cart-close mfp-close"
      @click="$emit('close')"
    >x
    </button>
    <h2
      class="px-4 text-xl font-medium leading-6 text-center font-wide text-txt mb-0"
    >
      Корзина
    </h2>
    <ul class="overflow-y-auto max-h-100 p-0">
      <li
        v-for="(item, key) in cardItems"
        :key="key"
        class="shopping-cart-item flex flex-col items-start justify-between py-4 border-b  border-divider gap-y-4"
      >
        <div class="w-full flex flex-row items-start justify-between">
          <div class="w-full flex flex-row items-start justify-start gap-3 mb-4">
            <div class="relative overflow-hidden rounded-lg w-11 h-11 order-1 md:order-[0]">
              <img :src="item.product.image" alt="" class="absolute z-10 object-cover w-full h-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2" />
            </div>
            <div class="flex flex-col items-start w-[150px] order-2 md:order-[0] pl-2">
              <h3 class="mb-1 font-medium leading-6 text-17 font-compact text-left">
                {{ item.product.title }}
              </h3>
              <div class="flex gap-2 items-center">
                <p v-if="item.product.size !== false" class="flex items-center justify-center w-6 h-6 text-xs font-normal leading-6 uppercase rounded-lg cursor-pointer bg-tertiary font-compact text-txt">
                  {{ item.product.size }}
                </p>
                <p v-if="item.product.color !== false" :style="`background:${item.product.color}`" class="flex items-center justify-center w-6 h-6 text-xs font-normal leading-6 uppercase rounded-lg cursor-pointer font-compact text-txt">
                </p>
              </div>
            </div>
          </div>
          <button class="order-3 md:order-[0] bg-transparent" @click="removeFromCart(item.product)">
            <img src="@app/assets/icons/delete.svg" alt="Delete button" />
          </button>
        </div>
        <div class="w-full flex flex-row items-start justify-between">
          <div class="flex items-center gap-2 order-4 md:order-[0]">
            <button @click="decreaseQuantity(item)" class="p-1 flex items-center bg-transparent">
              <img src="@app/assets/icons/minus.svg" alt="Minus button" />
            </button>
            <div class="flex items-center font-normal leading-6 font-compact text-17 text-txt">
              {{ item.quantity }}
            </div>
            <button @click="increaseQuantity(item)" class="p-1 flex items-center bg-transparent">
              <img src="@app/assets/icons/plus.svg" alt="Plus button" />
            </button>
          </div>
          <div class="flex items-center gap-1 font-medium leading-6 font-compact text-txt text-17 order-5 md:order-[0]">
            {{ convertAmount(item.product.total) }}
            <div class="w-6 h-6"><Coin /></div>
          </div>
        </div>
      </li>
    </ul>
    <div
      v-if="cartDetails.total !== 0"
      class="flex items-center gap-1 font-medium leading-6 text-center text-17 font-compact text-txt"
    >
      К списанию: {{ convertAmount(cartDetails.total) }}
      <div class="w-6 h-6"><Coin /></div>
    </div>
    <button
      :disabled="!($page.props.user.coins >= cartDetails.total) || cartDetails.total === 0"
      @click="$emit('proceedToCheckout')"
      class="store-btn flex items-center justify-center w-full py-[18px] text-xs font-bold tracking-wider uppercase font-wide text-white rounded-2xl"
    >
      Далее
    </button>
  </div>
</template>

<script>
import Coin from '../../assets/img/icons/Coin.vue'
import cartStore from '../../store/modules/cart'
import { EventBus } from '../../main';

export default {
  components: {
    Coin
  },
  inject: ['isMobile'],
  data() {
    return {
      cardItems: [],
      cartDetails: {
        total: 0
      }
    }
  },
  mounted() {
    this.updateCart()
  },
  methods: {
    increaseQuantity(item) {
      cartStore.methods.updateQuantity(item.product, item.quantity + 1)
      this.updateCart()
    },
    decreaseQuantity(item) {
      cartStore.methods.updateQuantity(item.product, item.quantity - 1)
      this.updateCart()
    },
    updateCart() {
      this.cardItems = cartStore.methods.getCart()
      this.cartDetails = cartStore.methods.getCartDetails()
    },
    removeFromCart(product) {
      cartStore.methods.removeFromCart(product)
      this.updateCart()
    },
    convertAmount(number) {
      return number.toLocaleString('fr-FR').replace(/\u00A0/g, ' ')
    }
  }
}
// const cart = cartStore.cart;
// const total = computed(() => cartStore.getCartTotal());
//

</script>
<style>
.store-btn{
  background-color: #ff0032;
}
.store-btn:disabled, .store-btn:disabled:hover{
  background-color: #BCC3D080;
}
.store-btn:hover{
  background-color: #EE0730;
}
.shopping-cart-close{
  cursor: pointer;
  position: absolute;
  right: 10px;
  top: 10px;
  color: #333;
}
.shopping-cart-close:hover{
  color: #ff0032;
}
.shopping-cart-item{
  border-bottom: 1px solid #BCC3D080
}
</style>
