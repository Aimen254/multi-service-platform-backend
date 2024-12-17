<template>
    <div class="card card-flush py-4 flex-row-fluid overflow-hidden">
        <div class="card-header">
            <div class="card-title">
                <h2>Order {{ order.order_id }}</h2>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0">
                    <thead>
                        <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                            <th class="min-w-100px">Product</th>
                            <th class="min-w-100px text-end">Quantity</th>
                            <th class="min-w-100px text-end">Unit Price</th>
                            <th class="min-w-100px text-end">Tax</th>
                            <th class="min-w-100px text-end">Total</th>
                            <th class="min-w-100px text-end" v-if="order.order_status_id == 2 || order.order_status_id == 10 || order.order_status_id == 7">Action</th>
                        </tr>
                    </thead>
                    <tbody class="fw-bold text-gray-600">
                        <tr v-for="item in order.items" :key="item.id">
                            <td class="px-4">
                                <div class="d-flex align-items-center">
                                    <a class="symbol symbol-50px" v-if="item.product.main_image">
                                        <span class="symbol-label" v-if="!item.product.main_image.is_external" 
                                            :style="{ 'background-image': 'url(' + getImage( item.product.main_image.path, true, 'product') + ')' }">
                                        </span>
                                        <span class="symbol-label" v-else :style="{'background-image': 'url('+item.product.main_image.path+')'}">
                                        </span>
                                    </a>
                                    <a class="symbol symbol-50px" v-else>
                                        <span class="symbol-label"  
                                            :style="{ 'background-image': 'url(' + getImage( null, true, 'product') + ')' }">
                                        </span>
                                    </a>
                                    <div class="ms-5">
                                        <span class="fw-bolder text-gray-600 text-hover-primary">
                                            {{ item.product.name }}<br>
                                        </span>
                                        <span v-if="item.product_variant && (item.product_variant.color || item.product_variant.custom_color)">Color: {{item.product_variant.color ? item.product_variant.color.title : item.product_variant.custom_color}}<br></span>
                                        <span v-if="item.product_variant && (item.product_variant.size || item.product_variant.custom_size)">Size: {{item.product_variant.size ? item.product_variant.size.title : item.product_variant.custom_size}}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-end">
                                <span class="text-muted fw-bold text-muted d-block fs-7 text-capitalize">
                                    {{ item.quantity }}
                                </span>
                            </td>
                            <td class="text-end">
                                <span class="text-muted fw-bold text-muted d-block fs-7 text-capitalize">
                                    <span class="fw-normal text-muted fs-7">$</span>{{ item.product.price ? item.product.price.toFixed(2) : item.product.price }}
                                </span>
                            </td>
                            <td class="text-end">
                                <span class="text-muted fw-bold text-muted d-block fs-7 text-capitalize">
                                    <span class="fw-normal text-muted fs-7">${{ item.tax_value ? item.tax_value.toFixed(2) :  item.tax_value}}</span>
                                </span>
                            </td>
                            <td class="text-end">
                                <span class="text-muted fw-bold text-muted d-block fs-7 text-capitalize" v-if="item.refunded || order.order_status_id == 10">
                                    <del><span class="fw-normal text-muted fs-7">$</span>{{ item.total ? item.total.toFixed(2) : item.total }}</del>
                                </span>
                                <span class="text-muted fw-bold text-muted d-block fs-7 text-capitalize" v-else>
                                    <span class="fw-normal text-muted fs-7">$</span>{{ item.total ? item.total.toFixed(2) : item.total }}
                                </span>
                            </td>
                            <td class="text-end" v-if="order.order_status_id == 2 || order.order_status_id == 10 || order.order_status_id == 7">
                                <span class="text-muted fw-bold text-muted d-block fs-7 text-capitalize">
                                    <button class="btn btn-sm btn-secondary w-auto" :class="[item.refunded || order.refunded ? 'disabled' : '']" @click='refundOrderItem(item , order)' ><span class="indicator-label">{{item.refunded || order.refunded ? 'Refunded' : 'Refund'}}</span></button>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end">Subtotal</td>
                            <td class="text-end">${{order.actual_price ? order.actual_price.toFixed(2) : order.actual_price}}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end">Total Tax</td>
                            <td class="text-end">${{order.total_tax_price ? order.total_tax_price.toFixed(2) : order.total_tax_price}}
                                <small v-if="order.tax_type && order.total_tax_price > 0">(Paying by business owner)</small>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end">Discount</td>
                            <td class="text-end">${{order.discount_price ? order.discount_price.toFixed(2) : order.discount_price}}</td>
                        </tr>
                        <tr v-if="order.delivery_fee > 0">
                            <td colspan="4" class="text-end">Delivery Fee</td>
                            <td class="text-end">${{order.delivery_fee ? order.delivery_fee.toFixed(2) : order.delivery_fee}}</td>
                        </tr>
                        <tr v-if="order.platform_commission > 0">
                            <td colspan="4" class="text-end">Platform Fee</td>
                            <td class="text-end">${{order.platform_commission ? order.platform_commission.toFixed(2) : order.platform_commission}}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="fs-3 text-dark text-end">Grand Total</td>
                            <td class="text-dark fs-3 fw-boldest text-end">${{ order.total ? order.total.toFixed(2) : order.total}}</td>
                        </tr>
                        <tr v-if="(order.order_status_id == 10 || order.order_status_id == 7 || order.order_status_id == 2) && order.amount_refunded">
                            <td colspan="4" class="fs-3 text-dark text-end">Actual Amount Refunded</td>
                            <td class="text-dark fs-3 fw-boldest text-end">${{ order.amount_refunded ? order.amount_refunded.toFixed(2) : order.amount_refunded}}</td>
                        </tr>
                        <tr v-if="(order.order_status_id == 10 || order.order_status_id == 7 || order.order_status_id == 2) && order.refunded_delivery_fee" >
                            <td colspan="4" class="fs-3 text-dark text-end">Delivery Refunded</td>
                            <td class="text-dark fs-3 fw-boldest text-end">${{ order.refunded_delivery_fee ? order.refunded_delivery_fee.toFixed(2) : order.refunded_delivery_fee}}</td>
                        </tr>
                        <tr v-if="(order.order_status_id == 10 || order.order_status_id == 7 || order.order_status_id == 2) && order.refunded_platform_fee">
                            <td colspan="4" class="fs-3 text-dark text-end">Platform Fee Refunded</td>
                            <td class="text-dark fs-3 fw-boldest text-end">${{ order.refunded_platform_fee ? order.refunded_platform_fee.toFixed(2) : order.refunded_platform_fee}}</td>
                        </tr>
                        <tr v-if="order.order_status_id == 10 || order.order_status_id == 7 || order.order_status_id == 2">
                            <td colspan="4" class="fs-3 text-dark text-end">Total Refunded</td>
                            <td class="text-dark fs-3 fw-boldest text-end">${{ (order.amount_refunded + order.refunded_delivery_fee + order.refunded_platform_fee).toFixed(2) }}</td>
                        </tr>
                        <tr v-if="order.order_status_id == 10 || order.order_status_id == 7 || order.order_status_id == 2">
                            <td colspan="4" class="fs-3 text-dark text-end">New Grand Total</td>
                            <td class="text-dark fs-3 fw-boldest text-end">${{ (order.total - order.amount_refunded - order.refunded_delivery_fee - order.refunded_platform_fee).toFixed(2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    import Helpers from '@/Mixins/Helpers'

    export default {
        props: ['order'],
        data() {
            return {
                itemReturnId: {
                    id: null,
                },
                totalRefund: 0,
            }
        },  
        methods: {
            refundOrderItem(item, order) {
                if (!item.refunded) {
                    this.itemReturnId.id = item.id
                    this.swal.fire({
                        title: "",
                        html: "<h1 class='text-lg text-gray-800 mb-1'>Refund Item</h1><p class='text-base'>Are you sure you want to refund this item?</p>",
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
                            this.$inertia.post(route('retail.dashboard.business.order.type.order.item.refund', [this.getSelectedModuleValue(), this.getSelectedBusinessValue(), 'active']), this.itemReturnId ,{
                                preserveScroll: false,
                                onSuccess: () => {
                                    hideWaitDialog()
                                    this.calculateTotalRefund(this.order.items)
                                },
                            })
                        }
                    })
                } else if(!item.refunded && !order.refunded) {
                    this.itemReturnId.id = item.id
                    this.swal.fire({
                        title: "",
                        html: "<h1 class='text-lg text-gray-800 mb-1'>Refund Item</h1><p class='text-base'>Are you sure you want to refund this item?</p>",
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
                            this.$inertia.post(route('dashboard.business.order.type.order.item.refund', [this.getSelectedBusinessValue(), 'active']), this.itemReturnId ,{
                                preserveScroll: false,
                                onSuccess: () => {
                                    hideWaitDialog()
                                    this.calculateTotalRefund(this.order.items)
                                },
                            })
                        }
                    })
                }
            },
            calculateTotalRefund(items) {
                items.forEach((value,key) => {
                    if (this.order.refunded) {
                        this.totalRefund += (value.actual_price + value.tax_value)
                    } else {
                        if (value.refunded) {
                        this.totalRefund += (value.actual_price + value.tax_value)
                    }
                    }
                })
            },
        },
        mounted() {
            this.calculateTotalRefund(this.order.items)
        },

        mixins: [Helpers]
    }
</script>