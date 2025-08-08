<template>
  <div class="cabinet__devices">
    <div
      v-for="( device, index ) in devices"
      :key="device.id"
      class="cabinet__device"
    >
      <div class="input-container">
        <input
          type="text"
          :value="`${device.brand} (${device.browser})`"
          readonly
        >
      </div>
      <div class="input-container input-container--os">
        <selectize
          :value="device.os_version.id"
          @input="value => changeOsVersion(device.id, +value, index)"
        >
          <option
            v-for="version in deviceOsVersions(device.brand)"
            :key="version.id"
            :value="version.id"
          >
            {{ version.os }} {{ version.version }}
          </option>
        </selectize>
      </div>
      <a
        class="cabinet__device-remove"
        href="#"
        @click="removeDevice(device.id,index)"
      >
        <svg class="ico ico-color-remove">
          <use xlink:href="@app/assets/img/sprite-color.svg#ico-color-remove" />
        </svg>
      </a>
    </div>
    <p
      v-if="!devices.length"
      style="text-align: center;"
    >
      Устройств пока нет
    </p>
  </div>
</template>

<script>
import Selectize from 'vue2-selectize'

export default {
  name: 'ProfileDevicesListWeb',
  components: {
    Selectize
  },
  inject: [
    'isModal'
  ],
  props: {
    userDevices: Array,
    osVersionsProp: Object
  },
  data() {
    const flatOsList                 =  {};
    [].concat( ...Object.keys( this.$props.osVersionsProp ).map( ( k ) => this.$props.osVersionsProp[ k ] ) ).forEach( os => flatOsList[ os.id ] = os )

    return                           {
      devices: this.$props.userDevices,
      flatOsList: flatOsList
    }
  },
  methods: {
    deviceOsVersions(brand) {
      return brand === 'Apple' ? this.osVersionsProp.macos.concat(this.osVersionsProp.ios) : this.osVersionsProp.windows.concat(this.osVersionsProp.android)
    },
    async changeOsVersion(id, o_s_version_id, index) {
      if (!o_s_version_id) return
      if ( this.isModal ) {
        this.devices[ index ].os_version =  this.flatOsList[ o_s_version_id ] ?? undefined
      } else {
        try {
          await this.$inertia.post(
            '/profile/devices/web/change-version',
            {
              id,
              o_s_version_id
            },
            { preserveScroll: true }
          )
        } catch (e) {
          this.$handleError(e)
        }
      }
    },
    async removeDevice(id, index) {
      if ( this.isModal ) {
        this.devices                =    this.devices.filter( ( e, i ) => i != index )
      } else {
        try {
          await this.$inertia.delete(`/profile/devices/web/${id}`,
            {
              preserveScroll: true,
              onSuccess: () => {
                this.devices                =    this.devices.filter( ( e, i ) => i != index )
              }
            })
        } catch (e) {
          this.$handleError(e)
        }
      }
    },
    pushDevice( device ) {
      this.devices.push( device )
    },
    getPostData() {
      return                        this.devices.map( ( device ) => {
        let result                 =  {
          brand: device.brand,
          o_s_version_id: device.os_version.id,
          browser: device.browser,
          is_new: device.isNew ?? false
        }
        if ( device.id ) {
          result[ 'id' ]          =  device.id
        }

        return                     result
      } )
    },
    clearFlagIsNew() {
      this.devices.forEach((device) => {
        if (device.isNew && device.isNew === true) {
          device.isNew = false
        }
      })
    }
  }
}
</script>
