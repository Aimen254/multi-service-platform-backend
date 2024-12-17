<template>
    <Head title="Service Providers" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`All Service Providers`" :path="`Service Providers`" />
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
                    <Filter :filterData="filterForm" :callType="type" :url="urlForFilter" :newKeyword="searchedKeyword">
                        <div class="mb-10">
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
                        <div class="mb-10">
                            <label class="form-label fw-bold">Status:</label>
                            <div class="mx-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" value="active" v-model="filterForm.status">
                                    <label class="form-check-label">Active</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" value="inactive"
                                        v-model="filterForm.status">
                                    <label class="form-check-label">Inactive</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-10">
                            <label class="form-label fw-bold">Review:</label>
                            <div class="mx-2">
                                <shapla-star-rating v-model="filterForm.reviewRating" :active-color="['#FFDF00']"
                                    :color="['#fede00']" class="fs-2 ps-1" />
                            </div>
                        </div>
                    </Filter>
                    <!--end::Menu-->
                </div>
                <Link :href="route('services.dashboard.service-provider.create', getSelectedModuleValue())"
                    v-if="checkUserPermissions('add_business')" class="btn btn-sm btn-primary">
                <span class="svg-icon svg-icon-2">
                    <inline-svg :src="'/images/icons/add.svg'" />
                </span>
                Add Service Provider
                </Link>
            </div>
        </template>
        <div class="card">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <ServicesSearchInput :callType="type" :searchedKeyword="searchedKeyword" :filterFormData="filterForm" />
                </h3>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bolder border-0 text-muted bg-light">
                                <th class="ps-4 w-25 rounded-start">Name</th>
                                <th class="min-w-120px">Service Provider Owner</th>
                                <th class="min-w-120px">Rating</th>
                                <th class="min-w-120px">Status</th>
                                <th class="min-w-120px">Created At</th>
                                <th class="min-w-120px rounded-end">Action</th>
                            </tr>
                        </thead>
                        <tbody v-if="businesses && businesses.data.length > 0">
                            <template v-for="business in businesses.data" :key="business.id">
                                <tr>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div v-if="business.logo != null">
                                                <div class="image-input image-input-empty">
                                                    <div class="image-input-wrapper w-50px h-50px"
                                                        :style="{ 'background-image': 'url(' + getImage(business.logo.path, true, 'logo') + ')' }">
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-else>
                                                <div class="image-input image-input-empty ">
                                                    <div class="image-input-wrapper w-50px h-50px"
                                                        :style="{ 'background-image': 'url(' + getImage(false, true, 'logo') + ')' }">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-start flex-column mx-2">
                                                <span class="text-dark fw-bolder text-hover-primary mb-1 fs-6"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    :data-bs-original-title="business.name">
                                                    {{ ellipsis(business.name) }}</span>
                                                <span v-if="business.is_featured"
                                                    class="text-muted fw-bold text-muted d-block fs-7">
                                                    <span style="margin-right:10px">
                                                        <i class="fas fa-check-circle text-success"></i> Featured
                                                    </span>

                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span v-if="business.business_owner"
                                            class="badge badge-light-primary fs-7 fw-bold text-capitalize">
                                            {{ business.business_owner.first_name }} {{ business.business_owner.last_name }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7">
                                            <shapla-star-rating v-model="business.reviews_avg" :is-static="true"
                                                :active-color="['#ffad0f']" :color="['#ffad0f']" :padding="['4']" />
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge fs-7 fw-bold text-capitalize"
                                            :class="{ 'badge-light-success': business.status == 'active', 'badge-light-danger': business.status === 'inactive' }">{{
                                                business.status }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7">{{
                                            formatDate(business.created_at) }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <Toggle v-if="checkUserPermissions('edit_business')"
                                                :status="booleanStatusValue(business.status)"
                                                @click.prevent="changeStatus(business)" />
                                            <delete-section permission="delete_business"
                                                :url="route('services.dashboard.service-provider.destroy', [getSelectedModuleValue(), business.uuid])" 
                                                :currentPage="businesses.current_page" :currentCount="businesses.data.length"/>
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
            <pagination :meta="businesses" :keyword="searchedKeyword" :selectedFilters="filterForm" :callType="type" />
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import { Head, Link } from '@inertiajs/inertia-vue3'
import Helpers from '@/Mixins/Helpers'
import InlineSvg from 'vue-inline-svg'
import Pagination from '@/Components/Pagination.vue'
import Toggle from '@/Components/ToggleButton.vue'
import Filter from "@/Components/Filter.vue";
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import ShaplaStarRating from '@shapla/vue-star-rating';
import '@shapla/vue-star-rating/dist/style.css';
import DeleteSection from "@/Components/DeleteSection.vue";
import EditSection from "@/Components/EditSection.vue";
import ServicesSearchInput from '../../Components/ServicesSearchInput.vue'

export default {
    components: {
        AuthenticatedLayout,
        Head,
        Link,
        InlineSvg,
        Pagination,
        Toggle,
        Filter,
        Breadcrumbs,
        ShaplaStarRating,
        DeleteSection,
        EditSection,
        ServicesSearchInput,
        ServicesSearchInput
    },
    props: ['businessList', 'searchedKeyword', 'orderBy', 'status', 'rating'],
    data() {
        return {
            businesses: null,
            type: 'business',
            logoUrl: '',
            urlForFilter: 'services.dashboard.service-provider.index',
            filterForm: {
                orderBy: this.orderBy ? this.orderBy : 'desc',
                status: this.status ? this.status : null,
                reviewRating: this.rating ? this.rating : null,
            },
            deliveryZoneErrors: null
        }
    },
    watch: {
        businessList: {
            handler(businessList) {
                this.businesses = businessList
            },
            deep: true
        }
    },

    created() {
        this.businesses = this.businessList
    },

    methods: {
        changeStatus(business) {
            let user = this.$page.props.auth.user;
            if (!(user.user_type == 'admin' || user.user_type == 'newspaper')
                && (business.status == 'inactive')
                && (business.status_updated_by && business.status_updated_by != user.id)
            ) {
                this.swal.fire({
                    title: "",
                    html: "<h1 class='text-lg text-gray-800 mb-1'>Activation Failed!</h1><p>Please contact your account manager to activate this business.</p>",
                    icon: 'error',
                    showCloseButton: true,
                    customClass: {
                        confirmButton: 'danger'
                    }
                });
                return;
            }
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
                    this.$inertia.visit(route('services.dashboard.service-provider.status', [this.getSelectedModuleValue(), business.id]), {
                        preserveScroll: false,
                        onSuccess: () => hideWaitDialog()
                    })
                }
            })
        },

        openModal(business) {
            this.emitter.emit("module-tag-modal", {
                business: business,
                modules: this.moduleTags
            });
        },
    },
    mounted() {
        this.emitter.emit('hide-dropdown')
    },
    mixins: [Helpers]
}
</script>
