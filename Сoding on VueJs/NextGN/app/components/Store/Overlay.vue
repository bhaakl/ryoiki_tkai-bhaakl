<template>
  <div
    class="fixed inset-0 z-50 flex items-center justify-center bg-txt/40"
    @click.self="$emit('close')"
  >
    <component
      :is="currentView"
      @proceedToCheckout="showCheckout"
      @close="$emit('close')"
      @returnToCart="showCart"
      @completeCheckout="showStatus"
    />
  </div>
</template>

<script>
import ShoppingCart from './ShoppingCart.vue'
import CheckoutForm from './CheckoutForm.vue'
import CheckoutStatus from './CheckoutStatus.vue'

export default {
  components: {
    ShoppingCart,
    CheckoutForm,
    CheckoutStatus
  },
  data() {
    return {
      view: 'ShoppingCart'
    }
  },
  computed: {
    currentView() {
      switch (this.view) {
      case 'ShoppingCart':
        return ShoppingCart
      case 'CheckoutForm':
        return CheckoutForm
      case 'CheckoutStatus':
        return CheckoutStatus
      default:
        return ShoppingCart
      }
    }
  },
  methods: {
    showCheckout() {
      this.view = 'CheckoutForm'
    },
    showCart() {
      this.view = 'ShoppingCart'
    },
    showStatus() {
      this.view = 'CheckoutStatus'
    }
  }
}
</script>
