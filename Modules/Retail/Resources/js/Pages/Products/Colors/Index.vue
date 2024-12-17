<template>
    <Head title="Colors" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Colors`" :path="`Products`" />
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button v-if="checkUserPermissions('add_product_colors')" @click="openModal()"
                    class="btn btn-sm btn-primary">
                    <span class="svg-icon svg-icon-2">
                        <inline-svg :src="'/images/icons/add.svg'"/>
                    </span>
                    Add Color
                </button>
            </div>
        </template>
        <div class="card">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <RetailSearchInput :callType="type" :searchedKeyword="searchedKeyword" />
                </h3>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bolder border-0 text-muted bg-light">
                                <th class="ps-4 min-w-120px rounded-start">Title</th>
                                <th class="min-w-120px">Created At</th>
                                <th class="min-w-120px rounded-end">Action</th>
                            </tr>
                        </thead>
                        <tbody v-if="colors && colors.data.length > 0">
                            <template v-for="color in colors.data" :key="color.id">
                                <tr>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="d-flex justify-content-start flex-column mx-2">
                                                <span
                                                    class="text-dark fw-bolder text-hover-primary mb-1 fs-6"
                                                    data-bs-toggle="tooltip" 
                                                    data-bs-placement="bottom"
                                                    :data-bs-original-title="color.title">
                                                        {{ ellipsis(color.title) }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7">
                                            {{ formatDate(color.created_at) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <edit-section iconType="modal"
                                                permission="edit_product_colors"
                                                @click="openModal(color)" />
                                            <delete-section
                                                permission="delete_product_categories"
                                                :url="route('retail.dashboard.business.colors.destroy', [this.getSelectedModuleValue(), business.uuid, color.id])" 
                                                :currentPage="colors.current_page" :currentCount="colors.data.length"/>
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
            <pagination :meta="colors" :keyword="searchedKeyword"/>
        </div>
    </AuthenticatedLayout>
    <color-modal :business="business" />
</template>

<script>
    import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
    import Breadcrumbs from '@/Components/Breadcrumbs.vue'
    import { Head } from '@inertiajs/inertia-vue3'
    import Helpers from '@/Mixins/Helpers'
    import InlineSvg from 'vue-inline-svg'
    import Toggle from '@/Components/ToggleButton.vue'
    import ColorModal from './ColorModal.vue'
    import RetailSearchInput from "../../Components/RetailSearchInput.vue";
    import Pagination from '@/Components/Pagination.vue'
    import EditSection from "@/Components/EditSection.vue";
    import DeleteSection from "@/Components/DeleteSection.vue";

    export default {
        props: ['business', 'colorsList', 'searchedKeyword'],
        
        components: {
            AuthenticatedLayout,
            Breadcrumbs,
            Head,
            InlineSvg,
            Toggle,
            ColorModal,
            RetailSearchInput,
            Pagination,
            EditSection,
            DeleteSection
        },

        data () {
            return {
                colors: this.colorsList,
                type: 'color'
            }
        },

        watch: {
            colorsList: {
                handler(colorsList) {
                    this.colors = colorsList
                },
                deep: true
            }
        },

        methods: {
            openModal (color = null) {
                this.emitter.emit("color-modal", {
                    color: color
                });
            },
        },

        mixins: [Helpers]
    }
</script>