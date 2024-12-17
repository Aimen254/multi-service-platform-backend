<template>
    <Head title="Orders" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="orderType+` Orders`" :path="`Orders`" />
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <div class="me-4">
                    <!--begin::Menu-->
                    <a
                        href="#"
                        class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder"
                        data-kt-menu-trigger="click"
                        data-kt-menu-placement="bottom-end"
                        data-kt-menu-flip="top-end"
                    >
                        <span class="svg-icon svg-icon-5 svg-icon-gray-500 me-1">   
                            <inline-svg :src="'/images/icons/filter.svg'" />
                        </span>
                        Filters
                    </a>
                    <Filter :filterData="filterForm " :callType="type" :url="urlForFilter" :newKeyword="searchedKeyword" :orderType="orderType"> 
                        <div class="mb-5">
                            <div class="row">
                                <div class="col-lg-6">
                                    <label class="form-label fw-bold">From:</label>
                                    <div class="mx-2">
                                        <Datepicker v-model="filterForm.from"
                                            :upperLimit="filterForm.to"
                                            class="start_date form-control form-control-sm form-control-solid" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label fw-bold">To:</label>
                                    <div class="mx-2">
                                        <Datepicker v-model="filterForm.to"
                                            :lowerLimit="filterForm.from"
                                            class="start_date form-control form-control-sm form-control-solid" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-5">
                            <label class="form-label fw-bold">Order By:</label>
                            <div>
                                <select name="name" id="name" class="form-select form-select-solid text-muted form-select-sm" v-model="filterForm.orderBy">
                                    <option value="desc" selected>Descending</option>
                                    <option value="asc">Ascending</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-5">
                            <label class="form-label fw-bold">Order Type:</label>
                            <div class="mx-2">
                                <select name="name" id="name" class="form-select form-select-solid text-muted form-select-sm"
                                    @change="onSelected($event)" v-model="filterForm.order_type">
                                    <option value="mail" selected>Mail</option>
                                    <option value="pick_up">Pick Up</option>
                                    <option value="delivery">Delivery</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-5" v-if="orderType == 'active'">
                            <label class="form-label fw-bold">Status:</label>
                            <div class="mx-2">
                                <select name="name" id="name" class="form-select text-capitalize form-select-solid text-muted form-select-sm"
                                v-model="filterForm.order_status_id">
                                    <option v-for="item in filterStatus" :key="item.id" :value="item.id"> {{ getGroupName(item.status) }} </option>
                                </select>
                            </div>
                        </div>
                    </Filter> 
                    <!--end::Menu-->
                </div>
            </div>
        </template>
        <div class="card">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <SearchInput :module="module" :callType="type" :searchedKeyword="searchedKeyword" :filterFormData="filterForm " :orderFrom="from" :orderTo="to" :orderType="orderType"/>
                </h3>
                <div class="text-muted pe-5 d-flex align align-items-center justify-content-end">
                    <label class="fw-bold fs-6 text-muted">Total Amount:</label>
                    <span class="ps-2">${{totalAmount}}</span>
                </div>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bolder border-0 text-muted bg-light">
                                <th class="ps-4 min-w-120px rounded-start">Order #</th>
                                <th class="ps-4 min-w-120px rounded-start">Order Type</th>
                                <th class="ps-4 min-w-120px rounded-start">Customer</th>
                                <th class="ps-4 min-w-120px rounded-start">Total Amount</th>
                                <th class="ps-4 min-w-120px rounded-start">Products</th>
                                <th class="ps-4 min-w-120px rounded-start">Status</th>
                                <th class="min-w-120px">Ordered At</th>
                                <th class="pe-4 min-w-120px rounded-end">Action</th>
                            </tr>
                        </thead>
                        <tbody v-if="orders && orders.data.length > 0">
                            <template v-for="(order, index) in orders.data" :key="order.id">
                                <tr>
                                    <td data-kt-ecommerce-order-filter="order_id" 
                                        class="text-gray-800 text-hover-primary fw-bolder px-4">
                                        {{ order.order_id }}
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7 text-capitalize px-2">
                                            {{ getGroupName(order.order_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-circle symbol-50px overflow-hidden">
                                                <div class="symbol-label">
                                                    <img :src="getImage(order?.model?.avatar, true, 'avatar')" class="w-100" />
                                                </div>
                                            </div>
                                            <div class="ms-5 text-gray-800 text-hover-primary fs-5 fw-bolder">
                                                {{ order.model.first_name }} {{ order.model.last_name }}
                                            </div>
                                            <span v-if="order.model.deleted_at"
                                                class="text-capitalize ms-4 badge badge-light-danger"> In Active User
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7 text-capitalize px-2">
                                            <span class="fw-normal text-muted fs-7">$</span>{{ order.total.toFixed(2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7 text-capitalize px-2">
                                            {{ order.items_count }}
                                        </span>
                                    </td>
                                    <td v-if="orderType === 'cancelled' || orderType === 'refunded'">
                                        <span class="text-muted fw-bold text-muted d-block fs-7 text-capitalize px-2">
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
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7">
                                            {{ formatDateTime(order.created_at)}}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <view-section
                                                permission="edit_orders"
                                                :url="route('retail.dashboard.business.order.type.orders.edit', [getSelectedModuleValue(), getSelectedBusinessValue(), orderType, order.id])"/>
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
            <pagination :meta="ordersList" :keyword="searchedKeyword" :selectedFilters="filterForm" :orderFrom="from" :orderTo="to" :callType="type"/>
        </div>
    </AuthenticatedLayout>
    <reject-modal></reject-modal>
</template>

<script>
    import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
    import Breadcrumbs from '@/Components/Breadcrumbs.vue'
    import { Head } from '@inertiajs/inertia-vue3'
    import SearchInput from '@/Components/SearchInput.vue'
    import Helpers from '@/Mixins/Helpers'
    import EditSection from '@/Components/EditSection.vue'
    import Pagination from '@/Components/Pagination.vue'
    import ViewSection from '@/Components/ViewSection.vue'
    import InlineSvg from 'vue-inline-svg'
    import Filter from "@/Components/Filter.vue"
    import Datepicker  from 'vue3-datepicker'
    import RejectModal from './Partials/RejectionMessageModal.vue'

    export default {
        props: ['ordersList', 'statusList', 'searchedKeyword', 'orderType', 'orderBy', 'deliveryMethod', 'status', 'from', 'to', 'decimalLength', 'decimalSeparator'],
        
        components: {
            AuthenticatedLayout,
            Breadcrumbs,
            Head,
            SearchInput,
            EditSection,
            Pagination,
            ViewSection,
            InlineSvg,
            Filter,
            Datepicker,
            RejectModal
        },

        data () {
            return {
                type: 'order',
                module: 'retail',
                form: null,
                dateFrom: this.from,
                dateTo: this.to,
                orders: null,
                urlForFilter: 'retail.dashboard.business.order.type.orders.index',
                filterForm  : {
                    orderBy : this.orderBy ? this.orderBy : 'desc',
                    order_type: this.deliveryMethod,
                    order_status_id: this.status,
                    from: this.dateFrom ? new Date(this.dateFrom) : null,
                    to: this.dateTo ? new Date(this.dateTo) : null,
                },
                filterStatus: null
            }
        },

        watch: {
            ordersList: {
                handler(ordersList) {
                    this.orders = ordersList
                },
                deep: true
            },
        },
        computed: {
            totalAmount: function () {
                // let total = 0
                return this.orderTotal(this.ordersList, this.decimalLength, this.decimalSeparator)
                // return total
            }
        },
        methods: {
            onChange(event, order, index) {
                this.form = this.$inertia.form({
                    id: order.id,
                    order_status_id: event.target.value,
                    charge_stripe: event.target.value == 2 ? true : false
                });
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
                        if (event.target.value == 14) {
                            this.openModal(this.form)
                        } else {
                            showWaitDialog()
                            this.form.put(route('retail.dashboard.business.order.type.orders.update', [this.getSelectedModuleValue(), this.getSelectedBusinessValue(), this.form.id, 'active']), {
                                errorBag: 'order',
                                preserveScroll: true,
                                onSuccess: () => hideWaitDialog()
                                
                            })
                        }
                    } else {
                        this.orders.data.splice(index, 1, this.ordersList.data[index])
                    }
                })
            },
            onSelected (event) {
                this.filterStatus = this.orderStatusFilter(this.statusList, event.target.value)
            },
            openModal(form) {
                this.emitter.emit("reject-modal", {
                    order_status_form: form ,
                });
            },
        },  
        
        mounted () {
            this.orders = _.cloneDeep(this.ordersList)
        },
        updated() {
            this.filterStatus = this.orderStatusFilter(this.statusList, this.deliveryMethod)
        },
        mixins: [Helpers]
    }
</script>