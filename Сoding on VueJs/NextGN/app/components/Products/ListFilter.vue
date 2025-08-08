<template>
  <div class="filter">
    <div
      v-if="isAuth"
      class="filter__row"
    >
      <div class="filter__column">
        <p>Статус</p>

        <div class="filter__buttons">
          <FilterBtn
            :is-active="participating === true"
            @click="updateParticipation(true)"
          >
            Участвую
          </FilterBtn>

          <FilterBtn
            :is-active="participating === false"
            @click="updateParticipation(false)"
          >
            Не участвую
          </FilterBtn>
        </div>
      </div>
    </div>

    <div class="filter__row">
      <div class="filter__column">
        <p>Платформа</p>

        <div class="filter__buttons">
          <FilterBtn
            :is-active="platforms.includes('android')"
            @click="updatePlatform('android')"
          >
            <template #prepend-icon>
              <svg class="ico ico-mono-android">
                <use :xlink:href="imagePath(`sprite-mono.svg`) + '#ico-mono-android'" />
              </svg>
            </template>

            Android
          </FilterBtn>

          <FilterBtn
            :is-active="platforms.includes('ios')"
            @click="updatePlatform('ios')"
          >
            <template #prepend-icon>
              <svg class="ico ico-mono-status-apple">
                <use :xlink:href="imagePath(`sprite-mono.svg`) + '#ico-mono-status-apple'" />
              </svg>
            </template>

            iOS
          </FilterBtn>

          <FilterBtn
            :is-active="platforms.includes('smart_tv')"
            @click="updatePlatform('smart_tv')"
          >
            <template #prepend-icon>
              <svg class="ico ico-mono-smart">
                <use :xlink:href="imagePath(`sprite-mono.svg`) + '#ico-mono-smart'" />
              </svg>
            </template>

            SmartTV
          </FilterBtn>

          <FilterBtn
            :is-active="platforms.includes('web')"
            @click="updatePlatform('web')"
          >
            <template #prepend-icon>
              <svg class="ico ico-mono-web">
                <use :xlink:href="imagePath(`sprite-mono.svg`) + '#ico-mono-web'" />
              </svg>
            </template>

            Web
          </FilterBtn>
        </div>
      </div>
    </div>

    <div class="filter__row">
      <div class="filter__column">
        <p>Тестирование</p>

        <div class="filter__buttons">
          <FilterBtn
            v-if="showAlpha"
            :is-active="stages.includes('0')"
            @click="updateStage('0')"
          >
            <template #prepend-icon>
              <svg class="ico ico-mono-status-alfa">
                <use :xlink:href="imagePath(`sprite-mono.svg`) + '#ico-mono-status-alfa'" />
              </svg>
            </template>

            Alpha
          </FilterBtn>

          <FilterBtn
            :is-active="stages.includes('1')"
            @click="updateStage('1')"
          >
            <template #prepend-icon>
              <svg class="ico ico-mono-status-beta">
                <use :xlink:href="imagePath(`sprite-mono.svg`) + '#ico-mono-status-beta'" />
              </svg>
            </template>

            Beta
          </FilterBtn>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import imagePath from '@app/mixins/imagePath.vue'
import FilterBtn from './FilterBtn.vue'

export default {
  components: {
    FilterBtn
  },

  props: {
    platforms: {
      type: Array,
      default: ()=>[]
    },
    stages: {
      type: Array,
      default: ()=>[]
    },
    participating: {
      type: Boolean,
      default: null
    }
  },

  mixin: [imagePath],

  computed: {
    ...mapGetters({
      isAuth: 'auth/isAuth'
    }),
    showAlpha() {
      return this.$page.props.roles.includes('alpha')
    }
  },

  mounted() {
    // if (this.$page.props.roles.includes('alpha')) {
    //    this.updateStage(0);
    // }
    // if (this.$page.props.roles.includes('beta')) {
    //    this.updateStage(1);
    // }
  },

  methods: {
    updatePlatform(platform) {
      this.$emit(
        'update:platforms',
        this.platforms.includes(platform)
          ? this.platforms.filter(item => item !== platform)
          : [platform, ...this.platforms]
      )
    },
    updateStage(stage, disabled = false) {
      if (disabled) {
        return
      }

      this.$emit(
        'update:stages',
        this.stages.includes(stage)
          ? this.stages.filter(item => item !== stage)
          : [stage, ...this.stages]
      )
    },
    updateParticipation(value) {
      if (value !== null && this.participating === value) {
        return this.$emit('update:participating', null)
      }

      this.$emit('update:participating', !!value)
    }
  }
}
</script>

<style lang="scss" scoped>
.filter {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.filter__row {
  display: flex;
  flex-direction: row;
  justify-content: space-between;

  gap: 10px;
  @media (max-width: 767px) {
    width: 75%;
  }
}

.filter__buttons {
  @media (max-width: 767px) {
    flex-direction: column;
  }
}

.filter__column {
  display: flex;
  text-align: center;
  flex: 1; /* Columns will grow to fill the row */
  @media(max-width: 767px) {
    justify-content: space-between;
  }
}

.filter__row:last-child {
  margin-left: 0;
}
</style>
