<template>
   <CRow>
      <CCol col="12" class="mx-auto">
         <CCard>
            <CCardHeader>
               <h4>Рассылка</h4>
            </CCardHeader>
            <CCardBody>
               <CDataTable
                  hover
                  striped
                  clickable-rows
                  columnFilter
                  @row-clicked="tester => (tester.checked = !tester.checked)"
                  :items="filteredTesters"
                  :fields="fields"
                  :items-per-page="15"
                  :active-page="activePage"
                  :pagination="{ doubleArrows: false, align: 'center' }"
                  :noItemsView="{ noResults: 'Нет подходящих вариантов', noItems: 'Нет результатов по вашему запросу' }"
               >
                  <template #checkbox-header>
                     <CInputCheckbox
                        @input="filteredTesters.map(tester => (tester.checked = !tester.checked))"
                        style="padding-bottom: 1.25rem;"
                     />
                  </template>
                  <template #checkbox="{ item: { checked } }">
                     <td>
                        <CInputCheckbox :checked="checked" />
                     </td>
                  </template>
                  <template #devices="{ item: { devices } }">
                     <td>
                        {{ devices.join(', ') }}
                     </td>
                  </template>
                  <template #role_label="{ item: { role: { name, label } } }">
                     <td>
                        <CBadge :color="getBadge(name)" class="mb-1 mr-1">
                           {{ label }}
                        </CBadge>
                     </td>
                  </template>
                  <template #oses="{ item: { oses } }">
                     <td>
                        <svg v-if="oses.includes('Android')" class="c-icon">
                           <use xlink:href="@admin/assets/brand.svg#cib-android-alt"></use>
                        </svg>
                        <svg v-if="oses.includes('iOS')" class="c-icon">
                           <use xlink:href="@admin/assets/brand.svg#cib-apple"></use>
                        </svg>
                     </td>
                  </template>
               </CDataTable>

               <div class="mt-5">
                  <CSelect
                     :value.sync="selectedMethod"
                     :options="methodOptions"
                     label="Способ отправки"
                     :horizontal="{ label: 'col-sm-2', input: 'col-sm-10' }"
                  />
                  <CInput
                     v-if="isMethodEmail"
                     v-model="$v.subject.$model"
                     label="Тема письма"
                     :horizontal="{ label: 'col-sm-2', input: 'col-sm-10' }"
                     :isValid="$v.subject.$dirty ? !$v.subject.$error : null"
                     invalidFeedback="Введите тему"
                  />
                  <CSelect
                     v-if="!isMethodEmail"
                     :value.sync="smsTemplate"
                     :options="smsTemplateOptions"
                     label="Шаблон"
                     :horizontal="{ label: 'col-sm-2', input: 'col-sm-10' }"
                  />
                  <!-- <CInput
                     v-if="!isMethodEmail && smsTemplate.example"
                     v-model="smsTemplate.example"
                     label="Пример сообщения"
                     :horizontal="{ label: 'col-sm-2', input: 'col-sm-10' }"
                     readonly
                  /> -->
                  <div role="group" class="form-group form-row">
                     <label class="col-form-label col-sm-2">
                        Сообщение
                     </label>
                     <div class="col-sm-10">
                        <VueTextEditor v-model="$v.message.$model" />
                     </div>
                  </div>
                  <CSelect
                     v-if="isMethodEmail"
                     :value.sync="signature"
                     :options="signatureOptions"
                     label="Подпись"
                     :horizontal="{ label: 'col-sm-2', input: 'col-sm-10' }"
                  />
               </div>
            </CCardBody>
            <CCardFooter>
               <CButton @click="send" :disabled="!checkedTesters.length" color="primary" class="float-right">
                  Создать рассылку
               </CButton>
            </CCardFooter>
         </CCard>
      </CCol>
   </CRow>
</template>

<script>
import { required, requiredIf, maxLength } from 'vuelidate/lib/validators';
import Layout from '@admin/containers/TheContainer';

