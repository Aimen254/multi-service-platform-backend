<template>
    <Head title="Tags" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Tags`" :path="`Tags`" />
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button
                    class="btn btn-sm btn-light-primary"
                    v-if="checkUserPermissions('add_industry_tag')"
                    @click="openModal"
                >
                    <span class="svg-icon svg-icon-2">
                        <inline-svg :src="'/images/icons/arr075.svg'" />
                    </span>
                    Add Tags
                </button>
            </div>
        </template>
        <!--begin::Tables Widget 11-->
        <div :class="widgetClasses" class="card">
            <!--begin::Header-->
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder fs-3 mb-1">
                        <SearchInput
                            :callType="type"
                            :searchedKeyword="searchedKeyword"
                        />
                    </span>
                </h3>
            </div>
            <!--end::Header-->

            <!--begin::Body-->
            <div class="card-body py-3">
                <!--begin::Table container-->
                <div class="table-responsive">
                    <!--begin::Table-->
                    <table
                        class="table align-middle gs-0 gy-4 table-row-dashed"
                    >
                        <!--begin::Table head-->
                        <thead>
                            <tr class="fw-bolder text-muted bg-light">
                                <th class="ps-4 min-w-150px rounded-start">
                                    Name
                                </th>
                                <th class="min-w-150px">Status</th>
                                <th class="min-w-150px">Created At</th>
                                <th class="min-w-50px rounded-end">Action</th>
                            </tr>
                        </thead>
                        <!--end::Table head-->

                        <!--begin::Table body-->
                        <tbody
                            v-if="productTag && productTag.data.length > 0"
                        >
                            <template
                                v-for="product_tag in productTag.data"
                                :key="product_tag.id"
                            >
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="image-input image-input-empty">
                                                    <div
                                                        class="image-input-wrapper w-50px h-50px"
                                                        :style="{ 'background-image': 'url(' + getImage(product_tag.icon, true, 'logo') + ')' }"
                                                    ></div>
                                            </div>
                                            <span
                                                class="text-dark fw-bolder text-hover-primary d-block mb-1 fs-6 text-capitalize ms-4"
                                            >
                                                {{ product_tag.name }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            :class="{
                                                'badge badge-light-success': product_tag.status == 'active',
                                                'badge badge-light-danger':
                                                product_tag.status === 'inactive',
                                            }"
                                            class="text-capitalize"
                                            >
                                            {{ product_tag.status }}
                                        </span>
                                </td>
                                    <td>
                                        <span
                                            class="text-muted fw-bold text-muted d-block text-capitalize fs-7"
                                        >
                                            {{
                                                
                                                formatDate(
                                                    product_tag.created_at
                                                )
                                            }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="d-flex">
                                            <Toggle
                                                v-if="
                                                    checkUserPermissions(
                                                        'edit_standard_tag'
                                                    )
                                                "
                                                :status="
                                                    booleanStatusValue(
                                                        product_tag.status
                                                    )
                                                "
                                                @click.prevent="
                                                    changeStatus(
                                                        product_tag.id
                                                    )
                                                "
                                            />
                                            <edit-section
                                                iconType="modal"
                                                permission="edit_standard_tag"
                                                @click="openModal(product_tag)"
                                            />
                                            <delete-section
                                                permission="delete_standard_tag"
                                                :url="
                                                    route(
                                                        'dashboard.tag.destroy',
                                                        [
                                                            product_tag.id,
                                                        ]
                                                    )
                                                "
                                            />
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
                    <pagination
                        :meta="productTag"
                        :keyword="searchedKeyword"
                    />
                </div>
                <!--end::Table container-->
            </div>
            <!--begin::Body-->
        </div>
    </AuthenticatedLayout>
    <IndustryTagModal :allTags="orphanTags"></IndustryTagModal>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import Header from "@/Components/Header.vue";
import Pagination from "@/Components/Pagination.vue";
import Helpers from "@/Mixins/Helpers";
import IndustryTagModal from "./IndustryTagModal.vue";
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
        IndustryTagModal,
    },

    props: ["productTags", "mediaIconSizes", "searchedKeyword", "orphanTags"],

    methods: {
        openModal(productTag = null) {
            this.emitter.emit("industry-tag-modal", {
                tag: productTag,
                iconSizes: this.mediaIconSizes,
            });
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
                        this.$inertia.visit(
                            route(
                                "dashboard.tag.status",
                                [
                                    id,
                                ]
                            ),
                            {
                                preserveScroll: false,
                                onSuccess: () => hideWaitDialog(),
                            }
                        );
                    }
                });
        },
    },

    data() {
        return {
            productTag: this.productTags,
            type: "industry_tag",
        };
    },

    watch: {
        productTags: {
            handler(productTags) {
                this.productTag = productTags;
            },
            deep: true,
        },
    },

    mixins: [Helpers],
};
</script>