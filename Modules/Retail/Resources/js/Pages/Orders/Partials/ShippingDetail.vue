<template>
    <div class="row">
        <div class="col-lg-6 pe-7">
            <div class="card p-4 card-flush">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="card-header" style="border:none">
                            <div class="card-title">
                                Billing Address
                            </div>
                        </div>
                        <div class="card-body pt-0" v-if="order.billing_address">
                            <p>{{order.billing_address.address}}</p>
                            <p>{{order.billing_address.note}}</p>
                        </div>
                        <div class="card-body pt-0" v-else>
                            <p>No billing address</p>
                        </div>
                    </div>
                    <div class="opacity-10 pe-none text-end">
                        <img src="/images/icons/ecm001.svg" class="w-175px" />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 ps-2">
            <div class="card card-flush py-4">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="card-header" style="border:none">
                            <div class="card-title">
                                Shipping Address
                            </div>
                        </div>
                        <div class="card-body pt-0" v-if="order.shipping_address">
                            <p>{{order.shipping_address.address}}</p>
                            <p>{{order.shipping_address.note}}</p>
                        </div>
                        <div v-else class="card-body pt-0">
                            <p>No shipping address</p>
                        </div>
                    </div>
                    <div class="opacity-10 pe-none text-end">
                        <img src="/images/icons/ecm006.svg" class="w-175px" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['order'],
    }
</script>