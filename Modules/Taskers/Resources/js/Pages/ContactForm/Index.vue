<template>
    <Head title="Communication Portal" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Communication Portal`" :path="`Communication Portal`" />
        </template>
        <div class="card">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <TaskerSearchInput :module="module" :callType="type" :searchedKeyword="searchedKeyword"/>
                </h3>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bolder border-0 text-muted bg-light">
                                <th class="ps-4 min-w-120px rounded-start">Tasker</th>
                                <th class="ps-4 min-w-120px rounded-start">Customer</th>
                                <th class="ps-4 min-w-120px rounded-start">Urgent</th>
                                <th class="ps-4 min-w-120px rounded-start">Subject</th>
                                <th class="min-w-120px">Received At</th>
                                <th class="min-w-120px rounded-end">Action</th>
                            </tr>
                        </thead>
                        <tbody v-if="contactForms && contactForms.data.length > 0">
                            <template v-for="form in contactForms.data" :key="form.id">
                                <tr>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="image-input image-input-empty">
                                                <div class="image-input-wrapper bg-image-position-contain w-50px h-50px"
                                                    :style="{ 'background-image': 'url(' + getImage(form?.product?.main_image?.path ?? null, true, 'product', form?.product?.main_image?.is_external ?? 0) + ')' }">
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-start flex-column mx-2">
                                                <span class="text-dark fw-bolder text-hover-primary mb-1 fs-6 text-capitalize"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    :data-bs-original-title="form.product.name">
                                                    {{ limit(form?.product?.name, 30) }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <!-- <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7">
                                            <span class="fw-normal text-muted fs-7"></span>{{ form.email }}
                                        </span>
                                    </td> -->
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7 text-capitalize">
                                            <span class="fw-normal text-muted fs-7"></span>{{ form?.first_name }} {{ form?.last_name }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7 text-capitalize">
                                            <span class="fw-normal text-muted fs-7"></span>{{ form.is_urgent ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7 text-capitalize">
                                            <span class="fw-normal text-muted fs-7"></span>{{ removeUnderscores(form?.subject) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7">{{
                                                formatDate(form?.created_at)
                                        }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <view-section permission="view_contact_form" :url="route('taskers.dashboard.communication-portal.show', [getSelectedModuleValue(), form.id])"/>
                                            <delete-section permission="delete_contact_form"
                                                :url="route('taskers.dashboard.communication-portal.destroy', [getSelectedModuleValue(), form.id])" 
                                                :currentPage="contactForms.current_page" :currentCount="contactForms.data.length"/>
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
            <pagination :meta="contactForms" :keyword="searchedKeyword"/>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import { Head, Link } from '@inertiajs/inertia-vue3';
import Pagination from '@/Components/Pagination.vue';
import Helpers from '@/Mixins/Helpers';
import DeleteSection from '@/Components/DeleteSection.vue';
import TaskerSearchInput from "../../Components/TaskerSearchInput.vue";
import ViewSection from '@/Components/ViewSection.vue'

export default {
    components: {
        AuthenticatedLayout,
        Head,
        Link,
        Pagination,
        DeleteSection,
        TaskerSearchInput,
        Breadcrumbs,
        ViewSection
    },

    props: ['forms', 'searchedKeyword'],
    data() {
        return {
            contactForms: this.forms,
            type: 'contact_form',
            module: 'taskers'
        }
    },
    watch: {
        forms: {
            handler(forms) {
                this.contactForms = forms
            },
            deep: true
        },
    },
    mixins: [Helpers]
}
</script>
