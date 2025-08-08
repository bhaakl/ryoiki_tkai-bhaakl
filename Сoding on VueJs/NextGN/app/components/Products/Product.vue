<template>
   <div class="products__item" :class="{ 'products__item--smart-tv': product.platform === 'smart_tv' }">
      <div class="products__image-container"
           :class="{ 'products__image-container--web' : product.platform === 'web' }"
           :style="{ 'background-color': product.background_color }">
         <p v-if="product.is_active === 2" class="products__status products__status--active">
            Активно <span> {{ formatDaysLeft(product) }}</span>
         </p>
         <p v-if="product.is_active === 1" class="products__status products__status--waiting">
            В ожидании <span>(начало {{ momentFormat(product.date_start, 'll') }})</span>
         </p>
         <p v-if="!product.is_active" class="products__status products__status--notactive">
            Неактивно
         </p>

         <div class="products__test">
            <span v-if="product.stage === 0" class="products__test-item" title="Альфа тестирование" js-tooltip>
               <svg class="ico ico-mono-status-alfa">
                  <use xlink:href="@app/assets/img/sprite-mono.svg#ico-mono-status-alfa"></use>
               </svg>
            </span>
            <span v-if="product.stage === 1" class="products__test-item" title="Бета тестирование" js-tooltip>
               <svg class="ico ico-mono-status-beta">
                  <use xlink:href="@app/assets/img/sprite-mono.svg#ico-mono-status-beta"></use>
               </svg>
            </span>

            <div>
             <span
                v-for="platform in platforms"
                v-if="product.platform === platform.value"
                :key="platform.value"
                class="products__test-item"
                :title="platform.title"
                js-tooltip>
               <svg v-if="platform.value == 'web_ios'" class="ico ico-mono-status-alfa">
                  <use xlink:href="@app/assets/img/sprite-mono.svg#ico-mono-status-apple"></use>
               </svg>

               <svg v-if="platform.value == 'web_android'" class="ico ico-mono-status-alfa">
                  <use xlink:href="@app/assets/img/sprite-mono.svg#ico-mono-android"></use>
               </svg>
                <template v-if="platform.value === 'smart_tv'" class="ico ico-mono-status-alfa">
                  <img :src="iconPathSmartTv()"/>
                </template>

                <svg v-else class="ico" :class="platform.iconClass">
                   <use v-if="platform.value === 'ios'"
                        xlink:href="@app/assets/img/sprite-mono.svg#ico-mono-status-apple"></use>
                   <use v-if="platform.value === 'android'"
                        xlink:href="@app/assets/img/sprite-mono.svg#ico-mono-android"></use>
                   <use v-if="platform.value.includes('web')"
                        xlink:href="@app/assets/img/sprite-mono.svg#ico-mono-web"></use>
               </svg>
             </span>
            </div>
         </div>
         <inertia-link
            :href="`/products/${product.id}`"
            class="products__image"
            :class="{
               'products__image--android': product.platform === 'android',
               'products__image--smart-tv': product.platform === 'smart_tv',
               'products__image--large': product.platform === 'web',
            }"
            tabindex="-1"
         >
            <template v-if="product.platform === 'android'">
               <img class="template" src="@app/assets/img/android-template.png" alt="product android"/>
               <div class="products__image-bg" :style="`background-image: url(${getProductImage(product)})`"></div>
               <img class="top" src="@app/assets/img/android-template.png" alt="product android"/>
            </template>
            <template v-else-if="product.platform === 'web'">
               <img class="template" src="@app/assets/img/web-template.png" alt="product web"/>
               <div class="products__image-bg" :style="`background-image: url(${getProductImage(product)})`"></div>
               <img class="top" src="@app/assets/img/web-template.png" alt="product web"/>
            </template>
            <template v-else-if="product.platform === 'smart_tv'">
               <img class="template" src="@app/assets/img/smart-template.png" alt="product smart"/>
               <div class="products__image-bg" :style="`background-image: url(${getProductImage(product)})`"></div>
               <img class="top" src="@app/assets/img/smart-template.png" alt="product smart"/>
            </template>
            <template v-else>
               <img class="template" src="@app/assets/img/iphone-template.png" alt="product iphone"/>
               <div class="products__image-bg" :style="`background-image: url(${getProductImage(product)})`"></div>
               <img class="top" src="@app/assets/img/iphone-template.png" alt="product iphone"/>
            </template>
         </inertia-link>
      </div>
      <div class="products__info">
         <p class="products__title" v-html="product.name_with_br"></p>
        <p class="products__coin" v-if="product.price_for_all_tasks_coins"> {{ product.price_for_all_tasks_coins }} <img class="ml-2" src="@app/assets/img/coin.svg" alt="Coin" /></p>
         <p class="products__about">
            {{ product.short_description || product.description }}
         </p>
        <div class="py-[14px]" v-if="maxTestersCount">
          <div
            class="relative flex w-full h-1 overflow-hidden rounded-sm bg-divider/50"
          >
            <div
              class="absolute left-0 top-0 h-1 bg-brand rounded-sm"
              :style="{width: Math.floor((product.testers_count / maxTestersCount) * 100) + '%'}"
            ></div>
          </div>
        </div>
        <p class="text-xs font-normal text-greytxt font-compact" v-if="maxTestersCount">
          Уже тестируют: {{ product.testers_count }} / {{ maxTestersCount }}
        </p>
        <div style="margin-top: auto;">
            <div class="products__link-container">
               <inertia-link v-if="!isAdmin" class="products__link font-bold" :href="`/products/${product.id}`">
                  Протестировать<span> →</span>
               </inertia-link>
               <inertia-link v-else class="products__link font-bold" :href="`/products/${product.id}`">
                  Посмотреть<span> →</span>
               </inertia-link>
            </div>

         </div>
      </div>
   </div>
