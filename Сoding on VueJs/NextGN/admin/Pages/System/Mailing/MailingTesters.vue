<template>
   <CDataTable
      hover
      clickable-rows
      columnFilter
      @row-clicked="tester => $inertia.visit(`/admin/testers/${tester.id}`)"
      :items="filteredTesters"
      :fields="fields"
      :items-per-page="5"
      :active-page="activePage"
      :pagination="{ doubleArrows: false, align: 'center' }"
   >
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
      <template #no-items-view>
         <p class="font-weight-bold text-center mt-2">Пока ни одного</p>
      </template>
   </CDataTable>
</template>

<script>
export default {
   name: 'MailingTesters',
   props: {
      testers: Array,
      regions: Array,
   },
   data() {
      return {
         fields: [
            { key: 'name', label: 'Имя', _classes: 'font-weight-bold' },
            { key: 'role_label', label: 'Роль' },
            { key: 'devices', label: 'Устройства' },
            { key: 'oses', label: 'ОС' },
            { key: 'osVersionsLabel', label: 'Версии' },
            { key: 'occupation', label: 'Род занятий' },
            { key: 'region', label: 'Регион' },
            { key: 'sex', label: 'Пол' },
         ],
         activePage: 1,
         filteredTesters: [],
      };
   },
   methods: {
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
            osVersionsLabel: tester.osVersions.join(', '),
            // role_label: tester.role.label,
         };
      });
   },
};
</script>
