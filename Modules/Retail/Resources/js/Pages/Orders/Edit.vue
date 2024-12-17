<template>
    <Head title="Order Detail" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Order Detail`" :path="`Order`" />
        </template>
        <div class="d-flex flex-column gap-7 gap-lg-10">
            <!-- status section -->
            <div class="card card-flush py-4 flex-row-fluid overflow-hidden">
                <div class="card-body pt-0 pb-0">
                    <form class="form row align-items-center"
                        v-if="form" @submit.prevent="submit">
                        <div class="col-md-9 d-flex">
                            <Label for="name" class="d-flex align-items-center me-3 mt-2" 
                            value="Status" />
                            <td v-if="order.order_status.status === 'cancelled' || order.order_status.status === 'refunded' || order.order_status.status === 'rejected'">
                                <span class="d-flex align-items-center me-3 mt-2 text-capitalize w-100 text-muted fw-bold text-muted d-block fs-7 text-capitalize px-2">
                                    {{order.order_status.status }} 
                                </span>
                            </td>
                            <td v-else>
                                <select v-model="order.order_status.id"
                                    @change.prevent="onChange($event, order, index)"
                                    class="form-select form-select-solid text-capitalize w-100">
                                    <option v-for="item in orderStatus(statusList, order)" :key="item.id" :value="item.id" > {{ getGroupName(item.status) }} </option>
                                </select>
                            </td>
                        </div>
                        <div class="col-md-3" v-if="order.order_status.status != 'cancelled' && order.order_status.status != 'refunded'">
                            <div class="d-flex justify-content-end">
                                <Button type="submit" :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing || orderType === 'refunded' || orderType === 'cancelled'">
                                    <span class="indicator-label" v-if="!form.processing">Update</span>
                                    <span class="indicator-progress" v-if="form.processing">
                                        <span class="spinner-border spinner-border-sm align-middle"></span>
                                    </span>
                                </Button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- customer detail order detail section -->
            <order-and-customer-detail :order="order" />
            <!-- shipping and billing addresses information section -->
            <shipping-detail :order="order"  v-if="order.order_type != 'pick_up'"/>

            <!-- products section -->
            <products-detail :order="order" />
        </div>
    </AuthenticatedLayout>
</template>

<script>
    import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
    import Breadcrumbs from '@/Components/Breadcrumbs.vue'
    import { Head } from '@inertiajs/inertia-vue3'
    import Helpers from '@/Mixins/Helpers'
    import Label from '@/Components/Label.vue'
    import Button from '@/Components/Button.vue'
    import ProductsDetail from './Partials/ItemsDetail.vue'
    import ShippingDetail from './Partials/ShippingDetail.vue'
    import OrderAndCustomerDetail from './Partials/OrderAndCustomerDetail.vue'

    export default {
        props: ['order', 'orderType', 'statusList'],

        components: {
            AuthenticatedLayout,
            Breadcrumbs,
            Head,
            Label,
            Button,
            ProductsDetail,
            ShippingDetail,
            OrderAndCustomerDetail
        },

        data () {
            return {
                form: null,
            }
        },

        methods: {
            onChange(event, order, index) {
                this.form = this.$inertia.form({
                    id: order.id,
                    order_status_id: event.target.value,
                    charge_stripe: event.target.value == 2 ? true : false
                });
            },
            submit () {
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
                        this.form.put(route('retail.dashboard.business.order.type.orders.update', [this.getSelectedModuleValue(), this.getSelectedBusinessValue(), this.form.id, 'active']), {
                            errorBag: 'order',
                            preserveScroll: true,
                            onSuccess: () => hideWaitDialog()
                        })
                    } 
                })
            }
        },

        mounted () {
            this.form = this.$inertia.form({
                id: this.order.id,
                order_status_id: this.order.order_status_id
            });
        },

        mixins: [Helpers]
    }
</script>