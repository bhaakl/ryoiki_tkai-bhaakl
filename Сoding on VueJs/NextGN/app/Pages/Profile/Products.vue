<template>
  <div class="cabinet__tab">
    <p class="cabinet__tab-title">
      Продукты на тестировании
    </p>
    <div class="cabinet__tab-status">
      <a
        href="#"
        :class="{'is-active':filterValue === 'all'}"
        @click="filterProducts('all')"
      >
        Все продукты
      </a>

      <a
        href="#"
        :class="{'is-active':filterValue === 'active'}"
        @click="filterProducts('active')"
      >
        Активные
      </a>

      <a
        href="#"
        :class="{'is-active':filterValue === 'ended'}"
        @click="filterProducts('ended')"
      >
        Неактивные
      </a>
    </div>

    <div class="cabinet__products">
      <p
        v-if="!productsPhone.length && !productsWeb.length && !productsSmartTv.length"
        class="no-products"
      >
        {{ noProductLabel }}
      </p>

      <div class="products__grid">
        <Product
          v-for="product in productsPhone"
          :key="product.id"
          :product="product"
          :tester-tasks="testerTasks"
          :devices="devices.userDevicesMobile"
          device-type="device"
          :regions="regions"
          :criticality-bugs="criticalityBugs"
          :type-bugs="typeBugs"
        />
        <Product
          v-for="product in productsWeb"
          :key="product.id"
          :product="product"
          :tester-tasks="testerTasks"
          :devices="product.platform === 'web' ? devices.userDevicesWeb : devices.userDevicesMobile"
          :device-type="product.platform === 'web' ? 'web_device' : 'device'"
          :regions="regions"
          :criticality-bugs="criticalityBugs"
          :type-bugs="typeBugs"
        />
        <Product
          v-for="product in productsSmartTv"
          :key="product.id"
          :product="product"
          :tester-tasks="testerTasks"
          :devices="devices.userDevicesSmartTv"
          device-type="smart_tv"
          :regions="regions"
          :criticality-bugs="criticalityBugs"
          :type-bugs="typeBugs"
        />
      </div>
    </div>
  </div>
</template>

<script>
import ProfileLayout from './ProfileLayout'
import Product from '@app/components/Profile/Product'
import Layout from '@app/components/Layout/Index'

export default {
  name: 'ProfileProducts',

  components: {
    Product
  },

  layout: [Layout, ProfileLayout],

  props: {
    products: Array,
    testerTasks: Object,
    devices: Object,
    regions: Array,
    criticalityBugs: Array,
    typeBugs: Array
  },

  data() {
    return {
      filterValue: 'all',
      filteredProducts: [],
      productsPhone: [],
      productsWeb: [],
      productsSmartTv: []
    }
  },

  computed: {
    noProductLabel() {
      switch (this.filterValue) {
      case 'all':
        return 'Продуктов на тестировании пока нет'
      case 'active':
        return 'Активных продуктов пока нет'
      case 'ended':
        return 'Неактивных продуктов пока нет'
      }
    }
  },

  beforeMount() {
    this.filterProducts('active')
    this.sortProducts()
  },

  updated() {
    this.$nextTick(() => {
      window.APP.Initilizer().init()
    })
  },

  methods: {
    sortProducts() {
      this.productsWeb = this.filteredProducts.filter(product => ['web', 'web_ios', 'web_android'].includes(product.platform.toLowerCase()))
      this.productsPhone = this.filteredProducts.filter(product => ['ios', 'android'].includes(product.platform.toLowerCase()))
      this.productsSmartTv = this.filteredProducts.filter(product => product.platform.toLowerCase() === 'smart_tv')
    },
    filterProducts(tab) {
      this.filterValue = tab
      switch (tab) {
      case 'all':
        this.filteredProducts = this.products
        break
      case 'active':
        this.filteredProducts = this.products.filter(({ status }) => status === 2)
        break
      case 'ended':
        this.filteredProducts = this.products.filter(({ status }) => status !== 2)
        break
      }
      this.sortProducts()
    }
  }

}
</script>

<style scoped>

.cabinet__content {
   width: 100% !important;
   padding: 0;
}
.cabinet__tab {
   display: block !important;
   width: 100% !important;
   margin: 0;
}

.no-products {
   margin: 3.85rem 0;
   text-align: center;
}

@media only screen and (max-width: 760px) {
   .no-products {
      font-size: 14px;
   }
}
</style>
