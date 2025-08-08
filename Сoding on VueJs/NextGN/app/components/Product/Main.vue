<template>
  <div class="page__content">
    <section class="product">
      <div class="container tw-fixed-wrap-page__content-container">
        <div class="container container--small tw-fixed-wrap-page__content-container">
          <profile-back
            title="Продукты для тестирования"
            href-link="/products"
          />
          <div
            class="product__head-container"
            :class="{ 'has-applink': product.applink }"
          >
            <div class="product__head">
              <h2 v-if="product.is_active === 0">
                Продукт неактивен!
              </h2>
              <div
                v-if="product.is_active !== 0"
                class="product__head-title"
              >
                <h2 v-html="product.name_with_br" />
                <div
                  v-if="![79].includes(product.id)"
                  class="product__head-os"
                >
                  <svg :class="`ico ${osIcon}`">
                    <use
                      v-if="isPlatformWeb"
                      xlink:href="@app/assets/img/sprite-mono.svg#ico-mono-web"
                    />
                    <use
                      v-else-if="product.platform === 'ios'"
                      xlink:href="@app/assets/img/sprite-mono.svg#ico-mono-status-apple"
                    />
                    <use
                      v-else
                      xlink:href="@app/assets/img/sprite-mono.svg#ico-mono-android"
                    />
                  </svg>
                  <span>{{ osLabel }}</span>
                </div>
              </div>
              <div
                v-if="product.is_active !== 0 || hasTasks"
                class="product__head-btn"
              >
                <div class="hidden md:block">
                  <button
                    @click.prevent="onDynamicBtnClick"
                    class="btn py-[14px] px-6 text-17 leading-6 font-medium font-compact text-white rounded-lg bg-brand"
                  >
                    <span class="px-2">{{ btnLabel.value }}</span>
                  </button>
                </div>
                <div class="block md:hidden">
                  <button
                    @click.prevent="onDynamicBtnClick"
                    class="btn py-[14px] w-full px-6 text-17 leading-6 font-medium font-compact text-white rounded-lg bg-brand"
                  >
                    <span class="px-2">{{ btnLabel.value }}</span>
                  </button>
                </div>
              </div>
            </div>
            <template v-if="product.is_active !== 0">
              <div class="flex items-center gap-5 mb-6 flex-wrap">
                <div class="flex items-center gap-2 text-xs font-normal font-compact text-active">
                  <svg
                    class="text-active"
                    height="12"
                    width="12"
                  >
                    <circle
                      cx="6"
                      cy="6"
                      r="6"
                      fill="currentColor"
                    />
                  </svg>

                  <span>Активно</span>
                </div>

                <div
                  v-if="dateLabel"
                  class="w-full flex items-start gap-1"
                >
                  <p class="w-full max-w-[160px] text-[#001424] text-17 leading-6 font-normal font-text">
                    Дата тестирования:
                  </p>
                  <p class="w-[calc(100%-160px)] text-xl font-medium leading-6 text-txt font-compact">
                    {{ dateLabel }}
                  </p>
                </div>
              </div>
              <div
                class="px-6 pt-4 pb-6 mb-6 rounded-3xl bg-tertiary lg:grid flex flex-col gap-[5%] grid-cols-[50%_45%]"
              >
                <div class="w-full">
                  <div class="mb-4 md-bar:mb-6">
                    <p class="inline-block font-medium leading-6 text-black text-17 font-compact">
                      Стоимость выполнения всех заданий:
                    </p>
                    <span class="items-center inline-block gap-2">{{ productCoin }}
                      <span class="inline-block w-6 h-6">
                        <!--                    <Coin />-->
                      </span></span>
                  </div>
                  <div
                    v-if="product.files.length > 0"
                    class="mb-3"
                  >
                    <p class="mb-3 font-medium leading-6 text-black text-17 font-compact">
                      Файлы для загрузки
                    </p>
                    <div class="flex items-center flex-wrap gap-3">
                      <a
                        v-for="file in product.files"
                        :key="file.id"
                        :href="`/storage/${file.path}`"
                        :download="`${file.name}.${file.extension}`"
                        class="cursor-pointer py-[14px] px-3 md:px-6 text-17 leading-6 font-medium font-compact text-white rounded-lg bg-brand"
                      >
                        <span class="px-2">{{ file.button_name }}</span>
                      </a>
                    </div>
                  </div>
                  <div class="mb-3">
                    <div class="py-[14px]">
                      <div
                        v-if="maxTestersCount"
                        class="relative flex w-full h-1 overflow-hidden rounded-sm bg-divider/50"
                      >
                        <div
                          class="absolute left-0 top-0 h-1 bg-brand rounded-sm"
                          :style="{
                            width: Math.floor((product.testers_count / maxTestersCount) * 100) + '%',
                          }"
                        />
                      </div>
                    </div>
                    <p class="text-xs font-normal text-greytxt font-compact">
                      Уже тестируют: {{ product.testers_count
                      }}{{ maxTestersCount ? ' / ' + maxTestersCount : '' }}
                    </p>
                    <p class="mb-3 font-medium leading-6 text-black text-17 font-compact">
                      Найдено ошибок: {{ product.error_reports_count }}
                    </p>
                    <p class="font-normal leading-6 text-17 text-greytxt font-text max-w-100">
                      Критичность определяется командой портала после анализа итогов тестирования
                    </p>
                  </div>
                </div>
                <div class="w-full">
                  <BarChart :data="chartData" />
                </div>
              </div>

              <div class="product__about">
                <p
                  class="ql-editor"
                  v-html="product.description"
                />
              </div>
              <div class="product__version">
                <svg :class="`ico ico-mono-status-${product.stage ? 'beta' : 'alfa'}`">
                  <use
                    v-if="product.stage === 0"
                    xlink:href="@app/assets/img/sprite-mono.svg#ico-mono-status-alfa"
                  />
                  <use
                    v-else
                    xlink:href="@app/assets/img/sprite-mono.svg#ico-mono-status-beta"
                  />
                </svg>
                <p v-if="product.versions.length > 0">
                  v. {{ lastVersion }}
                </p>
              </div>
              <template v-if="product.applink && $page.props.user">
                <div
                  v-if="!isPlatformWeb"
                  class="product__applink mb-3 md:mb-0"
                >
                  <button
                    v-if="!$mobileAndTabletCheck()"
                    class="btn py-[14px] px-6 text-17 leading-6 font-medium font-compact text-white rounded-lg bg-brand mb-2 md:mb-8 w-full md:w-auto"
                    @click.prevent="sendMailWithAppLink"
                  >
                    <span class="px-2">Получить приложение</span>
                  </button>
                  <button
                    v-else
                    :href="product.applink"
                    rel="noopener noreferrer"
                    class="btn py-[14px] px-6 text-17 leading-6 font-medium font-compact text-white rounded-lg bg-brand mb-2 md:mb-8 w-full md:w-auto"
                  >
                    Получить приложение
                  </button>
                </div>
                <div
                  v-else
                  class="product__applink"
                >
                  <a
                    :href="product.applink"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="btn"
                  >
                    Перейти на сайт
                  </a>
                </div>
              </template>
            </template>
          </div>
          <template v-if="product.is_active !== 0">
            <div class="product__slider-container rounded-3xl bg-[#E5EEF9] overflow-hidden">
              <swiper
                v-if="product.images.length"
                :options="productSwiperOptions"
                class="swiper"
                :class="{ 'pb-40px': isPlatformWeb }"
              >
                <swiper-slide
                  v-for="image in product.images"
                  :key="image.id"
                >
                  <template v-if="product.platform === 'android'">
                    <div class="products__image products__image--android">
                      <img
                        class="template"
                        src="@app/assets/img/android-template.png"
                        alt="product android"
                      >
                      <div
                        class="products__image-bg"
                        :style="`background-image: url(/storage/images/${image.name})`"
                      />
                      <img
                        class="top"
                        src="@app/assets/img/android-template.png"
                        alt="product android"
                      >
                    </div>
                  </template>
                  <template v-else-if="isPlatformWeb">
                    <div class="products__image products__image--large">
                      <img
                        class="template"
                        src="@app/assets/img/web-template.png"
                        alt="product web"
                      >
                      <div
                        class="products__image-bg"
                        :style="`background-image: url(/storage/images/${image.name})`"
                      />
                      <img
                        class="top"
                        src="@app/assets/img/web-template.png"
                        alt="product web"
                      >
                    </div>
                  </template>
                  <template v-else>
                    <div class="products__image">
                      <img
                        class="template"
                        src="@app/assets/img/iphone-template.png"
                        alt="product iphone"
                      >
                      <div
                        class="products__image-bg"
                        :style="`background-image: url(/storage/images/${image.name})`"
                      />
                      <img
                        class="top"
                        src="@app/assets/img/iphone-template.png"
                        alt="product iphone"
                      >
                    </div>
                  </template>
                </swiper-slide>
              </swiper>
              <swiper
                v-else
                :options="productSwiperOptions"
                class="pt-40px"
                :class="{ 'pb-40px': isPlatformWeb }"
              >
                <swiper-slide>
                  <div
                    v-if="product.platform !== 'web'"
                    class="products__image products__image--android"
                  >
                    <img
                      class="template"
                      src="@app/assets/img/android-template.png"
                      alt="product android"
                    >
                    <div
                      class="products__image-bg"
                      style="background-image: url(/images/phone-placeholder.png)"
                    />
                    <img
                      class="top"
                      src="@app/assets/img/android-template.png"
                      alt="product android"
                    >
                  </div>
                  <div
                    v-else
                    class="products__image products__image--large"
                  >
                    <img
                      class="template"
                      src="@app/assets/img/web-template.png"
                      alt="product web"
                    >
                    <div
                      class="products__image-bg"
                      style="background-image: url(/images/web-placeholder.png)"
                    />
                    <img
                      class="top"
                      src="@app/assets/img/web-template.png"
                      alt="product web"
                    >
                  </div>
                </swiper-slide>
              </swiper>
            </div>
            <p
              v-if="product.rules"
              class="product__sub-title"
            >
              Правила проведения тестирования
            </p>
            <div class="ql-editor product__info">
              <p v-html="product.rules" />
              <a
                href="/pages/promotion"
                style="margin-top: 10px"
                target="_blank"
              >Условия проведения акции</a>
            </div>
            <template v-if="sortedRewards && sortedRewards.length">
              <p class="product__sub-title">
                Вознаграждения
              </p>
              <div class="bonus__slider">
                <swiper :options="rewardSwiperOptions">
                  <swiper-slide
                    v-for="reward in sortedRewards"
                    :key="reward.id"
                  >
                    <span class="bonus__item no-cursor">
                      <div class="bonus__image-container">
                        <img
                          :src="`/storage/images/${reward.image}`"
                          alt=""
                        >
                      </div>
                      <div class="bonus__info">
                        <p class="bonus__title">{{ reward.title }}</p>
                        <p
                          class="bonus__about"
                          v-html="reward.description"
                        />
                      </div>
                    </span>
                  </swiper-slide>
                </swiper>
              </div>
            </template>
            <div class="product__devices-grid">
              <div
                v-if="product.devices && product.devices.length"
                class="product__devices-item"
              >
                <p class="product__sub-title">
                  Необходимые устройства
                </p>
                <div class="product__devices-list">
                  <div
                    v-for="device in product.devices"
                    :key="device.id"
                    class="product__device"
                  >
                    <p>{{ device.brand }} {{ device.model }}</p>
                  </div>
                </div>
                <!-- <a href="#" class="btn btn&#45;&#45;border">Показать все устройства ↓</a> -->
              </div>
              <div
                v-if="product.os_versions && product.os_versions.length"
                class="product__devices-item"
              >
                <p class="product__sub-title">
                  Необходимые ОС
                </p>
                <div class="product__devices-list">
                  <div
                    v-for="version in osVersions"
                    :key="version.id"
                    class="product__device"
                  >
                    <p>{{ getPlatformLabel(version.os) }} {{ version.version }}</p>
                  </div>
                  <div
                    v-for="version in osVersionsFrom"
                    :key="version.id"
                    class="product__device"
                  >
                    <p>От {{ getPlatformLabel(version.os) }} {{ version.version }} и выше</p>
                  </div>
                </div>
                <!-- <a class="btn btn&#45;&#45;border" href="#">Показать все ОС ↓</a> -->
              </div>
            </div>
          </template>
          <div
            ref="sectionTasksRef"
            v-if="product.is_active !== 0"
            class="relative"
          >
            <div
              v-if="isMaxTestersReached"
              class="absolute z-10 flex items-center justify-center font-normal leading-6 text-white -inset-6 backdrop-blur-sm bg-black/50 rounded-3xl font-text text-17"
            >
              <span class="text-center max-w-100">Достигнуто максимальное количество тестировщиков для выполнения заданий</span>
            </div>
            <div
              class="flex flex-col justify-between gap-4 mb-8 md:items-center md:flex-row"
              v-if="hasTasks"
            >
              <h3 class="text-2xl font-medium leading-7 text-black font-wide">
                Задания
              </h3>
              <a
                :href="`#report-modal-${product.id}`"
                js-popup
                class="py-[14px] px-6 text-17 leading-6 font-medium font-compact text-black rounded-lg bg-tertiary"
              >
                <ModalReport
                  :error-report="errorReport"
                  :product="product"
                  :user-devices="devices"
                  :device-type="product.platform === 'web' ? 'web_device' : 'device'"
                  :criticality-bugs="criticalityBugs"
                  :type-bugs="typeBugs"
                />
                <span class="px-2">Отчет об ошибке</span>
              </a>
            </div>

            <div class="flex flex-col grid-cols-4 gap-6 md:grid">
              <a
                v-for="(task, index) in sortedTasks"
                :key="index"
                @click="onTaskClick($event)"
                :disabled="isMaxTestersReached || !isDeviceAdded || !isPersonalInfoCompleted"
                class="flex flex-col gap-2.5 rounded-2xl bg-tertiary py-3 px-4"
                :href="taskId(task)"
                js-popup
              >
                <div class="flex items-center justify-between w-full">
                  <h4
                    :class="{
                      'text-greytxt': isMaxTestersReached || !isDeviceAdded || !isPersonalInfoCompleted,
                      'text-txt': !(isMaxTestersReached || !isDeviceAdded || !isPersonalInfoCompleted),
                    }"
                    class="font-medium leading-6 text-17 font-wide"
                  >
                    Задание <span>{{ task.id }}</span>
                  </h4>
                  <span
                    v-if="isFinishedTask(task) && !(isMaxTestersReached || !isDeviceAdded || !isPersonalInfoCompleted)"
                    class="px-1 py-0.5 text-xs font-medium leading-6 text-white rounded-md font-compact bg-positive"
                  >Выполнено</span>
                </div>
                <p class="font-normal leading-6 text-greytxt text-17 font-text">
                  Вопросов: <span>{{ task.questions_count }}</span>
                </p>
                <ModalTask
                  v-if="isDeviceAdded"
                  :regions="product.regions"
                  :task="task"
                  :product="product"
                  :user-devices="product.platform === 'web' ? devices.userDevicesWeb : devices.userDevicesMobile"
                  :device-type="product.platform === 'web' ? 'web_device' : 'device'"
                  @set-error-report="setErrorReport($event)"
                />
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import Swiper from '@app/components/Wrappers/Swiper'
import PromotionUrl from '../../elements/PagesUrls/PromotionUrl'
import ProfileBack from '../Shared/ProfileBack.vue'
import { mapGetters } from 'vuex'
import Layout from '@admin/containers/TheContainer'
import BarChart from '../Products/BarChart.vue'
import BaseModal from '../Modals/BaseModal.vue'
import ModalTask from '@app/components/Modals/Task/Index'
import ModalReport from '@app/components/Modals/Report'
import 'swiper/css/swiper.css'

