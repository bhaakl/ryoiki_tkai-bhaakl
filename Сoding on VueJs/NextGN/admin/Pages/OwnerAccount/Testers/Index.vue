<template>
    <CRow>
        <export-testers-to-email :only_register="true" ref="testerExportModal"/>
        <CCol col="12">
            <CCard>
                <CCardHeader class="d-flex justify-content-between align-items-end">
                    <CButton size="sm" color="dark" @click.prevent="$refs.testerExportModal.show()">Экспорт
                        тестировщиков
                    </CButton>
                </CCardHeader>
                <CCardHeader class="d-flex justify-content-between">
                    <h4>Тестировщики</h4>
                </CCardHeader>
                <CCardBody>
                    <CDataTable
                            hover
                            striped
                            clickable-rows
                            :table-filter="{ placeholder: 'Поиск', label: ' ', external: true }"
                            :table-filter-value.sync="filterQueryParams.search"
                            :sorter="{ external: true, resetable: false }"
                            :sorter-value="{ column: filterQueryParams.sort_column, asc: filterQueryParams.sort_mode === 'asc' }"
                            @row-clicked="item => $inertia.visit(`/admin/testers/${item.id}`)"
                            @update:sorter-value="sort"
                            :items="data"
                            :fields="fields"
                            :noItemsView="{ noResults: 'Нет подходящих вариантов', noItems: 'Нет результатов по вашему запросу' }"
                    >
                        <template #id="{ item: { id } }">
                            <td>{{ id }}</td>
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
                        <template #phone="{ item: { phone } }">
                            <td>
                                {{ phone || '-' }}
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

export default {
    name: 'TestersIndex',
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
                {key: 'phone', label: 'Номер телефона', _classes: 'font-weight-bold', sorter: false},
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
                this.$inertia.get(`/admin/testers`,this.filterQueryParams,{
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
