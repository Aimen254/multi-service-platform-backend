<template>

    <Head title="Extra Tags" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Extra Tags`" :path="`Business`" />
        </template>
        <div class="card">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <RetailSearchInput :callType="type" :searchedKeyword="searchedKeyword"/>
                </h3>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bolder border-0 text-muted bg-light">
                                <th class="ps-4 min-w-120px rounded-start">Name</th>
                                <th class="ps-4 min-w-120px rounded-start">Action</th>
                            </tr>
                        </thead>
                        <tbody v-if="extraTags && extraTags.data.length > 0">
                            <template v-for="tag in extraTags.data" :key="tag.id">
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
            <pagination :meta="extraTags" :keyword="searchedKeyword" />
        </div>
    </AuthenticatedLayout>
    <ExtraTagsMapperModalVue></ExtraTagsMapperModalVue>
</template>

<script>
import { Head } from '@inertiajs/inertia-vue3'
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import SearchInput from '@/Components/SearchInput.vue'
import Pagination from '@/Components/Pagination.vue'
import ExtraTagsMapperModalVue from './ExtraTagsMapperModal.vue'
import Helpers from '@/Mixins/Helpers'
import RetailSearchInput from "../../Components/RetailSearchInput.vue";

export default {
    components: {
        Head,
        AuthenticatedLayout,
        Breadcrumbs,
        SearchInput,
        Pagination,
        ExtraTagsMapperModalVue,
        RetailSearchInput
    },
    props: ['extraTagsList', 'attributesList', 'standardTagsList', 'searchedKeyword'],
    data() {
        return {
            extraTags: null,
            type: 'extra_tags',
        }
    },

    watch: {
        extraTagsList: {
            handler(extraTagsList) {
                this.extraTags = extraTagsList
            },
            deep: true
        }
    },

    methods: {
        openModal(tag) {
            this.emitter.emit("extra_tag-mapper-modal", {
                tag: tag,
                attributes: this.attributesList,
                standardTags: this.standardTagsList,
                modelType: 'mapper_model'
            });
        },

        cloneTag(tag) {
            this.emitter.emit("extra_tag-mapper-modal", {
                tag: tag,
                attributes: this.attributesList,
                modelType: 'clone_model'
            });
        },
    },

    created() {
        this.extraTags = this.extraTagsList;
    },

    mixins: [Helpers]
}
</script>

<style>

</style>