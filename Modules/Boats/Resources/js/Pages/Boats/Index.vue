<template>
    <Head title="Boats" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Boats`" :path="`Boats`" />
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <div class="me-4">
                    <!--begin::Menu-->
                    <a href="#" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder"
                        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                        <span class="svg-icon svg-icon-5 svg-icon-gray-500 me-1">
                            <inline-svg :src="'/images/icons/filter.svg'" />
                        </span>
                        Filter
                    </a>
                    <Filter :filterData="filterForm" :callType="type" :business="business" :url="urlForFilter"
                        :newKeyword="searchedKeyword" :maxPrice="maxPrice" :minPrice="minPrice" :year="filterForm.year">
                        <div class="mb-5">
                            <label class="form-label fw-bold">Type:</label>
                            <div>
                                <select name="name" id="name"
                                    class="form-select form-select-solid text-muted form-select-sm"
                                    v-model="filterForm.type">
                                    <option class="fw-bold" value="" disabled hidden>Select Type</option>
                                    <option value="new" selected>New</option>
                                    <option value="used">Used</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-5">
                            <label class="form-label fw-bold">Order By:</label>
                            <div>
                                <select name="name" id="name"
                                    class="form-select form-select-solid text-muted form-select-sm"
                                    v-model="filterForm.orderBy">
                                    <option value="desc" selected>Descending</option>
                                    <option value="asc">Ascending</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-5">
                            <label class="form-label fw-bold">Status:</label>
                            <div>
                                <select v-model="filterForm.status" aria-placeholder="Select"
                                    class="form-select form-select-solid text-muted form-select-sm">
                                    <option class="fw-bold" value="" disabled hidden>Select Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="tags_error">Tags Error</option>
                                    <option value="pending">Pending</option>
                                    <option value="sold">Sold</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-5">
                            <label class="form-label fw-bold">Tag:</label>
                            <div>
                                <select2 class="form-control-md text-capitalize form-control-solid" v-model="filterForm.tag"
                                    :options="standardTags" :settings="{ dropdownParent: '#filterMenu' }"
                                    placeholder="Select Tag" />
                            </div>
                        </div>
                        <div class="mb-5">
                            <label class="form-label fw-bold">Year:</label>
                            <div>
                                <Datepicker v-model="filterForm.year" year-picker></Datepicker>
                            </div>
                        </div>
                        <div class="mb-5" v-if="minPrice != maxPrice">
                            <label class="form-label fw-bold">Price:</label>
                            <div class="mx-2">
                                <VueSlider @change="UpdateValues" v-model="value" :min="0"
                                           :max="max" tooltip="always"/>
                            </div>
                        </div>
                    </Filter>
                    <!--end::Menu-->
                </div>
                <Link v-if="checkUserPermissions('add_products')"
                    :href="route('boats.dashboard.dealership.boats.create', [this.getSelectedModuleValue(), getSelectedBusinessValue()])"
                    class="btn btn-sm btn-primary">
                <span class="svg-icon svg-icon-2">
                    <inline-svg :src="'/images/icons/add.svg'" />
                </span>
                Add Boat
                </Link>
            </div>
        </template>
        <div class="card">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <BoatsSearchInput :module="module" :callType="type" :searchedKeyword="searchedKeyword"
                        :filterFormData="filterForm" :year="filterForm.year" />
                </h3>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bolder border-0 text-muted bg-light">
                                <th class="ps-4 min-w-120px rounded-start">Name</th>
                                <th class="ps-4 min-w-120px rounded-start">Type</th>
                                <th class="ps-4 min-w-120px rounded-start">Price</th>
                                <th class="ps-4 min-w-120px rounded-start">Year</th>
                                <th class="ps-4 min-w-120px rounded-start">Status</th>
                                <th class="min-w-120px">Created At</th>
                                <th class="min-w-120px rounded-end">Action</th>
                            </tr>
                        </thead>
                        <tbody v-if="vehicles && vehicles.data.length > 0">
                            <template v-for="vehicle in vehicles.data" :key="vehicle.id">
                                <tr
                                    :style="[vehicle.status == 'tags_error' || vehicle.status == 'variants_error' ? 'background:#ffe6e6' : null]">
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="image-input image-input-empty">
                                                <div class="image-input-wrapper bg-image-position-contain w-50px h-50px"
                                                    :style="{ 'background-image': 'url(' + getImage(vehicle.main_image ? vehicle.main_image.path : null, true, 'product', vehicle.main_image ? vehicle.main_image.is_external : 0) + ')' }">
                                                </div>

                                            </div>
                                            <div class="d-flex justify-content-start flex-column mx-2">
                                                <span class="text-dark fw-bolder text-hover-primary mb-1 fs-6"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    :data-bs-original-title="vehicle.name">
                                                    {{ ellipsis(vehicle.name) }}
                                                </span>
                                                <span class="text-muted fw-bold text-muted d-block fs-7">
                                                    <span v-if="vehicle.is_featured" style="margin-right:10px">
                                                        <i class="fas fa-check-circle text-success"></i>
                                                        Featured
                                                    </span>
                                                </span>
                                            </div>
                                            <div>
                                                <span class="fw-bolder d-block" v-if="vehicle.status == 'tags_error'">
                                                    <i class="fa fa-exclamation-triangle text-danger"
                                                        aria-hidden="true"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7 text-capitalize">
                                            <span class="fw-normal text-muted fs-7"></span>{{ vehicle.vehicle?.type }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7 text-capitalize">
                                            <span class="fw-normal text-muted fs-7">$</span>{{ vehicle.price }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7 text-capitalize">
                                            <span class="fw-normal text-muted fs-7"></span>{{ getYear(vehicle.vehicle?.year)
                                            }}
                                        </span>
                                    </td>
                                    <td>
                                        <span v-if="vehicle.status == 'tags_error'"
                                            :class="{ 'badge badge-light-success': vehicle.status == 'active', 'badge badge-light-danger': vehicle.status == 'inactive' || vehicle.status == 'tags_error' }"
                                            class="text-capitalize">
                                            {{ 'Tags Error' }}
                                        </span>
                                        <select v-model="vehicle.status" v-else
                                            @change.prevent="changeStatus($event, vehicle.uuid)"
                                            class="form-select form-select-solid text-capitalize w-100">
                                            <option value="active">Active</option>
                                            <option value="inactive">InActive</option>
                                            <option value="pending">Pending</option>
                                            <option value="sold">Sold</option>
                                        </select>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7">{{
                                            formatDate(vehicle.created_at)
                                        }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <edit-section iconType="link" permission="edit_products"
                                                :url="route('boats.dashboard.dealership.boats.edit', [getSelectedModuleValue(), getSelectedBusinessValue(), vehicle.uuid])" />
                                            <delete-section permission="delete_products"
                                                :url="route('boats.dashboard.dealership.boats.destroy', [getSelectedModuleValue(), getSelectedBusinessValue(), vehicle.uuid])" 
                                                :currentPage="vehicles.current_page" :currentCount="vehicles.data.length"/>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <div v-else class="p-4 text-muted">
                            Record Not Found
                        </div>
                    </table>
                </div>
            </div>
            <pagination :meta="vehicles" :keyword="searchedKeyword" :selectedFilters="filterForm" :callType="type"
                :year="filterForm.year" />
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import { Head, Link } from '@inertiajs/inertia-vue3';
import Header from '@/Components/Header.vue';
import Pagination from '@/Components/Pagination.vue';
import Helpers from '@/Mixins/Helpers';
import EditSection from '@/Components/EditSection.vue';
import DeleteSection from '@/Components/DeleteSection.vue';
import InlineSvg from 'vue-inline-svg'
import Filter from "@/Components/Filter.vue";
import VueSlider from 'vue-3-slider-component';
import BoatsSearchInput from "../../Components/BoatsSearchInput.vue";
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

export default {
    components: {
        AuthenticatedLayout,
        Head,
        Header,
        Link,
        Pagination,
        EditSection,
        DeleteSection,
        BoatsSearchInput,
        InlineSvg,
        Breadcrumbs,
        Filter,
        VueSlider,
        Datepicker
    },

    props: ['vehiclesList', 'tag', 'standardTags', 'barValueMax', 'barValueMin', 'minPrice', 'maxPrice', 'status', 'orderBy', 'searchedKeyword', 'type', 'year'],
    data() {
        return {
            vehicles: this.vehiclesList,
            type: 'boats',
            urlForFilter: 'boats.dashboard.dealership.boats.index',
            filterForm: {
                orderBy: this.orderBy ? this.orderBy : 'desc',
                status: this.status ? this.status : '',
                barMinValue: this.barValueMin ? parseFloat(this.barValueMin) : this.minPrice,
                barMaxValue: this.barValueMax ? parseFloat(this.barValueMax) : this.maxPrice,
                tag: this.tag ? this.tag : null,
                type: this.type ? this.type : '',
                year: this.year ? this.year : null
            },
            value: [this.barValueMin ? parseFloat(this.barValueMin) : this.minPrice,
                this.barValueMax ? parseFloat(this.barValueMax) : this.maxPrice],
            max: this.maxPrice + 10,
            module: 'boats'
        }
    },
    watch: {
        vehiclesList: {
            handler(vehiclesList) {
                this.vehicles = vehiclesList
            },
            deep: true
        },

    },

    methods: {
        UpdateValues(e) {
            this.filterForm.barMinValue = e[0];
            this.filterForm.barMaxValue = e[1];
        },
        changeStatus(event, uuid) {
            this.form = this.$inertia.form({
                status: event.target.value
            });
            this.swal.fire({
                title: "",
                html: "<h1 class='text-lg text-gray-800 mb-1'>Change Status</h1><p class='text-base'>Are you sure you want to change status?</p>",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: 'No',
                confirmButtonText: "Yes",
                customClass: {
                    confirmButton: 'danger'
                }
            }).then((result) => {
                if (result.value) {
                    showWaitDialog()
                    this.form.post(route('boats.dashboard.dealership.boat.status', [this.getSelectedModuleValue(), this.getSelectedBusinessValue(), uuid]), {
                        preserveScroll: false,
                        onSuccess: () => hideWaitDialog()
                    })
                }
            })
        }
    },
    mixins: [Helpers]
}
</script>
