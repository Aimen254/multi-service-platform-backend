<template>
    <Head title="Sizes" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Sizes`" :path="`Products`" />
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button v-if="checkUserPermissions('add_product_sizes')" @click="openModal()"
                    class="btn btn-sm btn-primary">
                    <span class="svg-icon svg-icon-2">
                        <inline-svg :src="'/images/icons/add.svg'"/>
                    </span>
                    Add Size
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
                        <tbody v-if="sizes && sizes.data.length > 0">
                            <template v-for="size in sizes.data" :key="size.id">
                                <tr>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="d-flex justify-content-start flex-column mx-2">
                                                <span
                                                    class="text-dark fw-bolder text-hover-primary mb-1 fs-6"
                                                    data-bs-toggle="tooltip" 
                                                    data-bs-placement="bottom"
                                                    :data-bs-original-title="size.title">
                                                        {{ ellipsis(size.title) }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7">
                                            {{ formatDate(size.created_at) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <edit-section iconType="modal"
                                                permission="edit_product_sizes"
                                                @click="openModal(size)" />
                                            <delete-section
                                                permission="delete_product_sizes"
                                                :url="route('retail.dashboard.business.sizes.destroy', [this.getSelectedModuleValue(), business.uuid, size.id])" 
                                                :currentPage="sizes.current_page" :currentCount="sizes.data.length"/>
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
            <pagination :meta="sizes" :keyword="searchedKeyword"/>
        </div>
    </AuthenticatedLayout>
    <size-modal :business="business" />
</template>

<script>
    import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
    import Breadcrumbs from '@/Components/Breadcrumbs.vue'
    import { Head } from '@inertiajs/inertia-vue3'
    import Helpers from '@/Mixins/Helpers'
    import InlineSvg from 'vue-inline-svg'
    import Toggle from '@/Components/ToggleButton.vue'
    import SizeModal from './SizeModal.vue'
    import RetailSearchInput from "../../Components/RetailSearchInput.vue";
    import Pagination from '@/Components/Pagination.vue'
    import EditSection from "@/Components/EditSection.vue";
    import DeleteSection from "@/Components/DeleteSection.vue";

    export default {
        props: ['business', 'sizesList', 'searchedKeyword'],
        
        components: {
            AuthenticatedLayout,
            Breadcrumbs,
            Head,
            InlineSvg,
            Toggle,
            SizeModal,
            RetailSearchInput,
            Pagination,
            EditSection,
            DeleteSection
        },

        data () {
            return {
                sizes: this.sizesList,
                type: 'size'
            }
        },

        watch: {
            sizesList: {
                handler(sizesList) {
                    this.sizes = sizesList
                },
                deep: true
            }
        },

        methods: {
            openModal (size = null) {
                this.emitter.emit("size-modal", {
                    size: size
                });
            },
        },

        mixins: [Helpers]
    }
</script>