export default {
  name: 'ProductMain',
  components: {
    BaseModal,
    BarChart,
    PromotionUrl,
    ProfileBack,
    Swiper,
    ModalTask,
    ModalReport
  },

  layout: Layout,

  props: {
    devices: [Array, Object],
    criticalityBugs: Array,
    typeBugs: Array,
    product: {
      required: true,
      type: Object
    }
  },
  data() {
    return {
      flagDynamicBtn: null,
      errorReport: {},
      chartData: {
        labels: ['33', '', '', ''],
        values: [18, 55, 90, 45]
      },
      pending: false,
      sortedRewards: [],
      sortedImages: [],
      sortedTasks: [],
      osVersions: [],
      osVersionsFrom: [],
      rewardSwiperOptions: {
        slidesPerView: 2,
        spaceBetween: 0,
        speed: 600,
        loop: false,
        pagination: {
          el: '.swiper-pagination',
          clickable: true
        },
        autoplay: false,
        navigation: {
          nextEl: '.swiper-arrow--next',
          prevEl: '.swiper-arrow--prev'
        },
        breakpoints: {
          640: {
            slidesPerView: 1,
            spaceBetween: 15,
            loop: false
          },
          992: {
            slidesPerView: 2,
            spaceBetween: 20,
            loop: false
          }
        }
      }
    }
  },
  watch: {
    'btnLabel.flagForDynBtn': {
      handler(val) {
        this.flagDynamicBtn = val || 0
      },
      immediate: true,
      deep: true
    }
  },
  computed: {
    ...mapGetters({
      isAuth: 'auth/isAuth'
    }),
    hasTasks() {
      return this.product.tasks > 0;
    },
    isDeviceAdded() {
      return this.isUserOsesCorrect
    },
    btnLabel() {
      if (
        (!this.$page.props.user.register_completed && !this.isDeviceAdded) ||
        (this.$page.props.user.register_completed &&
        !this.$page.props.user.is_personal_info_completed && !this.isDeviceAdded) ||
        (this.$page.props.user.register_completed && this.isDeviceAdded &&
        !this.$page.props.user.is_personal_info_completed)
      ) {
        return { value: 'Заполнить информацию о себе', flagForDynBtn: 0 }
      } else if (
        this.$page.props.user.register_completed &&
        this.$page.props.user.is_personal_info_completed &&
        !this.isDeviceAdded
      ) {
        return { value: 'Добавить устройство с подходящей ОС', flagForDynBtn: 1 }
      } else {
        return { value: 'Перейти к заданиям', flagForDynBtn: 2 }
      }
    },
    isBigScreen() {
      return window.innerWidth > 768
    },
    productCoin() {
      return this.product.price_for_all_tasks_coins ?? 0
    },
    maxTestersCount() {
      return this.product.max_testers_count
    },
    productImageLenght() {
      return this.product.images.length > 2
        ? {
          el: '.swiper-pagination',
          clickable: true
        }
        : false
    },
    isPlatformWeb() {
      return this.product.platform === 'web'
    },
    productSwiperOptions() {
      if (this.isPlatformWeb) {
        return {
          slidesPerView: 1,
          spaceBetween: 0,
          speed: 600,
          autoplay: false,
          pagination: {
            el: '.swiper-pagination',
            clickable: true
          },
          navigation: {
            nextEl: '.swiper-arrow--next',
            prevEl: '.swiper-arrow--prev'
          },
          loop: true,
          loopedSlides: 2,
          touchRatio: 1,
          watchSlidesVisibility: true,
          breakpoints: {
            0: {
              slidesPerView: 1,
              loop: true,
              loopedSlides: 2,
              touchRatio: 1,
              watchSlidesVisibility: true
            },
            540: {
              slidesPerView: 1,
              loop: false,
              touchRatio: 1,
              watchSlidesVisibility: true,
              pagination: this.productImageLenght
            }
          }
        }
      }

      return {
        speed: 600,
        loop: false,
        autoplay: false,
        slidesPerView: 2.5,
        spaceBetween: 50,
        breakpoints: {
          874: {
            slidesPerView: 2.5,
            spaceBetween: 50
          },
          0: {
            slidesPerView: 1.5,
            spaceBetween: 20
          },
        },
      }
    },
    isBtnDisabled() {
      return !this.$page.props.user || !this.$page.props.user.email_verified_at
    },
    isPersonalInfoCompleted() {
      return this.$page.props.user.is_personal_info_completed;
    },
    btnTitle() {
      if (!this.$page.props.user || !this.$page.props.user.email_verified_at) {
        return 'Вы не подтвердили email'
      }
      // if (!this.isGuidePassed) {
      //    return "Подготовиться к тестированию";
      // }
      if (!this.isUserOsesCorrect) {
        return 'Добавить устройство с подходящей ОС'
      }

      return 'Перейти к заданиям'
    },
    isUserOsesCorrect() {
      return (
        this.$page.props.user &&
        this.$page.props.user.oses &&
        this.$page.props.user.oses.map(os => os.toLowerCase()).includes(this.product.platform)
      )
    },
    isGuidePassed() {
      return this.$page.props.guide_enabled ? this.$page.props.user && this.$page.props.user.guide_passed : true
    },
    isBetaTester() {
      return this.$page.props.user && this.$page.props.roles.includes('beta')
    },
    isTesterAlreadyParticipated() {
      if (!this.isUserOsesCorrect) {
        return false
      }

      return (
        this.$page.props.user && this.product.testers.map(tester => tester.id).includes(this.$page.props.user.id)
      )
    },
    isMaxTestersReached() {
      return this.product.testers_count === this.maxTestersCount
    },
    lastVersion() {
      return this.product.versions[this.product.versions.length - 1].number
    },
    osIcon() {
      switch (this.product.platform) {
      case 'ios':
        return 'ico-mono-status-apple'
      case 'android':
        return 'ico-mono-android'
      case 'web':
        return 'ico-mono-web'
      }
    },
    osLabel() {
      switch (this.product.platform) {
      case 'ios':
        return 'iOS'
      case 'android':
        return 'Android'
      case 'web':
        return 'Web'
      }
    },
    statusClass() {
      switch (this.product.is_active) {
      case 0:
        return 'notactive'
      case 1:
        return 'waiting'
      case 2:
        return 'active'
      }
    },
    statusLabel() {
      switch (this.product.is_active) {
      case 0:
        return 'Неактивно'
      case 1:
        return 'В ожидании'
      case 2:
        return 'Активно'
      }
    },
    dateLabel() {
      if (!this.product.date_start && !this.product.date_end) {
        return 'не указана'
      }

      if (!this.product.date_start) {
        return `до ${this.$moment(this.product.date_end).format('D MMMM YYYY')}`
      }

      if (!this.product.date_end) {
        return `от ${this.$moment(this.product.date_start).format('D MMMM YYYY')}`
      }

      return `с ${this.$moment(this.product.date_start).format('D MMMM YYYY')} по ${this.$moment(
        this.product.date_end
      ).format('D MMMM YYYY')}`
    }
  },
  beforeMount() {
    this.sortedRewards = [...this.product.rewards].sort((a, b) => a.pivot.priority - b.pivot.priority)
    this.sortedTasks = [...this.product.tasks].sort((a, b) => a.id - b.id)
    this.sortedImages = [...this.product.images].sort(image => image.default * -1)

    this.osVersions = [...this.product.os_versions.filter(v => v.pivot && v.pivot.from === 0)].sort(
      (a, b) => a.id - b.id
    )
    this.osVersionsFrom = [...this.product.os_versions.filter(v => v.pivot && v.pivot.from === 1)].sort(
      (a, b) => a.id - b.id
    )
  },
  methods: {
    taskId(task) {
      if (!this.isDeviceAdded || !this.$page.props.user.is_personal_info_completed) return

      return `#task-modal-${task.id}`
    },
    onTaskClick() {
      if (!this.isDeviceAdded || !this.$page.props.user.is_personal_info_completed) {
        $('html, body').animate({ scrollTop: 0 }, 'slow')
      }
    },
    isFinishedTask(task) {
      return task.replies.length > 0 ? task.replies[0].is_finished : false
    },
    setErrorReport(payload) {
      this.errorReport = payload
    },
    onMouseOver() {
      if (this.btnTitle) {
        this.btnLabel.value = this.btnTitle
      }
    },
    getPlatformLabel(value) {
      const options = [
        { value: 'android', label: 'Android' },
        { value: 'ios', label: 'iOS' },
        { value: 'web', label: 'Web' }
      ]
      const option = options.find(option => option.value === value)

      return option.label
    },
    async sendMailWithAppLink() {
      if (this.isBtnDisabled || this.pending) return
      this.pending = true
      try {
        await this.$inertia.post('/send-mail-with-app-link', {
          product_id: this.product.id
        })
      } catch (e) {
        this.$handleError(e)
      } finally {
        this.pending = false
      }
    },
    async onDynamicBtnClick() {
      // if (this.isBtnDisabled || this.pending) return;
      if (!this.$page.props.user || this.pending) return
      try {
        const canProceed = await this.checkConditionsForDynamicBtn()
        if (this.flagDynamicBtn === 0) {
          if (canProceed && canProceed === true) {
            this.$inertia.visit('/products/show-register')
          }
        }
        if (this.flagDynamicBtn === 1) {
          if (canProceed && canProceed === true) {
            this.$inertia.visit('/products/show-register?step=3')
          }
        }
        if (this.flagDynamicBtn === 2) {
          let top = this.$refs.sectionTasksRef.getBoundingClientRect().top
          window.scrollBy({ top: top - 100, behavior: 'smooth' })

          return
        }
      } catch (e) {
        this.$handleError(e)
      } finally {
        this.pending = false
      }
    },
    async checkConditionsForDynamicBtn() {
      let iswflag = false
      if (!this.isGuidePassed) {
        this.pending = true
        await this.$inertia.post('/send-to-guides', {
          product_id: this.product.id
        })
        iswflag = true

        return false
      } else if (this.isGuidePassed && !iswflag) {
        return true
      }
      //  else if (!this.isUserOsesCorrect) {
      //     await this.$inertia.post('/send-to-devices', {
      //        product_id: this.product.id,
      //        platform: this.getPlatformLabel(this.product.platform),
      //     });
      //     iswflag = true;
      //     return false;
      //  }
    },
    scrollTo(view) {
      view?.scrollIntoView({ behavior: 'smooth' })
    }
  }
}
</script>

