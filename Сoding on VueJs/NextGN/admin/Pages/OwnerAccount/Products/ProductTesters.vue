<template>
   <div>
      <CDataTable
         hover
         :items="testers"
         :fields="fields"
         :items-per-page="5"
         :active-page="activePage"
         :pagination="{ doubleArrows: false, align: 'center' }"
      >
         <template #thead-top>
            <CButton :href="`/admin/product_testers/export/excel/${product_id}`" color="dark">Выгрузить</CButton>
         </template>
         <template #is_active="{ item: { pivot: { is_active } } }">
            <td>
               <CBadge :color="getBadgeColor(is_active)" class="mr-1 mb-1">
                  {{ getActivityText(is_active) }}
               </CBadge>
            </td>
         </template>
         <template #actions="{item: tester}">
            <td>
               <CButtonGroup>
                  <CButton v-if="canReward(tester)" @click="openRewardModal(tester)" color="success">
                     Наградить
                  </CButton>
                  <CButton v-else disabled :title="noRewardTitle" color="success">
                     Наградить
                  </CButton>
                  <CButton @click="$inertia.visit(`/admin/testers/${tester.id}`)" color="info">
                     Посмотреть
                  </CButton>
               </CButtonGroup>
            </td>
         </template>
         <template #no-items-view>
            <p class="font-weight-bold text-center mt-2">Пока ни одного</p>
         </template>
      </CDataTable>
      <CModal color="success" centered :show.sync="giveRewardModal">
         <template #header>
            <h6 class="modal-title">Наградить тестировщика</h6>
         </template>
         <CSelect :value.sync="selectedReward" :options="rewardOptions" label="Выберите вознаграждение:" />
         <CSelect
            v-if="isSelectedRewardPrivate && promocodeOptions.length > 0"
            :value.sync="selectedPromocode"
            :options="promocodeOptions"
            label="Выберите промокод:"
         />
         <label v-if="isSelectedRewardPrivate && promocodeOptions.length === 0">Промокоды закончились</label>
         <template #footer>
            <CButton @click="giveReward(true)" :disabled="isSelectedRewardPrivate && promocodeOptions.length === 0"  color="success">Наградить</CButton>
            <CButton @click="giveReward(false)" color="light">Отмена</CButton>
         </template>
      </CModal>
   </div>
</template>

<script>
export default {
   name: 'ProductTesters',
   props: {
      testers: Array,
      rewards: Array,
      product_id: Number,
   },
   data() {
      return {
         fields: [
            { key: 'name', label: 'Имя', sorter: false },
            // { key: 'is_active', label: 'Активность' },
            { key: 'actions', label: 'Действия', _style: 'width: 200px;' },
         ],
         activePage: 1,
         giveRewardModal: false,
         selectedReward: null,
         selectedTester: null,
         selectedPromocode: null,
         rewardOptions: [],
         promocodeOptions: [],
      };
   },
   computed: {
      noRewardTitle() {
         return !this.rewards.length ? 'У продукта нет вознаграждений' : 'Все доступные награды выданы';
      },
      isSelectedRewardPrivate() {
         const reward = this.rewards.find(reward => reward.id === this.selectedReward);
         if (!reward) return false;
         return reward.type === 'private';
      },
   },
   methods: {
      canReward(tester) {
         const rewardOptions = this.rewards.filter(reward => {
            const testerRewards = tester.rewards.map(testerReward => testerReward.id);
            return !testerRewards.includes(reward.id);
         });
         return rewardOptions.length > 0;
      },
      openRewardModal(tester) {
         this.selectedTester = tester;
         this.calculateRewardOptions();
         this.selectedReward = (this.rewardOptions[0] && this.rewardOptions[0].value) || null;
         this.giveRewardModal = true;
      },
      calculateRewardOptions() {
         this.rewardOptions = this.rewards
            .filter(reward => {
               if (this.selectedTester === null) return false;

               const testerRewards = this.selectedTester.rewards.map(testerReward => testerReward.id);
               return !testerRewards.includes(reward.id);
            })
            .map(reward => ({
               label: reward.title,
               value: reward.id,
            }));
         this.selectedReward = (this.rewardOptions[0] && this.rewardOptions[0].value) || null;
      },
      calculatePromocodeOptions() {
         const reward = this.rewards.find(reward => reward.id === this.selectedReward);
         if (!reward) return;
         this.promocodeOptions = reward.promocodes
            .filter(promocode => !promocode.is_public && !promocode.tester_id)
            .map(promocode => ({
               label: promocode.code,
               value: promocode.id,
            }));

         if (this.promocodeOptions.length) {
            this.selectedPromocode = this.promocodeOptions[0].value;
         }
      },
      async giveReward(modalValue) {
         this.giveRewardModal = false;
         if (!modalValue) return;
         try {
            await this.$inertia.post(
               '/admin/rewards/give',
               {
                  tester_id: this.selectedTester.id,
                  reward_id: this.selectedReward,
                  promocode_id: this.isSelectedRewardPrivate ? this.selectedPromocode : null,
               },
               {
                  preserveScroll: true,
                  onSuccess: () => {
                     this.calculateRewardOptions();
                     this.calculatePromocodeOptions();
                  },
               },
            );
         } catch (e) {
            this.$handleError(e);
         }
      },
      getActivityText(status) {
         switch (status) {
            case 0:
               return 'Завершил';
            case 1:
               return 'В ожидании';
            case 2:
               return 'Участвует';
            default:
               return 'Неизвестно';
         }
      },
      getBadgeColor(status) {
         switch (status) {
            case 0:
               return 'danger';
            case 1:
               return 'warning';
            case 2:
               return 'success';
            default:
               return 'secondary';
         }
      },
   },
   watch: {
      selectedReward() {
         this.calculatePromocodeOptions();
      },
   },
   beforeMount() {
      this.testers.sort((a, b) => {
         return a.pivot.is_active > b.pivot.is_active ? -1 : 1;
      });
      this.calculateRewardOptions();
      this.calculatePromocodeOptions();
   },
};
</script>

<style scoped>
.analytics__btn {
   padding: 0;
}
</style>
