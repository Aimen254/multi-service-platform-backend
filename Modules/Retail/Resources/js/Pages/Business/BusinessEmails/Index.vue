<template>
    <Head title="Additional Emails" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Additional Emails`" :path="`Businesses`"></Breadcrumbs>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <div v-if="checkUserPermissions('add_additional_emails')"
                    @click="openModal()"
                    class="btn btn-sm btn-primary">
                    <span class="svg-icon svg-icon-2">
                        <inline-svg :src="'/images/icons/add.svg'"/>
                    </span>
                    Add Additonal Email
                </div>
            </div>
        </template>
        <div class="card">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <RetailSearchInput :callType="type" :searchedKeyword="searchedKeyword"/>
                </h3>
                
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bolder border-0 text-muted bg-light">
                                <th class="ps-4 min-w-120px rounded-start">Personal Name</th>
                                <th class="min-w-120px">Title</th>
                                <th class="min-w-120px">Email</th>
                                <th class="min-w-120px">Created At</th>
                                <th class="min-w-120px rounded-end">Action</th>
                            </tr>
                        </thead>
                        <tbody v-if="emails && emails.data.length > 0">
                            <template v-for="businessEmail in emails.data" :key="businessEmail.id">
                                <tr>
                                    <td class="ps-4">
                                        <span class="text-dark fw-bolder text-hover-primary mb-1 fs-7">{{ businessEmail.personal_name }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7">{{ businessEmail.title }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7">{{ businessEmail.email }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7">{{ formatDate(businessEmail.created_at)}}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <edit-section iconType="modal"
                                                permission="edit_additional_emails"
                                                @click="openModal(businessEmail)" />
                                            <delete-section
                                                permission="delete_additional_emails"
                                                :url="route('retail.dashboard.business.emails.destroy', [getSelectedModuleValue(), businessEmail.id, getSelectedBusinessValue()])" 
                                                :currentPage="emails.current_page" :currentCount="emails.data.length"/>
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
            <pagination :meta="emails" :keyword="searchedKeyword"/>
        </div>
    </AuthenticatedLayout>
    <email-modal></email-modal>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
import Header from '@/Components/Header.vue';
import Pagination from '@/Components/Pagination.vue';
import Helpers from '@/Mixins/Helpers';
import EditSection from '@/Components/EditSection.vue';
import DeleteSection from '@/Components/DeleteSection.vue';
import InlineSvg from 'vue-inline-svg'
import Toggle from '@/Components/ToggleButton.vue';
import SearchInput from '@/Components/SearchInput.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import EmailModal from './EmailModal.vue'
import RetailSearchInput from "../../Components/RetailSearchInput.vue";

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
        EmailModal,
        RetailSearchInput
    },

    props: ['businessEmails', 'searchedKeyword'],

    data () {
        return {
            emails: this.businessEmails,
            type: 'additionalEmail'
        }
    },
    watch: {
        businessEmails: {
            handler(businessEmails) {
                this.emails = businessEmails
            },
            deep: true
        }
    },

    methods: {
        openModal(data = null) {
            this.emitter.emit("email-modal", {
                businessEmail: data
            });
        },
    },
    mixins: [Helpers]
}
</script>