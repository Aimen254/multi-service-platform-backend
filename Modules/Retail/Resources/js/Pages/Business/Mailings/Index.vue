<template>
    <Head title="Mail Settings" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Mail Settings`" :path="`Businesses`" />
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button class="btn btn-sm btn-primary" v-if="checkUserPermissions('add_business_mailings')"
                    @click="openModal()">
                    <span class="svg-icon svg-icon-2">
                        <inline-svg :src="'/images/icons/arr075.svg'" />
                    </span>
                    Add Mail
                </button>
            </div>
        </template>
        <div class="card">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <RetailSearchInput :business="business" :callType="type" :searchedKeyword="searchedKeyword"/>
                </h3>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bolder border-0 text-muted bg-light">
                                <th class="ps-4 min-w-120px rounded-start">Title</th>
                                <th class="min-w-120px">Minimum Amount</th>
                                <th class="min-w-120px">Price</th>
                                <th class="min-w-120px">Status</th>
                                <th class="min-w-120px">Created At</th>
                                <th class="min-w-120px rounded-end">Action</th>
                            </tr>
                        </thead>
                        <tbody v-if="mailings && mailings.data.length > 0">
                            <template v-for="mailing in mailings.data" :key="mailing.id">
                                <tr>
                                    <td class="ps-4">
                                        <span class="text-dark fw-bolder text-hover-primary mb-1 fs-7">{{ mailing.title }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7">
                                            <span>$</span>{{ mailing.minimum_amount }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7">
                                            <span>$</span>{{ mailing.price }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge fs-7 fw-bold text-capitalize"
                                            :class="{ 'badge-light-success': mailing.status == 'active', 'badge-light-danger': mailing.status === 'inactive' }">{{ mailing.status }}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="text-muted fw-bold text-muted d-block fs-7">{{ formatDate(mailing.created_at)}}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <Toggle v-if="checkUserPermissions('edit_business_mailings')"
                                                :status="booleanStatusValue(mailing.status)"
                                                @click.prevent="changeStatus(mailing.id)"
                                            />
                                            <edit-section iconType="modal" permission="edit_business_mailings"
                                                @click="openModal(mailing)" />
                                            <delete-section permission="delete_business_mailings"
                                                :url="route('retail.dashboard.business.mailings.destroy', [getSelectedModuleValue(), business.id, mailing.id])" 
                                                :currentPage="mailings.current_page" :currentCount="mailings.data.length"/>                                            
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
            <pagination v-if="mailings" :meta="mailings" :keyword="searchedKeyword"/>
        </div>
        <mail-modal />
    </AuthenticatedLayout>
</template>

<script>
    import { Head } from '@inertiajs/inertia-vue3'
    import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
    import Breadcrumbs from '@/Components/Breadcrumbs.vue'
    import SearchInput from '@/Components/SearchInput.vue'
    import Helpers from "@/Mixins/Helpers"
    import MailModal from './MailModal.vue'
    import Toggle from '@/Components/ToggleButton.vue'
    import Pagination from '@/Components/Pagination.vue'
    import EditSection from "@/Components/EditSection.vue";
    import DeleteSection from "@/Components/DeleteSection.vue";
    import InlineSvg from 'vue-inline-svg'
    import RetailSearchInput from "../../Components/RetailSearchInput.vue";

    export default {
        props : ['business', 'mailingList', 'searchedKeyword'],

        components : {
            Head,
            AuthenticatedLayout,
            Breadcrumbs,
            SearchInput,
            MailModal,
            Toggle,
            Pagination,
            EditSection,
            DeleteSection,
            InlineSvg,
            RetailSearchInput
        },

        data() {
            return {
                type: 'mailing',
                mailings: null,
            }
        },

        methods: {
            openModal(mailing = null) {
                this.emitter.emit("mailing-modal", {
                    mailing: mailing,
                    businessId: this.business.id
                });
            },
            onDelete(id) {
                this.swal.fire({
                    title: "",
                    html: "<h1 class='text-lg text-gray-800 mb-1'>Delete Record</h1><p class='text-base'>Are you sure want to delete this record?</p>",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: "Delete Record",
                    customClass: {
                        confirmButton: 'danger'
                    }
                }).then((result) => {
                    if (result.value) {
                        showWaitDialog()

                        this.$inertia.delete(route('retail.dashboard.business.mailings.destroy', [this.getSelectedModuleValue(), this.business.id, id]), {
                            preserveScroll: false,
                            onSuccess: () => hideWaitDialog()
                        })
                    }
                })
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

                        this.$inertia.visit(route('retail.dashboard.business.mailing.status', [this.getSelectedModuleValue(), this.business.id, id]), {
                            preserveScroll: false,
                            onSuccess: () => hideWaitDialog()
                        })
                    }
                })
            },
        },
        watch: {
            mailingList: {
                handler(mailingList) {
                    this.mailings = mailingList
                },
                deep: true
            }
        },
        mounted() {
            this.mailings = this.mailingList
        },

        mixins: [Helpers],
    }
</script>