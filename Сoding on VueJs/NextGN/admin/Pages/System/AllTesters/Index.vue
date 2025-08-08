<template>
    <CRow>
        <export-testers-to-email ref="testerExportModal"/>
        <CCol col="12">
            <CCard>
                <CCardHeader class="d-flex justify-content-between align-items-end">
                    <CButton size="sm" color="dark" @click.prevent="$refs.testerExportModal.show()">Экспорт тестировщиков</CButton>
                </CCardHeader>
                <CCardHeader class="d-flex justify-content-between">
                    <h4>Тестировщики</h4>
                </CCardHeader>
                <CCardBody>
                    <CDataTable
                            striped
                            :table-filter="{ placeholder: 'Поиск', label: ' ', external: true }"
                            :table-filter-value.sync="filterQueryParams.search"
                            :sorter="{ external: true, resetable: false }"
                            :sorter-value="{ column: filterQueryParams.sort_column, asc: filterQueryParams.sort_mode === 'asc' }"
                            @update:sorter-value="sort"
                            :items="data"
                            :fields="fields"
                            :noItemsView="{ noResults: 'Нет подходящих вариантов', noItems: 'Нет результатов по вашему запросу' }"
                    >
                        <template #id="{ item: { id } }">
                           <td>
                              <a :href="`/admin/all-testers/${id}`">{{ id }}</a>
                           </td>
                        </template>
                        <template #role="{ item: { role: { name, label } } }">
                            <td>
                                <CBadge :color="getBadge(name)" class="mr-1 mb-1">
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
                        <template #actions="{ item }">
                            <td style="width:125px">
                                <a :href="`/admin/users/${item.id}/edit`" target="_blank" class="btn btn-info">
                                    Изменить
                                </a>
                            </td>
                        </template>
                    </CDataTable>
                    <CPagination :active-page.sync="filterQueryParams.page"
                                 :limit="per_page"
                                 :pages="last_page"
                                 arrows dots align="center"/>
                </CCardBody>
            </CCard>
        </CCol>
    </CRow>
</template>

<script>
import Layout from '@admin/containers/TheContainer';
import ExportTestersToEmail from '@admin/modals/ExportTestersToEmail';
import parallax from "swiper/src/components/parallax/parallax";

export default {
    name: 'TestersIndex',
    computed: {
        parallax() {
            return parallax
        }
    },
    layout: Layout,
    components: {
        ExportTestersToEmail
    },
    props: {
        data: Array,
        current_page: Number,
        from: Number,
        to: Number,
        last_page: Number,
        per_page: Number,
    },
    data() {
        return {
            fields: [
                {key: 'id', label: 'ID', sorter: true},
                {key: 'name', label: 'Имя', _classes: 'font-weight-bold'},
                {key: 'email', label: 'E-mail'},
                {key: 'role', label: 'Роль', sorter: false},
                {key: 'oses', label: 'ОС', sorter: false},
                {key: 'actions', label: 'Действия', sorter: false},
            ],
            filterQueryParams: {
                search: '',
                sort_mode: 'desc',
                sort_column: 'id',
                page: this.current_page
            },
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
        sort({asc, column}) {
            this.filterQueryParams.sort_mode = asc === true ? 'asc' : 'desc';
            this.filterQueryParams.sort_column = column || 'id';
        },
    },
    watch: {
        filterQueryParams: {
            handler() {
                this.$inertia.get(`/admin/all-testers`,this.filterQueryParams,{
                    preserveState : true,
                    replace       : true,
                    preserveScroll: true,
                });
            },
            deep: true
        },
    },
};
</script>
