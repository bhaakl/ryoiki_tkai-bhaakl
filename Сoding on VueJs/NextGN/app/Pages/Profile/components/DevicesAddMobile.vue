<template>
   <div class="cabinet__inputs-grid">
      <div class="cabinet__inputs-item">
            <div
               class="input-container"
               :class="{ 'has-error': $v.newBrand.$dirty ? $v.newBrand.$error : false }"
            >
               <p>Бренд</p>
               <selectize v-model="newBrand"
                  id="brand" name="brand"
                  placeholder="Бренды"
               >
                  <option v-for="brand in brands" :key="brand" :value="brand">{{ brand }}</option>
               </selectize>
               <label
                  v-if="$v.newBrand.$dirty ? $v.newBrand.$error : false"
                     class="error ui-input__validation"
                     for="brand"
                  >
                  Выберите бренд
               </label>
            </div>
      </div>
      <div class="cabinet__inputs-item">
         <div
            class="input-container"
            :class="{ 'has-error': $v.newModel.$dirty ? $v.newModel.$error : false }"
         >
            <p>Модель</p>
            <selectize id="model" name="model" v-model="newModel" :disabled="!brandModels || !brandModels.length" placeholder="Модели">
               <option v-for="device in brandModels" :key="device.id" :value="device.id">{{ device.model }}</option>
            </selectize>
            <label
               v-if="$v.newModel.$dirty ? $v.newModel.$error : false"
                  class="error ui-input__validation"
                  for="model"
               >
               Выберите модель
            </label>
         </div>
      </div>
      <div class="cabinet__inputs-item">
         <div
            class="input-container"
            :class="{ 'has-error': $v.newOSVersion.$dirty ? $v.newOSVersion.$error : false }"
         >
            <p>Версия ОС</p>
            <selectize v-model="newOSVersion" id="os" name="os" placeholder="Версии">
               <option v-for="version in osVersions" :key="version.id" :value="version.id">
                  {{ version.os }} {{ version.version }}
               </option>
            </selectize>
            <label
            v-if="$v.newOSVersion.$dirty ? $v.newOSVersion.$error : false"
               class="error ui-input__validation"
               for="os"
            >
               Выберите версию ОС
            </label>
         </div>
      </div>
      <div class="cabinet__inputs-item">
         <div class="input-container">
            <p>&nbsp;</p>
            <button @click="addDevice" class="btn btn--border btn--add-device">
               Добавить
            </button>
         </div>
      </div>
   </div>
</template>

<script>
import Selectize from 'vue2-selectize';
import { required } from 'vuelidate/lib/validators'

export default {
   name: 'ProfileDevicesAddMobile',
   props: {
      brands: Array,
      osVersionsProp: Object
   },
   components: {
      Selectize,
   },
   inject : [
      'isModal'
   ],
   data() {
      return {
         newBrand: null,
         newModel: null,
         newOSVersion: null,
         brandModels: [],
         osVersions: [],
      };
   },
   methods: {
      async addDevice() {
         if (this.$v.$invalid) {
            this.$v.$touch()
            return
         }

         if (!this.newModel || !this.newOSVersion) {
            return;
         }
         if ( this.isModal ){
            this.addDeviceHelper()
            this.clear();
         } else {
            try {
               await this.$inertia.post(
                  `/profile/devices/add-device/`,
                  {
                     device_id: this.newModel,
                     o_s_version_id: this.newOSVersion,
                  },
                  {
                     preserveScroll: true, preserveState: true,
                     // onSuccess: () => {
                     //    this.addDeviceHelper();
                     // }
                  }
               );
               this.clear();
            } catch (e) {
               this.$handleError(e);
            }
         }
      },
      addDeviceHelper() {
         let modelName  =  '[NO MODEL]';
         for ( let model of this.brandModels ){
            if ( model.id == this.newModel ){
               modelName            =  model.model;
               break;
            }
         }
         let osVersion  =  undefined;
         for ( let item of this.osVersions ){
            if ( item.id == this.newOSVersion ){
               osVersion = item;
               break;
            }
         }
         return this.$emit( 'on-device-add', {
            id : undefined,
            brand : this.newBrand,
            model : modelName,
            model_id : this.newModel,
            os_version : osVersion,
            os_version_id : this.newOSVersion
         } );
      },
      async getBrandModels(brand) {
         try {
            await this.$inertia.post(
               `/profile/devices/brand-models`,
               {
                  brand,
               },
               {
                  preserveScroll: true,
                  preserveState: true,
                  onSuccess: event => {
                     this.brandModels = event.props.responseData;
                  },
               }
            );
         } catch (e) {
            this.$handleError(e);
         }
      },
      setOSVersions(brand) {
         if (brand === 'Apple') {
            this.osVersions = this.osVersionsProp.ios;
         } else {
            this.osVersions = this.osVersionsProp.android;
         }
      },
      clear() {
         this.newBrand = null;
         this.newModel = null;
         this.newOSVersion = null;
         this.brandModels = [];
         this.osVersions = this.osVersionsProp.android;
      },
   },
   watch: {
      async newBrand(brand) {
         this.newOSVersion = null;
         this.brandModels = [];
         if (!brand) return;
         await this.getBrandModels(brand);
         this.setOSVersions(brand);
      },
   },
   beforeMount() {
      this.osVersions = this.osVersionsProp.android;
   },
   validations: {
      newBrand: {
         required,
      },
      newModel: {
         required,
      },
      newOSVersion: {
         required,
      },
   }
};
</script>

<style scoped>
.is-invalid {
  border-color: red;
}

.btn:hover {
  background: rgba(0, 0, 0, 0);
  border: 2px solid rgba(188, 195, 208, 0.5);
  color: #ff0032;
}
</style>
