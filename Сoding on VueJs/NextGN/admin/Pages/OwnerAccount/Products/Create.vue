<template>
  <CRow class="justify-content-center">
    <CCol col="12">
      <CCard>
        <CCardHeader>
          <h3 class="mb-0">
            Добавить продукт
          </h3>
        </CCardHeader>
        <CCardBody>
          <CForm @submit.prevent="submit">
            <CTextarea
              v-model="$v.name.$model"
              label="Название"
              horizontal
              rows="2"
              :is-valid="$v.name.$dirty ? !$v.name.$error : null"
              invalid-feedback="Введите название"
            />
            <CInput
              v-model="$v.startDate.$model"
              label="Дата начала тестирования"
              type="date"
              horizontal
              :is-valid="$v.startDate.$dirty ? !$v.startDate.$error : null"
              invalid-feedback="Введите дату начала"
            />
            <CInput
              v-model="$v.endDate.$model"
              label="Дата окончания тестирования"
              type="date"
              horizontal
              :is-valid="$v.endDate.$dirty ? !$v.endDate.$error : null"
              :invalid-feedback="$v.endDate.required ? 'Должна быть после даты начала' : 'Введите дату окончания'"
            />
            <CSelect
              :value.sync="stage"
              :options="stageOptions"
              label="Стадия тестирования"
              horizontal
            />
            <CTextarea
              v-model="$v.short_description.$model"
              label="Краткое описание"
              description="Для карточки продукта – максимум 150 символов"
              rows="2"
              horizontal
              :is-valid="$v.short_description.$dirty ? !$v.short_description.$error : null"
              :invalid-feedback="
                !$v.short_description.maxLength ? 'Лимит в 150 символов превышен' : 'Введите краткое описание'
              "
            />
            <CInput
              v-model="max_testers_count"
              label="Максимальное количество тестировщиков"
              type="number"
              horizontal
            />
            <CTextarea
              v-model="$v.description.$model"
              label="Описание"
              rows="5"
              horizontal
              :is-valid="$v.description.$dirty ? !$v.description.$error : null"
              invalid-feedback="Введите описание"
            />
            <CSelect
              :value.sync="platform"
              :options="platformOptions"
              label="Платформа"
              horizontal
            />
            <CSelect
              v-if="isPlatformSmartTv"
              :value.sync="newOSVersion"
              :options="smart_os"
              label="Версия OS Smart TV"
              horizontal
            />
            <CTextarea
              v-model="$v.rules.$model"
              label="Правила проведения тестирования"
              rows="5"
              horizontal
              :is-valid="$v.rules.$dirty ? !$v.rules.$error : null"
              invalid-feedback="Введите правила"
            />
            <CInput
              v-model="$v.applink.$model"
              :label="`Ссылка на ${!isPlatformWeb ? 'приложение' : 'сайт'}`"
              description="Должна начинаться с http:// или https://"
              horizontal
              :is-valid="$v.applink.$dirty ? !$v.applink.$error : null"
              invalid-feedback="Введите корректную ссылку"
            />
            <CInputFile
              label="Скриншоты (макс. 3)"
              class="mb-4"
              horizontal
              multiple
              @change="addImage"
            >
              <template #description>
                <small class="form-text text-muted w-100">
                  Чтобы выбрать несколько файлов, зажмите Ctrl.<br>
                  Если оставить пустым, будет использована заглушка. <br>
                  Допустимые форматы: jpg, png.<br>
                  Размер для мобильных приложений: 350 x 512<br>
                  Размер для веб-приложений: 850 x 500<br>
                </small>
              </template>
            </CInputFile>
            <template v-if="previewImagesUrls && previewImagesUrls.length">
              <cropper
                v-for="url in previewImagesUrls"
                :key="url"
                ref="cropper"
                :src="url"
                :stencil-props="{
                  aspectRatio,
                }"
                class="cropper mb-4"
                style="max-height: 500px;"
              />
            </template>
            <SelectBackground v-model="background_color" />
            <CInputCheckbox
              label="Закрытое тестирование"
              :checked.sync="closed_testing"
              name="comment_required"
              style="margin: 1rem 0;"
            />
            <CInputCheckbox
              label="Включить прохождение заданий по порядку"
              :checked.sync="sequential"
              style="margin: 1rem 0;"
            />
            <CInputCheckbox
              :disabled="isUserCreateDraft"
              label="Черновик"
              :checked.sync="draft"
              style="margin: 1rem 0;"
              :checked="isUserCreateDraft"
            />
            <CInputCheckbox
              label="Разрешить отправку отчета об ошибке"
              :checked.sync="showReport"
              style="margin: 1rem 0;"
            />
            <CInputCheckbox
              label="Включить поле &quot;Тип бага&quot; для формы отчетов об ошибке"
              :checked.sync="type_bug"
              style="margin: 1rem 0;"
            />
            <CInputCheckbox
              label="Включить поле &quot;Критичность бага&quot; для формы отчетов об ошибке"
              :checked.sync="criticality_bug"
              style="margin: 1rem 0;"
            />
            <CButtonGroup>
              <CButton
                type="submit"
                color="success"
              >
                Добавить
              </CButton>
              <CButton
                color="light"
                @click="$inertia.visit('/admin/products')"
              >
                Назад
              </CButton>
            </CButtonGroup>
          </CForm>
        </CCardBody>
      </CCard>
    </CCol>
    <CModal
      color="success"
      centered
      :show.sync="addModal"
    >
      <template #header>
        <h6 class="modal-title">
          Добавить продукт?
        </h6>
      </template>
      Вы уверены, что хотите добавить продукт {{ name }}?
      <template #footer>
        <CButton
          color="success"
          @click="add(true)"
        >
          Добавить
        </CButton>
        <CButton
          color="light"
          @click="add(false)"
        >
          Отмена
        </CButton>
      </template>
    </CModal>
  </CRow>
