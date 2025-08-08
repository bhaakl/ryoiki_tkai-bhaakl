<template>
  <div class="products__el">
    <div
      class="products__item"
      :class="{ 'products__item--smart-tv': product.platform === 'smart_tv' }"
    >
      <div
        class="products__image-container"
        :style="{ 'background-color': product.background_color }"
      >
        <p
          v-if="product.status === 2"
          class="products__status products__status--active"
        >
          Активно <span> {{ formatDaysLeft(product) }}</span>
        </p>
        <p
          v-if="product.status === 1"
          class="products__status products__status--waiting"
        >
          В ожидании
        </p>
        <p
          v-if="product.status === 0"
          class="products__status products__status--notactive"
        >
          Неактивно
        </p>
        <div class="products__test">
          <span
            v-if="product.stage === 0"
            class="products__test-item"
            title="Альфа тестирование"
            js-tooltip
          >
            <svg class="ico ico-mono-status-alfa">
              <use xlink:href="@app/assets/img/sprite-mono.svg#ico-mono-status-alfa" />
            </svg>
          </span>
          <span
            v-if="product.stage === 1"
            class="products__test-item"
            title="Бета тестирование"
            js-tooltip
          >
            <svg class="ico ico-mono-status-beta">
              <use xlink:href="@app/assets/img/sprite-mono.svg#ico-mono-status-beta" />
            </svg>
          </span>
          <span
            v-for="platform in platforms"
            v-if="product.platform === platform.value"
            :key="platform.value"
            class="products__test-item"
            :title="platform.title"
            js-tooltip
          >
            <svg
              v-if="platform.value == 'web_ios'"
              class="ico ico-mono-status-alfa"
            >
              <use xlink:href="@app/assets/img/sprite-mono.svg#ico-mono-status-apple" />
            </svg>

            <svg
              v-if="platform.value == 'web_android'"
              class="ico ico-mono-status-alfa"
            >
              <use xlink:href="@app/assets/img/sprite-mono.svg#ico-mono-android" />
            </svg>
            <template
              v-if="platform.value === 'smart_tv'"
              class="ico ico-mono-status-alfa"
            >
              <img :src="iconPathSmartTv()">
            </template>

            <svg
              v-else
              class="ico"
              :class="platform.iconClass"
            >
              <use
                v-if="platform.value === 'ios'"
                xlink:href="@app/assets/img/sprite-mono.svg#ico-mono-status-apple"
              />
              <use
                v-if="platform.value === 'android'"
                xlink:href="@app/assets/img/sprite-mono.svg#ico-mono-android"
              />
              <use
                v-if="platform.value.includes('web')"
                xlink:href="@app/assets/img/sprite-mono.svg#ico-mono-web"
              />
            </svg>
          </span>
        </div>
        <span
          class="products__image"
          :class="{
            'products__image--smart-tv': product.platform === 'smart_tv',
          }"
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
                :style="`background-image: url(${getProductImage(product)})`"
              />
              <img
                class="top"
                src="@app/assets/img/android-template.png"
                alt="product android"
              >
            </div>
          </template>
          <template v-else-if="product.platform === 'web'">
            <div class="products__image">
              <img
                class="template"
                src="@app/assets/img/web-template.png"
                alt="product web"
              >
              <div
                class="products__image-bg products__image-bg--large"
                :style="`background-image: url(${getProductImage(product)})`"
              />
              <img
                class="top"
                src="@app/assets/img/web-template.png"
                alt="product web"
              >
            </div>
          </template>
          <template v-else-if="product.platform === 'smart_tv'">
            <div class="products__image products__image--smart-tv">
              <img
                class="template"
                src="@app/assets/img/smart-template.png"
                alt="product smart"
              >
              <div
                class="products__image-bg"
                :style="`background-image: url(${getProductImage(product)})`"
              />
              <img
                class="top"
                src="@app/assets/img/smart-template.png"
                alt="product smart"
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
                :style="`background-image: url(${getProductImage(product)})`"
              />
              <img
                class="top"
                src="@app/assets/img/iphone-template.png"
                alt="product iphone"
              >
            </div>
          </template>
        </span>
      </div>
      <div class="products__info">
        <p
          class="products__title"
          v-html="product.name_with_br"
        />
        <div
          v-if="![93,79].includes(product.id) && product.show_report"
          class="products__task-review"
        >
          <a
            v-if="product.status === 2"
            ref="addReportBtn"
            :href="`#report-modal-${product.id}`"
            class="btn btn--border"
            js-popup
            @keydown="onReportKeyDown"
          >
            Добавить отчет об ошибке
          </a>
          <button
            v-else
            disabled
            title="Продукт неактивен"
            class="btn btn--border btn-disabled"
          >
            Добавить отчет об ошибке
          </button>
        </div>
        <div class="products__task-buttons">
          <p class="tasks__title">
            Задания
          </p>
          <p
            v-if="product.status !== 2"
            class="tasks__no-task"
          >
            Продукт неактивен.<br>
            Задания приостановлены.
          </p>
          <template v-else-if="product.tasks && product.tasks.length">
            <div
              v-for="task in nonMadeTasks"
              :key="task.id"
              class="products__task-button"
            >
              <a
                v-if="isBigScreen"
                :id="taskId(task.id)"
                ref="taskModalBtn"
                :href="taskLink(task)"
                :class="['btn', 'btn--border', {'disabled': !task.step}]"
                :style="{'color': task.step ? '' : '#8e8e8e'}"
                v-popover:tooltip.top="task.step ? task.title : 'Выполните предыдущее задание'"
                :disabled="!task.step"
                :js-popup="task.step"
                @keydown="e => onTaskKeyDown(e, task.id)"
              >
                <span>{{ task.title }}</span>
              </a>
              <a
                v-else
                :id="taskId(task.id)"
                ref="taskModalBtn"
                :href="taskLink(task)"
                :class="['btn', 'btn--border', {'disabled': !task.step}]"
                :style="{'color': task.step ? '' : '#8e8e8e'}"
                :disabled="!task.step"
                :js-popup="task.step"
                @keydown="e => onTaskKeyDown(e, task.id)"
              >
                <span>{{ task.title }}</span>
              </a>
              <ModalTask
                :regions="regions"
                :task="task"
                :product="product"
                :user-devices="devices"
                :device-type="deviceType"
                @set-error-report="setErrorReport($event)"
              />
            </div>
            <p
              v-if="!nonMadeTasks || !nonMadeTasks.length"
              class="tasks__no-task"
            >
              Все задания выполнены: <br>
              После успешной проверки выполненных заданий промокод отобразится в разделе "Вознаграждения".
            </p>
          </template>
          <p
            v-else
            class="tasks__no-task"
          >
            Заданий пока нет.
          </p>
        </div>
        <div class="products__link-container">
          <inertia-link
            :href="`/products/${product.id}`"
            class="products__link products__link--grey"
          >
            К странице продукта →
          </inertia-link>
        </div>
      </div>
    </div>
    <ModalReport
      :error-report="errorReport"
      :product="product"
      :user-devices="devices"
      :device-type="deviceType"
      :criticality-bugs="criticalityBugs"
      :type-bugs="typeBugs"
    />
  </div>