</template>

<script>
import formatDaysLeft from '@app/libs/plural';
import getProductImage from '@app/mixins/getProductImage';

export default {
   name: 'ProductsListProduct',
   mixins: [getProductImage],
   props: {
      product: Object,
      isAdmin: Boolean,
   },

   data() {
      return {
         platforms: [
            {
               value: 'ios',
               title: 'Для платформ Apple',
               iconClass: 'ico-mono-status-apple',
               iconPath: '@app/assets/img/sprite-mono.svg#ico-mono-status-apple'
            },
            {
               value: 'android',
               title: 'Для платформ Android',
               iconClass: 'ico-mono-android',
               iconPath: '@app/assets/img/sprite-mono.svg#ico-mono-android'
            },
            {
               value: 'web',
               title: 'Веб',
               iconClass: 'ico-mono-web',
               iconPath: '@app/assets/img/sprite-mono.svg#ico-mono-web'
            },
            {
               value: 'web_ios',
               title: 'Веб iOS',
               iconClass: 'ico-mono-web',
               iconPath: '@app/assets/img/sprite-mono.svg#ico-mono-web'
            },
            {
               value: 'web_android',
               title: 'Веб Android',
               iconClass: 'ico-mono-web',
               iconPath: '@app/assets/img/sprite-mono.svg#ico-mono-web'
            },
            {
               value: 'smart_tv',
               title: this.product.osVersionSmartTv ? `Smart TV | ${this.product.osVersionSmartTv?.os}` : 'Smart TV',
               iconClass: 'ico-mono-smart',
               iconPath: '@app/assets/img/sprite-mono.svg#ico-mono-smart'
            }
         ]
      }
   },
   computed: {
      maxTestersCount() {
        return this.product.max_testers_count;
      }
   },
   methods: {
      iconPathSmartTv() {
         if (!this.product.osVersionSmartTv) {
            return null;
         }
         return require(`@app/assets/img/smart-tv/${this.smartTvSlug()}.svg`)
      },
      formatDaysLeft: formatDaysLeft,
      momentFormat(date, format) {
         return this.$moment(date).format(format)
      },
      smartTvSlug() {
         return this.generateSlug(this.product.osVersionSmartTv?.os || '');
      },
      generateSlug(str) {
         return str
            .toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .trim();
      }
   },
};
</script>
<style scoped>
.products__image-container--web {
   padding-top: 270px;
}

.products__coin {
  font-weight: 500;
  font-size: 17px;
  margin-top: 5px;
}

@media only screen and (max-width: 760px) {
   .products__image-container--web {
      padding-top: 168px;
   }
}
</style>
