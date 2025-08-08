<template>
   <div class="cabinet__inputs-grid">
      <div class="cabinet__inputs-item">
         <div
            class="input-container"
            :class="{ 'has-error': $v.newBrand.$dirty ? $v.newBrand.$error : false }"
         >
            <p>Бренд</p>
            <selectize 
               v-model="newBrand"
               id="brand" name="brand" placeholder="Бренды">
               <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.brand }}</option>
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
            :class="{ 'has-error': $v.newOSVersion.$dirty ? $v.newOSVersion.$error : false }"
         >
            <p>Версия ОС</p>
            <selectize 
               v-model="newOSVersion"
               id="version" name="version" placeholder="Версии"
            >
               <option v-for="version in osVersions" :key="version.id" :value="version.id">
                  {{ version.os }}
               </option>
            </selectize>
            <label
               v-if="$v.newOSVersion.$dirty ? $v.newOSVersion.$error : false"
                  class="error ui-input__validation"
                  for="version"
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
   name: 'ProfileDevicesAddSmartTv',
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
         newOSVersion: null,
         osVersions: [],
      };
   },
   methods: {
      async addDevice() {
         if (this.$v.$invalid) {
            this.$v.$touch()
            return
         }
         if (!this.newBrand || !this.newOSVersion) {
            return;
         }
         if ( this.isModal ){
            let osVersion  =  undefined;
            for ( let item of this.osVersions ){
               if ( item.id == this.newOSVersion ){
                  osVersion = item;
                  break;
               }
            }
            let deviceSmartTv = undefined ;
            for   ( let item of this.$props.brands ){
               if ( item.id == this.newBrand ){
                  deviceSmartTv = item;
                  break;
               }
            }
            console.debug( "stv", deviceSmartTv  );
            this.$emit( 'on-device-add', {
               device_id : this.newBrand,
               device_smart_tv : deviceSmartTv,
               o_s_version_id : this.newOSVersion,
               os_version: osVersion,
            } );
            this.clear();
         } else { 
            try {
               await this.$inertia.post(
                  `/profile/devices/smart-tv/add-device/`,
                  {
                     device_id: this.newBrand,
                     o_s_version_id: this.newOSVersion,
                  },
                  { preserveScroll: true }
               );
               this.clear();
            } catch (e) {
               this.$handleError(e);
            }
         }
      },
      setOSVersions(brand) {
         // if (brand === 'Apple') {
         //    this.osVersions = this.osVersionsProp.macos.concat(this.osVersionsProp.ios);
         // } else {
         //    this.osVersions = this.osVersionsProp.windows.concat(this.osVersionsProp.android);
         // }
         console.log(this.osVersions)
      },
      clear() {
         this.newBrand = null;
         this.newBrowser = null;
         this.newOSVersion = null;
         // this.osVersions = this.osVersionsProp.windows;
      },
   },
   watch: {
      newBrand(brand) {
         this.newOSVersion = null;
         if (!brand) return;
         // this.setOSVersions(brand);
      },
   },
   beforeMount() {
      this.osVersions = this.osVersionsProp.smart_tv;
   },
   validations: {
      newBrand: {
         required,
      },
      newOSVersion: {
         required,
      },
   }
};
</script>

<style scoped>
.btn:hover {
  background: rgba(0, 0, 0, 0);
  border: 2px solid rgba(188, 195, 208, 0.5);
  color: #ff0032;
}
</style>