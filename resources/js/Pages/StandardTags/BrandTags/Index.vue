<template>
    <Head title="Brand Tags" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Brand Tags`" :path="`Brand Tags`" />
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button
                    class="btn btn-sm btn-light-primary"
                    v-if="checkUserPermissions('add_standard_tag')"
                    @click="openModal"
                >
                    <span class="svg-icon svg-icon-2">
                    <inline-svg :src="'/images/icons/arr075.svg'" />
                    </span>
                    Add Brand Tags
                </button>
            </div>
        </template>
        <!--begin::Tables Widget 11-->
        <div :class="widgetClasses" class="card">
        <!--begin::Header-->
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bolder fs-3 mb-1">
                <SearchInput :callType="type" :searchedKeyword="searchedKeyword"/>
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
                    <th class="ps-4 min-w-350px rounded-start">Name</th>
                    <th class="min-w-350px">Created At</th>
                    <th class="min-w-50px rounded-end">Action</th>
                </tr>
                </thead>
                <!--end::Table head-->

                <!--begin::Table body-->
                <tbody v-if="brandTag && brandTag.data.length > 0 ">
                <template v-for="brand in brandTag.data" :key="brand.id">
                    <tr>
                    <td>
                        <span
                        class="
                            text-dark
                            fw-bolder
                            text-hover-primary
                            d-block
                            mb-1
                            fs-6
                            text-capitalize
                            ms-4
                        "
                        >
                        {{ brand.name }}
                        </span>
                    </td>

                    <td>
                        <span
                        class="
                            text-muted fw-bold text-muted d-block text-capitalize fs-7
                        "
                        >
                        {{ formatDate(brand.created_at) }}
                        </span>
                    </td>

                    <td>
                        <div class="d-flex">
                            <Toggle
                                v-if="checkUserPermissions('edit_standard_tag')"
                                :status="booleanStatusValue(brand.status)"
                                @click.prevent="changeStatus(brand.id)"
                            />
                            <delete-section permission="edit_standard_tag"
                                :url="route('dashboard.brandTag.destroy', brand.id)" />
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
            <pagination :meta="brandTag" :keyword="searchedKeyword" />
            </div>
            <!--end::Table container-->
        </div>
        <!--begin::Body-->
        </div>
    </AuthenticatedLayout>
    <standardTagModal :allTags="standardTags"></standardTagModal>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import Header from "@/Components/Header.vue";
import Pagination from "@/Components/Pagination.vue";
import Helpers from "@/Mixins/Helpers";
import standardTagModal from "./BrandTagModal.vue";
import SearchInput from "@/Components/SearchInput.vue";
import EditSection from "@/Components/EditSection.vue";
import DeleteSection from "@/Components/DeleteSection.vue";
import Toggle from "@/Components/ToggleButton.vue";
import InlineSvg from "vue-inline-svg";
import Breadcrumbs from "@/Components/Breadcrumbs.vue";

export default {
    components: {
        AuthenticatedLayout,
        Head,
        Link,
        Header,
        Pagination,
        SearchInput,
        EditSection,
        DeleteSection,
        Toggle,
        InlineSvg,
        Breadcrumbs,
        standardTagModal
    },

    props: ["standardTags", "brandTags", "searchedKeyword", "orphanTags"],

    methods: {
        openModal() {
            this.emitter.emit("tag-modal");
        },

        changeStatus(id) {
            this.swal
            .fire({
                title: "",
                html: "<h1 class='text-lg text-gray-800 mb-1'>Change Status</h1><p class='text-base'>Are you sure you want to change status?</p>",
                icon: "warning",
                showCancelButton: true,
                cancelButtonText: "No",
                confirmButtonText: "Yes",
                customClass: {
                    confirmButton: "danger",
                },
            })
            .then((result) => {
                if (result.value) {
                    showWaitDialog();
                    this.$inertia.visit(route("dashboard.brandTag.status", [id]) , {
                    preserveScroll: false,
                    onSuccess: () => hideWaitDialog(),
                    });
                }
            });
        },
    },

    data() {
        return {
            brandTag: this.brandTags,
            type: "standard_tag_brand",
        };
    },

    watch: {
        brandTags: {
            handler(brandTags) {
                this.brandTag = brandTags;
            },
            deep: true,
        },
    },
    mixins: [Helpers],
};
</script>