<style>
.product__back {
  position: relative;
  top: inherit;
  left: inherit;
  margin-top: 0;
  margin-bottom: 20px;
}

.truncate {
  text-overflow: ellipsis;
  overflow: hidden;
  white-space: nowrap;
}

.product__slider-container {
  padding-right: 0 !important;
  padding-top: 0 !important;
}

.pt-40px {
  padding-top: 40px;
}

.pb-40px {
  padding-bottom: 40px;
}

@media only screen and (max-width: 760px) {
  .product__slider-container {
    padding-left: 10px !important;
  }
}
</style>

<style lang="scss" scoped>
::v-deep .ql-align-justify {
  text-align: unset !important;
}
.swiper {
  padding: 50px 50px 0 50px;
}
.swiper-wrapper {
  height: 385px;
  overflow: hidden;
}

.btn-disabled {
  background: #ccc;
}
@media screen and (max-width: 1125px) {
  .swiper-wrapper {
    height: 300px;
  }
}

@media screen and (max-width: 760px) {
  .swiper-wrapper {
    height: 225px;
  }
}

.product__head-btn .btn-disabled {
  background: #ccc !important;
}

.product__applink .btn {
  margin-top: 23px;
}

.product__back {
  position: unset;
}

@media screen and (max-width: 760px) {
  .product__applink .btn {
    font-size: 11px;
    position: relative;
  }
}

.btn {
  border: 1px solid #cccccc;
}

.btn btn-disabled:hover {
  border: 1px solid #cccccc;
}

.files--list {
  margin: 15px 0 15px 0;

  .btn {
    position: relative;
    margin-right: 10px;
    top: unset;
    bottom: unset;
    left: unset;
    right: unset;

    @media only screen and (max-width: 760px) {
      margin-bottom: 10px;
    }
  }
}

.no-cursor {
  cursor: default;
}
</style>

<style scoped>
@media only screen and (min-width:1200px) {
  .container--small {
    padding-left: 0;
    padding-right: 0;
  }
}
</style>
