<template>
    <Head title="Post Detail" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs title="View Post" :path="`Posts - ${limit(post?.name, 36)}`" />
        </template>
        <div>
            <div class="row gx-5 gx-xl-10">
                <div class="col-xl-12 mb-5 mb-xl-10">
                    <!--begin::Chart widget 8-->
                    <div class="card card-flush h-xl-100">
                        <!--begin::Header-->
                        <div class="card-header pt-5">
                            <div class="card-title">
                                <h2>Post Detail</h2>
                            </div>
                        </div>
                        <!--end::Header-->

                        <!--begin::Body-->
                        <div class="card-body pt-6">
                            <div class="row">
                                <div class="col-md-3 pb-3">
                                    <h4>Title:</h4>
                                </div>
                                <div class="col-md-9 d-flex">
                                    <div class="image-input image-input-empty pb-3">
                                        <div class="image-input-wrapper w-150px h-150px"
                                            :style="{ 'background-image': 'url(' + getImage(post.main_image ? post.main_image.path : null, true, 'product', post.main_image ? post.main_image.is_external : 0) + ')' }">
                                        </div>
                                    </div>
                                    <span class="text-gray-800 fs-5 d-flex align-items-center px-3">{{ post.name}}</span>
                                </div>
                                <div class="col-md-3 pb-3">
                                    <h4>Description:</h4>
                                </div>
                                <div class="col-md-9 pb-3">
                                    <div>
                                        <div class="fw-normal fw-bold text-muted d-block fs-7" v-html="displayedDescription"></div>
                                        <span v-if="!showAll && needsToggle" style="color: blue; cursor: pointer" @click="toggleContent">see more</span>
                                        <span v-else-if="showAll" style="color: blue; cursor: pointer" @click="toggleContent">see less</span>
                                    </div>
                                </div>
                                <div class="col-md-3 pb-3">
                                    <h4>Created By:</h4>
                                </div>
                                <div class="col-md-9 pb-3">
                                    <span class="fw-normal fw-bold text-muted d-block fs-7">{{ post.user.first_name}} {{ post.user.last_name}}</span>
                                </div>
                                <div class="col-md-3 pb-3">
                                    <h4>Created At:</h4>
                                </div>
                                <div class="col-md-9 pb-3">
                                    <span class="fw-normal fw-bold text-muted d-block fs-7">{{ formatDate(post.created_at) }}</span>
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
                    <h4>Comments <span v-if="post?.is_commentable === 0" style="font-size: 10px; color: #6c757d;">&#40;Comments are turned off&#41;</span></h4>
                    <h3 class="card-title align-items-start flex-column">
                        <h3 class="card-title align-items-start flex-column">
                    <PostSearchInput :module="module" :callType="type" :searchedKeyword="searchedKeyword" :product="post.uuid"/>
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
                                                        {{comment.user.first_name }} {{ comment.user.last_name }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                        <td>
                                            <span class="fw-normal fw-bold text-muted d-block fs-7 text-capitalize">
                                                {{ comment.comment.length > count ? comment.comment.slice(0, count) + '...' : comment.comment }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted fw-bold d-block fs-7 text-capitalize">
                                                {{ formatDate(comment.created_at) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <button permission="view_comment" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" @click="openModal(comment)"
                                                    data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss="click" data-bs-placement="bottom" data-bs-original-title="view"
                                                    ><i class="fas fa-eye"></i></button>
                                                <delete-section permission="delete_comment"
                                                    :url="route('posts.dashboard.posts.comment.destroy',[getSelectedModuleValue(),post.uuid, comment.id])" />
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <div v-else>
                                Record Not Found
                            </div>
                        </table>
                    </div>
                </div>
            <pagination :meta="comments" :searchedKeyword="searchedKeyword"/>
            </div>
            <!--end-->
            </div>
        </div>
    </AuthenticatedLayout>
    <PostCommentModal></PostCommentModal>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import { Head, Link } from '@inertiajs/inertia-vue3';
import Pagination from '@/Components/Pagination.vue';
import DeleteSection from '@/Components/DeleteSection.vue';
import ViewSection from '@/Components/ViewSection.vue';
import PostSearchInput from "../../Components/PostSearchInput.vue";
import PostCommentModal from '../Comments/PostCommentModal.vue';
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
    PostCommentModal,
    PostSearchInput,
},

    props: ['post', 'searchedKeyword', 'commentList'],
    data() {
        return {
            module: 'posts',
            count: 40,
            type: 'comments',
            post: this.post,
            showAll: false,
            comments: this.commentList,
        }
    },
    computed: {
        truncatedDescription() {
        const maxLength = 700;
        const descriptionLength = this.post.description.length;

        if (this.showAll || descriptionLength <= maxLength) {
            return this.post.description;
        } else {
            const lastWordIndex = this.post.description.lastIndexOf(' ', maxLength);
            const truncatedText = this.post.description.substring(0, lastWordIndex);
            return `${truncatedText} .....`;
        }
        },
        displayedDescription() {
        return this.showAll ? this.post.description : this.truncatedDescription;
        },
        needsToggle() {
        return this.post.description.length > 700;
        }
    },
    methods: {
        openModal(comment = null) {
            this.emitter.emit("comment-modal", {
                comment: comment,
            });
        },
        toggleContent() {
            this.showAll = !this.showAll;
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
    mixins: [Helpers]
}
</script>
