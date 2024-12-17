<template>

    <Head title="Marketplace" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Items`" :path="`Marketplace`"></Breadcrumbs>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <div class="me-4">
                    <!--begin::Menu-->
                    <a href="#" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder"
                        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                        <span class="svg-icon svg-icon-5 svg-icon-gray-500 me-1">
                            <inline-svg :src="'/images/icons/filter.svg'" />
                        </span>
                        Filter
                    </a>
                    <Filter @removeSelectedTags="getTags()" :filterData="filterForm" :callType="type"
                        :url="urlForFilter" :maxPrice="maxPrice" :minPrice="minPrice" :newKeyword="searchedKeyword">
                        <div class="mb-5">
                            <label class="form-label fw-bold">Order By:</label>
                            <div>
                                <select name="name" id="name"
                                    class="form-select form-select-solid text-muted form-select-sm"
                                    v-model="filterForm.orderBy">
                                    <option value="desc" selected>Descending</option>
                                    <option value="asc">Ascending</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-5">
                            <label class="form-label fw-bold">Status:</label>
                            <div>
                                <select v-model="filterForm.status" aria-placeholder="Select"
                                    class="form-select form-select-solid text-muted form-select-sm">
                                    <option class="fw-bold" value="" disabled hidden>Select Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="tags_error">Tags Error</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-5">
                            <label class="form-label fw-bold">Tag:</label>
                            <div>
                                <select class="select form-control-md text-capitalize form-control-solid"
                                    v-model="filterForm.tag"></select>
                            </div>
                        </div>
                        <div class="mb-5" v-if="minPrice != maxPrice">
                            <label class="form-label fw-bold">Price:</label>
                            <div class="mx-2">
                                <VueSlider @change="UpdateValues" v-model="value" :min="0" :max="max" tooltip="always"
                                    style="padding: 10px 0px" />
                            </div>
                        </div>
                    </Filter>
                    <!--end::Menu-->
                </div>
            </div>
        </template>
        <!--begin::Tables Widget 11-->
        <div :class="widgetClasses" class="card">
            <!--begin::Header-->
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder fs-3 mb-1">
                        <ClassifiedSearchInput :callType="type" :searchedKeyword="searchedKeyword" />
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
                                <th class="ps-4 min-w-120px rounded-start">Price</th>
                                <th class="ps-4 min-w-150px rounded-start">Total Comments</th>
                                <th class="min-w-150px">Created At</th>
                                <th class="min-w-150px">Status</th>
                                <th class="min-w-150px rounded-end">Action</th>
                            </tr>
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody v-if="classifieds?.data?.length > 0">
                            <template v-for="item in classifieds.data" :key="item.id">
                                <tr>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="image-input image-input-empty">
                                                <div class="image-input-wrapper w-50px h-50px"
                                                    :style="{ 'background-image': 'url(' + getImage(item?.main_image?.path ?? null, true, 'product', item?.main_image?.is_external ?? 0) + ')' }">
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
                                        <span class="text-muted fw-bold text-muted d-block fs-7 text-capitalize">
                                            <span class="fw-normal text-muted fs-7">$</span>{{ item?.price }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block text-capitalize fs-7">{{
                item?.comments_count }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block text-capitalize fs-7">
                                            {{ formatDate(item?.created_at) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            :class="{ 'badge badge-light-success': item.status == 'active', 'badge badge-light-danger': item.status == 'inactive' || item.status == 'tags_error' }"
                                            class="text-capitalize">
                                            {{ getGroupName(item.status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <Toggle v-if="checkUserPermissions('edit_products')"
                                                :status="booleanStatusValue(item.status)"
                                                @click.prevent="changeStatus(item.uuid)" />
                                            <view-section permission="view_products"
                                                :url="route('classifieds.dashboard.classifieds.show', [getSelectedModuleValue(), item.uuid])" />
                                            <edit-section iconType="link" permission="edit_products"
                                                :url="route('classifieds.dashboard.classifieds.edit', [getSelectedModuleValue(), item.uuid])" />
                                            <delete-section permission="delete_products"
                                                :url="route('classifieds.dashboard.classifieds.destroy', [getSelectedModuleValue(), item.uuid])" />
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
            <pagination :meta="classifieds" :keyword="searchedKeyword" :selectedFilters="filterForm" :callType="type" />
        </div>
        <!--end::Tables Widget 11-->
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import Filter from "@/Components/Filter.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import Header from "@/Components/Header.vue";
import Pagination from "@/Components/Pagination.vue";
import Helpers from "@/Mixins/Helpers";
import EditSection from "@/Components/EditSection.vue";
import DeleteSection from "@/Components/DeleteSection.vue";
import ViewSection from "@/Components/ViewSection.vue";
import Toggle from "@/Components/ToggleButton.vue";
import ClassifiedSearchInput from "../../Components/ClassifiedSearchInput.vue";
import InlineSvg from "vue-inline-svg";
import VueSlider from 'vue-3-slider-component'

export default {
    components: {
        AuthenticatedLayout,
        Head,
        Header,
        Filter,
        Link,
        Pagination,
        EditSection,
        DeleteSection,
        ViewSection,
        InlineSvg,
        Breadcrumbs,
        Toggle,
        ClassifiedSearchInput,
        VueSlider
    },

    props: ["classifiedsList", "searchedKeyword", "orderBy", "status", 'maxPrice', 'minPrice', 'barValueMin', 'barValueMax', "tag"],

    data() {
        return {
            classifieds: this.classifiedsList,
            type: "classifieds",
            module: 'classifieds',
            urlForFilter: 'classifieds.dashboard.classifieds.index',
            filterForm: {
                orderBy: this.orderBy ? this.orderBy : 'desc',
                status: this.status ? this.status : '',
                barMinValue: this.barValueMin ? parseFloat(this.barValueMin) : this.minPrice,
                barMaxValue: this.barValueMax ? parseFloat(this.barValueMax) : this.maxPrice,
                tag: this.tag ? this.tag : null
            },
            value: [this.barValueMin ? parseFloat(this.barValueMin) : this.minPrice,
            this.barValueMax ? parseFloat(this.barValueMax) : this.maxPrice],
            max: this.maxPrice + 10,
        };
    },
    watch: {
        classifiedsList: {
            handler(classifiedsList) {
                this.classifieds = classifiedsList;
            },
            deep: true,
        },
    },
    methods: {
        UpdateValues(e) {
            this.filterForm.barMinValue = e[0];
            this.filterForm.barMaxValue = e[1];
        },

        changeStatus(uuid) {
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

                    this.$inertia.visit(route('classifieds.dashboard.classifieds.status', [this.getSelectedModuleValue(), uuid]), {
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
                        url: route("classifieds.dashboard.search.classifieds.tags", parameters),
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

                // get selected value
                $(".select").on("select2:select", function (e) {
                    vm.filterForm.tag = e.params.data.id
                });
            }, 0);
        },
        removeSelectedTags() {
            this.getTags()
        }
    },
    mounted() {
        this.getTags();
    },
    mixins: [Helpers],
};
</script>
