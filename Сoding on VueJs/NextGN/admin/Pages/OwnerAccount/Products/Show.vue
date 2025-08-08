<template>
   <CRow class="justify-content-center">
      <send-report-to-email :productId="product.id" ref="reportModal"/>
      <send-reward-document :productId="product.id" ref="uploadRewardModal"/>
      <CCol col="12">
         <CCard>
            <CCardHeader class="d-flex justify-content-between align-items-end">
               <div>
                <CButton size="sm" color="dark" @click.prevent="$refs.reportModal.show()">Получить отчет на E-mail</CButton>
                <CButton v-if="product.is_active === 0" @click.prevent="$refs.uploadRewardModal.show()" title="Загрузите файл Excel с проставленными критичностями по задачам. Они будут автоматически учтены, и тестировщики получат вознаграждение" size="sm" color="dark">Обработать вознаграждение</CButton>
               </div>
               <CButton v-if="product.draft && checkViewDraftControlBtn"
                        size="sm"
                        color="danger"
                        @click.prevent="$inertia.visit(`/admin/products/${product.id}/draft`)">Доступ к черновику продукта
               </CButton>
               <CButton v-if="product.closed_testing && checkViewDraftControlBtn"
                        size="sm"
                        color="success"
                        @click.prevent="$inertia.visit(`/admin/products/${product.id}/closed-testing`)">Участники закрытого тестирования
               </CButton>
            </CCardHeader>
            <CCardHeader class="d-flex justify-content-between align-items-end">
               <CCardTitle class="mb-0" v-html="product.name_with_br"></CCardTitle>
               <div>
                  <CBadge v-if="product.draft" color="info">
                     Черновик
                  </CBadge>
                  <CBadge :color="getBadgeColor(product.is_active)" class="mb-1 mr-1">
                     {{ getStatusText(product.is_active) }}
                  </CBadge>
               </div>
            </CCardHeader>
            <CCardBody>
               <CListGroup v-if="product.short_description">
                  <CCardText class="font-weight-bold">Краткое описание:</CCardText>
                  <CListGroupItem>{{ product.short_description }}</CListGroupItem>
               </CListGroup>
               <CListGroup :class="{ 'mt-3': product.short_description }">
                  <CCardText class="font-weight-bold">Описание:</CCardText>
                  <CListGroupItem v-html="product.description"></CListGroupItem>
               </CListGroup>
               <CListGroup class="mt-3">
                  <CCardText class="font-weight-bold">Платформа:</CCardText>
<!--                  <CListGroupItem>{{ getPlatformLabel(product.platform) }}</CListGroupItem>-->
               </CListGroup>
               <CListGroup v-if="product.rules" class="mt-3">
                  <CCardText class="font-weight-bold">Правила проведения тестирования:</CCardText>
                  <CListGroupItem v-html="product.rules"></CListGroupItem>
               </CListGroup>
               <CListGroup v-if="product.applink" class="mt-3">
                  <CCardText v-if="!isPlatformWeb" class="font-weight-bold">Ссылка на приложение:</CCardText>
                  <CCardText v-else class="font-weight-bold">Ссылка на сайт:</CCardText>
                  <CListGroupItem>
                     <a :href="product.applink" target="_blank" rel="noopener noreferrer" class="text-default">
                        {{ product.applink }}
                     </a>
                  </CListGroupItem>
               </CListGroup>
               <CListGroup>
                  <CCardText class="font-weight-bold">Награда за выполнение всех заданий(coins):</CCardText>
                  <CListGroupItem>{{ product.price_for_all_tasks_coins }}</CListGroupItem>
               </CListGroup>
               <CListGroup>
                  <CCardText class="font-weight-bold">Награда за выполнение всех заданий(cashback):</CCardText>
                  <CListGroupItem>{{ product.price_for_all_tasks_cashback }}</CListGroupItem>
               </CListGroup>
               <CListGroup class="mt-3">
                  <CCardText class="font-weight-bold">
                     Даты проведения тестирования:
                     <span class="font-weight-normal">
                        {{ dates }}
                     </span>
                  </CCardText>
               </CListGroup>
               <CListGroup class="mt-3">
                  <CCardText class="font-weight-bold">
                     Стадия тестирования:
                     <span class="font-weight-normal">
                        {{ stage }}
                     </span>
                  </CCardText>
               </CListGroup>
              <CListGroup class="mt-3">
                <CCardText class="font-weight-bold">
                  Максимальное количество тестировщиков:
                  <span class="font-weight-normal">
                        {{ maxTestersCount }}
                     </span>
                </CCardText>
              </CListGroup>
              <CListGroup class="mt-3">
                  <CCardText class="font-weight-bold">
                     Файлы проекта (всего: {{ product.files && product.files.length }})
                  </CCardText>
                  <ProductFiles :productId="product.id" :files="product.files" />
               </CListGroup>
               <CListGroup class="mt-3">
                  <CCardText class="font-weight-bold">
                     Версии продукта (всего: {{ product.versions && product.versions.length }})
                  </CCardText>
                  <ProductVersions :productId="product.id" :versions="product.versions" />
               </CListGroup>
               <CListGroup v-if="!isPlatformWeb" class="mt-3">
                  <CCardText class="font-weight-bold">
                     Устройства (всего: {{ product.devices && product.devices.length }})
                  </CCardText>
                  <ProductDevices
                     :productId="product.id"
                     :platform="product.platform"
                     :devices="product.devices"
                     :brands="brands"
                  />
               </CListGroup>
               <CListGroup v-if="!isPlatformWeb" class="mt-3">
                  <CCardText class="font-weight-bold">
                     Версии ОС (всего: {{ product.os_versions && product.os_versions.length }})
                  </CCardText>
                  <ProductOSVersions
                     :productId="product.id"
                     :versions="product.os_versions"
                     :versionOptions="osVersions"
                  />
               </CListGroup>
               <CListGroup class="mt-3">
                  <CCardText class="font-weight-bold">
                     Вознаграждения (всего: {{ product.rewards && product.rewards.length }})
                  </CCardText>
                  <ProductRewards :productId="product.id" :rewards="product.rewards" :userRewards="userRewards" />
               </CListGroup>
               <CListGroup class="mt-3">
                  <CCardText class="font-weight-bold">
                     Тестировщики (всего: {{ product.testers && product.testers.length }})
                  </CCardText>
                  <ProductTesters :product_id="product.id" :testers="product.testers" :rewards="product.rewards" />
               </CListGroup>
               <CListGroup v-if="product.images.length" class="mt-3">
                  <CCardText class="font-weight-bold">
                     Изображения (всего: {{ product.images && product.images.length }})
                  </CCardText>
                  <ProductImages :productId="product.id" :images="product.images" />
               </CListGroup>
               <CButton
                  @click="$inertia.visit(`/admin/products/${product.id}/edit`)"
                  color="success"
                  class="float-left mt-3"
               >
                  Изменить
               </CButton>
               <CButtonGroup class="float-right mt-3">
                  <CButton @click="copyModal = true" color="warning">Копировать</CButton>
                  <CButton
                     @click="$inertia.visit(`/admin/tasks/create`, { data: { product_id: product.id } })"
                     color="success"
                  >
                     Добавить задание
                  </CButton>
                  <CButton @click="$inertia.visit(`/admin/error-reports/by-product/${product.id}`)" color="danger">
                     Отчеты
                  </CButton>
               </CButtonGroup>
            </CCardBody>
            <CCardFooter>
               <CButton @click="$inertia.visit('/admin/products')" color="light">К продуктам</CButton>
               <CButton
                  type="a"
                  :href="product.link"
                  color="info"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="float-right"
               >
                  Посмотреть на сайте
               </CButton>
            </CCardFooter>
         </CCard>
      </CCol>
      <CModal color="warning" centered :show.sync="copyModal">
         <template #header>
            <h6 class="modal-title">Копирование продукта</h6>
         </template>
         Скопировать продукт вместе с заданиями ?
         <template #footer>
            <CButton @click="copy(true)" color="warning">Копировать</CButton>
            <CButton @click="copy(false)" color="light">Отмена</CButton>
         </template>
      </CModal>
   </CRow>
