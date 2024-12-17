<template>

    <Head title="News Detail" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`View News`" :path="`News - ${limit(product?.name, 36)}`" />
        </template>
        <div>
            <div class="row gx-5 gx-xl-10">
                <div class="col-xl-12 mb-5 mb-xl-10">
                    <!--begin::Chart widget 8-->
                    <div class="card card-flush h-xl-100">
                        <!--begin::Header-->
                        <div class="card-header pt-5">
                            <div class="card-title">
                                <h2 class="text-center">News Detail</h2>
                            </div>
                        </div>
                        <!--end::Header-->

                        <!--begin::Body-->
                        <div class="card-body pt-6">
                            <div class="row">
                                <div class="col-md-3 pb-3">
                                    <h4>Author:</h4>
                                </div>
                                <div class="col-md-9 pb-3 d-flex align-items-center">
                                    <div class="image-input-wrapper">
                                        <img :src="getImage(product?.user?.avatar, true, 'avatar')" alt="author image"
                                            style="height: 37px !important;width: 37px !important;border: 1px solid rgb(156 163 175);border-radius: 50%;" />
                                    </div>
                                    <div>
                                        <span class="fw-bold" style="margin-left: 10px;">{{ product?.user?.first_name +
                ' ' +
                product?.user?.last_name }}</span>
                                    </div>
                                </div>

                                <div class="col-md-3 pb-3 mt-3">
                                    <h4>Title:</h4>
                                </div>
                                <div class="col-md-9 d-flex mt-3">
                                    <div class="image-input image-input-empty pb-3">
                                        <div class="image-input-wrapper w-150px h-150px"
                                            :style="{ 'background-image': 'url(' + getImage(product?.main_image ? product?.main_image.path : null, true, 'product', product?.main_image ? product?.main_image?.is_external : 0) + ')' }">
                                        </div>
                                    </div>
                                    <span class="text-gray-800 fs-5 d-flex align-items-center px-3">{{
                product?.name }}</span>
                                </div>
                                <div class="col-md-3 mt-3">
                                    <h4>Description:</h4>
                                </div>
                                <div class="col-md-9 mt-3">
                                    <div>
                                        <span class="fw-normal fw-bold text-muted d-block fs-7"
                                            v-html="displayedDescription"></span>
                                        <span v-if="!showAll && needsToggle" style="color: blue; cursor: pointer"
                                            @click="toggleContent">see more</span>
                                        <span v-else-if="showAll" style="color: blue; cursor: pointer"
                                            @click="toggleContent">see less</span>
                                    </div>
                                </div>

                                <div class="col-md-3 mt-3">
                                    <h4>Created By:</h4>
                                </div>
                                <div class="col-md-9 mt-3">
                                    <span class="fw-normal fw-bold text-muted d-block fs-7">{{
                product?.user?.first_name }} {{
                product?.user?.last_name }}</span>
                                </div>
                                <div class="col-md-3 mt-3">
                                    <h4>Created At:</h4>
                                </div>
                                <div class="col-md-9 mt-3">
                                    <span class="fw-normal fw-bold text-muted d-block fs-7">{{
                formatDate(product?.created_at)
                ?? 'N/A' }}</span>
                                </div>

                                <div class="col-md-3 mt-3">
                                    <h4>Published At:</h4>
                                </div>
                                <div class="col-md-9 mt-3 d-flex align-items-center">
                                    <span class="fw-normal fw-bold text-muted d-block fs-7">{{
                formatDate(product?.created_at)
                ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Chart widget 8-->
                </div>

            </div>
            <div class="">
                <!--begin-->
                <div class="card">
                    <div class="card-header border-0 pt-5">
                        <h4>Comments <span v-if="product?.is_commentable === 0"
                                style="font-size: 10px; color: #6c757d;">&#40;Comments are turned off&#41;</span></h4>
                        <h3 class="card-title align-items-start flex-column">
                            <h3 class="card-title align-items-start flex-column">
                                <NewsSearchInput :module="module" :callType="type" :searchedKeyword="searchedKeyword"
                                    :product="product.uuid" />
                            </h3>
                        </h3>
                    </div>
                    <div class="card-body py-3">
                        <div class="table-responsive">
                            <table class="table table-row-dashed align-middle gs-0 gy-4">
                                <thead>
                                    <tr class="fw-bolder border-0 text-muted bg-light">
                                        <th class="ps-4 min-w-120px rounded-start">User</th>
                                        <th class="ps-4 min-w-120px rounded-start">Comments</th>
                                        <th class="ps-4 min-w-120px rounded-start">Created At</th>
                                        <th class="min-w-120px rounded-end">Action</th>
                                    </tr>
                                </thead>

                                <tbody v-if="comments && comments.data.length > 0">
                                    <template v-for="comment in comments.data" :key="comment.id">
                                        <tr>
                                            <td class="px-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="image-input image-input-empty">
                                                        <div class="image-input-wrapper w-50px h-50px"
                                                            :style="{ 'background-image': 'url(' + getImage(comment.user.avatar) + ')' }">
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-start flex-column mx-2">
                                                        <span class="
                                                            text-dark
                                                            fw-bolder
                                                            text-hover-primary
                                                            mb-1
                                                            fs-6
                                                        ">
                                                            {{ comment.user.first_name }} {{ comment.user.last_name }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted fw-bold d-block fs-7 text-capitalize">
                                                    <span class="fw-normal text-muted fs-7">{{ comment.comment &&
                comment.comment.length > count ? comment.comment.slice(0, count)
            + '...' : comment.comment }}</span>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-muted fw-bold d-block fs-7 text-capitalize">
                                                    <span class="fw-normal text-muted fs-7"></span>
                                                    {{ formatDate(comment.created_at) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <button permission="view_comment"
                                                        class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"
                                                        @click="openModal(comment)" data-bs-toggle="tooltip"
                                                        data-bs-trigger="hover" data-bs-dismiss="click"
                                                        data-bs-placement="bottom" data-bs-original-title="view"><i
                                                            class="fas fa-eye"></i></button>
                                                    <delete-section permission="delete_comment"
                                                        :url="route('news.dashboard.news.comment.destroy',[getSelectedModuleValue(),product.uuid, comment.id])" />
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
                    <pagination :meta="comments" :searchedKeyword="searchedKeyword" />
                </div>
                <!--end-->
            </div>
        </div>
    </AuthenticatedLayout>
    <CommentModal></CommentModal>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import CommentModal from '../Comments/CommentModal.vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
import Pagination from '@/Components/Pagination.vue';
import DeleteSection from '@/Components/DeleteSection.vue';
import ViewSection from '@/Components/ViewSection.vue';
import NewsSearchInput from "../../Components/NewsSearchInput.vue";
import Helpers from '@/Mixins/Helpers';

export default {
    components: {
        AuthenticatedLayout,
        Head,
        Link,
        Breadcrumbs,
        Pagination,
        DeleteSection,
        ViewSection,
        CommentModal,
        NewsSearchInput,
    },

    props: ['product', 'commentList', 'searchedKeyword'],
    data() {
        return {
            count: 40,
            module: 'news',
            type: 'comments',
            showAll: false,
            product: this.product,
            comments: this.commentList
        }
    },
    methods: {
        openModal(comment = null) {
            this.emitter.emit("comment-modal", {
                comment: comment,
            });
        },
        toggleContent() {
            this.showAll = !this.showAll
        },
    },
    computed: {
        truncatedDescription: function truncatedDescription() {
            var maxLength = 700;
            var descriptionLength = this.product?.description?.length;
            if (this.showAll || descriptionLength <= maxLength) {
                return this.product.description;
            } else {
                var lastWordIndex = this.product.description?.lastIndexOf(' ', maxLength);
                var truncatedText = this.product.description?.substring(0, lastWordIndex);
                return "".concat(truncatedText, " .....");
            }
        },
        displayedDescription() {
            return this.showAll ? this.product.description : this.truncatedDescription;
        },
        needsToggle() {
            return this.product?.description?.length > 700;
        }
    },
    watch: {
        commentList: {
            handler(commentList) {
                this.comments = commentList
            },
            deep: true
        },

    },
    mounted() {
        //
    },
    mixins: [Helpers]
}
</script>
