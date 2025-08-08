<template>
   <div class="bonus__item">
      <div class="bonus__image-container">
         <img :src="`/storage/images/${reward.image}`" alt="" />
      </div>
      <div class="bonus__info">
         <p class="bonus__title">{{ reward.title }}</p>
         <p class="bonus__about" v-html="reward.description"></p>
         <p v-if="reward.info" class="bonus__title-small">Информация для получения:</p>
         <p v-if="reward.info" class="bonus__about">{{ reward.info }}</p>
         <template v-if="hasPromocodes">
            <p class="bonus__title-small">Промокод</p>
            <div class="bonus__about">
               <div class="input-container">
                  <input type="text" style="text-align: center" :value="promocode" readonly />
               </div>
            </div>
         </template>
         <div class="bonus__row">
            <div class="bonus__row-item">
               <p class="bonus__title-small">
                  Тип награды:<br />
                  {{ type }}
               </p>
            </div>
            <div class="bonus__row-item">
               <p class="bonus__title-small">
                  Дата получения:<br />
                  {{ date }}
               </p>
            </div>
         </div>
      </div>
   </div>
</template>

<script>
export default {
   name: 'ProfileRewardItem',
   props: {
      reward: Object,
   },
   computed: {
      hasPromocodes() {
         return (
            this.reward.type !== 'gift' &&
            this.reward.promocodes &&
            this.reward.promocodes.length &&
            this.$page.props &&
            this.$page.props.user
         );
      },
      promocode() {
         if (this.reward.type == 'public') {
            const publicPromocodes = this.reward.promocodes.filter(promocode => promocode.is_public);
            if (!publicPromocodes || !publicPromocodes.length) {
               return '–';
            }
            return publicPromocodes[0].code;
         }

         const promocode = this.reward.promocodes.find(promocode => promocode.tester_id === this.$page.props.user.id);
         if (!promocode) {
            return '–';
         }
         return promocode.code;
      },
      type() {
         switch (this.reward.type) {
            case 'public':
               return 'Общий промокод';
            case 'private':
               return 'Личный промокод';
            case 'gift':
               return 'Ценный приз';
         }
      },
      date() {
         return this.$moment(this.reward.pivot.created_at).format('DD.MM.YYYY');
      },
   },
};
</script>

<style>
.bonus__item {
   cursor: auto;
}
</style>
