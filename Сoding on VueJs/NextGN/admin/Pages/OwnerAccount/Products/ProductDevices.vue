<template>
    <div>
        <CDataTable
                :items="devices"
                :fields="fields"
                :items-per-page="itemsPerPage"
                :active-page="activePage"
                :pagination="{ doubleArrows: false, align: 'center' }"
        >
            <template #actions="{item: {id}}">
                <td>
                    <CButton
                            @click="$inertia.delete(`/admin/products/${productId}/devices/${id}`, { preserveScroll: true })"
                            color="danger"
                    >
                        Удалить
                    </CButton>
                </td>
            </template>
            <template #no-items-view>
                <p class="mt-2 text-center font-weight-bold">Пока ни одной</p>
            </template>
        </CDataTable>
        <CButton @click="showAddModal" color="success" class="float-right mt-3">
            Добавить
        </CButton>

        <CModal color="success" :closeOnBackdrop="false" centered :show.sync="addModal">
            <template #header>
                <h6 class="modal-title">Добавить устройство</h6>
            </template>
            <CRow>
                <CCol col="3" class="d-flex align-items-center">
                    <label class="mb-0">Бренд</label>
                </CCol>
                <CCol col="9">
                    <v-select v-model="newBrand" :options="brandOptions" :clearable="false"/>
                </CCol>
            </CRow>
            <CRow>
                <CCol col="3" class="d-flex align-items-center">
                    <label class="mb-0">Модель</label>
                </CCol>
                <CCol col="9">
                    <v-select
                            v-model="newModel"
                            :options="brandModels"
                            :disabled="!brandModels || !brandModels.length"
                            :clearable="false"
                            class="mt-2"
                    />
                </CCol>
            </CRow>
            <template #footer>
                <CButton @click="addDevice(true)" :disabled="isBtnDisabled" color="success">Добавить</CButton>
                <CButton @click="addDevice(false)" color="light">Отмена</CButton>
            </template>
        </CModal>
    </div>
</template>

<script>
import {required} from 'vuelidate/lib/validators';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';

export default {
    name: 'ProductDevices',
    props: {
        productId: Number,
        devices: Array,
        brands: {
            default: () => [],
            type: Array
        },
        platform: String,
    },
    components: {
        vSelect,
    },
    data() {
        return {
            fields: [
                {key: 'brand', label: 'Бренд', _style: 'min-width: 100px;'},
                {key: 'model', label: 'Модель', _style: 'min-width: 100px;'},
                {key: 'actions', label: 'Действия', _style: 'min-width: 100px;'},
            ],
            activePage: 1,
            itemsPerPage: 5,
            addModal: false,
            newBrand: null,
            newModel: null,
            brandModels: [],
        };
    },
    computed: {
        brandOptions() {
            return this.platform === 'ios'
                ? this.brands.filter(b => b === 'Apple')
                : this.brands.filter(b => b !== 'Apple');
        },
        isBtnDisabled() {
            if (!this.newBrand || !this.newModel) {
                return true;
            }

            return false;
        },
    },
    methods: {
        async showAddModal() {
            await this.$inertia.reload({
                only: ['brands']
            })
            this.addModal = true;
        },
        async addDevice(modalValue) {
            this.addModal = false;
            if (this.isBtnDisabled || !modalValue) {
                return;
            }
            try {
                await this.$inertia.post(
                    `/admin/products/devices`,
                    {
                        product_id: this.productId,
                        device_id: this.newModel.value,
                    },
                    {preserveScroll: true}
                );
                this.clear();
            } catch (e) {
                this.$handleError(e);
            }
        },
        async getBrandModels(brand) {
            try {
                await this.$inertia.post(
                    `/admin/products/devices/brand-models`,
                    {
                        brand,
                    },
                    {
                        preserveScroll: true,
                        onSuccess: event => {
                            this.brandModels = event.props.responseData.map(device => ({
                                value: device.id,
                                label: device.model,
                            }));
                            if (this.brandModels && this.brandModels.length) {
                                this.newModel = this.brandModels[0];
                            }
                        },
                    }
                );
            } catch (e) {
                this.$handleError(e);
            }
        },
        setOSVersions(brand) {
            if (brand === 'Apple') {
                this.osVersions = this.devices.osVersions.ios;
            }
        },
        async removeDevice(deviceId) {
            try {
                await this.$inertia.delete(`/admin/products/${this.productId}/devices/${deviceId}`, {
                    preserveScroll: true,
                });
            } catch (e) {
                this.$handleError(e);
            }
        },
        clear() {
            this.newBrand = null;
            this.newModel = null;
            this.brandModels = [];
        },
    },
    watch: {
        async newBrand(brand) {
            this.brandModels = [];
            this.newModel = null;
            if (!brand) return;
            await this.getBrandModels(brand);
        },
    },
};
</script>
