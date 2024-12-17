<template>

    <Head title="Create HeadLine" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Create HeadLine`" :path="`News - HeadLine`" />
        </template>
        <div>
            <div v-if="filterForm">
                <!--begin::Layout-->
                <div class="d-flex flex-column flex-xl-row">
                    <div class="flex-lg-row-fluid">
                        <div class="card mb-5 mb-xl-8">
                            <div class="card-body">
                                <div class="row mb-4 fv-plugins-icon-container">
                                    <div class="col-lg-12 py-2">
                                        <Label for="stock" value="Headline Type" />
                                        <select v-model="filterForm.type" @change="reInitailizeData()"
                                            class="form-select text-capitalize form-select-solid">
                                            <option class="text-capitalize">Primary</option>
                                            <option class="text-capitalize">Secondary</option>
                                        </select>
                                        <!-- <error :message="form.errors.type"></error> -->
                                    </div>
                                    <div class="col-lg-12 py-2" v-if="filterForm.type == 'Secondary'">
                                        <Label for="stock" class="required" value="Level Two Tags" />
                                        <select2 class="form-control-md text-capitalize form-control-solid"
                                            v-model="filterForm.tag" :options="levelTwoTags"
                                            :placeholder="'Select Level Two tag'" />
                                        <!-- <error :message="form.errors.level_two_tag"></error> -->
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="text-end">
                                        <!-- {{ fetchProductProcessing }} -->
                                        <Button :class="{ 'opacity-25': fetchProductProcessing }"
                                            :disabled="fetchProductProcessing" @click="featchProducts()"
                                            ref="submitButton">
                                            <span class="indicator-label" v-if="!fetchProductProcessing">
                                                Fetch Products </span>
                                            <span class="indicator-progress" v-if="fetchProductProcessing">
                                                <span class="spinner-border spinner-border-sm align-middle"></span>
                                            </span>
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Content-->
                </div>
                <!--end::Layout-->
            </div>

            <div :class="widgetClasses" class="card">
                <!--begin::Header-->
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">
                            <NewsSearchInput :callType="'headLineFilter'" :filterFormData="filterForm"
                                :searchedKeyword="searchedKeyword" />
                        </span>
                    </h3>
                </div>
                <!--end::Header-->
                <div v-if="news">
                    <!--begin::Body-->
                    <div class="card-body py-10">
                        <!--begin::Table container-->
                        <div class="table-responsive">
                            <!--begin::Table-->
                            <table class="table align-middle gs-0 gy-4 table-row-dashed">
                                <!--begin::Table head-->
                                <thead>
                                    <tr class="fw-bolder text-muted bg-light">
                                        <th class="ps-4 min-w-150px rounded-start">Title</th>
                                        <th class="min-w-150px">Created At</th>
                                        <th class="min-w-150px rounded-end">Action</th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody v-if="news?.data?.length > 0">
                                    <template v-for="item in news.data" :key="item.id">
                                        <tr>
                                            <td class="px-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="image-input image-input-empty">
                                                        <div class="image-input-wrapper w-50px h-50px"
                                                            :style="{ 'background-image': 'url(' + getImage(item.main_image ? item.main_image.path : null, true, 'product', item.main_image ? item.main_image.is_external : 0) + ')' }">
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
                                                            {{ limit(item?.name) }}
                                                        </span>
                                                        <!-- {{ item }} -->
                                                        <span class="text-muted fw-bold text-muted d-block fs-7">
                                                            <span v-if="item.is_featured" style="margin-right:10px">
                                                                <i class="fas fa-check-circle text-success"></i>
                                                                Featured
                                                            </span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="text-muted fw-bold text-muted d-block text-capitalize fs-7">
                                                    {{ formatDate(item.created_at) }}
                                                </span>
                                            </td>
                                            <td>
                                                <HeadlineAction :class="{ 'opacity-25': processing }"
                                                    @click="!processing ? makeHeadLine(item) : ''" />
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                                <div v-else class="p-4 text-muted text-center">
                                    Record Not Found
                                </div>
                                <!--end::Table body-->
                            </table>
                            <!--end::Table-->
                        </div>
                        <!--end::Table container-->
                    </div>
                    <!--begin::Body-->
                    <pagination :meta="news" :keyword="searchedKeyword" :callType="'headLineFilter'"
                        :selectedFilters="filterForm" />
                </div>
                <div v-else class="p-4 text-muted text-center">
                    Record Not Found
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import { Head, router } from "@inertiajs/inertia-vue3";
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Breadcrumbs from "@/Components/Breadcrumbs.vue";
import Input from '@/Components/Input.vue';
import Label from '@/Components/Label.vue';
import Button from '@/Components/Button.vue';
import Error from '@/Components/InputError.vue';
import RemoveImageButton from '@/Components/RemoveImage.vue';
import EditImage from '@/Components/EditImage.vue';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import Select2 from 'vue3-select2-component';
import Helpers from '@/Mixins/Helpers';
import NewsSearchInput from "../../Components/NewsSearchInput.vue";
import HeadlineAction from "@/Components/HeadlineAction.vue";
import Pagination from "@/Components/Pagination.vue";

export default {
    props: ['levelTwoTags', 'newsList', 'type', 'tag', 'searchedKeyword'],
    components: {
        Head,
        AuthenticatedLayout,
        Breadcrumbs,
        Input,
        Label,
        Button,
        Error,
        RemoveImageButton,
        EditImage,
        QuillEditor,
        Select2,
        NewsSearchInput,
        HeadlineAction,
        Pagination
    },

    data() {
        return {
            form: null,
            news: this.newsList,
            levelTwoTag: this.tag,
            headLineType: this.type,
            filterForm: {
                tag: this.tag ? this.tag : null,
                type: localStorage.getItem('headLineType') ? localStorage.getItem('headLineType') : 'Primary',
            },
            fetchProductProcessing: false,
            processing: false
        }
    },


    methods: {
        featchProducts() {
            localStorage.setItem('headLineType', this.filterForm.type)
            this.fetchProductProcessing = true
            this.$inertia.visit(route('news.dashboard.headlines.create', this.getSelectedModuleValue()), {
                data: {
                    type: this.filterForm.type,
                    tag: this.filterForm.tag
                },
            })
        },

        reInitailizeData() {
            this.levelTwoTag = null
            this.filterForm.tag = null
            this.news = null
        },

        makeHeadLine(product) {
            this.swal.fire({
                title: "",
                html: "<h1 class='text-lg text-gray-800 mb-1'>Make Headline</h1><p class='text-base'>Are you sure you want to make this headline?</p>",
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
                    window.axios.post(route('news.dashboard.headlines.store', this.getSelectedModuleValue()), {
                        type: this.filterForm.type,
                        level_two_tag_id: this.filterForm.tag,
                        product_id: product.id
                    }).then((response) => {
                        this.$notify({
                            group: "toast",
                            type: 'success',
                            text: response.data.message
                        }, 3000)
                        hideWaitDialog()
                        this.featchProducts()
                    }).catch(error => {
                        hideWaitDialog()
                        this.$notify({
                            group: "toast",
                            type: 'error',
                            text: error.response.data.message
                        }, 3000)

                    });
                }
            })
        }
    },

    watch: {
        newsList: {
            handler(newsList) {
                this.news = newsList;
            },
            deep: true,
        },
    },

    mounted() {
        if (this.filterForm.type == 'Secondary' && this.filterForm.tag == null) {
            this.news = null
        }
    },
    mixins: [Helpers]
};
</script>
