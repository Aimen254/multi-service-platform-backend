<template>

    <Head title="Reviews" />
    <AuthenticatedLayout>

        <template #breadcrumbs>
            <Breadcrumbs :title="`Reviews`" :path="`Departments`" />
        </template>

        <!-- new table -->
        <!--begin::Tables Widget 11-->
        <div :class="widgetClasses" class="card">
            <!--begin::Header-->
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder fs-3 mb-1">
                        <GovernmentSearchInput :business="business" :callType="type" :searchedKeyword="searchedKeyword"/>
                    </span>
                </h3>
            </div>
            <!--end::Header-->
            <!--begin::Body-->
            <div class="card-body py-3">
                <!--begin::Table container-->
                <div class="table-responsive">
                    <!--begin::Table-->
                    <table class="table align-middle gs-0 gy-4 table-row-dashed">
                        <!--begin::Table head-->
                        <thead>
                            <tr class="fw-bolder text-muted bg-light">
                                <th class="ps-4 min-w-120px rounded-start">Name</th>
                                <th class="w-25">Comment</th>
                                <th class="min-w-120px">Rating</th>
                                <th class="min-w-120px">Status</th>
                                <th class="min-w-120px">Created At</th>
                                <th class="min-w-120px rounded-end">Action</th>
                            </tr>
                        </thead>
                        <!--end::Table head-->

                        <!--begin::Table body-->
                        <tbody v-if="reviews && reviews.data.length > 0">
                            <template v-for="review in reviews.data" :key="review.id">
                                <tr>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="d-flex justify-content-start flex-column mx-2">
                                                <span
                                                    class="text-dark fw-bolder text-hover-primary mb-1 fs-6"> {{ review.user.first_name }} {{ review.user.last_name }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="text-muted fw-bold text-muted d-block fs-7"
                                            data-bs-toggle="tooltip" 
                                            data-bs-placement="bottom"
                                            :data-bs-original-title="review.comment"
                                            >{{ ellipsis(review.comment) }}</span>
                                    </td>
                                    <td>
                                        <shapla-star-rating v-model="review.rating" :is-static="true" :active-color="['#ffad0f']" :color="['#ffad0f']"/>
                                    </td>
                                    <td>
                                        <span class="badge fs-7 fw-bold text-capitalize"
                                            :class="{ 'badge-light-success': review.status == 'active', 'badge-light-danger': review.status === 'inactive' }">{{ review.status }}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="text-muted fw-bold text-muted d-block fs-7">{{ formatDate(review.created_at) ? formatDate(review.created_at) : 'Null' }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <Toggle v-if="checkUserPermissions('edit_reviews')"
                                            :status="booleanStatusValue(review.status)"
                                            @click.prevent="changeStatus(review.id)" />
                                            <delete-section
                                            permission="delete_reviews"
                                            :url="route('government.dashboard.department.reviews.destroy', [getSelectedModuleValue(), review.id, business.id])" 
                                            :currentPage="reviews.current_page" :currentCount="reviews.data.length"/>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <div v-else class="p-4 text-muted">
                            Record Not Found
                        </div>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                    <pagination :meta="reviews" :keyword="searchedKeyword"/>
                </div>
                <!--end::Table container-->
            </div>
            <!--begin::Body-->
        </div>
        <!--end::Tables Widget 11-->
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import Header from "@/Components/Header.vue";
import Helpers from "@/Mixins/Helpers";
import Toggle from '@/Components/ToggleButton.vue'
import DeleteSection from "@/Components/DeleteSection.vue";
import Pagination from "@/Components/Pagination.vue";
import InlineSvg from "vue-inline-svg";
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import ShaplaStarRating from '@shapla/vue-star-rating';
import '@shapla/vue-star-rating/dist/style.css';    
import GovernmentSearchInput from "../../../Components/GovernmentSearchInput.vue";

export default {
    components: {
        AuthenticatedLayout,
        Head,
        Header,
        Link,
        Toggle,
        DeleteSection,
        Pagination,
        InlineSvg,
        Breadcrumbs,
        ShaplaStarRating,
        GovernmentSearchInput
    },

    props: [
        "business",
        "reviews",
        "title",
        "searchedKeyword",
    ],
    data() {
        return {
            reviews: this.reviews,
            type: "review",
            disable: false,
        };
    },
    watch: {
        reviews: {
            handler(val) {
                this.reviews = val;
            },
            deep: true,
        },
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

                    this.$inertia.visit(route('government.dashboard.department.review.status', [this.getSelectedModuleValue(), id, this.business.id]), {
                        preserveScroll: false,
                        onSuccess: () => hideWaitDialog()
                    })
                }
            })
        },
    },
    mixins: [Helpers],
};
</script>