<template>
    <Head title="Broker" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Broker`" :path="`Brokers`" />
        </template>
        <div class="d-flex flex-column flex-lg-row">
            <div
                class="flex-lg-row-fluid me-lg-15 order-2 order-lg-1 mb-10 mb-lg-0"
            >
                <div class="card card-flush pt-3 mb-5 mb-xl-10">
                    <div class="card-header">
                        <div class="card-title">
                            <h2 class="fw-bold">Broker Details</h2>
                        </div>
                        <!-- <div class="card-toolbar"><a href="#/subscriptions/add" class="btn btn-light-primary">Update
                                Product</a></div> -->
                    </div>
                    <div class="card-body pt-3">
                        <div class="mb-10">
                            <div v-if="business.logo != null">
                                <div class="image-input image-input-empty">
                                    <div class="image-input-wrapper w-80px h-80px" :style="{ 'background-image':'url(' + getImage( business.logo.path, true, 'logo' ) + ')',}">
                                    </div>
                                </div>
                            </div>
                            <div v-else>
                                <div class="image-input image-input-empty">
                                    <div class="image-input-wrapper w-50px h-50px" :style="{ 'background-image': 'url(' + getImage(false, true, 'logo') + ')', }" >
                                     </div>
                                </div>
                            </div>
                            <h5 class="mb-4">Brokerage Details:</h5>
                            <div class="d-flex flex-wrap py-5">
                                <div class="flex-equal me-5">
                                    <table class="table fs-6 fw-semobold gs-0 gy-2 gx-2 m-0">
                                        <tr>
                                            <td class="text-gray-400 min-w-175px w-175px">
                                                Broker Name:
                                            </td>
                                            <td class="text-gray-800">
                                                {{ Business.name }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-400">
                                                Broker Name:
                                            </td>
                                            <td class="text-gray-800 min-w-200px">
                                                {{ Business.email }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="text-gray-400">Phone:</td>
                                            <td class="text-gray-800">
                                                {{ Business.phone? Business.phone: "null"}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-400">Address:</td>
                                            <td class="text-gray-800">
                                                {{ Business.address ? Business.address: "null"}}
                                            </td>
                                        </tr>
                                        <tr v-if="Business.status == 'rejected'">
                                            <td class="text-gray-400">Reason for rejection:</td>
                                            <td class="text-gray-800">
                                                {{ Business.reason }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex-column flex-lg-row-auto w-lg-250px w-xl-300px mb-10 order-1 order-lg-2">
                <div class="card card-flush mb-0"
                    id="kt_view_summary"
                    data-kt-sticky="false"
                    data-kt-sticky-name="view-subscription-summary"
                    data-kt-sticky-offset="{default: false, lg: '200px'}"
                    data-kt-sticky-width="{lg: '250px', xl: '300px'}"
                    data-kt-sticky-left="auto"
                    data-kt-sticky-top="150px"
                    data-kt-sticky-animation="false"
                    data-kt-sticky-zindex="95">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Owner</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0 fs-6">
                        <div class="mb-7">
                            <div class="row align-items-center">
                                <div class="image-input image-input-empty col-4">
                                    <div class="image-input-wrapper w-50px h-50px mx-5"
                                        :style="{'background-image':'url(' + getImage(
                                                    BusinessOwner.avatar,
                                                    true,
                                                    'avatar') + ')',}">
                                    </div>
                                </div>
                                <div class="col-8">
                                    <h4>
                                        {{ BusinessOwner.first_name }}
                                        {{ BusinessOwner.last_name }}
                                    </h4>
                                    <p>{{ BusinessOwner.email }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="separator separator-dashed mb-7"></div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Breadcrumbs from "@/Components/Breadcrumbs.vue";
import { Head } from "@inertiajs/inertia-vue3";
import Helpers from "@/Mixins/Helpers";
import moment from "moment";

export default {
    props: [
        "is_admin",
        "business",
        "mediaLogoSizes",
        "mediaThumbnailSizes",
        "businessOwner",
        "mediaBannerSizes",
    ],

    components: {
        AuthenticatedLayout,
        Breadcrumbs,
        Head,
    },

    data() {
        return {
            BusinessOwner: this.businessOwner,
            Business: this.business,
            data: {},
        };
    },

    watch: {
        businessOwner: {
            handler(businessOwner) {
                this.BusinessOwner = businessOwner;
            },
            deep: true,
        },
        business: {
            handler(business) {
                this.Business = business;
            },
            deep: true,
        },
    },

    methods: {},
    mixins: [Helpers],
};
</script>
