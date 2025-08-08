<template>
   <CRow class="justify-content-center">
      <CCol col="12">
         <CCard>
            <CCardHeader>
               <CCardTitle class="mb-0">Вознаграждение «{{ reward.title }}»</CCardTitle>
            </CCardHeader>
            <CCardBody>
               <CListGroup>
                  <CCardText class="font-weight-bold">Описание:</CCardText>
                  <CListGroupItem v-html="reward.description"></CListGroupItem>
               </CListGroup>
               <CListGroup class="mt-3">
                  <CCardText class="font-weight-bold">Тип:</CCardText>
                  <CListGroupItem>{{ type }}</CListGroupItem>
               </CListGroup>
               <CListGroup class="mt-3">
                  <CCardText class="font-weight-bold">Всего выдано:</CCardText>
                  <CListGroupItem>{{ reward.given_count }}</CListGroupItem>
               </CListGroup>
               <template v-if="isTypePublic">
                  <CListGroup class="mt-3">
                     <CCardText class="font-weight-bold">Выдано:</CCardText>
                     <RewardTesters :testers="reward.testers" />
                  </CListGroup>
                  <CListGroup class="mt-3">
                     <CCardText class="font-weight-bold">Промокод:</CCardText>
                     <CListGroupItem>{{ commonPromocode }}</CListGroupItem>
                  </CListGroup>
               </template>
               <CListGroup v-else-if="isTypePrivate" class="mt-3">
                  <CCardText class="font-weight-bold">Промокоды:</CCardText>
                  <RewardPromocodes :promocodes="reward.promocodes" />
               </CListGroup>
               <CListGroup class="mt-3">
                  <CCardText class="font-weight-bold">Изображение:</CCardText>
                  <CImg :src="`/storage/images/${reward.image}`" thumbnail width="75%" align="center" />
               </CListGroup>
               <CButtonGroup class="mt-4">
                  <CButton v-if="canEdit" @click="$inertia.visit(`/admin/rewards/${reward.id}/edit`)" color="success">
                     Изменить
                  </CButton>
                  <CButton v-if="isTypePublic" @click="copyModal = true" color="warning">Копировать</CButton>
                  <CButton @click="$inertia.visit('/admin/rewards')" color="light">К вознаграждениям</CButton>
               </CButtonGroup>
               <CButton v-if="isTypePrivate" @click="addPromocodeModal = true" color="info" class="float-right mt-4">
                  Добавить промокоды
               </CButton>
            </CCardBody>
         </CCard>
      </CCol>
      <CModal color="info" centered :show.sync="addPromocodeModal">
         <template #header>
            <h6 class="modal-title">Добавить промокоды</h6>
         </template>
         <CTextarea v-model="newPromocode" placeholder="Введите промокоды через запятую"></CTextarea>
         <template #footer>
            <CButton @click="addPromocode(true)" color="info">Добавить</CButton>
            <CButton @click="addPromocode(false)" color="light">Отмена</CButton>
         </template>
      </CModal>
      <CModal color="warning" centered :show.sync="copyModal">
         <template #header>
            <h6 class="modal-title">Копировать вознаграждение</h6>
         </template>
         <CInput
            v-model="$v.copiedData.title.$model"
            label="Заголовок"
            horizontal
            :isValid="$v.copiedData.title.$dirty ? !$v.copiedData.title.$error : null"
            invalidFeedback="Введите заголовок"
         />
         <CTextarea
            v-model="$v.copiedData.description.$model"
            label="Описание"
            rows="5"
            horizontal
            :isValid="$v.copiedData.description.$dirty ? !$v.copiedData.description.$error : null"
            invalidFeedback="Введите описание"
         />
         <CInput
            v-model="$v.copiedData.code.$model"
            label="Промокод"
            horizontal
            :isValid="$v.copiedData.code.$dirty ? !$v.copiedData.code.$error : null"
            invalidFeedback="Введите промокод"
         />
         <template #footer>
            <CButton @click="copy(true)" color="warning">Копировать</CButton>
            <CButton @click="copy(false)" color="light">Отмена</CButton>
         </template>
      </CModal>
   </CRow>
</template>

<script>
import { required } from 'vuelidate/lib/validators';
import Layout from '@admin/containers/TheContainer';
import RewardTesters from './RewardTesters';
import RewardPromocodes from './RewardPromocodes';

export default {
   name: 'RewardsShow',
   layout: Layout,
   props: {
      reward: Object,
   },
   components: {
      RewardTesters,
      RewardPromocodes,
   },
   data() {
      return {
         addPromocodeModal: false,
         newPromocode: '',
         copyModal: false,
         copiedData: {
            title: '',
            description: '',
            code: '',
         },
      };
   },
   computed: {
      canEdit() {
         if (!this.$page.props || !this.$page.props.user) {
            return false;
         }

         if (this.$page.props.roles && this.$page.props.roles.includes('admin')) {
            return true;
         }

         if (this.$page.props.user.id && this.$page.props.user.id === this.reward.user_id) {
            return true;
         }

         return false;
      },
      type() {
         switch (this.reward.type) {
            case 'public':
               return 'Общий промокод';
            case 'private':
               return 'Личный промокод';
            case 'gift':
               return 'Ценные призы';
         }
      },
      isTypePublic() {
         return this.reward.type === 'public';
      },
      isTypePrivate() {
         return this.reward.type === 'private';
      },
      commonPromocode() {
         if (!this.isTypePublic || !this.reward.promocodes) return;
         const promocode = this.reward.promocodes.find(promocode => promocode.is_public);
         if (!promocode) return;
         return promocode.code;
      },
   },
   methods: {
      async copy(modalValue) {
         this.copyModal = false;
         if (!modalValue || this.$v.copiedData.$invalid) return;
         try {
            await this.$inertia.post(`/admin/rewards/${this.reward.id}/copy`, this.copiedData);
         } catch (e) {
            this.$handleError(e);
         }
      },
      async addPromocode(modalValue) {
         this.addPromocodeModal = false;
         if (!modalValue) return;
         try {
            await this.$inertia.post(
               `/admin/rewards/${this.reward.id}/add-promocodes`,
               {
                  reward_id: this.reward.id,
                  code: this.newPromocode,
               },
               {
                  preserveScroll: true,
               }
            );
            this.newPromocode = '';
         } catch (e) {
            this.$handleError(e);
         }
      },
   },
   validations: {
      copiedData: {
         title: {
            required,
         },
         description: {
            required,
         },
         code: {
            required,
         },
      },
   },
   beforeMount() {
      this.copiedData.title = this.reward.title;
      this.copiedData.description = this.reward.description;
   },
};
</script>
