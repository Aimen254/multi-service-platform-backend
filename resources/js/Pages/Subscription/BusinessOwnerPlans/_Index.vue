<template>

    <Head title="Plans" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Plans`" :path="`Subscriptions`" />
        </template>

        <div class="card card-flush pt-3 mb-5 mb-lg-10" data-kt-subscriptions-form="pricing">
            <div class="card-header">
                <div class="card-title">
                    <h2 class="fw-bold">Select Payment Method</h2>
                </div>
                <!-- <div class="card-toolbar"><a href="#" class="btn btn-light-primary" data-bs-toggle="modal"
                        data-bs-target="#kt_modal_new_card">New Method</a></div> -->
            </div>
            <div class="card-body pt-0">
                <div id="kt_create_new_payment_method">
                    <div class="py-1" v-for="(card, index) in creditCard" :key="index">
                        <div class="py-3 d-flex flex-stack flex-wrap">
                            <div class="d-flex align-items-center collapsible toggle collapsed"
                                data-bs-toggle="collapse" :data-bs-target="`#kt_create_new_payment_method_${index}`"
                                aria-expanded="false">
                                <div class="btn btn-sm btn-icon btn-active-color-primary ms-n3 me-2"><span
                                        class="svg-icon toggle-on svg-icon-primary svg-icon-2"><svg fill="none"
                                            viewBox="0 0 24 24" height="24" width="24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect xmlns="http://www.w3.org/2000/svg" opacity="0.3" x="2" y="2"
                                                width="20" height="20" rx="5" fill="currentColor"></rect>
                                            <rect xmlns="http://www.w3.org/2000/svg" x="6.0104" y="10.9247" width="12"
                                                height="2" rx="1" fill="currentColor"></rect>
                                        </svg></span><span class="svg-icon toggle-off svg-icon-2"><svg fill="none"
                                            viewBox="0 0 24 24" height="24" width="24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect xmlns="http://www.w3.org/2000/svg" opacity="0.3" x="2" y="2"
                                                width="20" height="20" rx="5" fill="currentColor"></rect>
                                            <rect xmlns="http://www.w3.org/2000/svg" x="10.8891" y="17.8033" width="12"
                                                height="2" rx="1" transform="rotate(-90 10.8891 17.8033)"
                                                fill="currentColor"></rect>
                                            <rect xmlns="http://www.w3.org/2000/svg" x="6.01041" y="10.9247" width="12"
                                                height="2" rx="1" fill="currentColor"></rect>
                                        </svg></span></div>
                                <div class="me-3">
                                    <div class="d-flex align-items-center fw-bold text-capitalize">{{ card.brand }}
                                        <div class="badge badge-light-primary ms-5" v-if="card.default">Primary</div>
                                    </div>
                                    <div class="text-muted">Expires {{ getMonth(card.expiry_month) }} {{
                                            card.expiry_year
                                    }}
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex my-3 ms-9"><label
                                    class="form-check form-check-custom form-check-solid me-5"><input
                                        class="form-check-input" type="radio" name="payment_method" :checked="card.default"
                                        :value="card.payment_method_id" v-model="this.data.card"></label></div>
                        </div>
                        <div :id="`kt_create_new_payment_method_${index}`" class="fs-6 ps-10 collapse">
                            <div class="d-flex flex-wrap py-5">
                                <div class="flex-equal me-5">
                                    <table class="table table-flush fw-semobold gy-1">
                                        <tbody>
                                            <tr>
                                                <td class="text-muted min-w-125px w-125px">Name</td>
                                                <td class="text-gray-800 text-capitalize">{{ card.user_name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted min-w-125px w-125px">Number</td>
                                                <td class="text-gray-800">****{{ card.last_four }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted min-w-125px w-125px">Expires</td>
                                                <td class="text-gray-800">{{ getMonth(card.expiry_month) }}
                                                    {{ card.expiry_year }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted min-w-125px w-125px">Type</td>
                                                <td class="text-gray-800">{{ card.brand }} credit card</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="flex-equal">
                                    <table class="table table-flush fw-semobold gy-1">
                                        <tbody>
                                            <tr>
                                                <td class="text-muted min-w-125px w-125px"> Country </td>
                                                <td class="text-gray-800">{{ card.country }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted min-w-125px w-125px">Phone</td>
                                                <td class="text-gray-800">No phone provided</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted min-w-125px w-125px">Email</td>
                                                <td class="text-gray-800">{{ card.email }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted min-w-125px w-125px">CVC check</td>
                                                <td class="text-gray-800"> Passed <span
                                                        class="svg-icon svg-icon-2 svg-icon-success"><svg fill="none"
                                                            viewBox="0 0 24 24" height="24" width="24"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <rect xmlns="http://www.w3.org/2000/svg" opacity="0.3" x="2"
                                                                y="2" width="20" height="20" rx="10"
                                                                fill="currentColor"></rect>
                                                            <path xmlns="http://www.w3.org/2000/svg"
                                                                d="M10.4343 12.4343L8.75 10.75C8.33579 10.3358 7.66421 10.3358 7.25 10.75C6.83579 11.1642 6.83579 11.8358 7.25 12.25L10.2929 15.2929C10.6834 15.6834 11.3166 15.6834 11.7071 15.2929L17.25 9.75C17.6642 9.33579 17.6642 8.66421 17.25 8.25C16.8358 7.83579 16.1642 7.83579 15.75 8.25L11.5657 12.4343C11.2533 12.7467 10.7467 12.7467 10.4343 12.4343Z"
                                                                fill="currentColor"></path>
                                                        </svg></span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="separator separator-dashed"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <!-- Start from here -->
            <div class="card-header">
                <div class="card-title">
                    <h2 class="fw-bold">Subscription Plans</h2>
                </div>
                <div class="fv-row mb-4 fv-plugins-icon-container">
                    <Label for="title" value="Interval" />
                    <select class="form-select form-select-solid" name="interval" id="interval"
                        :class="{ 'is-invalid border border-danger': false }" v-model="selectedModule" @change="getPlans($event)">
                        <option value="" disabled>Select Module</option>
                        <option v-for="(module,index) in modules" :key="index" :value="module.slug" >{{module.name}}</option>
                    </select>
                </div>
            </div>
            <div class="card-body py-3 pt-10">
                <div class="py-5">
                    <div class="row row-cols-3 g-5">
                        <div class="col" v-for="(plan, index) in subscriptionPlans" :key="index">
                            <div class="d-flex h-100 align-items-center">
                                <div class="w-100 d-flex flex-column flex-center rounded-3 bg-light bg-opacity-75 py-15 px-10">
                                    <div class="mb-7 text-center">
                                        <h1 class="text-dark mb-5 fw-bolder text-capitalize">
                                            {{ plan.product.name }}
                                        </h1>
                                        <div class="text-gray-400 fw-semibold mb-5">
                                            {{ plan.product.description }}
                                        </div>
                                        <div class="text-center">
                                            <span class="fs-3x fw-bold text-primary" 
                                                data-kt-plan-price-month="39"
                                                data-kt-plan-price-annual="399">
                                                ${{ plan.price.unit_amount / 100 }}
                                            </span>
                                            <span class="fs-7 fw-semibold opacity-50">/
                                                <span data-kt-element="period text-capitalize text-capitalize">
                                                    {{ plan.price.recurring.interval }}
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="w-100 mb-10">
                                        <div class="d-flex align-items-center mb-5" v-for="(permission, index) in plan.permissions" :key="index">
                                            <span class="fw-semibold fs-6 text-gray-800 flex-grow-1 pe-3 text-capitalize">{{setTypeFormat(permission.key)}}<span class="badge badge-light-warning fw-bold fs-8 ms-2" v-if="permission.status">{{permission.value == -1 ? 'unlimited' : permission.value}}</span></span>
                                            <span class="svg-icon svg-icon-1 svg-icon-success" v-if="permission.status">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10"
                                                        fill="currentColor"></rect>
                                                    <path
                                                        d="M10.4343 12.4343L8.75 10.75C8.33579 10.3358 7.66421 10.3358 7.25 10.75C6.83579 11.1642 6.83579 11.8358 7.25 12.25L10.2929 15.2929C10.6834 15.6834 11.3166 15.6834 11.7071 15.2929L17.25 9.75C17.6642 9.33579 17.6642 8.66421 17.25 8.25C16.8358 7.83579 16.1642 7.83579 15.75 8.25L11.5657 12.4343C11.2533 12.7467 10.7467 12.7467 10.4343 12.4343Z"
                                                        fill="currentColor"></path>
                                                </svg>
                                            </span>
                                            <span class="svg-icon svg-icon-1" v-else>
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10"
                                                        fill="currentColor"></rect>
                                                    <rect x="7" y="15.3137" width="12" height="2" rx="1"
                                                        transform="rotate(-45 7 15.3137)" fill="currentColor"></rect>
                                                    <rect x="8.41422" y="7" width="12" height="2" rx="1"
                                                        transform="rotate(45 8.41422 7)" fill="currentColor"></rect>
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                    <!--end::Features-->
                                    <!--begin::Select-->
                                    <!-- {{ checkSubscriptionModuleMatch() }} -->
                                    <!--Testing Start-->
                                    <div v-if="plan.currentPlan">
                                        {{this.checkPlanInSubscriptions(plan, index)}}
                                        <div v-if="planMatched[index]"
                                            class="row">
                                            <div class="col">
                                                <a class="btn btn-sm btn-danger"
                                                    >
                                                    <span @click="cancelSubscription(plan.product)" v-if="!processing">
                                                        Cancel
                                                    </span>
                                                    <span class="indicator-progress" v-else>
                                                        <span class="spinner-border spinner-border-sm align-middle"></span>
                                                    </span>
                                                </a>
                                            </div>
                                            <div class="col">
                                                <Link class="btn btn-sm btn-primary"
                                                    :href="route('dashboard.subscription.subscribe.show', plan.price.id)">
                                                    Detail
                                            </Link>
                                            </div>
                                            <div class="col" v-if="subscriptionPastDue[index]">
                                                <a class="btn btn-sm btn-warning"
                                                    >
                                                    <span @click="payLastInvoice()" v-if="!processing">
                                                        Pay
                                                    </span>
                                                    <span class="indicator-progress" v-else>
                                                        <span class="spinner-border spinner-border-sm align-middle"></span>
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else>
                                        <div v-if="checkAnyPlanSubscriped">
                                            <a class="btn btn-sm btn-primary " >
                                                <span @click="upgradeSubscription(plan.product)" v-if="!processing">
                                                    Change Plan
                                                </span>
                                                <span class="indicator-progress" v-else>
                                                    <span class="spinner-border spinner-border-sm align-middle"></span>
                                                </span>
                                            </a>
                                        </div>
                                        <div v-else>
                                            <a class="btn btn-sm btn-primary">
                                                <span  @click="subscribe(plan.product)" v-if="!processing">
                                                    Subscribe
                                                </span>
                                                <span class="indicator-progress" v-else>
                                                    <span class="spinner-border spinner-border-sm align-middle"></span>
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                    <!--Testing End-->
                                    <!--end::Select-->
                                    <span class="badge badge-light-danger" style="position: relative;top: -45em;left: 35%;" v-if="subscriptionPastDue[index]">Subscription Past Due</span>
                                    <span class="badge badge-light-success" style="position: relative;top: -45em;left: 35%;" v-else-if="plan.currentPlan">Subscription Active</span>
                                    <span v-else style="color:#f8fafb">.</span>
                                    <!--  -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
    <BusinessModal></BusinessModal>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import { Head , Link} from '@inertiajs/inertia-vue3'
import Helpers from '@/Mixins/Helpers'
import BusinessModal from "./ActiveBusinessesModal.vue";
import Log from 'laravel-mix/src/Log';

export default {
    props: ['plans', 'subs', 'cards', 'modules', 'selected_module'],

    components: {
        AuthenticatedLayout,
        Breadcrumbs,
        Head,
        Link,
        BusinessModal
    },

    data() {
        return {
            subscriptionPlans: this.plans,
            subscriptions: this.subs,
            data: {},
            creditCard: this.cards,
            subscriptionFlag: false,
            subscriptionId: null,
            processing: false,
            card_id: null,
            selectedModule: this.selected_module ? this.selected_module : null,
            subscriptionMatched: false,
            planMatched: [],
            subscriptionPastDue: [],
            activeSubscription: null,
        }
    },

    watch: {
        plans: {
            handler(plans) {
                this.subscriptionPlans = plans
            },
            deep: true
        },
        subs: {
            handler(subs) {
                this.subscriptions = subs
                this.filterPlanByPrice()
                this.emitter.emit('subscription_changed')
            },
            deep: true
        },
        cards: {
            handler(cards) {
                this.creditCard = cards
            },
            deep: true
        }
    },
    computed: {
        checkAnyPlanSubscriped() {
            return this.subscriptionPlans.some(element => element.currentPlan == true);
        }
    },
    methods: {
        subscribe(plan) {
            if (!this.data.card) {
                this.card_id = this.cards.filter((val,key) => {
                    if (val.default) {
                        return val
                    }
                })
                if (Object.keys(this.card_id).length === 0) {
                    this.$notify({
                        group: "toast",
                        type: "error",
                        text: 'Please select a card !',
                    },
                        3000
                    ); // 3s
                    return 
                } else {
                    this.data.card = this.card_id[0].payment_method_id
                }
            }
            this.data.productId = plan.id
            this.data.priceId = plan.default_price
            this.data.module = this.selectedModule
            this.swal.fire({
                title: "",
                html: "<h1 class='text-lg text-gray-800 mb-1'>Subscription</h1><p class='text-base'>Are you sure you want to Activate this subscription?</p>",
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
                    this.$inertia.post(route('dashboard.subscription.subscribe.getPlan'), this.data, {
                        onSuccess: page => {
                            hideWaitDialog()
                            // this.checkSubscriptionModuleMatch()
                        },
                        onError: errors => { 
                            hideWaitDialog()
                            console.log(errors) 
                        },
                    })
                }
            })
        },
        cancelSubscription(plan) {
            this.getSubscription(plan.default_price)
            this.swal.fire({
                title: "",
                html: "<h1 class='text-lg text-gray-800 mb-1'>Subscription</h1><p class='text-base'>Are you sure you want to Cancel this subscription?</p>",
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
                    this.$inertia.delete(route('dashboard.subscription.subscribe.destroy', this.subscriptionId), {
                        onSuccess: page => { 
                            hideWaitDialog()
                            // this.checkSubscriptionModuleMatch()
                            this.planMatched = []
                        },
                        onError: errors => {  
                            hideWaitDialog()
                            console.log(errors) 
                        },
                    })
                }
            })
        },
        upgradeSubscription(plan) {
            let data = {
                'newPlan': plan,
            }
            let subId = this.subscriptions.data.filter((sub, key) => {
                if (sub.metadata.module == this.selectedModule) {
                    return sub
                }
            })
            data.subscriptionId = subId
            this.swal.fire({
                title: "",
                html: "<h1 class='text-lg text-gray-800 mb-1'>Subscription</h1><p class='text-base'>Changing Subscription can affect your active products. Are you sure you want to change this subscription? </p>",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: 'No',
                confirmButtonText: "Yes",
                customClass: {
                confirmButton: 'danger'
                }
            }).then((result) => {
                if (result.value) {
                    if(this.selectedModule == 'news') {
                        showWaitDialog()
                            this.$inertia.put(route('dashboard.subscription.subscribe.update', subId[0].id), data, {
                                onSuccess: page => { 
                                    hideWaitDialog()
                                    // this.checkSubscriptionModuleMatch()
                                },
                                onError: errors => { 
                                    hideWaitDialog()
                                    console.log(errors) 
                                },
                            })
                    } else {
                        window.axios.post(route('dashboard.subscription.subscribe.checkActiveBusinesses'), {Data : data})
                        .then((response) => {
                            if (response.data.flag) {
                                showWaitDialog()
                                this.$inertia.put(route('dashboard.subscription.subscribe.update', subId[0].id), data, {
                                    onSuccess: page => { 
                                        hideWaitDialog()
                                        // this.checkSubscriptionModuleMatch()
                                    },
                                    onError: errors => { 
                                        hideWaitDialog()
                                        console.log(errors) 
                                    },
                                })
                            } else {
                                this.openModal(response.data)
                                this.emitter.on('selected_active_businesses', (args) => {
                                    data.selectedBusinesses =  args.businesses
                                    showWaitDialog()
                                    this.$inertia.put(route('dashboard.subscription.subscribe.update', subId[0].id), data, {
                                        onSuccess: page => {
                                            hideWaitDialog()
                                            // this.checkSubscriptionModuleMatch()
                                        },
                                        onError: errors => { 
                                            hideWaitDialog()
                                            console.log(errors) 
                                        },
                                    })
                                })
                            }
                        }).catch((error) => {

                        });
                    }
                }
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
        },
        getPlans(event) {
            showWaitDialog()
            this.$inertia.get(route('dashboard.subscription.subscribe.index'), {'moduleSlug' :this.selectedModule}, {
                onSuccess: page => { 
                    hideWaitDialog()
                },
                onError: errors => {  
                    hideWaitDialog()
                },
            })
        },
        checkPlanInSubscriptions(plan, index) {
            console.log(this.subscriptions.data.length)
            if (this.subscriptions.data.length > 0) {
                for (let i = 0; i < this.subscriptions.data.length; i++) {
                    const element = this.subscriptions.data[i];
                    if (element.plan.id == plan.product.default_price) {
                        this.planMatched[index] = true
                        this.activeSubscription = element
                        if (element.status == 'past_due') {
                            this.subscriptionPastDue[index] = true
                        }
                        break
                    } else {
                        this.planMatched[index] = false
                        this.subscriptionPastDue[index] = false
                    }
                }
            }
        },
        // checkSubscriptionModuleMatch() {
        //     console.log('call');
        //     if (this.subscriptions.data.length > 0) {
        //         this.subscriptionMatched = this.subscriptions.data.some(element => element.metadata.module === this.selectedModule);
        //     } else {
        //         this.subscriptionMatched = false; // Set to false if there are no subscriptions
        //     }
        // },
        filterPlanByPrice() {
            var byDate = this.subscriptionPlans.slice(0);
            byDate.sort(function(a,b) {
                return a.price.unit_amount - b.price.unit_amount;
            });
            this.subscriptionPlans = byDate
        },
        openModal(data = null) {
            this.emitter.emit("active_businesses", {
                businesses: data.businesses,
                allowedBusinesses: data.allowedBusinesses,
            });
        },
        payLastInvoice(){
            if (!this.data.card) {
                this.card_id = this.cards.filter((val,key) => {
                    if (val.default) {
                        return val
                    }
                })
                this.activeSubscription.card = this.card_id[0].payment_method_id
                if (!this.card_id) {
                    this.$notify({
                        group: "toast",
                        type: "error",
                        text: 'Please select a card !',
                    },
                        3000
                    ); // 3s
                }
            } else {
                this.activeSubscription.card = this.data.card
            }
            this.swal.fire({
                title: "",
                html: "<h1 class='text-lg text-gray-800 mb-1'>Subscription</h1><p class='text-base'>Are you sure you want to pay its latest invoice.</p>",
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
                    this.$inertia.post(route('dashboard.subscription.subscribe.pay'),{subscription: this.activeSubscription}, {
                        onSuccess: page => { hideWaitDialog()},
                        onError: errors => {  
                            hideWaitDialog()
                            console.log(errors) 
                        },
                    })
                }
            })
        }
    },
    mounted() {
        // this.checkSubscriptionModuleMatch()
        this.filterPlanByPrice()
    },
    mixins: [Helpers]
}
</script>