</template>

<script>
import Layout from '@admin/containers/TheContainer';
import ProductVersions from './ProductVersions';
import ProductFiles from './ProductFiles';
import ProductDevices from './ProductDevices';
import ProductOSVersions from './ProductOSVersions';
import ProductTesters from './ProductTesters';
import ProductRewards from './ProductRewards';
import ProductImages from './ProductImages';
import SendReportToEmail from './SendReportToEmail';
import SendRewardDocument from './SendRewardDocument.vue';

export default {
   name: 'ProductsShow',
   layout: Layout,
   props: {
      product: Object,
      userRewards: Array,
      osVersions: Array,
      brands: Array,
   },
   components: {
      ProductVersions,
      ProductFiles,
      ProductDevices,
      ProductOSVersions,
      ProductTesters,
      ProductRewards,
      ProductImages,
      SendReportToEmail,
      SendRewardDocument,
   },
   data() {
      return {
         copyModal: false
      };
   },
   computed: {
      dates() {
         const start = this.$moment(this.product.date_start).format('D MMMM YYYY');
         const end = this.$moment(this.product.date_end).format('D MMMM YYYY');
         return `${start} – ${end}`;
      },
      stage() {
         return this.product.stage ? 'Beta' : 'Alpha';
      },
      maxTestersCount() {
        return this.product.max_testers_count ? this.product.max_testers_count : 'Нет'
      },
      isPlatformWeb() {
         return this.product.platform === 'web';
      },
      checkViewDraftControlBtn() {
         const roleToCheck = "view_draft_control_btn";

         if (this.$page.props.user.permissions.includes(roleToCheck)) {
            return true
         }

         return false
      }
   },
   methods: {
      async copy(modalValue) {
         this.copyModal = false;
         if (!modalValue) return;
         try {
            await this.$inertia.post(`/admin/products/${this.product.id}/copy`, {
               product_id: this.product.id,
            });
         } catch (e) {
            this.$handleError(e);
         }
      },
      getPlatformLabel(value) {
         const options = [
            { label: 'Android', value: 'android' },
            { label: 'iOS', value: 'ios' },
            { label: 'Web Desktop', value: 'web' },
            { label: 'Web Android', value: 'web_android' },
            { label: 'Web iOS', value: 'web_ios' },
            { label: 'Smart TV', value: 'smart_tv' },
         ];
         const option = options.find(option => option.value === value);
         return option.label;
      },
      getStatusText(status) {
         switch (status) {
            case 0:
               return 'Неактивно';
            case 1:
               return 'В ожидании';
            case 2:
               return 'Активно';
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
};
</script>
