<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <h3>Dashboard</h3>
        </template>
        <div class="row">
            <div class="col-xl-4">
                <div class="card h-100">
                    <div class="card-header flex-nowrap border-0 pt-9">
                        <div class="card-title m-0">
                            <div class="symbol me-5">
                                <i class="fa fa-blog display-6"></i>
                            </div>
                            <a href="#" class="fs-4 fw-bold text-hover-primary text-capitalize text-gray-600 m-0">Total Taskers</a>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column px-9 pt-6 pb-8">
                        <div class="fs-2tx fw-bolder mb-3 text-black-50">{{ totalTaskers }}</div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
    import { Head } from '@inertiajs/inertia-vue3';
    import AuthenticatedLayout from '@/Layouts/Authenticated.vue';

    export default {
        props: ['totalTaskers'],
        components: {
            Head,
            AuthenticatedLayout
        },
    }
</script>
