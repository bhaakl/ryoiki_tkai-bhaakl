<template>
  <div class="cabinet__tab">
    <p
      v-if="!$props.isModal"
      class="cabinet__tab-title"
    >
      Мои устройства
    </p>
    <p class="cabinet__tab-sub-title">
      Мобильные устройства
    </p>
    <DevicesListMobile
      ref="moblieDeviceList"
      :user-devices="devices.userDevicesMobile"
      :os-versions-prop="devices.osVersions"
    />
    <div class="cabinet__separator" />
    <p class="cabinet__tab-sub-title">
      Web-устройства
    </p>
    <DevicesListWeb
      ref="webDeviceList"
      :user-devices="devices.userDevicesWeb"
      :os-versions-prop="devices.osVersions"
    />
    <div class="cabinet__separator" />
    <p class="cabinet__tab-sub-title">
      Smart TV устройства
    </p>
    <DevicesListSmartTv
      ref="smartTvDeviceList"
      :user-devices="devices.userDevicesSmartTv"
      :os-versions-prop="devices.osVersions"
    />
    <div class="cabinet__separator" />
    <template v-if="$props.isModal">
      <div class="cabinet-inputs__item row-container">
        <button
          class="btn btn--border btn--add-device"
          :class="{ 'active' : addMode == 0 }"
          @click.prevent="setAddMode( 0 )"
        >
          Добавить мобильное устройство
        </button>
        <button
          class="btn btn--border btn--add-device"
          :class="{ 'active' : addMode == 1 }"
          @click.prevent="setAddMode( 1 )"
        >
          Добавить web-устройство
        </button>
        <button
          class="btn btn--border btn--add-device"
          :class="{ 'active' : addMode == 2 }"
          @click.prevent="setAddMode( 2 )"
        >
          Добавить Smart TV устройство
        </button>
      </div>
      <template v-if="addMode === 0">
        <DevicesAddMobile
          :brands="devices.brands"
          :os-versions-prop="devices.osVersions"
          @on-device-add="( d ) => onDeviceAdd( 'mobile', d )"
        />
      </template>
      <template v-else-if="addMode === 1">
        <DevicesAddWeb
          :brands="devices.brands"
          :os-versions-prop="devices.osVersions"
          @on-device-add="( d ) => onDeviceAdd( 'web', d )"
        />
      </template>
      <template v-else-if="addMode === 2">
        <DevicesAddSmartTv
          :brands="devices.brands_smart_tv"
          :os-versions-prop="devices.osVersions"
          @on-device-add="( d ) => onDeviceAdd( 'smartTv', d )"
        />
      </template>
    </template>
    <template v-else>
      <p class="cabinet__tab-sub-title">
        Добавить мобильное устройство
      </p>
      <DevicesAddMobile
        :brands="devices.brands"
        :os-versions-prop="devices.osVersions"
      />
      <div class="cabinet__separator" />
      <p class="cabinet__tab-sub-title">
        Добавить web-устройство
      </p>
      <DevicesAddWeb
        :brands="devices.brands"
        :os-versions-prop="devices.osVersions"
      />
      <div class="cabinet__separator" />
      <p class="cabinet__tab-sub-title">
        Добавить Smart TV устройство
      </p>
      <DevicesAddSmartTv
        :brands="devices.brands_smart_tv"
        :os-versions-prop="devices.osVersions"
      />
    </template>
  </div>
</template>

<script>
import Layout from '@app/components/Layout/Index'
import ProfileLayout from './ProfileLayout'
import DevicesListMobile from './components/DevicesListMobile'
import DevicesAddMobile from './components/DevicesAddMobile'
import DevicesListWeb from './components/DevicesListWeb'
import DevicesListSmartTv from './components/DevicesListSmartTv'
import DevicesAddWeb from './components/DevicesAddWeb'
import DevicesAddSmartTv from './components/DevicesAddSmartTv'

export default {
  name: 'ProfileDevices',
  components: {
    DevicesListMobile,
    DevicesAddMobile,
    DevicesListWeb,
    DevicesListSmartTv,
    DevicesAddWeb,
    DevicesAddSmartTv
  },
  provide() {
    return                           {
      isModal: this.$props.isModal
    }
  },
  layout: [Layout, ProfileLayout],
  props: {
    devices: Object,
    isModal: Boolean
  },
  data() {
    return                           {
      addMode: undefined
    }
  },
  methods: {
    setAddMode( m ) {
      this.addMode                  =  m
    },
    onDeviceAdd( deviceType, device ) {
      const data = { ...device, isNew: true }
      switch   ( deviceType ) {
      case  'mobile':
        this.$refs.moblieDeviceList.pushDevice( data )
        break
      case  'web':
        this.$refs.webDeviceList.pushDevice( data )
        break
      case  'smartTv':
        this.$refs.smartTvDeviceList.pushDevice( data )
        break
      }
    },
    getPostData() {
      return                        {
        'mobile': this.$refs.moblieDeviceList.getPostData(),
        'web': this.$refs.webDeviceList.getPostData(),
        'smartTv': this.$refs.smartTvDeviceList.getPostData()
      }
    },
    clearFlagIsNew() {
      this.$refs.moblieDeviceList.clearFlagIsNew()
      this.$refs.webDeviceList.clearFlagIsNew()
      this.$refs.smartTvDeviceList.clearFlagIsNew()
    }
  }
}
</script>

<style scoped>
.btn, .btn--add-device, .btn--border {
  box-sizing: border-box;
}

.btn--add-device:disabled {
   background: none !important;
   color: #666d74; 
   border: 2px solid #e2e5eb;
}
.btn.active{
   background: #fbbf1b;
   color : white;
}
.cabinet-inputs__item.row-container{
   display: flex;
   justify-content: space-between;
}

</style>
