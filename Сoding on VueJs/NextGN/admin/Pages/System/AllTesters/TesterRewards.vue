<template>
   <div>
      <CDataTable
         hover
         :items="rewards"
         :fields="fields"
         :items-per-page="5"
         :active-page="activePage"
         :pagination="{ doubleArrows: false, align: 'center' }"
         :noItemsView="{ noResults: 'Нет подходящих вариантов', noItems: 'Пока ни одного' }"
      >
         <template #no-items-view>
            <p class="font-weight-bold text-center mt-2">Пока ни одного</p>
         </template>
      </CDataTable>
   </div>
</template>

<script>
export default {
   name: 'TesterRewards',
   props: {
      testerId: Number,
      rewards: Array,
      userRewards: Array,
   },
   data() {
      return {
         fields: [{ key: 'title', label: 'Название', sorter: false }],
         activePage: 1,
         selectedReward: null,
         selectedPromocode: null,
         rewardOptions: [],
         promocodeOptions: [],
         giveRewardModal: false,
         takeRewardModal: false,
      };
   },
};
</script>
