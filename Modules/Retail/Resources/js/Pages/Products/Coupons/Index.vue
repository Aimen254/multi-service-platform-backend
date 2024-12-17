<template>

    <Head title="Coupons" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Coupons`" :path="`Products - ${product?.name}`" />
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button v-if="checkUserPermissions('add_product_coupons')" @click="openModal()"
                class="btn btn-sm btn-primary">
                    <span class="svg-icon svg-icon-2">
                        <inline-svg :src="'/images/icons/add.svg'" />
                    </span>
                    Add Coupon
                </button>
            </div>
        </template>
        <div class="d-flex flex-column flex-lg-row">
            <ProductSidebar :product="product" :width="'w-lg-225px'" />
            <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10 bg-white">
                <div class="card">
                    <div class="card-body" style="padding: 1rem 1.25rem">
                        <div class="table-responsive">
                            <table class="table table-row-dashed align-middle gs-0 gy-4">
                                <thead>
                                    <tr class="fw-bolder border-0 text-muted bg-light">
                                        <th class="ps-4 min-w-120px rounded-start">Code</th>
                                        <th class="min-w-120px">Type</th>
                                        <th class="min-w-120px">Discount</th>
                                        <th class="min-w-120px">Status</th>
                                        <th class="min-w-120px rounded-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody v-if="couponsList && couponsList.data.length > 0">
                                    <template v-for="coupon in couponsList.data" :key="coupon.id">
                                        <tr>
                                            <td class="px-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="d-flex justify-content-start flex-column mx-2">
                                                        <span class="text-dark fw-bolder text-hover-primary mb-1 fs-6">
                                                            {{ coupon.code }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="text-muted fw-bold text-muted d-block fs-7 text-capitalize">
                                                    {{ coupon.discount_type }}
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="text-muted fw-bold text-muted d-block fs-7 text-capitalize">
                                                    <span v-if="coupon.discount_type == 'fixed'"
                                                        class="fw-normal text-muted fs-7">$</span>{{ coupon.discount_value }}<span
                                                        v-if="coupon.discount_type == 'percentage'"
                                                        class="fw-normal text-muted fs-7">%</span>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge fs-7 fw-bold text-capitalize"
                                                    :class="{ 'badge-light-success': coupon.pivot.status == 'active', 'badge-light-danger': coupon.pivot.status === 'inactive' }">{{ coupon.pivot.status }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <Toggle v-if="checkUserPermissions('edit_product_coupons')"
                                                        :status="booleanStatusValue(coupon.pivot.status)"
                                                        @click.prevent="changeStatus(coupon.id)" />
                                                    <delete-section permission="delete_product_coupons"
                                                        :url="route('retail.dashboard.product.coupons.destroy', [this.getSelectedModuleValue(), product.uuid, coupon.id])" 
                                                        :currentPage="couponsList.current_page" :currentCount="couponsList.data.length"/>
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
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
    <coupon-modal :product="product"></coupon-modal>
</template>

<script>
    import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
    import Breadcrumbs from '@/Components/Breadcrumbs.vue'
    import { Head } from '@inertiajs/inertia-vue3'
    import Helpers from '@/Mixins/Helpers'
    import InlineSvg from 'vue-inline-svg'
    import CouponModal from './CouponModal.vue'
    import Toggle from '@/Components/ToggleButton.vue'
    import ProductSidebar from '../Partials/ProductSideMenu.vue'
    import EditSection from "@/Components/EditSection.vue"
    import DeleteSection from "@/Components/DeleteSection.vue"

    export default {
        props: ['product', 'couponsList', 'codesList'],

        components: {
            AuthenticatedLayout,
            Breadcrumbs,
            Head,
            InlineSvg,
            CouponModal,
            Toggle,
            ProductSidebar,
            EditSection,
            DeleteSection
        },

        data () {
            return {
                type: 'discount'
            }
        },

        methods: {
            openModal (coupon = null) {
                this.emitter.emit("discount-modal", {
                    codes: this.codesList,
                    coupon: coupon
                });
            },

            changeStatus(id) {
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
                        
                        this.$inertia.visit(route('retail.dashboard.product.discount.status', [this.getSelectedModuleValue(), this.product.uuid, id]), {
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