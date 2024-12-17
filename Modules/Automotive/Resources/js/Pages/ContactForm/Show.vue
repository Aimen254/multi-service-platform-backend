<template>
    <Head title="Contact Form" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`${form.product.name}`" :path="`Contact Form`" />
        </template>
        <div class="row gx-5 gx-xl-10">
            <div class="col-xxl-6 mb-5 mb-xl-10">
                <!--begin::Chart widget 8-->
                <div class="card card-flush h-xl-100">
                    <!--begin::Header-->
                    <div class="card-header pt-5">
                        <div class="card-title">
                            <h2>Vehicle Details</h2>
                        </div>
                    </div>
                    <!--end::Header-->

                    <!--begin::Body-->
                    <div class="card-body pt-6">
                        <table class="table fs-6 fw-semibold gs-0 gy-2 gx-2 m-0">
                            <tbody>
                                <tr>
                                    <td class="text-gray-400 min-w-175px w-175px">Name</td>
                                    <td class="text-gray-800 min-w-200px">
                                        {{ form.product.name ?? 'null' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-gray-400 min-w-175px w-175px">Model</td>
                                    <td class="text-gray-800 min-w-200px">
                                        {{ form.product?.vehicle?.model?.name ?? 'null' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-gray-400 min-w-175px w-175px">Maker</td>
                                    <td class="text-gray-800 min-w-200px">
                                        {{ form.product?.vehicle?.maker?.name ?? 'null' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-gray-400 min-w-175px w-175px">Body Style</td>
                                    <td class="text-gray-800 min-w-200px">
                                        {{ form.product?.vehicle?.body_type?.name ?? 'null' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-gray-400 min-w-175px w-175px">Engine</td>
                                    <td class="text-gray-800 min-w-200px">
                                        {{ form.product?.vehicle?.engine ?? 'null' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-gray-400 min-w-175px w-175px">Fuel Type</td>
                                    <td class="text-gray-800 min-w-200px">
                                        {{ form.product?.vehicle?.fuel_type ?? 'null' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-gray-400 min-w-175px w-175px">Mileage</td>
                                    <td class="text-gray-800 min-w-200px">
                                        {{ form.product?.vehicle?.mileage ?? 'null' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-gray-400 min-w-175px w-175px">Transmission</td>
                                    <td class="text-gray-800 min-w-200px">
                                        {{ form.product?.vehicle?.transmission ?? 'null' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-gray-400 min-w-175px w-175px">Trim</td>
                                    <td class="text-gray-800 min-w-200px">
                                        {{ form.product?.vehicle?.trim ?? 'null' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-gray-400 min-w-175px w-175px">Type</td>
                                    <td class="text-gray-800 min-w-200px">
                                        {{ form.product?.vehicle?.type ?? 'null' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-gray-400 min-w-175px w-175px">Vin</td>
                                    <td class="text-gray-800 min-w-200px">
                                        {{ form.product?.vehicle?.vin ?? 'null' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-gray-400 min-w-175px w-175px">Year</td>
                                    <td class="text-gray-800 min-w-200px">
                                        {{ form.product?.vehicle?.year ?? 'null' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Chart widget 8-->
            </div>
            <div class="col-xxl-6 mb-5 mb-xl-10">
                <!--begin::Chart widget 8-->
                <div class="card card-flush h-xl-100">
                    <!--begin::Header-->
                    <div class="card-header pt-5">
                        <div class="card-title">
                            <h2>Customer Details</h2>
                        </div>
                    </div>
                    <!--end::Header-->

                    <!--begin::Body-->
                    <div class="card-body pt-6">
                        <table class="table fs-6 fw-semibold gs-0 gy-2 gx-2 m-0">
                            <tbody>
                                <tr>
                                    <td class="text-gray-400 min-w-175px w-175px">Name</td>
                                    <td class="text-gray-800 min-w-200px">
                                        {{ form.user.first_name }} {{ form.user.last_name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-gray-400 min-w-175px w-175px">Email</td>
                                    <td class="text-gray-800 min-w-200px">
                                        {{ form.user.email ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-gray-400 min-w-175px w-175px">Phone</td>
                                    <td class="text-gray-800 min-w-200px">
                                        {{ form?.phone ?? '' }}
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
        <div class="d-flex flex-column flex-xl-row gap-7 gap-lg-10">
            <!--begin::Order details-->
            <div class="card card-flush py-6 flex-row-fluid w-50">
                <!--begin::Card header-->
                <div class="card-header">
                    <div class="card-title">
                        <h2>Query Details (#{{ form.id }})</h2>
                    </div>
                </div>
                <!--end::Card header-->

                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table align-middle table-row-bordered mb-0 fs-6 gy-5 min-w-300px">
                            <tbody class="fw-semibold text-gray-600">
                                <tr>
                                    <td class="text-muted">
                                        <div class="d-flex align-items-center">
                                            Subject
                                        </div>
                                    </td>
                                    <td class="fw-bold">{{ removeUnderscores(form.subject) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">
                                        <div class="d-flex align-items-center">
                                            comment
                                        </div>
                                    </td>
                                    <td class="fw-bold">{{ form.comment }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">
                                        <div class="d-flex align-items-center">
                                            Trade In
                                        </div>
                                    </td>
                                    <td class="fw-bold text-capitalize">{{ form.trade_in }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <!--end::Table-->
                    </div>
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Order details-->
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

    props: ['form'],
    data() {
        return {
            contactForm: this.form,
        }
    },
    mounted(){
        console.log(this.form)
    },
    mixins: [Helpers]
}
</script>