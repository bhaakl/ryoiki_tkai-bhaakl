<template>
  <CRow class="justify-content-center">
    <CCol col="12">
      <CCard>
        <CCardHeader>
          <h3 class="mb-0">Изменить продукт</h3>
        </CCardHeader>
        <CCardBody>
          <CForm @submit.prevent="submit">
            <CInput
              v-model="$v.name.$model"
              label="Название"
              horizontal
              :isValid="$v.name.$dirty ? !$v.name.$error : null"
              invalidFeedback="Введите название"
            />
            <CInput
              v-model="$v.startDate.$model"
              label="Дата начала тестирования"
              type="date"
              horizontal
              :isValid="$v.startDate.$dirty ? !$v.startDate.$error : null"
              invalidFeedback="Введите дату начала"
            />
            <CInput
              v-model="$v.endDate.$model"
              label="Дата окончания тестирования"
              type="date"
              horizontal
              :isValid="$v.endDate.$dirty ? !$v.endDate.$error : null"
              :invalidFeedback="
                $v.endDate.required
                  ? 'Должна быть после даты начала'
                  : 'Введите дату окончания'
              "
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
              :isValid="$v.short_description.$dirty ? !$v.short_description.$error : null"
              :invalidFeedback="
                !$v.short_description.maxLength
                  ? 'Лимит в 150 символов превышен'
                  : 'Введите краткое описание'
              "
            />
            <div role="group" class="form-group form-row">
              <label class="col-form-label col-sm-3"> Описание </label>
              <div class="col-sm-9">
                <VueTextEditor v-model="$v.description.$model" />
              </div>
            </div>

             <CSelect :value.sync="platform" :options="platformOptions" label="Платформа" horizontal />
             <CSelect v-if="isPlatformSmartTv" :value.sync="os_smart_tv" :options="arraySmrtTvOs" label="Версия OS Smart TV" horizontal />

            <div role="group" class="form-group form-row">
              <label class="col-form-label col-sm-3">
                Правила проведения тестирования
              </label>
              <div class="col-sm-9">
                <VueTextEditor v-model="$v.rules.$model" />
              </div>
            </div>
            <CInput
              v-model="$v.applink.$model"
              :label="`Ссылка на ${!isPlatformWeb ? 'приложение' : 'сайт'}`"
              description="Должна начинаться с http:// или https://"
              horizontal
              :isValid="$v.applink.$dirty ? !$v.applink.$error : null"
              invalidFeedback="Введите корректную ссылку"
            />
            <CInputFile
              @change="addImage"
              label="Скриншоты (макс. 3)"
              class="mb-4"
              horizontal
              multiple
            >
              <template #description>
                <small class="form-text text-muted w-100">
                  Чтобы выбрать несколько файлов, зажмите Ctrl.<br />
                  Если оставить пустым, скриншоты останутся прежними<br />
                  Допустимые форматы: jpg, png.<br />
                  Размер для мобильных приложений: 350 x 512<br />
                  Размер для веб-приложений: 850 x 500<br />
                </small>
              </template>
            </CInputFile>
            <template v-if="previewImagesUrls && previewImagesUrls.length">
              <cropper
                v-for="url in previewImagesUrls"
                :key="url"
                :src="url"
                :stencil-props="{
                  aspectRatio,
                }"
                class="cropper mb-4"
                style="max-height: 500px"
                ref="cropper"
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
                name="comment_required"
                style="margin: 1rem 0;"
             />
             <CInputCheckbox
                label="Черновик"
                :checked.sync="draft"
                style="margin: 1rem 0;"
             />
             <CInputCheckbox
                label="Разрешить отправку отчета об ошибке"
                :checked.sync="showReport"
                style="margin: 1rem 0;"
             />
             <CInputCheckbox
                label='Включить поле "Тип бага" для формы отчетов об ошибке'
                :checked.sync="type_bug"
                style="margin: 1rem 0;"
             />
             <CInputCheckbox
                label='Включить поле "Критичность бага" для формы отчетов об ошибке'
                :checked.sync="criticality_bug"
                style="margin: 1rem 0;"
             />
            <CButtonGroup>
              <CButton type="submit" color="success">Изменить</CButton>
              <CButton @click="$inertia.visit('/admin/all-products')" color="light"
                >Назад</CButton
              >
            </CButtonGroup>
            <CButton @click="removeModal = true" color="danger" class="float-right"
              >Удалить</CButton
            >
          </CForm>
        </CCardBody>
      </CCard>
    </CCol>
    <CModal color="success" centered :show.sync="editModal">
      <template #header>
        <h6 class="modal-name">Изменить продукт?</h6>
      </template>
      Вы уверены, что хотите изменить продукт "{{ name }}"?
      <template #footer>
        <CButton @click="edit(true)" color="success">Изменить</CButton>
        <CButton @click="edit(false)" color="light">Отмена</CButton>
      </template>
    </CModal>
    <CModal color="danger" centered :show.sync="removeModal">
      <template #header>
        <h6 class="modal-name">Удалить продукт?</h6>
      </template>
      Вы уверены, что хотите удалить продукт "{{ name }}"?
      <template #footer>
        <CButton @click="remove(true)" color="danger">Удалить</CButton>
        <CButton @click="remove(false)" color="light">Отмена</CButton>
      </template>
    </CModal>
  </CRow>
