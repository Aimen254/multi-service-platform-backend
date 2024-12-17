<template>

    <Head title="Tags Mapper" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Orphan Tags Mapper`" :path="`Tags`" />
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button class="btn btn-sm btn-light-primary" v-if="checkUserPermissions('edit_tags_mapper')"
                    @click="markedExtra()">
                    <span class="svg-icon svg-icon-2">
                        <inline-svg :src="'/images/icons/arr075.svg'" />
                    </span>
                    Store Tags
                </button>
            </div>
        </template>
        <div class="card">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <SearchInput :callType="type" :searchedKeyword="searchedKeyword" />
                </h3>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bolder border-0 text-muted bg-light">
                                <th class="ps-4 min-w-120px rounded-start">Name</th>
                                <th class="">Type</th>
                                <th class="min-w-120px rounded-end">Action</th>
                            </tr>
                        </thead>
                        <tbody v-if="orphanTags && orphanTags.data.length > 0">
                            <template v-for="tag in orphanTags.data" :key="tag.id">
                                <tr>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="d-flex justify-content-start flex-column mx-2">
                                                <span
                                                    class="text-dark fw-bolder text-hover-primary text-capitalize mb-1 fs-6">{{
                                                        tag.name
                                                    }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-muted fw-bolder text-capitalize">
                                            {{ tag.type ? tag.type : '-' }}
                                        </div>
                                        <span class="text-muted">{{ tag.attribute ? tag.attribute.name : '' }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <div @click="cloneTag(tag)" v-if="checkUserPermissions('edit_tags_mapper')"
                                                class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"
                                                data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss="click"
                                                data-bs-placement="bottom"
                                                data-bs-original-title="Mark as standard tag">
                                                <i class="icon-xl la la-clone" style="font-size:1.5rem"></i>
                                            </div>
                                            <div @click="openModal(tag)" v-if="checkUserPermissions('edit_tags_mapper')"
                                                class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"
                                                data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss="click"
                                                data-bs-placement="bottom" data-bs-original-title="Map">
                                                <i class="icon-xl la la-code-fork" style="font-size:1.5rem"></i>
                                            </div>
                                            <!-- <div @click="hideTag(tag.id)"
                                                v-if="checkUserPermissions('edit_tags_mapper')"
                                                class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"
                                                data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss="click" data-bs-placement="bottom" data-bs-original-title="Hide">
                                                <i class="icon-xl la la-eye-slash  " style="font-size:1.5rem"></i>
                                            </div> -->
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <div v-else class="p-4 text-muted">
                            Record Not Found
                        </div>
                    </table>
                </div>
            </div>
            <pagination :meta="orphanTags" :keyword="searchedKeyword" />
        </div>
    </AuthenticatedLayout>
    <tag-mapper-modal></tag-mapper-modal>
    <!-- <tag-cloning-modal></tag-cloning-modal> -->
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import { Head } from '@inertiajs/inertia-vue3'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import SearchInput from '@/Components/SearchInput.vue'
import Pagination from '@/Components/Pagination.vue'
import Helpers from '@/Mixins/Helpers'
import TagMapperModal from './TagMapperModal.vue'
// import TagCloningModal from './TagCloningModal.vue' 
export default {
    components: {
        AuthenticatedLayout,
        Breadcrumbs,
        Head,
        SearchInput,
        Pagination,
        TagMapperModal,
        // TagCloningModal
    },

    props: ['orphanTagList', 'attributesList', 'standardTagsList', 'searchedKeyword'],

    data() {
        return {
            orphanTags: null,
            type: 'tags_mapper',
        }
    },

    created() {
        this.orphanTags = this.orphanTagList;
    },

    watch: {
        orphanTagList: {
            handler(orphanTagList) {
                this.orphanTags = orphanTagList
            },
            deep: true
        }
    },

    methods: {
        openModal(tag) {
            this.emitter.emit("tag-mapper-modal", {
                tag: tag,
                attributes: this.attributesList,
                standardTags: this.standardTagsList,
                modelType: 'mapper_model'
            });
        },

        cloneTag(tag) {
            this.emitter.emit("tag-mapper-modal", {
                tag: tag,
                attributes: this.attributesList,
                modelType: 'clone_model'
            });
        },

        markedExtra() {
            this.swal.fire({
                title: "",
                html: "<h1 class='text-lg text-gray-800 mb-1'>Extra Tags</h1><p class='text-base'>Are you sure you want to make extra tags?</p>",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: 'No',
                confirmButtonText: "Yes",
                customClass: {
                    confirmButton: 'danger',
                }
            }).then((result) => {
                if (result.value) {
                    showWaitDialog()
                    this.$inertia.visit(route('dashboard.tag-mappers.extra-tag'), {
                        preserveScroll: true,
                        onSuccess: () => hideWaitDialog()
                    })
                }
            })
        }

    },

    mixins: [Helpers]
}
</script>