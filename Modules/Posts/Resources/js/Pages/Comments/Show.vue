<template>
    <Head title="Post Detail" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs title="View Comment" :path="`View Comment`" />
        </template>
       <div>
        <div class="row gx-5 gx-xl-10">
            <div class="col-xxl-6 mb-5 mb-xl-10">
                <!--begin::Chart widget 8-->
                <div class="card card-flush h-xl-100">
                    <!--begin::Header-->
                    <div class="card-header pt-5">
                        <div class="card-title">
                            <h2>Comment Detail</h2>
                        </div>
                    </div>
                    <!--end::Header-->

                    <!--begin::Body-->
                    <div class="card-body pt-6">
                        <table class="table fs-6 fw-semibold gs-0 gy-2 gx-2 m-0">
                            <tbody>
                                <tr>
                                    <td class="text-gray-400 min-w-175px w-175px">Comment</td>
                                    <td class="text-gray-800 min-w-200px">
                                        {{ comment.comment }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-gray-400 min-w-175px w-175px">Created By</td>
                                    <td class="text-gray-800 min-w-200px">
                                        {{ comment.user.first_name }} {{ comment.user.last_name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-gray-400 min-w-175px w-175px">Created At</td>
                                    <td class="text-gray-800 min-w-200px">
                                        {{ formatDate(comment.created_at) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Chart widget 8-->
            </div>
           
        </div>
       </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import { Head, Link } from '@inertiajs/inertia-vue3';
import Helpers from '@/Mixins/Helpers';

export default {
    components: {
        AuthenticatedLayout,
        Head,
        Link,
        Breadcrumbs,
    },

    props: ['commentList'],
    data() {
        return {
            comment: this.commentList
        }
    },
    mixins: [Helpers]
}
</script>