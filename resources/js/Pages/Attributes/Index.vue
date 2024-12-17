<template>
    <Head title="Attributes" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Attributes`" :path="`Tags Manager`" />
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button
                    class="btn btn-sm btn-light-primary"
                    v-if="checkUserPermissions('add_attributes')"
                    @click="openModal"
                >
                    <span class="svg-icon svg-icon-2">
                    <inline-svg :src="'/images/icons/arr075.svg'" />
                    </span>
                    Add Attributes
                </button>
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
                    <th class="ps-4 min-w-150px rounded-start">Name</th>
                    <th class="min-w-150px">Module</th>
                    <th class="min-w-150px">Created At</th>
                    <th class="min-w-50px rounded-end">Action</th>
                </tr>
                </thead>
                <!--end::Table head-->

                <!--begin::Table body-->
                <tbody v-if="attributes && attributes.data.length > 0 ">
                <template v-for="attribute in attributes.data" :key="attribute.id">
                    <tr>
                    <td>
                        <span
                        class="
                           
                        "
                        >
                            <Link class=" text-dark
                                        fw-bolder
                                        text-hover-primary
                                        d-block
                                        mb-1
                                        fs-6
                                        text-capitalize
                                        ms-4"
                                    :href="route(`dashboard.attributeTag.index`, [attribute.slug])">
                                {{ attribute.name }}
                            </Link>
                        </span>
                    </td>
                    <td class="ps-0">
                        <div class="d-flex align-items-center">
                            <div class="d-flex justify-content-start flex-column">
                                <span v-if="attribute.module_tags">
                                    <span  class="badge badge-light-primary"
                                        v-for="(tag,index) in attribute.module_tags" :key="index">
                                        {{ tag ? tag.text : 'none' }}
                                    </span>
                                </span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span
                        class="
                            text-muted fw-bold text-muted d-block text-capitalize fs-7
                        "
                        >
                        {{ formatDate(attribute.created_at) }}
                        </span>
                    </td>

                    <td>
                        <div class="d-flex">
                            <Toggle
                                v-if="checkUserPermissions('edit_attributes')"
                                :status="booleanStatusValue(attribute.status)"
                                @click.prevent="changeStatus(attribute.id)"
                            />
                            <edit-section iconType="modal" 
                                permission="edit_attributes" @click="openModal(attribute)"/>
                            <delete-section permission="delete_attributes"
                                :url="route('dashboard.attributes.destroy', [attribute.id])" 
                                :currentPage="attributes.current_page" :currentCount="attributes.data.length"/>
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
            <pagination :meta="attributes" :keyword="searchedKeyword" />
            </div>
            <!--end::Table container-->
        </div>
        <!--begin::Body-->
        </div>
    </AuthenticatedLayout>
    <attributeModal></attributeModal>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import Header from "@/Components/Header.vue";
import Pagination from "@/Components/Pagination.vue";
import Helpers from "@/Mixins/Helpers";
import attributeModal from "./attributeModal.vue";
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
        attributeModal
    },

    props: ["attributes", "modules", "searchedKeyword"],

    methods: {
        openModal(attribute = null) {
            this.emitter.emit("attribute-modal", {
                attribute: attribute,
                moduleTags: this.modules
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
                    this.$inertia.visit(route("dashboard.attributes.status", [id]) , {
                    preserveScroll: false,
                    onSuccess: () => hideWaitDialog(),
                    });
                }
            });
        },
    },

    data() {
        return {
        attributes: this.attributes,
        type: "attributes",
        };
    },

    watch: {
        attributes: {
        handler(attributes) {
            this.attributes = attributes;
        },
        deep: true,
        },
    },
    mixins: [Helpers],
};
</script>