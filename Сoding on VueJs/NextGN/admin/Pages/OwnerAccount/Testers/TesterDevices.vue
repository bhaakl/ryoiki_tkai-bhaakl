<template>
   <CDataTable
      :items="devices"
      :fields="fields"
      :items-per-page="5"
      sorter
      :active-page="activePage"
      :pagination="{ doubleArrows: false, align: 'center' }"
      :noItemsView="{ noResults: 'Нет подходящих вариантов', noItems: 'Пока ни одного' }"
   >
      <template #version="{ item: { pivot: {o_s_version_id} } }">
         <td>{{ version(o_s_version_id) }}</td>
      </template>
      <template #no-items-view>
         <p class="font-weight-bold text-center mt-2">Пока ни одного</p>
      </template>
   </CDataTable>
</template>

<script>
export default {
   name: 'TesterDevices',
   props: {
      devices: Array,
      osVersions: Array,
   },
   data() {
      return {
         fields: [
            { key: 'brand', label: 'Бренд' },
            { key: 'model', label: 'Модель', sorter: false },
            { key: 'os', label: 'ОС' },
            { key: 'version', label: 'Версия', sorter: false },
         ],
         activePage: 1,
      };
   },
   methods: {
      version(versionId) {
         const osVersion = this.osVersions.find(v => v.id === versionId);
         return osVersion.version;
      },
   },
};
</script>
