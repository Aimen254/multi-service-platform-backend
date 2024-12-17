<template>
    <Head title="Attribute Tags" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="attributeTypes.name" sub_path="Attributes" path="Tags Manager" />
        </template>
        <!--begin::Tables Widget 11-->
        <div class="card">
        <!--begin::Header-->
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bolder fs-3 mb-1">
                <SearchInput :callType="type" :searchedKeyword="searchedKeyword" :slug="attributeTypes.slug"/>
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
                    <th class="min-w-150px">Created At</th>
                    <th class="min-w-50px" v-if="attributeTypes.manual_position">Position</th>
                    <th class="min-w-50px rounded-end">Action</th>
                </tr>
                </thead>
                <!--end::Table head-->

                <!--begin::Table body-->
                <tbody v-if="attributeTag && attributeTag.data.length > 0 ">
                <template v-for="attribute in attributeTag.data" :key="attribute.id">
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
                        {{ attribute.text }}
                        </span>
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
                    <td v-if="attributeTypes.manual_position">
                        <span class="text-muted fw-bold text-muted d-block text-capitalize fs-7">
                            <select class="form-select text-capitalize form-select-solid" @change="setPosition($event, attribute.id)" v-model="attribute.attribute_position[0].pivot.position">
                                <option :value="null" selected>Default</option>
                                <option class="text-capitalize" v-for="n in attributeTagCount" :key="n" :value="n">{{ n }}</option>
                            </select>
                        </span>
                    </td>

                    <td>
                        <div class="d-flex">
                            <Toggle
                                v-if="checkUserPermissions('edit_standard_tag')"
                                :status="booleanStatusValue(attribute.status)"
                                @click.prevent="changeStatus(attribute.id)"
                            />
                            <delete-section permission="delete_standard_tag"
                                :url="route('dashboard.attributeTag.destroy', [attributeTypes.slug, attribute.id])" />
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
            <pagination :meta="attributeTag" :keyword="searchedKeyword" />
            </div>
            <!--end::Table container-->
        </div>
        <!--begin::Body-->
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import Header from "@/Components/Header.vue";
import Pagination from "@/Components/Pagination.vue";
import Helpers from "@/Mixins/Helpers";
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
    },

    props: ["attributeTag", "searchedKeyword", "tags", "attributeTypes", "attributeTagCount"],

    methods: {
        openModal(tagData = null) {
            this.emitter.emit("tag-modal", {
                tag: tagData,
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
                    this.$inertia.visit(route("dashboard.attributeTag.status", [id]) , {
                    preserveScroll: false,
                    onSuccess: () => hideWaitDialog(),
                    });
                }
            });
        },
        setPosition(event, attributeId) {
            this.$inertia.post(route('dashboard.attributeTag.position'), {
                'position': event.target.value,
                'attribute_tag_id': attributeId,
                'attribute_type': this.attributeTypes.id,
            }, {
                onSuccess: () => {},
                onError: error => {console.log(error)}
            })
        }
    },

    data() {
        return {
            standardTag: this.standardTags,
            type: "standard_tag_attribute",
        };
    },
    watch: {
        standardTags: {
        handler(standardTags) {
            this.standardTag = standardTags;
        },
        deep: true,
        },
    },
    mounted(){
        console.log(this.attributeTag);
    },
    mixins: [Helpers],
};
</script>