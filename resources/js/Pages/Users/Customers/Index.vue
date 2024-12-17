<template>

    <Head title="Customers" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Customers`" :path="`Users`" />
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <Link v-if="checkUserPermissions('add_customers')" :href="route('dashboard.customers.create')"
                    class="btn btn-sm btn-primary">
                <span class="svg-icon svg-icon-2">
                    <inline-svg :src="'/images/icons/add.svg'" />
                </span>
                Add Customer
                </Link>
            </div>
        </template>
        <div class="card">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <SearchInput :callType="type" :searchedKeyword="searchedKeyword" />
                </h3>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bolder border-0 text-muted bg-light">
                                <th class="ps-4 min-w-120px rounded-start">Name</th>
                                <th class="min-w-120px">Role</th>
                                <th class="min-w-120px">Status</th>
                                <th class="min-w-120px">Created At</th>
                                <th class="min-w-120px rounded-end">Action</th>
                            </tr>
                        </thead>
                        <tbody v-if="customers && customers.data.length > 0">
                            <template v-for="customer in customers.data" :key="customer.id">
                                <tr>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="image-input image-input-empty">
                                                <div class="image-input-wrapper w-50px h-50px"
                                                    :style="{ 'background-image': 'url(' + getImage(customer.avatar, true, 'avatar') + ')' }">
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-start flex-column mx-2">
                                                <span class="text-dark fw-bolder text-hover-primary mb-1 fs-6">{{
                customer.first_name }}
                                                    {{ customer.last_name }}
                                                </span>
                                                <span class="text-muted fw-bold text-muted d-block fs-7">
                                                    {{ customer.email }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block text-capitalize fs-7">
                                            {{ getRoleName(customer.user_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge fs-7 fw-bold text-capitalize"
                                            :class="{ 'badge-light-success': customer.status == 'active', 'badge-light-danger': customer.status === 'inactive' }">{{
                customer.status }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7">{{
                formatDate(customer.created_at) }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <Toggle v-if="checkUserPermissions('edit_customers')"
                                                :status="booleanStatusValue(customer.status)"
                                                @click.prevent="changeStatus(customer.id)" />

                                            <div @click="removeTooltip; openModal(customer)"
                                                class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"
                                                data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss="click"
                                                data-bs-placement="bottom" data-bs-original-title="change role">
                                                <span class="me-2 my-3">
                                                    <i class="fas fa-user-shield"></i>
                                                </span>
                                            </div>
                                            <edit-section iconType="link" permission="edit_customers"
                                                :url="route('dashboard.customers.edit', customer.id)" />
                                            <delete-section permission="delete_customers"
                                                :url="route('dashboard.customers.destroy', customer.id)" 
                                                :currentPage="customers.current_page" :currentCount="customers.data.length"/>

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
            <UserRoleModal />
            <pagination :meta="customers" :keyword="searchedKeyword" />
        </div>
    </AuthenticatedLayout>
</template>
<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
import Header from '@/Components/Header.vue';
import Pagination from '@/Components/Pagination.vue';
import Helpers from '@/Mixins/Helpers';
import EditSection from '@/Components/EditSection.vue';
import DeleteSection from '@/Components/DeleteSection.vue';
import Toggle from '@/Components/ToggleButton.vue';
import SearchInput from '@/Components/SearchInput.vue';
import InlineSvg from 'vue-inline-svg';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import UserRoleModal from './UserRoleModal.vue';

export default {
    components: {
        AuthenticatedLayout,
        Head,
        Header,
        Link,
        Pagination,
        EditSection,
        DeleteSection,
        Toggle,
        SearchInput,
        InlineSvg,
        Breadcrumbs,
        UserRoleModal
    },

    props: ['customersList', 'searchedKeyword'],

    data() {
        return {
            customers: this.customersList,
            type: 'customer'
        }
    },

    watch: {
        customersList: {
            handler(customersList) {
                this.customers = customersList
            },
            deep: true
        }
    },

    methods: {
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
                    this.$inertia.visit(route('dashboard.customer.change.status', id), {
                        preserveScroll: false,
                        onSuccess: () => hideWaitDialog()
                    })
                }
            })
        },
        openModal(customer) {
            this.emitter.emit("user-role-modal", customer);
        },
    },
    mixins: [Helpers]
}
</script>
