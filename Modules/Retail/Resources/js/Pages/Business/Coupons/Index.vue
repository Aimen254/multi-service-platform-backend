<template>

    <Head title="Businesses Coupon" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Coupon`" :path="`Businesses`"></Breadcrumbs>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button class="btn btn-sm btn-primary" v-if="checkUserPermissions('add_business_coupons')"
                    @click="openCouponeModal(coupons, 'post', this.business.uuid, delivery_type)">
                    <span class="svg-icon svg-icon-2">
                        <inline-svg :src="'/images/icons/arr075.svg'" />
                    </span>
                    Add Coupon
                </button>
            </div>
        </template>

        <div :class="widgetClasses" class="card">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder fs-3 mb-1">
                        <RetailSearchInput :business="business" :callType="type"
                            :searchedKeyword="searchedKeyword"/>
                    </span>
                </h3>
            </div>

            <div class="card-body py-3">

                <div class="table-responsive">
                    <table class="table align-middle gs-0 gy-4 table-row-dashed">
                        <thead>
                            <tr class="fw-bolder text-muted bg-light">
                                <th class="ps-4 min-w-150px rounded-start">Code</th>
                                <th class="min-w-150px">Discount</th>
                                <th class="min-w-150px">Status</th>
                                <th class="min-w-150px">Type</th>
                                <th class="min-w-150px">Expiry</th>
                                <th class="min-w-150px rounded-end">Action</th>
                            </tr>
                        </thead>
                        <tbody v-if="coupons && coupons.data.length > 0">
                            <template v-for="coupon in coupons.data" :key="coupon.id">
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="d-flex justify-content-start flex-column">
                                                <span class="text-dark fw-bolder text-hover-primary mb-1 fs-6 ms-4">
                                                    {{ coupon.code }}
                                                    <span class="text-muted fw-bold text-muted d-block fs-7"
                                                        v-if="coupon.product">{{ coupon.product.name }}</span>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block text-capitalize fs-7">
                                            <span v-if="coupon.discount_type == 'fixed'"
                                                class="fw-normal text-muted fs-7">$</span>{{ coupon.discount_value }}<span
                                                v-if="coupon.discount_type == 'percentage'"
                                                class="fw-normal text-muted fs-7">%</span>
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            :class="{'badgeee badge-light-success': coupon.status == 'active', 'badge badge-light-danger' : coupon.status === 'inactive', 'badge badge-light-warning' : coupon.status === 'expired'}"
                                            class="text-capitalize">
                                            {{ coupon.status }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block text-capitalize fs-7">
                                            {{ coupon.coupon_type }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block text-capitalize fs-7">
                                            {{ formatDate(coupon.end_date) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <Toggle v-if="checkUserPermissions('edit_business_coupons')"
                                                :status="booleanStatusValue(coupon.status)"
                                                @click.prevent="changeStatus(business.uuid, coupon.id)" />
                                            <edit-section iconType="modal" permission="edit_business_coupons"
                                                @click="openCouponeModal(coupon, type, business.uuid ,delivery_type)" />
                                            <delete-section permission="delete_business_coupons"
                                                :url="route('retail.dashboard.business.coupons.destroy', [this.getSelectedModuleValue(), getSelectedBusinessValue(), coupon.id])" 
                                                :currentPage="coupons.current_page" :currentCount="coupons.data.length"/>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <div v-else class="p-4 text-muted">
                            Record Not Found
                        </div>
                    </table>
                    <pagination :meta="coupons" :keyword="searchedKeyword" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
    <CouponModal></CouponModal>
</template>

<script>
import { Head } from "@inertiajs/inertia-vue3";
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import RetailSearchInput from "../../Components/RetailSearchInput.vue";
import Helpers from "@/Mixins/Helpers";
import CouponModal from "./CouponModal.vue";
import EditSection from "@/Components/EditSection.vue";
import DeleteSection from "@/Components/DeleteSection.vue";
import InlineSvg from "vue-inline-svg";
import Pagination from "@/Components/Pagination.vue";
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import Toggle from '@/Components/ToggleButton.vue'

export default {
    props: ["business", "productCategoriesList", "couponsList", "searchedKeyword", "delivery_zone"],

    components: {
        Head,
        AuthenticatedLayout,
        RetailSearchInput,
        CouponModal,
        EditSection,
        DeleteSection,
        InlineSvg,
        Pagination,
        Breadcrumbs,
        Toggle,
    },

    data() {
        return {
            title: 'Coupons',
            coupons: this.couponsList,
            type: 'coupon',
            delivery_type: this.delivery_zone ? this.delivery_zone.delivery_type : null
        };
    },
    watch: {
        couponsList: {
        handler(val) {
            this.coupons = val;
        },
        deep: true,
        },
    },

    methods: {
        openCouponeModal(coupons, type, businessUuid ,delivery_type) {
            this.emitter.emit('coupon-model', {
                businessUuid: businessUuid,
                type: type,
                productCategoriesList: this.productCategoriesList,
                coupons: coupons ? coupons : null,
                delivery_type: delivery_type ? delivery_type : null
            })
        },

        changeStatus (businessID, id) {
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
                    this.$inertia.visit(route('retail.dashboard.business.coupon.change.status', [this.getSelectedModuleValue(), businessID, id]), {
                        preserveScroll: false,
                        onSuccess: () => hideWaitDialog()
                    })
                }
            })
        }
    },

    mounted() {
        this.coupons = this.couponsList;
    },
    mixins: [Helpers],
};
</script>