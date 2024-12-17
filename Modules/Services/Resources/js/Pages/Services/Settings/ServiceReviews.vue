<template>
    <Head title="Service Reviews" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Service reviews`" :path="`Service -${product?.name}`" />
        </template>
        <div class="d-flex flex-column flex-lg-row">
            <ProductSidebar :product="product" style="width: 25%!important;" />
            <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10 bg-white" style="width: 75%!important;">
                <div class="card card-flush py-4">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <h3 class="card-title align-items-start flex-column">
                                <ServiceSearchInput :module="module" :callType="type" :searchedKeyword="searchedKeyword"
                                    :product="product.uuid" />
                            </h3>
                        </h3>
                    </div>
                    <div class="card-body py-3 row">
                        <div class="table-responsive">
                            <table class="table table-row-dashed align-middle gs-0 gy-4">
                                <thead>
                                    <tr class="fw-bolder border-0 text-muted bg-light">
                                        <th class="ps-4 min-w-90px rounded-start">User</th>
                                        <th class="ps-4 min-w-90px rounded-start">Ratings</th>
                                        <th class="ps-4 min-w-90px rounded-start">Reviews</th>
                                        <th class="min-w-90px rounded-end">Action</th>
                                    </tr>
                                </thead>

                                <tbody v-if="reviews && reviews.data.length > 0">
                                    <template v-for="review in reviews.data" :key="review.id">
                                        <tr>
                                            <td class="px-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="image-input image-input-empty">
                                                        <div class="image-input-wrapper w-40px h-40px"
                                                            :style="{ 'background-image': 'url(' + getImage(review.user.avatar) + ')' }"
                                                            style="border-radius: 50%;">
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-start flex-column mx-2">
                                                        <span class="
                                                            text-dark
                                                            fw-bolder
                                                            text-hover-primary
                                                            mb-1
                                                            fs-7
                                                        ">
                                                            {{ review.user.first_name }} {{ review.user.last_name }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted fw-bold d-block fs-7 text-capitalize">
                                                    <span class="fw-normal text-muted fs-7">
                                                        <shapla-star-rating v-model="review.rating" :is-static="true"
                                                            :active-color="['#ffad0f']" :color="['#ffad0f']" />
                                                    </span>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-muted fw-bold d-block fs-7 text-capitalize">
                                                    <span class="fw-normal text-muted fs-7"></span>
                                                    {{ limit(review.comment) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <button permission="view_reviews"
                                                        class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"
                                                        @click="openModal(review)" data-bs-toggle="tooltip"
                                                        data-bs-trigger="hover" data-bs-dismiss="click"
                                                        data-bs-placement="bottom" data-bs-original-title="view"><i
                                                            class="fas fa-eye"></i></button>
                                                    <delete-section permission="delete_reviews"
                                                        :url="route('services.dashboard.services.reviews.destroy', [getSelectedModuleValue(), product.uuid, review.id])" 
                                                        :currentPage="reviews.current_page" :currentCount="reviews.data.length"/>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                                <div v-else>
                                    <tr>
                                        <td colspan="3">
                                            Record Not Found
                                        </td>
                                    </tr>
                                </div>
                            </table>
                        </div>
                    </div>
                    <pagination :meta="reviews" :keyword="searchedKeyword" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
    <ReviewModal />
</template>

<script>
import { Head } from '@inertiajs/inertia-vue3'
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import ProductSidebar from '../Partials/ProductSideMenu.vue'
import Label from '@/Components/Label.vue'
import Button from '@/Components/Button.vue'
import ShaplaStarRating from '@shapla/vue-star-rating';
import '@shapla/vue-star-rating/dist/style.css';
import Helpers from '@/Mixins/Helpers'
import Pagination from '@/Components/Pagination.vue';
import DeleteSection from '@/Components/DeleteSection.vue';
import ViewSection from '@/Components/ViewSection.vue';
import ReviewModal from '../Settings/ReviewModal.vue';
import ServiceSearchInput from '../../../Components/ServicesSearchInput.vue';
export default {
    props: ['product', 'reviews', 'searchedKeyword'],

    components: {
        Head,
        AuthenticatedLayout,
        Breadcrumbs,
        ProductSidebar,
        ShaplaStarRating,
        Pagination,
        ViewSection,
        DeleteSection,
        Label,
        ServiceSearchInput,
        Button,
        ReviewModal
    },

    data() {
        return {
            type: 'reviews'
        }
    },

    methods: {
        openModal(review = null) {
            this.emitter.emit("review-modal", {
                review: review,
            });
        },
    },
    mixins: [Helpers]
}
</script>
