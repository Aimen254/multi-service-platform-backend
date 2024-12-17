<template>

    <Head title="Plans" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Plan`" :path="`Subscriptions`" />
            <div class="d-flex align-items-center gap-2 gap-lg-3">

            </div>
        </template>
        <form class="mb-4" v-if="form" @submit.prevent="submit">
            <div class="card mb-5">
                <div class="card-header">
                    <div class="card-title fs-3 fw-bold">Create Plan</div>
                </div>
                <div class="card-body py-3 pt-10">
                    <div class="fv-row mb-4 fv-plugins-icon-container">
                        <Label for="title" class="required" value="Name" />
                        <Input id="name" type="text" :class="{ 'is-invalid border border-danger': form.errors.name }"
                            v-model="form.name" placeholder="Enter title" />
                        <error :message="form.errors.name"></error>
                    </div>
                    <div class="row">
                        <div class="col-md-6 fv-row mb-4 fv-plugins-icon-container">
                            <Label for="price" class="required" value="Price" />
                            <Input id="price" type="number"
                                :class="{ 'is-invalid border border-danger': form.errors.price }" v-model="(form.price)"
                                placeholder="Enter title" />
                            <error :message="form.errors.price"></error>
                        </div>
                        <div class="col-md-6 fv-row mb-4 fv-plugins-icon-container">
                            <Label for="title" class="required" value="Interval" />
                            <select class="form-select form-select-solid" name="interval" id="interval"
                                v-model="form.interval"
                                :class="{ 'is-invalid border border-danger': form.errors.interval }">
                                <option value="" disabled>Select Interval</option>
                                <option value="day">Day</option>
                                <option value="week">Week</option>
                                <option value="month">Month</option>
                                <option value="year">Year</option>
                            </select>
                            <error :message="form.errors.interval"></error>
                        </div>
                    </div>
                    <div class="fv-row mb-4 fv-plugins-icon-container">
                        <Label for="title" value="Description" />
                        <textarea class="form-control form-control-lg form-control-solid" rows="4"
                            placeholder="Shipping and return Policy" v-model="form.description"
                            :class="{ 'is-invalid border border-danger': form.errors.description }"></textarea>
                        <error :message="form.errors.description"></error>
                    </div>
                </div>
            </div>
            <div class="card mb-5">
                <div class="card-header">
                    <div class="card-title fs-3 fw-bold">Select Permissions</div>
                </div>
                <div class="card-body py-3 pt-10">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4 ">
                        <!--begin::Table head-->
                        <thead>
                            <tr class="fw-bold text-muted">
                                <th class="w-25px">
                                </th>
                                <th class="min-w-200px">Permissions</th>
                                <th class="min-w-150px text-end">Enter Quantity</th>
                            </tr>
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody>
                            <tr>
                                <td>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                        <input class="form-check-input widget-9-check" type="checkbox" value="1"
                                            v-model="permissions.total_businesses.status">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex justify-content-start flex-column"
                                            style="margin-right:550px;">
                                            <a href="#" class="text-dark fw-bold text-hover-primary fs-6">Total Allowed Departments</a>
                                            <!-- <span class="text-muted fw-semibold text-muted d-block fs-7">xyz</span> -->
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <Input id="price" type="number" class="text-end" :class="{'is-invalid border border-danger': form.errors['permissions.total_businesses.value']}" :disabled="!permissions.total_businesses.status"
                                        v-model="permissions.total_businesses.value" style="width:40%; display: -webkit-inline-box;"/>
                                        <Error :message="form.errors['permissions.total_businesses.value']"></Error>
                                    <span class="text-danger d-block fs-7">Enter -1 for unlimited access</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                        <input class="form-check-input widget-9-check" type="checkbox"
                                            v-model="permissions.featured_businesses.status">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex justify-content-start flex-column"
                                            style="margin-right:550px;">
                                            <a href="#" class="text-dark fw-bold text-hover-primary fs-6">Total Allowed
                                                Featured Departments</a>
                                            <!-- <span class="text-muted fw-semibold text-muted d-block fs-7">xyz</span> -->
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <Input id="price" type="number" class="text-end" :class="{'is-invalid border border-danger' : form.errors['permissions.featured_businesses.value']}" :disabled="!permissions.featured_businesses.status"
                                        v-model="permissions.featured_businesses.value" style="width:40%; display: -webkit-inline-box;"/>
                                    <Error :message="form.errors['permissions.featured_businesses.value']"></Error>
                                    <span class="text-danger d-block fs-7">Enter -1 for unlimited access</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                        <input class="form-check-input widget-9-check" type="checkbox"
                                            v-model="permissions.total_products.status">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex justify-content-start flex-column"
                                            style="margin-right:550px;">
                                            <a href="#" class="text-dark fw-bold text-hover-primary fs-6">Total Allowed
                                                Posts</a>
                                            <!-- <span class="text-muted fw-semibold text-muted d-block fs-7">xyz</span> -->
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <Input id="price" type="number" class="text-end" :class="{ 'is-invalid border border-danger': form.errors['permissions.total_products.value'] }" :disabled="!permissions.total_products.status"
                                        v-model="permissions.total_products.value" style="width:40%; display: -webkit-inline-box;"/>
                                    <error :message="form.errors['permissions.total_products.value']"></error>
                                    <span class="text-danger d-block fs-7">Enter -1 for unlimited access</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                        <input class="form-check-input widget-9-check" type="checkbox"
                                            v-model="permissions.type.status">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex justify-content-start flex-column"
                                            style="margin-right:550px;">
                                            <a href="#" class="text-dark fw-bold text-hover-primary fs-6">Subscription Type</a>
                                            <!-- <span class="text-muted fw-semibold text-muted d-block fs-7">xyz</span> -->
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <!-- <Input id="price" type="number" class="text-end" :disabled="!permissions.type.status"
                                        v-model="permissions.type.value" style="width:40%; display: -webkit-inline-box;"/> -->
                                    <!-- <error :message="form.errors.price"></error> -->
                                    <select class="form-select form-select-solid" :class="{'is-invalid border border-danger': form.errors['permissions.type.value']}" v-model="permissions.type.value">
                                        <option value="" disabled>Select Type</option>
                                        <option value="L1">L1</option>
                                        <option value="L2">L2</option>
                                        <option value="L3">L3</option>
                                    </select>
                                    <Error :message="form.errors['permissions.type.value']"></Error>
                                    <span class="text-danger d-block fs-7">Enter -1 for unlimited access</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                        <input class="form-check-input widget-9-check" type="checkbox"
                                            v-model="permissions.featured_products.status">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex justify-content-start flex-column"
                                            style="margin-right:550px;">
                                            <a href="#" class="text-dark fw-bold text-hover-primary fs-6">Featured Posts</a>
                                            <!-- <span class="text-muted fw-semibold text-muted d-block fs-7">xyz</span> -->
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <!-- <Input id="price" type="number" class="text-end" :disabled="!permissions.featured_products.status"
                                        v-model="permissions.featured_products.value" style="width:40%; display: -webkit-inline-box;"/>
                                    <span class="text-danger d-block fs-7">Enter -1 for unlimited access</span> -->
                                </td>
                            </tr>
                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <div class="text-end mt-5">
                        <Button type="submit" :classes="'btn btn-lg btn-primary'"
                            :class="{ 'opacity-25': form.processing }" :disabled="form.processing" ref="submitButton">
                            <span class="indicator-label" v-if="!form.processing">
                                Save plan
                                <span class="svg-icon svg-icon-3 ms-2 me-0"><svg fill="none" viewBox="0 0 24 24"
                                        height="24" width="24" xmlns="http://www.w3.org/2000/svg">
                                        <rect xmlns="http://www.w3.org/2000/svg" opacity="0.5" x="18" y="13" width="13"
                                            height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor"></rect>
                                        <path xmlns="http://www.w3.org/2000/svg"
                                            d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z"
                                            fill="currentColor"></path>
                                    </svg>
                                </span>
                            </span>
                            <span class="indicator-progress" v-if="form.processing">
                                <span class="spinner-border spinner-border-sm align-middle"></span>
                            </span>
                        </Button>
                    </div>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import { Head } from '@inertiajs/inertia-vue3'
import Helpers from '@/Mixins/Helpers'
import InlineSvg from 'vue-inline-svg'
import Toggle from '@/Components/ToggleButton.vue'
import SearchInput from '@/Components/SearchInput.vue'
import Pagination from '@/Components/Pagination.vue'
import EditSection from "@/Components/EditSection.vue"
import DeleteSection from "@/Components/DeleteSection.vue"
import moment from 'moment'
import Label from '@/Components/Label.vue'
import { useForm } from '@inertiajs/inertia-vue3'
import Input from '@/Components/Input.vue'
import Error from '@/Components/InputError.vue'
import Button from '@/Components/Button.vue'


export default {
    props: ['plan'],

    components: {
        AuthenticatedLayout,
        Breadcrumbs,
        Head,
        InlineSvg,
        Toggle,
        SearchInput,
        Pagination,
        EditSection,
        DeleteSection,
        Label,
        Input,
        Error,
        Button
    },

    data() {
        return {
            form: null,
            processing: false,
            permissions: {
                'total_businesses': {
                    'status': false,
                    'key': 'total_businesses',
                    'value': null
                },
                'featured_businesses': {
                    'status': false,
                    'key': 'featured_businesses',
                    'value': null
                },
                'featured_products': {
                    'status': false,
                    'key': 'featured_products',
                    'value': null
                },
                'total_products': {
                    'status': false,
                    'key': 'total_products',
                    'value': null
                },
                'type': {
                    'status': false,
                    'key': 'type',
                    'value': null
                },
            }
        }
    },

    methods: {
        async submit() {
            this.form.permissions = this.permissions
            this.processing = true;
            if (this.form.id) {
                this.form.put(route('government.dashboard.subscription.plan.update', [this.getSelectedModuleValue(), this.form.id]), {
                    errorBag: null,
                    preserveScroll: false,
                    onSuccess: () => {
                        this.processing = false
                    },
                    onError: () => {}
                })
            } else {
                this.form.post(route('government.dashboard.subscription.plan.store', [this.getSelectedModuleValue()]), {
                    errorBag: null,
                    preserveScroll: false,
                    onSuccess: () => {
                        this.processing = false
                    },
                    onError: () => {}
                })
            }
        },
    },

    mounted() {
        this.form = useForm({
            id: this.plan ? this.plan.product.id : null,
            priceId: this.plan ? this.plan.price.id : null,
            name: this.plan ? this.plan.product.name : null,
            description: this.plan ? this.plan.product.description : null,
            price: this.plan ? (this.plan.price.unit_amount / 100) : null,
            interval: this.plan ? this.plan.price.recurring.interval : null,
            permissions: this.plan ? this.plan.permissions : null,
        })
        if (this.form.id) {
            this.permissions.total_businesses = {
                'status' : this.plan.permissions[0].status ? true : false,
                'key' : this.plan.permissions[0].key,
                'value' : this.plan.permissions[0].value,
            }
            this.permissions.featured_businesses = {
                'status' : this.plan.permissions[1].status ? true : false,
                'key' : this.plan.permissions[1].key,
                'value' : this.plan.permissions[1].value,
            }
            this.permissions.featured_products = {
                'status' : this.plan.permissions[2].status ? true : false,
                'key' : this.plan.permissions[2].key,
                'value' : this.plan.permissions[2].value,
            }
            this.permissions.total_products = {
                'status' : this.plan.permissions[3].status ? true : false,
                'key' : this.plan.permissions[3].key,
                'value' : this.plan.permissions[3].value,
            }
            this.permissions.type = {
                'status' : this.plan.permissions[4].status ? true : false,
                'key' : this.plan.permissions[4].key,
                'value' : this.plan.permissions[4].value,
            }
        }

    },
    mixins: [Helpers]
}
</script>
