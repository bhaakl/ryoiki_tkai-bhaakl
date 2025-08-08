<template>
  <div>
    <CDataTable
      ref="rewardsTable"
      hover
      :items="sortedRewards"
      :fields="fields"
      :items-per-page="5"
      :active-page="activePage"
      :pagination="{ doubleArrows: false, align: 'center' }"
    >
      <template #actions="{ item: { id } } ">
        <td>
          <CButton
            color="danger"
            @click="
              selectedReward = id;
              removeModal = true;
            "
          >
            Удалить
          </CButton>
        </td>
      </template>
      <template #no-items-view>
        <p class="mt-2 text-center font-weight-bold">
          Пока ни одного
        </p>
      </template>
    </CDataTable>
    <CButtonGroup class="float-right mt-3">
      <template v-if="sortedRewards && sortedRewards.length">
        <CButton
          v-if="!reordering"
          color="warning"
          @click="reorder"
        >
          Изменить приоритет
        </CButton>
        <CButton
          v-else
          color="success"
          @click="saveNewOrder"
        >
          Сохранить приоритет
        </CButton>
      </template>
      <CButton
        v-if="selectedReward"
        color="success"
        @click="addModal = true"
      >
        Добавить вознаграждение
      </CButton>
    </CButtonGroup>
    <CModal
      color="success"
      centered
      :show.sync="addModal"
    >
      <template #header>
        <h6 class="modal-title">
          Добавить вознаграждение
        </h6>
      </template>
      <CSelect
        :value.sync="selectedReward"
        :options="rewardOptions"
        :disabled="!rewardOptions.length"
        label="Выберите вознаграждение:"
      />
      <template #footer>
        <CButton
          color="success"
          @click="addReward(true)"
        >
          Добавить
        </CButton>
        <CButton
          color="light"
          @click="addReward(false)"
        >
          Отмена
        </CButton>
      </template>
    </CModal>
    <CModal
      color="danger"
      centered
      :show.sync="removeModal"
    >
      <template #header>
        <h6 class="modal-title">
          Удалить вознаграждение?
        </h6>
      </template>
      Вы уверены, что хотите удалить вознаграждение?
      <template #footer>
        <CButton
          color="danger"
          @click="removeReward(true)"
        >
          Удалить
        </CButton>
        <CButton
          color="light"
          @click="removeReward(false)"
        >
          Отмена
        </CButton>
      </template>
    </CModal>
  </div>
</template>

<script>
import tableDragger from 'table-dragger/src/index.js'
import arrayMove from 'array-move'

export default {
  name: 'ProductRewards',
  props: {
    productId: Number,
    rewards: Array,
    userRewards: Array
  },
  data() {
    return {
      fields: [
        { key: 'title', label: 'Название', _style: 'min-width: 200px;', _classes: 'draggable' },
        { key: 'description', label: 'Описание', _style: 'min-width: 200px;', _classes: 'draggable' },
        { key: 'actions', label: 'Действия', _style: 'width: 100px;', _classes: 'draggable' }
      ],
      activePage: 1,
      items: [],
      sortedRewards: [],
      addModal: false,
      removeModal: false,
      selectedReward: null,
      rewardOptions: [],
      reordering: false,
      reorderingRewards: [],
      dragger: null
    }
  },
  watch: {
    rewards(value) {
      this.setItems()
      this.sortRewards()
    }
  },
  beforeMount() {
    this.setItems()
    this.sortRewards()
    this.calculateRewardOptions()
  },
  methods: {
    async addReward(modalValue) {
      this.addModal = false
      if (!modalValue) return
      try {
        await this.$inertia.post(
          `/admin/products/${this.productId}/rewards/add`,
          {
            reward_id: this.selectedReward
          },
          { preserveScroll: true }
        )
        this.calculateRewardOptions()
      } catch (e) {
        this.$handleError(e)
      }
    },
    async removeReward(modalValue) {
      this.removeModal = false
      if (!modalValue) return
      try {
        await this.$inertia.delete(`/admin/products/${this.productId}/rewards/${this.selectedReward}`, {
          preserveScroll: true
        })
        this.calculateRewardOptions()
      } catch (e) {
        this.$handleError(e)
      }
    },
    calculateRewardOptions() {
      this.rewardOptions = this.userRewards
        .filter(reward => {
          const productRewardsIds = this.sortedRewards.map(r => r.id)

          return !productRewardsIds.includes(reward.id)
        })
        .map(reward => ({
          label: reward.title,
          value: reward.id
        }))
      this.selectedReward = (this.rewardOptions[0] && this.rewardOptions[0].value) || null
    },
    sortRewards() {
      this.sortedRewards = [...this.items].sort((a, b) => a.pivot.priority - b.pivot.priority)
    },
    reorder() {
      const tableElement = this.$refs.rewardsTable.$el.firstElementChild.firstChild
      this.dragger = tableDragger(tableElement, {
        mode: 'row',
        dragHandler: '.draggable',
        onlyBody: true,
        animation: 300
      })
      this.reordering = true
      this.reorderingRewards = this.sortedRewards.map(r => r.id)
      this.dragger.on('drop', this.reordered)
    },
    reordered(from, to) {
      const fromIndex = from - 1
      const toIndex = to - 1
      this.reorderingRewards = arrayMove(this.reorderingRewards, fromIndex, toIndex)
    },
    setItems() {
      this.items = this.rewards.map(reward => {
        const descriptionWithoutTags = reward.description.replace(/<[^>]+>/g, '')

        return {
          ...reward,
          description: descriptionWithoutTags
        }
      })
    },
    async saveNewOrder() {
      try {
        this.dragger.destroy()
        this.reordering = false
        await this.$inertia.post(
          `/admin/products/${this.productId}/rewards/reorder`,
          {
            rewards: this.reorderingRewards
          },
          { preserveScroll: true }
        )
      } catch (e) {
        this.$handleError(e)
      }
    }
  }
}
</script>