export default {
   name: 'MailingIndex',
   layout: Layout,
   props: {
      testers: Array,
      regions: Array,
      signatures: Array,
      smsTemplates: Array,
   },
   data() {
      return {
         isSms: false,
         filteredTesters: [],
         selectedOs: '–',
         selectedBrand: '–',
         selectedModel: '–',
         selectedOccupation: '–',
         selectedMethod: 'email',
         subject: '',
         message: '',
         signature: '',
         smsTemplate: '',
         fields: [
            { key: 'checkbox', label: '', filter: false, _style: 'width: 50px;' },
            { key: 'name', label: 'Имя тестировщика', _classes: 'font-weight-bold' },
            { key: 'role_label', label: 'Роль' },
            { key: 'devices', label: 'Устройства' },
            { key: 'oses', label: 'ОС' },
            { key: 'osVersionsLabel', label: 'Версии' },
            { key: 'occupation', label: 'Род занятий' },
            { key: 'region', label: 'Регион' },
            { key: 'sex', label: 'Пол' },
         ],
         activePage: 1,
         signatureOptions: [
            {
               value: 'NextGen Тестирование',
               label: 'NextGen Тестирование',
            },
         ],
         smsTemplateOptions: [],
         methodOptions: [
            { label: 'Email', value: 'email' },
            { label: 'SMS', value: 'sms' },
         ],
      };
   },
   computed: {
      isMethodEmail() {
         return this.selectedMethod === 'email';
      },
      messageInvalidFeedback() {
         if (!this.$v.message.maxLength) {
            return this.isMethodEmail ? 'Максимум 2000 символов' : 'Максимум 140 символов';
         }
         return 'Введите сообщение';
      },
      checkedTesters() {
         return this.filteredTesters.filter(tester => tester.checked);
      },
   },
   methods: {
      getModels() {
         const models = this.testers
            .flatMap(tester => tester.devices.filter(device => device.brand === this.selectedBrand))
            .map(brand => brand.model);
         return ['–', ...models];
      },
      async send() {
         this.$v.$touch();
         if (this.$v.$invalid || !this.checkedTesters.length) return;
         try {
            await this.$inertia.post('/admin/mailing', {
               method: this.selectedMethod,
               ids: this.checkedTesters.map(tester => tester.id),
               subject: this.isMethodEmail ? this.subject : null,
               message: this.message,
               signature: this.isMethodEmail ? this.signature : null,
               smsTemplate: !this.isMethodEmail ? +this.smsTemplate : null,
            });
         } catch (e) {
            this.$handleError(e);
         }
      },
      getBadge(role) {
         switch (role) {
            case 'alpha':
               return 'danger';
            case 'beta':
               return 'success';
            default:
               return 'secondary';
         }
      },
   },
   validations() {
      return {
         subject: {
            required: requiredIf(function() {
               return this.isMethodEmail;
            }),
         },
         message: {
            required: requiredIf(function() {
               return this.isMethodEmail;
            }),
            maxLength: maxLength(this.isMethodEmail ? 2000 : 140),
         },
      };
   },
   beforeMount() {
      this.filteredTesters = this.testers.map(tester => {
         const devices = tester.devices.map(device => `${device.brand} ${device.model}`);
         const occupation = (tester.personal_info && tester.personal_info.occupation) || '';

         let region = '';
         if (tester.personal_info && tester.personal_info.region_id) {
            const regionObject = this.regions.find(region => region.id === tester.personal_info.region_id);
            if (regionObject) {
               region = regionObject.name;
            }
         }

         let sex = '';
         if (tester.personal_info && tester.personal_info.sex) {
            switch (tester.personal_info.sex) {
               case 'male':
                  sex = 'Мужской';
                  break;
               case 'female':
                  sex = 'Женский';
                  break;
               default:
                  break;
            }
         }

         return {
            ...tester,
            devices,
            occupation,
            region,
            sex,
            role_label: tester.role.label,
            osVersionsLabel: tester.osVersions.join(', '),
         };
      });

      if (this.signatures && this.signatures.length) {
         this.signatureOptions = this.signatures.map(signature => ({
            value: signature.name,
            label: signature.name,
         }));
      }
      this.signature = this.signatureOptions[0].value;

      this.smsTemplateOptions = this.smsTemplates.map(template => ({
         value: template.id,
         label: `(${template.template_id}) ${template.description}`,
      }));

      if (this.smsTemplateOptions && this.smsTemplateOptions.length) {
         this.smsTemplate = this.smsTemplateOptions[0].value;
      }
   },
};
</script>
