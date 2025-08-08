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
         <template #actions="{ item: reward }">
            <td>
               <CButton
                  v-if="canTake(reward.id)"
                  @click="
                     selectedTakeReward = reward.pivot.id;
                     takeRewardModal = true;
                  "
                  color="danger"
               >
                  Отозвать
               </CButton>
               <CButton v-else disabled title="Вознаграждение было выдано не вами" color="danger">
                  Отозвать
               </CButton>
            </td>
         </template>
         <template #no-items-view>
            <p class="font-weight-bold text-center mt-2">Пока ни одного</p>
         </template>
      </CDataTable>
      <CButton v-if="rewardOptions.length" @click="openRewardModal" color="success" class="float-right mr-4">
         Наградить
      </CButton>
      <CButton v-else disabled title="Все доступные награды выданы" color="success" class="float-right mr-4">
         Наградить
      </CButton>
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
            <CButton @click="giveReward(true)" :disabled="isSelectedRewardPrivate && promocodeOptions.length === 0" color="success">Наградить</CButton>
            <CButton @click="giveReward(false)" color="light">Отмена</CButton>
         </template>
      </CModal>
      <CModal color="danger" centered :show.sync="takeRewardModal">
         <template #header>
            <h6 class="modal-title">Отозвать вознаграждение?</h6>
         </template>
         Вы уверены, что хотите отозвать вознаграждение?
         <template #footer>
            <CButton @click="takeReward(true)" color="danger">Отозвать</CButton>
            <CButton @click="takeReward(false)" color="light">Отмена</CButton>
         </template>
      </CModal>
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
         fields: [
            { key: 'title', label: 'Название', sorter: false },
            { key: 'actions', label: 'Действия', _style: 'width: 120px;' },
         ],
         activePage: 1,
         selectedReward: null,
         selectedTakeReward: null,
         selectedPromocode: null,
         rewardOptions: [],
         promocodeOptions: [],
         giveRewardModal: false,
         takeRewardModal: false,
      };
   },
   computed: {
      isSelectedRewardPrivate() {
         const reward = this.userRewards.find(reward => reward.id === this.selectedReward);
         if (!reward) return false;
         return reward.type === 'private';
      },
   },
   methods: {
      canTake(rewardId) {
         if (!this.$page.props || !this.$page.props.user) return false;
         const includes = this.userRewards.map(r => r.id).includes(rewardId);
         if (!includes) return false;
         const reward = this.rewards.find(r => r.id == rewardId);
         return this.$page.props.user.id === reward.pivot.given_by;
      },
      calculateRewardOptions() {
         this.rewardOptions = this.userRewards
            .filter(reward => {
               if (reward.type === 'public') {
                  const testerRewards = this.rewards.map(testerReward => testerReward.id);
                  return !testerRewards.includes(reward.id);
               }

               return true;
            })
            .map(reward => ({
               label: reward.title,
               value: reward.id,
            }));
         this.selectedReward = (this.rewardOptions[0] && this.rewardOptions[0].value) || null;
      },
      calculatePromocodeOptions() {
         const reward = this.userRewards.find(reward => reward.id === this.selectedReward);
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
      openRewardModal() {
         this.selectedReward = this.rewardOptions[0].value || null;
         this.giveRewardModal = true;
      },
      async giveReward(modalValue) {
         this.giveRewardModal = false;
         if (!modalValue) return;
         try {
            await this.$inertia.post(
               '/admin/rewards/give',
               {
                  tester_id: this.testerId,
                  reward_id: this.selectedReward,
                  promocode_id: this.isSelectedRewardPrivate ? this.selectedPromocode : null,
               },
               {
                  preserveScroll: true,
                  preserveState: true,
                  onSuccess: () => {
                     this.calculateRewardOptions();
                     this.calculatePromocodeOptions();
                  }
               }
            );
         } catch (e) {
            this.$handleError(e);
         }
      },
      async takeReward(modalValue) {
         this.takeRewardModal = false;
         if (!modalValue) return;
         try {
            await this.$inertia.post(
               '/admin/rewards/take',
               {
                  tester_id: this.testerId,
                  reward_pivot_id: this.selectedTakeReward,
               },
               {
                  preserveScroll: true,
                  onFinish: () => {
                     this.calculateRewardOptions();
                  }
               }
            );

         } catch (e) {
            this.$handleError(e);
         }
      },
   },
   watch: {
      selectedReward() {
         this.calculatePromocodeOptions();
      },
   },
   beforeMount() {
      this.calculateRewardOptions();
      this.calculatePromocodeOptions();
   },
};
</script>
