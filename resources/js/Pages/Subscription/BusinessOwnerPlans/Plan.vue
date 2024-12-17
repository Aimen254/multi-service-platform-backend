<template>

    <Head title="Plans" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Plans`" :path="`Subscriptions`" />
        </template>
        <div class="d-flex flex-column flex-lg-row">
            <div class="flex-lg-row-fluid me-lg-15 order-2 order-lg-1 mb-10 mb-lg-0">
                <div class="card card-flush pt-3 mb-5 mb-xl-10">
                    <div class="card-header">
                        <div class="card-title">
                            <h2 class="fw-bold">Product Details</h2>
                        </div>
                        <!-- <div class="card-toolbar"><a href="#/subscriptions/add" class="btn btn-light-primary">Update
                                Product</a></div> -->
                    </div>
                    <div class="card-body pt-3">
                        <div class="mb-10">
                            <h5 class="mb-4">Billing Address:</h5>
                            <div class="d-flex flex-wrap py-5">
                                <div class="flex-equal me-5">
                                    <table class="table fs-6 fw-semobold gs-0 gy-2 gx-2 m-0">
                                        <tr>
                                            <td class="text-gray-400 min-w-175px w-175px">Bill to:</td>
                                            <td class="text-gray-800 min-w-200px"> {{ User.email }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-400">Customer Name:</td>
                                            <td class="text-gray-800">{{ User.first_name }} {{ User.last_name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-400">Address:</td>
                                            <td class="text-gray-800"> {{ User.addresses.length > 0 ?
                                                    User.addresses[0].address : 'null'
                                            }} </td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-400">Phone:</td>
                                            <td class="text-gray-800">{{ User.phone ? User.phone : 'null' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="flex-equal">
                                    <table class="table fs-6 fw-semobold gs-0 gy-2 gx-2 m-0">
                                        <tbody>
                                            <tr>
                                                <td class="text-gray-400 min-w-175px w-175px"> Subscribed Product: </td>
                                                <td class="text-gray-800 min-w-200px">{{ Product.name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-400">Subscription Fees:</td>
                                                <td class="text-gray-800">${{ subscription.plan.amount / 100 }}
                                                    </td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-400">Billing method:</td>
                                                <td class="text-gray-800">{{ subscription.plan.interval }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-gray-400">Currency:</td>
                                                <td class="text-gray-800">{{ subscription.plan.currency }} - US Dollar
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="mb-0">
                            <h5 class="mb-4">Subscribed Products:</h5>
                            <div class="table-responsive">
                                <table class="table align-middle table-row-dashed fs-6 gy-4 mb-0">
                                    <thead>
                                        <tr
                                            class="border-bottom border-gray-200 text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                            <th class="min-w-150px">Product</th>
                                            <th class="min-w-125px">Subscription ID</th>
                                            <th class="min-w-125px">Qty</th>
                                            <th class="min-w-125px">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-semobold text-gray-800">
                                        <tr>
                                            <td><label class="w-150px">{{ Product.name }}</label>
                                                <div class="fw-normal text-gray-600">Type: {{ Product.type }}</div>
                                            </td>
                                            <td><span class="badge badge-light-danger">{{ subscription.id }}</span></td>
                                            <td>{{ subscription.items.data[0].quantity }}</td>
                                            <td>${{ subscription.plan.amount / 100 }} <span
                                                    class="text-capitalize"></span>/<span
                                                    class="text-capitalize">{{ subscription.plan.interval }}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card pt-2 undefined mb-5 mb-xl-10">
                    <div class="card-header border-0">
                        <div class="card-title">
                            <h2>Invoices</h2>
                        </div>
                        <div class="card-toolbar m-0">
                            <ul class="nav nav-stretch fs-5 fw-semobold nav-line-tabs nav-line-tabs-2x border-transparent"
                                role="tablist">
                                <!-- <li class="nav-item" role="presentation"><a id="kt_referrals_year_tab"
                                        class="nav-link text-active-primary active" data-bs-toggle="tab" role="tab"
                                        href="#kt_customer_details_invoices_1" aria-selected="true"> This Year </a></li> -->
                            </ul>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div id="kt_referred_users_tab_content" class="tab-content">
                            <div id="kt_customer_details_invoices_1" class="py-0 tab-pane fade active show"
                                role="tabpanel" aria-labelledby="#kt_referrals_year_tab">
                                <div class="dataTables_wrapper dt-bootstrap4 no-footer">
                                    <div class="table-responsive">
                                        <table
                                            class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer">
                                            <thead>
                                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                    <!---->
                                                    <th class="" style="min-width: 0px; width: auto; cursor: auto;">
                                                        Invoice number
                                                        <!---->
                                                    </th>
                                                    <th class="" style="min-width: 0px; width: auto; cursor: auto;">
                                                        Amount
                                                        <!---->
                                                    </th>
                                                    <th class="" style="min-width: 0px; width: auto; cursor: auto;">
                                                        Status
                                                        <!---->
                                                    </th>
                                                    <th class="" style="min-width: 0px; width: auto; cursor: auto;">Date
                                                        <!---->
                                                    </th>
                                                    <th class="text-end"
                                                        style="min-width: 0px; width: auto; cursor: auto;">Invoice
                                                        <!---->
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="fw-semibold text-gray-600" v-if="Invoice?.data.length > 0">
                                                <tr v-for="(invoice, index) in Invoice.data" :key="index">
                                                    <!---->
                                                    <td class="">{{ invoice.number }}</td>
                                                    <td class=""><span class="text-success">${{ invoice.total / 100 }}
                                                            </span></td>
                                                    <td class=""><span
                                                            class="badge badge-light-success">{{ invoice.status }}</span>
                                                    </td>
                                                    <td class="">{{ formatDateTimeUnix(invoice.period_start) }}</td>
                                                    <td class="text-end">
                                                        <a :href="invoice.invoice_pdf">
                                                            <button
                                                                class="btn btn-sm btn-light btn-active-light-primary">
                                                                Download </button>
                                                        </a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <!---->
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card pt-2 undefined" v-if="upcoming_invoice">
                    <div class="card-header border-0">
                        <div class="card-title">
                            <h2>Upcoming Invoice</h2>
                        </div>
                        <div>
                            <p>This is a preview of the invoice that will be billed on {{formatDateTimeUnix(upcoming_invoice.period_end)}}. It may change if the subscription is updated.</p>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="flex-grow-1">
                            <!--begin::Table-->
                            <div class="table-responsive border-bottom mb-9">
                                <table class="table mb-3">
                                    <thead>
                                        <tr class="border-bottom fs-6 fw-bold text-muted">
                                            <th class="min-w-175px pb-2">Description</th>
                                            <th class="min-w-70px text-end pb-2">QTY</th>
                                            <th class="min-w-100px text-end pb-2">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="fw-bold text-gray-700 fs-5 text-end" v-for="(invoiceLine, index) in upcoming_invoice.lines.data" :key="index">
                                            <td class="d-flex align-items-center pt-6">
                                                <i class="fa fa-genderless text-danger fs-2 me-2"></i>{{invoiceLine.description}}
                                            </td>
                                            <td class="pt-6">{{invoiceLine.quantity}}</td>
                                            <td class="pt-6 text-dark fw-bolder text-capitalize">${{invoiceLine.amount/100}} </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--end::Table-->
                            <!--begin::Container-->
                            <div class="d-flex justify-content-end">
                                <!--begin::Section-->
                                <div class="mw-300px">
                                    <!--begin::Item-->
                                    <div class="d-flex flex-stack mb-3">
                                        <!--begin::Accountname-->
                                        <div class="fw-semibold pe-10 text-gray-600 fs-7">Subtotal:</div>
                                        <!--end::Accountname-->
                                        <!--begin::Label-->
                                        <div class="text-end fw-bold fs-6 text-gray-800">${{stripeAmount(upcoming_invoice.subtotal)}} </div>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Item-->
                                    <!--begin::Item-->
                                    <div class="d-flex flex-stack">
                                        <!--begin::Code-->
                                        <div class="fw-semibold pe-10 text-gray-600 fs-7">Total</div>
                                        <!--end::Code-->
                                        <!--begin::Label-->
                                        <div class="text-end fw-bold fs-6 text-gray-800">${{stripeAmount(upcoming_invoice.total)}} </div>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Item-->
                                    <!--begin::Item-->
                                    <div class="d-flex flex-stack">
                                        <!--begin::Code-->
                                        <div class="fw-semibold pe-10 text-gray-600 fs-7">Amount Due</div>
                                        <!--end::Code-->
                                        <!--begin::Label-->
                                        <div class="text-end fw-bold fs-6 text-gray-800">${{stripeAmount(upcoming_invoice.amount_due)}} </div>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Item-->
                                </div>
                                <!--end::Section-->
                            </div>
                            <!--end::Container-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex-column flex-lg-row-auto w-lg-250px w-xl-300px mb-10 order-1 order-lg-2">
                <div class="card card-flush mb-0" id="kt_view_summary" data-kt-sticky="false"
                    data-kt-sticky-name="view-subscription-summary"
                    data-kt-sticky-offset="{default: false, lg: '200px'}"
                    data-kt-sticky-width="{lg: '250px', xl: '300px'}" data-kt-sticky-left="auto"
                    data-kt-sticky-top="150px" data-kt-sticky-animation="false" data-kt-sticky-zindex="95">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Summary</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0 fs-6">
                        <div class="mb-7">
                            <div class="row align-items-center">
                                <div class="image-input image-input-empty col-4">
                                    <div class="image-input-wrapper w-50px h-50px mx-5"
                                        :style="{ 'background-image': 'url(' + getImage(User.avatar, true, 'avatar') + ')' }">
                                    </div>
                                </div>
                                <div class="col-8">
                                    <h4>{{ User.first_name }} {{ User.last_name }}</h4>
                                    <p>{{ User.email }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="separator separator-dashed mb-7"></div>
                        <div class="mb-7">
                            <h5 class="mb-4">Product details</h5>
                            <div class="mb-0"><span class="badge badge-light-info me-2">{{ Product.name }}</span><span
                                    class="fw-semobold text-gray-600">${{ subscription.plan.amount / 100 }} <span
                                        class="text-capitalize"></span>/<span
                                        class="text-capitalize">{{ subscription.plan.interval }}</span></span></div>
                        </div>
                        <div class="separator separator-dashed mb-7"></div>
                        <div class="mb-10">
                            <h5 class="mb-4">Payment Details</h5>
                            <div class="mb-0">
                                <div class="fw-semobold text-gray-600 d-flex align-items-center text-capitalize">
                                    {{ Card?.brand }}</div>
                                <div class="fw-semobold text-gray-600">Last Four Digits: {{ Card?.last_four }}</div>
                                <div class="fw-semobold text-gray-600">Expires at
                                    {{ getMonth(Card?.expiry_month) }} {{ Card?.expiry_year }}</div>
                            </div>
                        </div>
                        <div class="separator separator-dashed mb-7"></div>
                        <div class="mb-10">
                            <h5 class="mb-4">Subscription Details</h5>
                            <div class="row pb-5">
                                <div class="col-12 text-gray-400">Subscription ID:</div>
                                <div class="col-12 text-gray-800">{{ subscription.id }}</div>
                            </div>
                            <div class="row pb-5">
                                <div class="col-12 text-gray-400">Started:</div>
                                <div class="col-12 text-gray-800">
                                    {{ formatDateTimeUnix(subscription.current_period_start) }}</div>
                            </div>
                            <div class="row pb-5">
                                <div class="col-12 text-gray-400">Expires on:</div>
                                <div class="col-12 text-gray-800">
                                    {{ formatDateTimeUnix(subscription.current_period_end) }}</div>
                            </div>
                            <div class="row pb-5">
                                <div class="col-4 text-gray-400">Status:</div>
                                <div class="col-8"><span
                                        class="badge badge-light-success text-capitalize">{{ subscription.status }}</span>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="mb-0"><a href="#/subscriptions/add" class="btn btn-primary"
                                id="kt_subscriptions_create_button"> Edit Subscription </a></div> -->
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import { Head } from '@inertiajs/inertia-vue3'
import Helpers from '@/Mixins/Helpers'
import moment from 'moment';

export default {
    props: ['product', 'subs', 'invoice', 'upcomingInvoice', 'user', 'card'],

    components: {
        AuthenticatedLayout,
        Breadcrumbs,
        Head,
    },

    data() {
        return {
            subscription: this.subs,
            Product: this.product,
            Invoice: this.invoice,
            upcoming_invoice: this.upcomingInvoice,
            User: this.user,
            Card: this.card,
            data: {},
        }
    },

    watch: {
        product: {
            handler(product) {
                this.Product = product
            },
            deep: true
        },
        subs: {
            handler(subs) {
                this.subscription = subs
            },
            deep: true
        },
        invoice: {
            handler(invoice) {
                this.Invoice = invoice
            },
            deep: true
        },
        upcomingInvoice: {
            handler(upcomingInvoice) {
                this.upcoming_invoice = upcomingInvoice
            },
            deep: true
        },
        user: {
            handler(user) {
                this.User = user
            },
            deep: true
        },
        card: {
            handler(card) {
                this.Card = card
            },
            deep: true
        }
    },

    methods: {
        subscribe(plan) {
            if (this.data.card) {
                this.data.priceId = plan.default_price
                this.$inertia.post(route('dashboard.subscription.subscribe.getPlan'), this.data, {
                    onSuccess: page => { },
                    onError: errors => { console.log(errors) },
                })
            } else {
                this.$notify({
                    group: "toast",
                    type: "error",
                    text: 'Please select a card !',
                },
                    3000
                ); // 3s
            }
        },
        cancelSubscription(plan) {
            this.getSubscription(plan.default_price)
            this.$inertia.delete(route('dashboard.subscription.subscribe.destroy', this.subscriptionId), {
                onSuccess: page => { },
                onError: errors => { console.log(errors) },
            })
        },
        upgradeSubscription(plan) {
            let data = {
                'newPlan': plan,
            }
            let subId = this.subscriptions.data[0].id
            this.$inertia.put(route('dashboard.subscription.subscribe.update', subId), data, {
                onSuccess: page => { },
                onError: errors => { console.log(errors) },
            })
        },
        getSubscription(price) {
            this.subscriptions.data.filter((sub, index) => {
                if (sub.plan.id == price) {
                    this.subscriptionId = sub.id
                }
            })
        },
        matchSubscription(price) {
            for (let index = 0; index < this.subscriptions.data.length; index++) {
                const element = this.subscriptions.data[index];
                if (element.plan.id == price) {
                    this.subscriptionFlag = true
                    break
                } else {
                    this.subscriptionFlag = false
                }
            }
            return true
        }
    },
    mixins: [Helpers]
}
</script>