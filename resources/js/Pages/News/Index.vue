<template>
    <Head title="News" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`News`" :path="`News`"></Breadcrumbs>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <Link v-if="checkUserPermissions('add_news')" :href="route('dashboard.news.create')"
                    class="btn btn-sm btn-primary">
                <span class="svg-icon svg-icon-2">
                    <inline-svg :src="'/images/icons/arr075.svg'" />
                </span>
                Add News
                </Link>
            </div>
        </template>
        <!--begin::Tables Widget 11-->
        <div :class="widgetClasses" class="card">
            <!--begin::Header-->
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder fs-3 mb-1">
                        <SearchInput :callType="type" :searchedKeyword="searchedKeyword" />
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
                                <th class="ps-4 min-w-150px rounded-start">Title</th>
                                <th class="min-w-150px">Slug</th>
                                <th class="min-w-150px">Category</th>
                                <th class="min-w-150px">Created At</th>
                                <th class="min-w-150px rounded-end">Action</th>
                            </tr>
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody v-if="news && news.data.length > 0">
                            <template v-for="item in news.data" :key="item.id">
                                <tr>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="image-input image-input-empty">
                                                <div class="image-input-wrapper w-50px h-50px"
                                                    :style="{ 'background-image': 'url(' + getImage(item.image, true, 'news') + ')' }">
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-start flex-column mx-2">
                                                <span class="text-dark fw-bolder text-hover-primary mb-1 fs-6">
                                                    {{ item.title }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block text-capitalize fs-7">
                                            {{ item.slug }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block text-capitalize fs-7">
                                            {{ item.news_category.category_name }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block text-capitalize fs-7">
                                            {{ formatDate(item.created_at) }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="d-flex">
                                            <edit-section iconType="link" permission="edit_news"
                                                :url="route('dashboard.news.edit', item.id)" />
                                            <delete-section permission="delete_news"
                                                :url="route('dashboard.news.destroy', item.id)" 
                                                :currentPage="news.current_page" :currentCount="news.data.length"/>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <!--end::Table body-->
                        <div v-else class="p-4 text-muted">
                            Record Not Found
                        </div>
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Table container-->
            </div>
            <!--begin::Body-->
            <pagination :meta="news" :keyword="searchedKeyword" />
        </div>
        <!--end::Tables Widget 11-->
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import Header from "@/Components/Header.vue";
import Pagination from "@/Components/Pagination.vue";
import Helpers from "@/Mixins/Helpers";
import EditSection from "@/Components/EditSection.vue";
import DeleteSection from "@/Components/DeleteSection.vue";
import Toggle from "@/Components/ToggleButton.vue";
import SearchInput from "@/Components/SearchInput.vue";
import InlineSvg from "vue-inline-svg";
import Breadcrumbs from '@/Components/Breadcrumbs.vue'

export default {
    components: {
        AuthenticatedLayout,
        Head,
        Header,
        Link,
        Pagination,
        EditSection,
        DeleteSection,
        Toggle,
        SearchInput,
        InlineSvg,
        Breadcrumbs,
    },

    props: ["newsList", "searchedKeyword"],

    data() {
        return {
            news: this.newsList,
            type: "news",
        };
    },
    watch: {
        newsList: {
            handler(newsList) {
                this.news = newsList;
            },
            deep: true,
        },
    },
    methods: {
        changeStatus(event, uuid) {
            this.form = this.$inertia.form({
                status: event.target.value
            });
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
                    this.form.post(route('news.dashboard.news.status', [this.getSelectedModuleValue(), uuid]), {
                        preserveScroll: false,
                        onSuccess: () => hideWaitDialog()
                    })
                }
            })
        },

        getTags() {
            let parameters = [this.getSelectedModuleValue()];
            var vm = this;
            setTimeout(() => {
                $(".select").select2({
                    placeholder: "Select tags",
                    dropdownParent: $('#filterMenu'),
                    // ajax request
                    ajax: {
                        url: route("news.dashboard.search.news.tags", parameters),
                        dataType: "json",
                        delay: 250,
                        data: function (params) {
                            return {
                                keyword: params.term, // search query
                                page: params.page,
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data.tags.map(function (item) {
                                    return item
                                }),
                                pagination: {
                                    more: params.page * 50 < data.tags.total,
                                },
                            };
                        },
                        cache: true,
                    },
                    minimumInputLength: 0, // minimum characters required to trigger search
                });

                // //   get selected value
                $(".select").on("select2:select", function (e) {
                    vm.filterForm.tag = e.params.data.id
                });
            }, 0);
        }
    },
    mounted() {
        this.getTags();
    },
    mixins: [Helpers],
};
</script>
