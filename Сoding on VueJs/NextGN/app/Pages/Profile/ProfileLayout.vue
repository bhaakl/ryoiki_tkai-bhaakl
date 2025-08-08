<template>
   <div class="page__content">
      <section class="cabinet">
         <div class="container px-5 tw-fixed-wrap-page__content-container">
            <div class="cabinet__head">
               <h2 class="h2-title">{{ $page.props.user.name }}</h2>
               <div class="cabinet__status">
                  <div v-if="$page.props.roles.includes('alpha')" class="cabinet__status-item">
                     <svg class="ico ico-mono-status-alfa">
                        <use xlink:href="@app/assets/img/sprite-mono.svg#ico-mono-status-alfa"></use>
                     </svg>
                     <span>Alpha-тестировщик</span>
                  </div>
                  <div v-if="$page.props.roles.includes('beta')" class="cabinet__status-item">
                     <svg class="ico ico-mono-status-beta">
                        <use xlink:href="@app/assets/img/sprite-mono.svg#ico-mono-status-beta"></use>
                     </svg>
                     <span>Beta-тестировщик</span>
                  </div>
               </div>
            </div>
            <div class="cabinet__grid flex justify-between">
               <div class="cabinet__buttons-overlay">
                  <div class="cabinet__buttons profile-leftbar">
                     <inertia-link href="/profile" :class="{ 'is-active': $page.url === '/profile' }">
                        Личная информация
                     </inertia-link>
                     <inertia-link href="/profile/about" :class="{ 'is-active': $page.url === '/profile/about' }">
                        О себе
                     </inertia-link>
                     <!-- <inertia-link href="/profile/products" :class="{ 'is-active': $page.url === '/profile/products' }">
                       Продукты на тестировании
                    </inertia-link> -->
                     <inertia-link href="/profile/devices" :class="{ 'is-active': $page.url === '/profile/devices' }">
                        Мои устройства
                     </inertia-link>
                     <template v-if="canAccessBeta">
                        <inertia-link
                           href="/profile/rewards"
                           :class="{ 'is-active': $page.url === '/profile/rewards' }"
                        >
                           Вознаграждения
                        </inertia-link>
                     </template>
                  </div>
               </div>
               <div class="cabinet__content">
                  <slot />
               </div>
            </div>
         </div>
      </section>
   </div>
</template>

<script>
import { mapGetters } from 'vuex';
import Layout from '@app/components/Layout/Index';

export default {
   name: 'ProfileIndex',
   layout: Layout,
   computed: {
      ...mapGetters({
         isAuth: 'auth/isAuth',
         adminHasAccess: 'auth/adminHasAccess',
         isAuthBetaTester: 'auth/isAuthBetaTester',
         isAuthAlphaTester: 'auth/isAuthAlphaTester',
      }),
      canAccessBeta() {
         if (!this.isAuth || !this.$page.props.roles || !this.$page.props.roles.length) {
            return false;
         }
         const isAlphaTester = this.isAuthAlphaTester;
         const hasBetaRole = this.isAuthBetaTester;
         const hasOtherAllowedRoles = this.adminHasAccess(['admin', 'product_owner', 'moderator']);

         return (!isAlphaTester && hasOtherAllowedRoles) || (isAlphaTester && hasBetaRole);
      },
   },
};
</script>

<style scoped>
.cabinet__buttons-overlay,
.cabinet__buttons,
.cabinet__buttons a,
.cabinet__content > .cabinet__tab {
   border: none !important;
}

.cabinet__content {
   padding: 0;
}
.cabinet__tab {
   display: block !important;
   margin: 0;
}
   
.profile-leftbar > a {
   color: #1d2023;
}
</style>
