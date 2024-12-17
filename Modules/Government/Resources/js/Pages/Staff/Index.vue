<template>
    <Head title="Government Staff" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Government Staff`" :path="`Staffs`" />
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <Link
                    v-if="checkUserPermissions('add_government_staff')"
                    :href="route('government.dashboard.department.staffs.create', [getSelectedModuleValue(), getSelectedBusinessValue()])"
                    class="btn btn-sm btn-primary">
                    <span class="svg-icon svg-icon-2">
                        <inline-svg :src="'/images/icons/add.svg'" />
                    </span>
                    Add Government Staff
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
                        <tbody v-if="staffs && staffs.data.length > 0">
                            <template v-for="staff in staffs.data" :key="staff.id">
                                <tr>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="image-input image-input-empty">
                                                <div
                                                class="image-input-wrapper w-50px h-50px"
                                                :style="{ 'background-image': 'url(' + getImage(staff.avatar, true, 'avatar') + ')' }"
                                                ></div>
                                            </div>
                                            <div class="d-flex justify-content-start flex-column mx-2">
                                                <span
                                                    class="text-dark fw-bolder text-hover-primary mb-1 fs-6">{{ staff.first_name }}
                                                    {{ staff.last_name }}
                                                </span>
                                                <span class="text-muted fw-bold text-muted d-block fs-7">
                                                    {{ staff.email}}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block text-capitalize fs-7">
                                            {{ getRoleName(staff.user_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge fs-7 fw-bold text-capitalize"
                                            :class="{ 'badge-light-success': staff.status == 'active', 'badge-light-danger': staff.status === 'inactive' }">{{ staff.status }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="text-muted fw-bold text-muted d-block fs-7">{{ formatDate(staff.created_at)}}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <Toggle v-if="checkUserPermissions('edit_government_staff')"
                                                :status="booleanStatusValue(staff.status)"
                                                @click.prevent="changeStatus(staff.id)" />
                                            <edit-section iconType="link"
                                                permission="edit_government_staff"
                                                :url="route('government.dashboard.department.staffs.edit', [getSelectedModuleValue(), getSelectedBusinessValue(), staff.id])"/>
                                            <delete-section v-if="staff.businesses.length == 0" permission="delete_government_staff"
                                                :url="route('government.dashboard.department.staffs.destroy', [getSelectedModuleValue(), getSelectedBusinessValue(), staff.id])" 
                                                :currentPage="staffs.current_page" :currentCount="staffs.data.length"/>
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
            <pagination :meta="staffs" :keyword="searchedKeyword" />
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import { Head, Link } from '@inertiajs/inertia-vue3'
import Header from '@/Components/Header.vue';
import Pagination from '@/Components/Pagination.vue'
import Helpers from '@/Mixins/Helpers'
import EditSection from '@/Components/EditSection.vue'
import DeleteSection from '@/Components/DeleteSection.vue'
import Toggle from '@/Components/ToggleButton.vue';
import SearchInput from '@/Components/SearchInput.vue';
import InlineSvg from 'vue-inline-svg';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

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
        Breadcrumbs
    },
    props: ['staffsList', 'searchedKeyword'],
    data() {
        return {
            staffs: this.staffsList,
            type: 'government_staff',
        }
    },
    methods: {
        changeStatus (id) {
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
                    this.$inertia.visit(route('government.dashboard.department.staffs.change.status', [this.getSelectedModuleValue(), this.getSelectedBusinessValue(), id]), {
                        preserveScroll: false,
                        onSuccess: () => hideWaitDialog()
                    })
                }
            })
        }
    },
    watch: {
        staffsList: {
            handler(staffsList) {
                this.staffs = staffsList
            },
            deep: true
        }
    },
    mixins: [Helpers]
}
</script>
