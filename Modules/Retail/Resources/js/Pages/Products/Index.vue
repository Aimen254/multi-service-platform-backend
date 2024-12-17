<template>

    <Head title="Products" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Products`" :path="`Products`" />
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
                        :newKeyword="searchedKeyword" :maxPrice="maxPrice" :minPrice="minPrice">
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
                                    <option value="variants_error">Variants Error</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-5">
                            <label class="form-label fw-bold">Tag:</label>
                            <div>
                                <select2
                                    class="form-control-md text-capitalize form-control-solid"
                                    v-model="filterForm.tag"
                                    :options="standardTags"
                                    :settings="{ dropdownParent: '#filterMenu' }"
                                    placeholder="Select Tag"
                                />
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
                    :href="route('retail.dashboard.business.products.create', [getSelectedModuleValue(), getSelectedBusinessValue()])"
                    class="btn btn-sm btn-primary">
                <span class="svg-icon svg-icon-2">
                    <inline-svg :src="'/images/icons/add.svg'" />
                </span>
                Add Product
                </Link>
            </div>
        </template>
        <div class="card">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <SearchInput :module="module" :callType="type" :searchedKeyword="searchedKeyword" :filterFormData="filterForm" />
                </h3>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bolder border-0 text-muted bg-light">
                                <th class="ps-4 min-w-120px rounded-start">Name</th>
                                <th class="ps-4 min-w-120px rounded-start">Price</th>
                                <th class="ps-4 min-w-120px rounded-start">Weight</th>
                                <th class="ps-4 min-w-120px rounded-start">Status</th>
                                <th class="min-w-120px">Created At</th>
                                <th class="min-w-120px rounded-end">Action</th>
                            </tr>
                        </thead>
                        <tbody v-if="products && products.data.length > 0">
                            <template v-for="product in products.data" :key="product.id">
                                <tr
                                    :style="[product.status == 'tags_error' || product.status == 'variants_error' ? 'background:#ffe6e6' : null]">
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="image-input image-input-empty">
                                                <div class="image-input-wrapper bg-image-position-contain w-50px h-50px"
                                                    :style="{ 'background-image': 'url(' + getImage(product.main_image ? product.main_image.path : null, true, 'product', product.main_image ? product.main_image.is_external : 0) + ')' }">
                                                </div>

                                            </div>
                                            <div class="d-flex justify-content-start flex-column mx-2">
                                                <span class="text-dark fw-bolder text-hover-primary mb-1 fs-6"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    :data-bs-original-title="product.name">
                                                    {{ ellipsis(product.name) }}
                                                </span>
                                                <span class="text-muted fw-bold text-muted d-block fs-7">
                                                    <span v-if="product.is_featured" style="margin-right:10px">
                                                        <i class="fas fa-check-circle text-success"></i>
                                                        Featured
                                                    </span>
                                                </span>
                                            </div>
                                            <div>
                                                <span class="fw-bolder d-block" v-if="product.status == 'tags_error'">
                                                    <i class="fa fa-exclamation-triangle text-danger"
                                                        aria-hidden="true"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7 text-capitalize">
                                            <span class="fw-normal text-muted fs-7">$</span>{{ product.price }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7 text-capitalize">
                                            {{ product.weight }} <span class="fw-normal text-muted fs-9"> {{
                                                    product.weight_unit
                                            }}</span>
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            :class="{ 'badge badge-light-success': product.status == 'active', 'badge badge-light-danger': product.status == 'inactive' || product.status == 'tags_error' || product.status == 'variants_error' }"
                                            class="text-capitalize">
                                            {{ getGroupName(product.status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7">{{
                                                formatDate(product.created_at)
                                        }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <Toggle v-if="checkUserPermissions('edit_products')"
                                                :status="booleanStatusValue(product.status)"
                                                @click.prevent="changeStatus(product.uuid)" />
                                            <edit-section iconType="link" permission="edit_products"
                                                :url="route('retail.dashboard.business.products.edit', [getSelectedModuleValue(), getSelectedBusinessValue(), product.uuid])" />
                                            <delete-section permission="delete_products"
                                                :url="route('retail.dashboard.business.products.destroy', [getSelectedModuleValue(), getSelectedBusinessValue(), product.uuid])" 
                                                :currentPage="products.current_page" :currentCount="products.data.length"/>
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
            <pagination :meta="products" :keyword="searchedKeyword" :selectedFilters="filterForm" :callType="type" />
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
import Toggle from '@/Components/ToggleButton.vue';
import SearchInput from '@/Components/SearchInput.vue';
import Filter from "@/Components/Filter.vue";
import VueSlider from 'vue-3-slider-component';

export default {
    components: {
        AuthenticatedLayout,
        Head,
        Header,
        Link,
        Pagination,
        EditSection,
        DeleteSection,
        Toggle,
        SearchInput,
        InlineSvg,
        Breadcrumbs,
        Filter,
        VueSlider
    },

    props: ['productsList', 'searchedKeyword', 'orderBy', 'status', 'maxPrice', 'minPrice', 'barValueMin', 'barValueMax', 'standardTags', 'tag'],
    data() {
        return {
            products: this.productsList,
            type: 'products',
            urlForFilter: 'retail.dashboard.business.products.index',
            filterForm: {
                orderBy: this.orderBy ? this.orderBy : 'desc',
                status: this.status ? this.status : '',
                barMinValue: this.barValueMin ? parseFloat(this.barValueMin) : this.minPrice,
                barMaxValue: this.barValueMax ? parseFloat(this.barValueMax) : this.maxPrice,
                tag: this.tag ? this.tag : null
            },
            value: [this.barValueMin ? parseFloat(this.barValueMin) : this.minPrice,
                this.barValueMax ? parseFloat(this.barValueMax) : this.maxPrice],
            max: this.maxPrice + 10,
            module: 'retail'

        }
    },
    watch: {
        productsList: {
            handler(productsList) {
                this.products = productsList
            },
            deep: true
        },

    },

    methods: {
        UpdateValues(e) {
            this.filterForm.barMinValue = e[0];
            this.filterForm.barMaxValue = e[1];
        },
        changeStatus(uuid) {
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

                    this.$inertia.visit(route('retail.dashboard.business.product.status', [this.getSelectedModuleValue(), this.getSelectedBusinessValue(), uuid]), {
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
