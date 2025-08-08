<template>
  <div
    :key="cardKey"
    class="store-card-item w-full overflow-hidden min-w-[280px] rounded-3xl shadow-shadow"
  >
    <div class="relative w-full overflow-hidden h-80">
      <img
        class="absolute z-10 object-cover w-full h-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2"
        :src="getGoodsImage(product)"
        :alt="product.title"
      >
      <img
        v-if="product.images && product.images.length"
        src="../../assets/icons/search-icon.svg"
        class="absolute z-20 cursor-pointer bottom-4 right-4"
        alt=""
        @click="$emit('open-details', product)"
      >
    </div>
    <div class="p-4 bg-white">
      <div class="mb-4 min-h-29">
        <h3 class="mb-3 text-2xl font-medium leading-7 font-wide text-txt">
          {{ product.title }}
        </h3>
        <p class="mb-3 text-sm font-normal font-compact text-greytxt">
          {{ product.description }}
        </p>
        <ul
          v-if="specifications.length"
          class="flex gap-2"
        >
          <li
            v-for="size in specifications"
            :key="size"
          >
            <input
              :id="`size-${size}-${product.id}`"
              v-model="selectedSize"
              type="radio"
              :name="`size-${product.id}`"
              :value="size"
              class="custom-radio"
            >
            <label
              :for="`size-${size}-${product.id}`"
              class="flex items-center justify-center font-normal leading-6 uppercase transition-colors cursor-pointer w-11 h-11 bg-tertiary font-compact text-17 text-txt rounded-2xl"
            >
              {{ size.toUpperCase() }}
            </label>
          </li>
        </ul>
        <ul
          v-if="selectedSize"
          class="flex gap-2"
        >
          <li
            v-for="item in product.specifications[selectedSize]"
            :key="item.color"
          >
            <input
              :id="`color-${item.color}-${product.id}`"
              v-model="selectedVariant"
              type="radio"
              :name="`color-${product.id}`"
              :value="item"
              class="custom-color-radio"
            >
            <label
              :for="`color-${item.color}-${product.id}`"
              :style="`background:${item.color}`"
              class="flex items-center justify-center font-normal leading-6 uppercase transition-colors cursor-pointer w-11 h-11 bg-tertiary font-compact text-17 text-txt rounded-2xl"
            />
          </li>
        </ul>
      </div>
      <div class="flex items-center gap-2 mb-3">
        <span class="text-2xl font-medium leading-7 font-wide text-txt">
          {{ convertAmount(!selectedVariant ? product.default_price : selectedVariant['price']) }}
        </span>

        <div class="w-6 h-6">
          <Coin />
        </div>
      </div>
      <button
        v-if="selectedVariant ? isInCart[selectedSize] : isInCart.singleProduct"
        class="store-btn flex items-center justify-center w-full py-2.5 text-2.5 leading-3 font-bold tracking-wider uppercase font-wide text-white rounded-xl"
        @click="EventBus.$emit('cartOpen')"
      >
        В корзине
      </button>
      <button
        v-else
        class="store-btn flex items-center justify-center w-full py-2.5 text-2.5 leading-3 font-bold tracking-wider uppercase font-wide text-white rounded-xl"
        @click="addToCart"
      >
        Добавить в корзину
      </button>
    </div>
  </div>
</template>

<script>
import Coin from '../../assets/img/icons/Coin.vue'
import cartStore from '../../store/modules/cart'
import getGoodsImage from '@app/mixins/getGoodsImage'
import images from 'swiper/src/components/core/images'
import { EventBus } from '../../main'

export default {
  components: {
    Coin
  },
  mixins: [getGoodsImage],
  props: {
    product: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      selectedSize: false,
      selectedVariant: false,
      cartList: null,
      cardKey: 0,
      isInCart: {
        singleProduct: false
      }
    }
  },
  computed: {
    EventBus() {
      return EventBus
    },
    images() {
      return images
    },
    specifications() {
      return this.product.specifications ? Object.keys(this.product.specifications) : []
    }
  },
  watch: {
    selectedSize: {
      handler: function() {
        this.selectedVariant = this.product.specifications[this.selectedSize][0]
      },
      deep: true
    },
    selectedVariant: {
      handler: function() {
        this.checkInCart(this.product.id)
      },
      deep: true
    }
  },
  mounted() {
    if (this.specifications.length) {
      this.selectedSize = this.specifications[0]
    } else {
      this.checkInCart(this.product.id)
    }
  },
  methods: {
    checkInCart(id) {
      this.cartList = cartStore.methods.getCart()
      this.cartList.map((item) => {
        if (this.selectedVariant && this.selectedVariant.id === item.product.specificationId) {
          this.isInCart[this.selectedSize] = true

          return true
        } else {
          if (!this.selectedVariant && item.product.id === id) {
            this.isInCart.singleProduct = true

            return true
          }
        }
      })
    },
    addToCart() {
      let residual = this.selectedVariant ? this.selectedVariant.residual : this.product.default_residual
      if ( residual ) {
        let image = this.getGoodsImage(this.product)
        let price = this.selectedVariant ? this.selectedVariant.price : this.product.default_price
        const cardProduct = {
          id: this.product.id,
          specificationId: this.selectedVariant ? this.selectedVariant.id : null,
          title: this.product.title,
          image: image,
          size: this.selectedSize || false,
          color: this.selectedVariant ? this.selectedVariant.color : false,
          price: price,
          total: price
        }
        cartStore.methods.addProductToCart(cardProduct)
        this.checkInCart(this.product.id)
      } else {
        this.$handleError()
      }
      this.cardKey++
    },
    convertAmount(number) {
      return number.toLocaleString('fr-FR').replace(/\u00A0/g, ' ')
    }
  }
}
</script>

<style scoped>
.custom-radio {
  position: absolute;
  z-index: -1;
  opacity: 0;
}
.custom-radio:checked + label {
  background-color: #fff;
}
.custom-color-radio {
  position: absolute;
  z-index: -1;
  opacity: 0;
}
.custom-color-radio + label {
  opacity: .5
}
.custom-color-radio:checked + label {
  opacity: 1
}
.store-btn{
  background-color: #ff0032;
}
.store-btn:hover{
  background-color: #EE0730;
}
.store-card-item{
  box-shadow: 0px 4px 16px 0px #00000014;
}
</style>