</template>

<script>
import { required, maxLength, url } from "vuelidate/lib/validators";
import { Cropper } from "vue-advanced-cropper";
import Layout from "@admin/containers/TheContainer";
import SelectBackground from "./SelectBackground";

export default {
  name: "ProductsEdit",
  layout: Layout,
  props: {
    product: Object,
  },
  components: {
    Cropper,
    SelectBackground
  },
  data() {
    return {
      name: "",
      short_description: "",
      description: "",
      rules: "",
      applink: "",
      startDate: null,
      endDate: null,
      stage: null,
      platform: null,
      images: [],
       os_smart_tv: null,
      background_color: null,
       closed_testing: null,
       sequential: false,
       draft: false,
      editModal: false,
      removeModal: false,
      stageOptions: [
        { label: "Alpha", value: 0 },
        { label: "Beta", value: 1 },
      ],
       platformOptions: [
          { label: 'Android', value: 'android' },
          { label: 'iOS', value: 'ios' },
          { label: 'Web Desktop', value: 'web' },
          { label: 'Web Android', value: 'web_android' },
          { label: 'Web iOS', value: 'web_ios' },
          { label: 'Smart TV', value: 'smart_tv' },
       ],
      previewImagesUrls: null,
       showReport: true,
       type_bug: false,
       criticality_bug: false
    };
  },
  computed: {
    isPlatformWeb() {
      return this.platform === "web";
    },
     isPlatformSmartTv() {
        return this.platform === 'smart_tv';
     },
     arraySmrtTvOs() {
        return this.product.o_s_versions_smart_tv.map(item => {
           return {
              label: item.os,
              value: item.id
           };
        });
     },
    aspectRatio() {
      if (this.isPlatformWeb) {
        return 1108 / 647;
      }
      return 515 / 752;
    },
  },
  methods: {
    addImage(files) {
      this.images = Array.from(files).slice(0, 3);
      this.previewImagesUrls = this.images.map((image) => URL.createObjectURL(image));
    },
    submit() {
      this.$v.$touch();
      if (this.$v.$invalid) {
        this.$window.scrollTo({
          top: 0,
          behavior: "smooth",
        });
        return;
      }
      this.editModal = true;
    },
    async edit(modalValue) {
      this.editModal = false;
      if (!modalValue) return;
      try {
        const formData = new FormData();
        formData.append("name", this.name);
        formData.append("short_description", this.short_description);
        formData.append("description", this.description);
        if (this.rules) {
          formData.append("rules", this.rules);
        }
        if (this.applink) {
          formData.append("applink", this.applink);
        }
        formData.append("stage", this.stage);
        formData.append(
          "date_start",
          this.$moment(this.startDate).format("YYYY-MM-DD HH:mm:ss")
        );
        formData.append(
          "date_end",
          this.$moment(this.endDate).format("YYYY-MM-DD HH:mm:ss")
        );
        formData.append("platform", this.platform);
        formData.append("background_color", this.background_color);
        formData.append("closed_testing", this.closed_testing);
        formData.append("sequential", this.sequential);
        formData.append("draft", this.draft);
        formData.append("show_report", this.showReport);
        formData.append("o_s_version_smart_tv_id", this.os_smart_tv);
        formData.append("type_bug", this.type_bug);
        formData.append("criticality_bug", this.criticality_bug);
        if (this.images && this.images.length) {
          this.images.forEach((image) => {
            formData.append("images[]", image);
          });
          const coordinates = this.$refs.cropper.map((i) => {
            const { coordinates } = i.getResult();
            return coordinates;
          });
          formData.append("coordinates", JSON.stringify(coordinates));
        }
        await this.$inertia.post(`/admin/all-products/${this.product.id}`, formData);
      } catch (e) {
        this.$handleError(e);
      }
    },
    async remove(modalValue) {
      this.removeModal = false;
      if (!modalValue) return;
      try {
        await this.$inertia.delete(`/admin/all-products/${this.product.id}`);
      } catch (e) {
        this.$handleError(e);
      }
    },
  },
  validations: {
    name: {
      required,
    },
    short_description: {
      required,
      maxLength: maxLength(150),
    },
    description: {
      required,
    },
    rules: {},
    applink: {
      url,
    },
    startDate: {
      required,
    },
    endDate: {
      required,
      moreThanStartDate: function (value) {
        return value > this.startDate;
      },
    },
  },
  beforeMount() {
    this.name = this.product.name;
    this.startDate = this.$moment(this.product.date_start).format("YYYY-MM-DD");
    this.endDate = this.$moment(this.product.date_end).format("YYYY-MM-DD");
    this.short_description = this.product.short_description;
    this.description = this.product.description;
    this.rules = this.product.rules;
    this.applink = this.product.applink;
    this.stage = this.product.stage;
    this.platform = this.product.platform;
    this.background_color = this.product.background_color;
    this.closed_testing = this.product.closed_testing;
    this.sequential = this.product.sequential;
    this.draft = this.product.draft;
    this.os_smart_tv = this.product.o_s_version_smart_tv_id;
    this.showReport = this.product.show_report;
    this.type_bug = this.product.type_bug;
    this.criticality_bug = this.product.criticality_bug;
  },
};
</script>
