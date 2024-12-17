<template>
    <Head title="Users Remote Assistants" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Remote Assistants`" :path="`Users`" />
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <Link v-if="checkUserPermissions('add_remote_assistants')"
                    :href="route('dashboard.assistants.create')"
                    class="btn btn-sm btn-primary">
                    <span class="svg-icon svg-icon-2">
                        <inline-svg :src="'/images/icons/add.svg'" />
                    </span>
                    Add Remote Assistant
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
                        <tbody v-if="assistants && assistants.data.length > 0">
                            <template v-for="assistant in assistants.data" :key="assistant.id">
                                <tr>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="image-input image-input-empty">
                                                <div
                                                class="image-input-wrapper w-50px h-50px"
                                                :style="{ 'background-image': 'url(' + getImage(assistant.avatar, true, 'avatar') + ')' }"
                                                ></div>
                                            </div>
                                            <div class="d-flex justify-content-start flex-column mx-2">
                                                <span
                                                    class="text-dark fw-bolder text-hover-primary mb-1 fs-6">{{ assistant.first_name }}
                                                    {{ assistant.last_name }}
                                                </span>
                                                <span class="text-muted fw-bold text-muted d-block fs-7">
                                                    {{ assistant.email}}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block text-capitalize fs-7">
                                            {{ getRoleName(assistant.user_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge fs-7 fw-bold text-capitalize"
                                            :class="{ 'badge-light-success': assistant.status == 'active', 'badge-light-danger': assistant.status === 'inactive' }">{{ assistant.status }}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="text-muted fw-bold text-muted d-block fs-7">{{ formatDate(assistant.created_at)}}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <Toggle v-if="checkUserPermissions('edit_remote_assistants')"
                                                :status="booleanStatusValue(assistant.status)"
                                                @click.prevent="changeStatus(assistant.id)" />
                                            <edit-section iconType="link" 
                                                permission="edit_remote_assistants"
                                                :url="route('dashboard.assistants.edit', assistant.id)"/>
                                            <delete-section permission="delete_remote_assistants"
                                                :url="route('dashboard.assistants.destroy', assistant.id)" />
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
            <pagination :meta="assistants" :keyword="searchedKeyword" />
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

    props: ['assistantsList', 'searchedKeyword'],

    data () {
        return {
            assistants: this.assistantsList,
            type: 'remote_assistant'
        }
    },

    watch: {
        assistantsList: {
            handler(assistantsList) {
                this.assistants = assistantsList
            },
            deep: true
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
                    
                    this.$inertia.visit(route('dashboard.assistant.change.status', id), {
                        preserveScroll: false,
                        onSuccess: () => hideWaitDialog()
                    })
                }
            })
        }
    },

    mixins: [Helpers]
}
</script>
