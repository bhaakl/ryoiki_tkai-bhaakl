<template>
   <CRow>
      <CCol col="12">
         <CCard>
            <CCardHeader>
               <h4>Вознаграждения</h4>
            </CCardHeader>
            <CCardBody>
               <CDataTable
                  hover
                  striped
                  clickable-rows
                  column-filter
                  @row-clicked="reward => $inertia.visit(`/admin/rewards/${reward.id}`)"
                  :items="items"
                  :fields="fields"
                  :items-per-page="30"
                  :active-page="activePage"
                  :pagination="{ doubleArrows: false, align: 'center' }"
                  :noItemsView="{ noResults: 'Нет подходящих вариантов', noItems: 'Вознаграждений пока нет' }"
               >
                  <template #index="{ index }">
                     <td>{{ index + 1 }}</td>
                  </template>
                  <template #description="{ item: { description }}">
                     <td v-html="description"></td>
                  </template>
               </CDataTable>

               <CCol col="6" sm="4" md="2" class="mt-4 mb-3 ml-auto mb-xl-0">
                  <CButton
                     @click="$inertia.visit('/admin/rewards/create')"
                     block
                     color="success"
                     style="min-width: 90px"
                  >
                     Добавить
                  </CButton>
               </CCol>
            </CCardBody>
         </CCard>
      </CCol>
   </CRow>
</template>

<script>
import Layout from '@admin/containers/TheContainer';

export default {
   name: 'RewardsIndex',
   layout: Layout,
   props: {
      rewards: Array,
   },
   data() {
      return {
         fields: [
            { key: 'index', label: '№', sorter: false, filter: false },
            { key: 'title', label: 'Название', _classes: 'font-weight-bold', _style: 'min-width: 200px;' },
            { key: 'typeLabel', label: 'Тип', _style: 'min-width: 150px;' },
            // { key: 'description', label: 'Описание', _style: 'min-width: 200px;' },
            { key: 'givenLabel', label: 'Выдано', _style: 'min-width: 100px;' },
         ],
         activePage: 1,
         items: [],
      };
   },
   methods: {
      getTypeLabel(value) {
         const typeOptions = [
            { value: 'public', label: 'Общий промокод' },
            { value: 'private', label: 'Личный промокод' },
            { value: 'gift', label: 'Ценные призы' },
         ];
         const option = typeOptions.find(option => option.value == value);
         return option.label;
      },
   },
   beforeMount() {
      this.items = this.rewards.map(reward => ({
         ...reward,
         typeLabel: this.getTypeLabel(reward.type),
         givenLabel: reward.type === 'private' ? `${reward.given_count} из ${reward.promocodes.length}` : reward.given_count,
      }));
   },
};
</script>