</template>

<script>
import { required, maxLength, url } from 'vuelidate/lib/validators'
import { Cropper } from 'vue-advanced-cropper'
import Layout from '@admin/containers/TheContainer'
import SelectBackground from './SelectBackground'

export default {
  name: 'ProductsCreate',
  components: {
    Cropper,
    SelectBackground
  },
  layout: Layout,
  props: {
    smart_os: Array
  },
  data() {
    return {
      showReport: true,
      name: '',
      short_description: '',
      description: '',
      rules: '',
      applink: '',
      startDate: null,
      endDate: null,
      stage: 0,
      platform: 'android',
      max_testers_count: null,
      images: [],
      background_color: null,
      closed_testing: false,
      sequential: false,
      draft: false,
      addModal: false,
      stageOptions: [
        { label: 'Alpha', value: 0 },
        { label: 'Beta', value: 1 }
      ],
      platformOptions: [
        { label: 'Android', value: 'android' },
        { label: 'iOS', value: 'ios' },
        { label: 'Web Desktop', value: 'web' },
        { label: 'Web Android', value: 'web_android' },
        { label: 'Web iOS', value: 'web_ios' },
        { label: 'Smart TV', value: 'smart_tv' }
      ],
      previewImagesUrls: null,
      newOSVersion: null,
      criticality_bug: false,
      type_bug: false
    }
  },
  computed: {
    isPlatformWeb() {
      return this.platform.includes('web')
    },
    aspectRatio() {
      if (this.isPlatformWeb) {
        return 1108 / 647
      }

      return 515 / 752
    },
    isPlatformSmartTv() {
      return this.platform == 'smart_tv'
    },
    isUserCreateDraft() {
      return this.$page.props.roles.includes('create_draft')
    }
  },
  methods: {
    addImage(files) {
      this.images = Array.from(files).slice(0, 3)
      this.previewImagesUrls = this.images.map(image => URL.createObjectURL(image))
    },
    submit() {
      this.$v.$touch()
      if (this.$v.$invalid) {
        this.$window.scrollTo({
          top: 0,
          behavior: 'smooth'
        })

        return
      }
      this.addModal = true
    },
    async add(modalValue) {
      this.addModal = false
      if (!modalValue) return
      try {
        const formData = new FormData()
        formData.append('name', this.name)
        formData.append('short_description', this.short_description)
        formData.append('description', this.description)
        if (this.rules) {
          formData.append('rules', this.rules)
        }
        if (this.applink) {
          formData.append('applink', this.applink)
        }
        formData.append('stage', this.stage)
        formData.append('date_start', this.$moment(this.startDate).format('YYYY-MM-DD HH:mm:ss'))
        formData.append('date_end', this.$moment(this.endDate).format('YYYY-MM-DD HH:mm:ss'))
        formData.append('platform', this.platform)
        formData.append('background_color', this.background_color)
        formData.append('sequential', this.background_color)
        formData.append('o_s_version_smart_tv_id', this.newOSVersion)
        formData.append('closed_testing', this.closed_testing)
        formData.append('sequential', this.sequential)
        formData.append('draft', this.draft)
        formData.append('show_report', this.showReport)
        formData.append('type_bug', this.type_bug)
        formData.append('criticality_bug', this.criticality_bug)
        formData.append('max_testers_count', this.max_testers_count)
        if (this.images && this.images.length) {
          this.images.forEach(image => {
            formData.append('images[]', image)
          })
          const coordinates = this.$refs.cropper.map(i => {
            const { coordinates } = i.getResult()

            return coordinates
          })
          formData.append('coordinates', JSON.stringify(coordinates))
        }
        await this.$inertia.post('/admin/products', formData)
      } catch (e) {
        this.$handleError(e)
      }
    }
  },
  validations() {
    return {
      name: {
        required
      },
      short_description: {
        required,
        maxLength: maxLength(150)
      },
      description: {
        required
      },
      rules: {},
      applink: {
        url
      },
      startDate: {
        required
      },
      endDate: {
        required,
        moreThanStartDate: function(value) {
          return value > this.startDate
        }
      }
    }
  }
}
</script>
