<template>
   <div class="cabinet__tab">
      <p class="cabinet__tab-title">Вознаграждения</p>
      <p class="cabinet__tab-sub-title">Внимание. Каждое начисление происходит в течение 5-7 рабочих дней после выполнения условий.</p>
      <p class="cabinet__tab-title">
         История начисления кешбеков
      </p>

      <CashbackHistoryTable :operations="balanceHistory.length ?  balanceHistory : operations" />

      <div class="cabinet__bonuses mt-13">
         <reward v-for="reward in rewards" :key="reward.id" :reward="reward" />
      </div>
   </div>
</template>

<script>
import Reward from '@app/components/Profile/Reward';
import Layout from '@app/components/Layout/Index';
import CashbackHistoryTable from './components/CashbackHistory';
import ProfileLayout from './ProfileLayout';

export default {
   name: 'ProfileRewards',
   layout: [Layout, ProfileLayout],
   props: {
      rewards: Array,
      balanceHistory: {
         type: Array,
         default: () => []
      },
      pages: {
         type: Number,
         default: 1
      }
   },
   data() {
      return {
         operations: [],
      }
   },
   components: {
      Reward,
      CashbackHistoryTable,
   },
   computed: {
      // rewardsHistory() {
      //    return [
      //       {
      //          type: '+343',
      //          date: `12.04.2020`,
      //          status: 'processing',
      //          desc: 'Lorem Lalaladfadfsssssssssssdf ALa'
      //       },
      //       {
      //          type: '+34',
      //          date: `11.03.2022`,
      //          status: 'accrued',
      //          desc: 'Lorem Lorem фавыаыфваыв ALaALa'
      //       },
      //       {
      //          type: '+22',
      //          date: `11.03.2024`,
      //          status: 'accrued',
      //          desc: 'Lorem фавыаыфваыв ALadjfj'
      //       }
      //    ]
      // },
   },
};
</script>

<style>
.no-rewards {
   font-size: 1.5rem;
   margin: 3.85rem 0;
   text-align: center;
}
</style>
