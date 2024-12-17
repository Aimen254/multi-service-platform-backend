<template>
    <Head title="Comments" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Comments`" :path="`Services - ${limit(service?.name, 36)}`" />
        </template>
        <div class="d-flex flex-column flex-lg-row">
            <ProductSidebar :product="service" style="width: 25%!important;" />
            <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10 bg-white" style="width: 75%!important;">
                <div class="card card-flush py-4">
                    <div class="card-body py-3 row">
                        <h4>Comments <span v-if="service?.is_commentable === 0"
                                style="font-size: 10px; color: #6c757d;">&#40;Comments are turned off&#41;</span></h4>
                        <h3 class="card-title align-items-start flex-column">
                            <h3 class="card-title align-items-start flex-column">
                                <ServicesSearchInput :module="module" :callType="type" :searchedKeyword="searchedKeyword"
                                    :product="service.uuid" />
                            </h3>
                        </h3>
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
                                                        comment.comment.length > count ? comment.comment.slice(0, count) +
                                                    '...' : comment.comment }}</span>
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
                                                        :url="route('services.dashboard.services.comments.destroy', [getSelectedModuleValue(), service.uuid, comment.id])" 
                                                        :currentPage="comments.current_page" :currentCount="comments.data.length"/>
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
                        <Pagination :meta="comments" :keyword="searchedKeyword" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
    <CommentModal></CommentModal>
</template>

<script>
import { Head } from '@inertiajs/inertia-vue3'
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import ProductSidebar from '../Partials/ProductSideMenu.vue'
import Label from '@/Components/Label.vue'
import Button from '@/Components/Button.vue'
import Helpers from '@/Mixins/Helpers'
import Error from '@/Components/InputError.vue';
import DeleteSection from '@/Components/DeleteSection.vue';
import ServicesSearchInput from '../../../Components/ServicesSearchInput.vue'
import Pagination from '@/Components/Pagination.vue';
import CommentModal from './CommentModal.vue'
export default {
    props: ['commentList', 'service', "searchedKeyword"],

    components: {
        Head,
        AuthenticatedLayout,
        Breadcrumbs,
        ProductSidebar,
        Label,
        Button,
        Error,
        DeleteSection,
        ServicesSearchInput,
        Pagination,
        CommentModal
    },

    data() {
        return {
            width: 370,
            count: 40,
            module: 'services',
            type: 'comments',
            showAll: false,
            service: this.service,
            comments: this.commentList
        }
    },

    methods: {
        openModal(comment = null) {
            this.emitter.emit("comment-modal", {
                comment: comment,
            });
        },
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
    },

    mixins: [Helpers]
}
</script>