</template>

<script>
import getProductImage from '@app/mixins/getProductImage'
import ModalReport from '@app/components/Modals/Report'
import ModalTask from '@app/components/Modals/Task/Index'
import formatDaysLeft from '@app/libs/plural'

export default {
  name: 'ProfileProductItem',

  components: {
    ModalReport,
    ModalTask
  },
  mixins: [getProductImage],
  props: {
    product: Object,
    testerTasks: Object,
    devices: Array,
    deviceType: String,
    regions: Array,
    criticalityBugs: Array,
    typeBugs: Array
  },

  data() {
    return {
      platforms: [
        {
          value: 'ios',
          title: 'Для платформ Apple',
          iconClass: 'ico-mono-status-apple',
          iconPath: '@app/assets/img/sprite-mono.svg#ico-mono-status-apple'
        },
        {
          value: 'android',
          title: 'Для платформ Android',
          iconClass: 'ico-mono-android',
          iconPath: '@app/assets/img/sprite-mono.svg#ico-mono-android'
        },
        {
          value: 'web',
          title: 'Веб',
          iconClass: 'ico-mono-web',
          iconPath: '@app/assets/img/sprite-mono.svg#ico-mono-web'
        },
        {
          value: 'web_ios',
          title: 'Веб iOS',
          iconClass: 'ico-mono-web',
          iconPath: '@app/assets/img/sprite-mono.svg#ico-mono-web'
        },
        {
          value: 'web_android',
          title: 'Веб Android',
          iconClass: 'ico-mono-web',
          iconPath: '@app/assets/img/sprite-mono.svg#ico-mono-web'
        },
        {
          value: 'smart_tv',
          title: this.product.os_version_smart_tv ? `Smart TV | ${this.product.os_version_smart_tv?.os}` : 'Smart TV',
          iconClass: 'ico-mono-smart',
          iconPath: '@app/assets/img/sprite-mono.svg#ico-mono-smart'
        }
      ],
      errorReport: {}
    }
  },
  computed: {
    nonMadeTasks() {
      return this.product.tasks.filter(task => !this.testerTasks.finished.includes(task.id))
    },
    isBigScreen() {
      return window.innerWidth > 768
    }
  },
  methods: {
    setErrorReport(payload) {
      this.errorReport = payload
    },
    onReportKeyDown(e) {
      if (e.keyCode === 13) {
        e.preventDefault()
        this.$refs.addReportBtn.click()
      }
    },
    onTaskKeyDown(e, taskId) {
      if (e.keyCode === 13) {
        e.preventDefault()
        const ref = this.$refs.taskModalBtn.find(ref => ref.id === `task-btn-${taskId}`)
        ref.click()
      }
    },
    formatDaysLeft: formatDaysLeft,
    taskLink(task) {
      return task.step ? `#task-modal-${task.id}` : void (0)
    },
    taskId(id) {
      return `task-btn-${id}`
    },
    iconPathSmartTv() {
      return require(`@app/assets/img/smart-tv/${this.smartTvSlug()}.svg`)
    },
    smartTvSlug() {
      return this.generateSlug(this.product.os_version_smart_tv?.os || '')
    },
    generateSlug(str) {
      return str
        .toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .trim()
    }
  }
}
</script>

<style scoped>
.tasks__title {
   font-weight: bold;
   width: 100%;
   text-align: center;
   font-size: 14px;
   margin-top: 0.5rem;
   margin-bottom: 0.35rem;
}

.tasks__no-task {
   margin-top: 0.5rem;
   margin-bottom: 0.5rem;
   font-size: 14px;
}

.btn-disabled {
   border: 2px solid #e2e5eb;
   background: none !important;
   padding-top: 12px;
   padding-bottom: 12px;
   color: #666d74 !important;
}

.btn-disabled:hover {
   background: none !important;
   color: #666d74 !important;
   border-color: #e2e5eb !important;
}

.products__image-bg--large {
   left: 0 !important;
   width: 100% !important;
}

.disabled {
   cursor: not-allowed;
}

.products__info .btn {
    display: flex;
    align-items: center;
    justify-content: center;
    background: #1D2023;
    color: #FAFAFA;
    min-height: 52px;
    border-radius: 16px;
    padding: 14px;
}
</style>
