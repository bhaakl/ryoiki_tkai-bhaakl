<template>
  <CRow>
    <CCol col="12">
      <CCard>
        <CCardHeader>
          <h4>Добавить Товар</h4>
        </CCardHeader>
        <CCardBody>
          <CForm @submit.prevent="submit">
            <div class="row mx-3 my-2">
              <div class="col-2">
                <p class="font-weight-bold">
                  Тип товара:
                </p>
                <ul style="list-style-type: none; margin-left: -23px">
                  <li
                    v-for="item in categories"
                    :key="item.id"
                  >
                    <label>
                      <input
                        :id="`option-one-${item.id}`"
                        v-model="selectedCategory"
                        type="checkbox"
                        :value="item.id"
                      >
                      {{ item.name }}
                    </label>
                  </li>
                </ul>
              </div>
            </div>
            <CInput
              v-model="$v.title.$model"
              label="Название товара"
              horizontal
              :is-valid="$v.title.$dirty ? !$v.title.$error : null"
              invalid-feedback="Введите название товара"
            />
            <CInputFile
              label="Изображении"
              class="mb-4"
              horizontal
              multiple
              @change="addImage"
            >
              <template #description>
                <small class="form-text text-muted w-100">
                  Чтобы выбрать несколько файлов, зажмите Ctrl.<br>
                  Если оставить пустым, скриншоты останутся прежними<br>
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
            <CTextarea
              v-model="$v.description.$model"
              label="Описание"
              rows="5"
              horizontal
              :is-valid="$v.description.$dirty ? !$v.description.$error : null"
              invalid-feedback="Введите описание товара"
            />
            <CInput
              v-model="$v.default_price.$model"
              label="Стоимость"
              horizontal
              :is-valid="$v.default_price.$dirty ? !$v.default_price.$error : null"
              invalid-feedback="Введите Стоимость товара"
            />
            <CInput
              v-model="$v.default_residual.$model"
              label="Остаток"
              horizontal
              :is-valid="$v.default_residual.$dirty ? !$v.default_residual.$error : null"
              invalid-feedback="Введите Остаток товара"
            />
            <CCardFooter>
              <CButton
                color="success"
                @click="submit"
              >
                Создать Товар
              </CButton>
              <CButton
                color="danger"
                class="float-right"
                @click="$inertia.visit('/admin/goods')"
              >
                Отмена
              </CButton>
            </CCardFooter>
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
          Добавить товар?
        </h6>
      </template>
      Вы уверены, что хотите добавить товар "{{ title }}"?
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
import Layout from '@admin/containers/TheContainer'
import VueSlider from 'vue-slider-component'
import { required } from 'vuelidate/lib/validators'
import { Cropper } from 'vue-advanced-cropper'

export default {
  components: {
    VueSlider,
    Cropper
  },
  layout: Layout,
  props: {
    categories: Array
  },
  data() {
    return {
      images: [],
      addModal: false,
      selectedCategory: [],
      previewImagesUrls: null,
      title: '',
      description: '',
      default_price: '',
      default_residual: ''
    }
  },
  computed: {
    aspectRatio() {
      return 515 / 752
    }
  },
  beforeMount() {

  },
  methods: {
    addImage(files) {
      this.images = Array.from(files).slice(0, 10)
      this.previewImagesUrls = this.images.map(image => URL.createObjectURL(image))
    },
    submit() {
      this.addModal = true
    },
    async add(modalValue) {
      this.addModal = false
      if (!modalValue) return
      try {
        const formData = new FormData()
        if (this.selectedCategory && this.selectedCategory.length) {
          this.selectedCategory.forEach(category => {
            formData.append('categories[]', category)
          })
        }
        formData.append('title', this.title)
        formData.append('description', this.description)
        formData.append('default_price', this.default_price)
        formData.append('default_residual', this.default_residual)
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
        await this.$inertia.post('/admin/goods', formData)

      } catch (e) {
        this.$handleError(e)
      }
    }
  },
  validations: {
    title: {
      required
    },
    description: {
      required
    },
    default_price: {
      required
    },
    default_residual: {
      required
    }
  }
}
</script>
<style scoped>

</style>

