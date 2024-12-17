<template>
    <Head title="Variants" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Variants`" :path="`Products - ${product?.name}`" />
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <div class="dropdown" v-if="(selectAll && selected.length > 0) || selected.length > 0">
                    <button class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder " type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                        Actions
                        <span class="svg-icon svg-icon-5 svg-icon-gray-500 me-1">   
                            <inline-svg :src="'/images/icons/arrowDown.svg'" />
                        </span>
                    </button>
                    <ul class="dropdown-menu p-5" aria-labelledby="dropdownMenuButton1">
                        <li><a class="dropdown-item" @click="onDeleteAll()" href="#">Delete Selected</a></li>
                        <li><a class="dropdown-item" @click="openImageModal()" href="#">Upload Image</a></li>
                    </ul>
                </div>
                <div class="mx-4">
                    <a
                        href="#"
                        class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder"
                        data-kt-menu-trigger="click"
                        data-kt-menu-placement="bottom-end"
                        data-kt-menu-flip="top-end"
                    >
                        Auto Generate
                        <span class="svg-icon svg-icon-5 svg-icon-gray-500 me-1">   
                            <inline-svg :src="'/images/icons/arrowDown.svg'" />
                        </span>
                    </a>
                    <VariantGenerateModal :sizes="sizes" :colors="colors" :product="product"> 
                    </VariantGenerateModal> 
                    <!--end::Menu-->
                </div>
                <button v-if="checkUserPermissions('add_product_variants')" @click="openModal()"
                    class="btn btn-sm btn-primary">
                    <span class="svg-icon svg-icon-2">
                        <inline-svg :src="'/images/icons/add.svg'"/>
                    </span>
                    Add Variant
                </button>

            </div>
        </template>
        <div class="d-flex flex-column flex-lg-row">
            <ProductSidebar :product="product" :width="'w-lg-225px'"/>
            <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10 bg-white">
                <div class="card">
                    <div class="card-header border-0 pt-5 pb-0" style="padding: 1rem 1.25rem">
                        <h3 class="card-title align-items-start flex-column">
                            <RetailSearchInput :callType="type" :searchedKeyword="searchedKeyword" :product="product"/>
                        </h3>
                    </div>
                    <div class="card-body" style="padding: 1rem 1.25rem">
                        <div class="table-responsive">
                            <table class="table table-row-dashed align-middle gs-0 gy-4">
                                <thead>
                                    <tr class="fw-bolder border-0 text-muted bg-light">
                                        <th class="ps-4 min-w-30px rounded-start"><label class="form-check form-check-custom form-check-solid me-5">
                                        <input class="form-check-input h-20px w-20px" type="checkbox" v-model="selectAll" @click="selectAllItems" >
                                        </label></th>
                                        <th class="ps-4 min-w-120px">Variant</th>
                                        <th class="min-w-120px">Price</th>
                                        <th class="min-w-120px text-start">Discount Price</th>
                                        <th class="min-w-120px">Quantity</th>
                                        <th class="min-w-120px rounded-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody v-if="variants && variants.data.length > 0">
                                    <template v-for="variant in variants.data" :key="variant.id">
                                        <tr>
                                            <td class="px-4 w-30">
                                                <label class="form-check form-check-custom form-check-solid me-5"> 
                                                <input class="form-check-input h-20px w-20px" type="checkbox" :value="variant.id" v-model="selected">
                                                </label>
                                            </td>
                                            <td class="px-4 w-50">
                                                <div class="d-flex align-items-center">
                                                    <div class="image-input image-input-empty">
                                                        <div
                                                        class="image-input-wrapper w-50px h-50px"
                                                        :style="{ 'background-image': 'url(' + getImage(variant.image ? variant.image.path : '', true, 'product') + ')' }"
                                                        ></div>
                                                    </div>
                                                    <div class="d-flex justify-content-start flex-column mx-2">
                                                        <span
                                                            class="text-dark fw-bolder text-hover-primary mb-1 fs-6"
                                                            data-bs-toggle="tooltip" 
                                                            data-bs-placement="bottom"
                                                            :data-bs-original-title="variant.title">
                                                                {{ ellipsis(variant.title) }}
                                                        </span>
                                                        <span class="text-muted fw-bold text-muted d-block fs-7">
                                                            <span v-if="variant.size_id" class="text-uppercase">{{ variant.size.title }}</span><span v-if="!variant.size_id && variant.custom_size" class="text-uppercase">{{ variant.custom_size }}</span> <span v-if="variant.size_id && variant.color_id || variant.custom_size && variant.custom_color" > - </span><span class="text-capitalize" v-if="variant.color_id">{{ variant.color.title }}</span><span class="text-capitalize" v-if="!variant.color_id && variant.custom_color">{{ variant.custom_color }}</span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="w-25">
                                                <span class="text-muted fw-bold text-muted d-block fs-7 text-capitalize">
                                                    ${{ variant.price }}
                                                </span>
                                            </td>
                                            <td class="w-25">
                                                <span class="text-muted fw-bold text-muted d-block fs-7 text-capitalize">
                                                    ${{ variant.discount_price ? variant.discount_price : 0 }}
                                                </span>
                                            </td>

                                            <td class="w-25">
                                                <span class="text-muted fw-bold text-muted d-block fs-7 text-capitalize ">
                                                    <input type="number" class="form-control form-control-md form-control-solid w-100" :value="variant.quantity" @change="changeQuantity($event,variant.id)" min="0"> 
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <Toggle v-if="checkUserPermissions('edit_product_variants')"
                                                        :status="booleanStatusValue(variant.status)"
                                                        @click.prevent="changeStatus(variant.id)"
                                                    />
                                                    <edit-section iconType="modal"
                                                        permission="edit_product_variants"
                                                        @click="openModal(variant)" />
                                                    <delete-section
                                                        permission="delete_product_variants"
                                                        :url="route('retail.dashboard.product.variants.destroy', [this.getSelectedModuleValue(), this.product.uuid, variant.id])" 
                                                        :currentPage="variants.current_page" :currentCount="variants.data.length"/>
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
                    <pagination :meta="variants" :callType="type" :keyword="searchedKeyword"/>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
    <variant-modal :product="product" :productImageSizes="productImageSizes" :sizes="sizes" :colors="colors"></variant-modal>
    <multiple-image-modal :product="product" :productImageSizes="productImageSizes" v-on:childToParent="onChildClick"></multiple-image-modal>
</template>

<script>
    import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
    import Breadcrumbs from '@/Components/Breadcrumbs.vue'
    import { Head, Link } from '@inertiajs/inertia-vue3'
    import Helpers from '@/Mixins/Helpers'
    import InlineSvg from 'vue-inline-svg'
    import Toggle from '@/Components/ToggleButton.vue'
    import ProductSidebar from '../Partials/ProductSideMenu.vue'
    import VariantModal from './VariantModal.vue'
    import MultipleImageModal from './MultipleImageModal.vue'
    import VariantGenerateModal from './VariantGenerateModal.vue'
    import EditSection from "@/Components/EditSection.vue"
    import DeleteSection from "@/Components/DeleteSection.vue"
    import Pagination from '@/Components/Pagination.vue';
    import RetailSearchInput from "../../Components/RetailSearchInput.vue";
    export default {
        props: ['product', 'variantsList', 'productImageSizes', 'sizes', 'colors', 'searchedKeyword'],
        components: {
            AuthenticatedLayout,
            Breadcrumbs,
            Head,
            InlineSvg,
            Toggle,
            ProductSidebar,
            VariantModal,
            Link,
            VariantGenerateModal,
            EditSection,
            DeleteSection,
            Pagination,
            MultipleImageModal,
            RetailSearchInput
        },
        data () {
            return {
                variants: this.variantsList,
                type: 'variants',
                selectAll: false,
                selected: [],
            }
        },
        watch: {
            variantsList: {
                handler(variantsList) {
                    this.variants = variantsList
                },
                deep: true
            },
        },
        methods: {
            openModal (variant = null) {
                this.emitter.emit("variant-modal", {
                    variant: variant
                });
            },
            openImageModal () {
                this.emitter.emit("variant-image-modal", {
                    variant: this.selected
                });
            },
            changeStatus (id) {
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
                        this.$inertia.visit(route('retail.dashboard.product.variant.status', [this.getSelectedModuleValue(), this.product.uuid, id]), {
                            preserveScroll: false,
                            onSuccess: () => hideWaitDialog()
                        })
                    }
                })
            },
            changeQuantity($event,id){
                let quantity = $event.target.value;
                window.axios.post(route('retail.dashboard.product.variant.updateQuantity', [this.getSelectedModuleValue(), this.product.uuid, id]) , {
                    variantQuantity: quantity
                })
                .then((response) => {
                    this.$notify({
                        group: "toast",
                        type: response.data.status,
                        text: response.data.message,
                    }, 3000) // 3s
                })
                .catch(error => {
                    this.$notify({
                        group: "toast",
                        type: 'error',
                        text: error.response.data.message
                    }, 3000) // 3s
                });
            },
            selectAllItems(){
                this.selected = []
                if (!this.selectAll) {
                    for (let i in this.variants.data) {
                        this.selected.push(this.variants.data[i].id)
                    }
                }
            },
            onDeleteAll () {
                let ids = JSON.stringify(this.selected)
                this.swal.fire({ 
                    title: "",
                    html: "<h1 class='text-lg text-gray-800 mb-1'>Delete Record</h1><p class='text-base'>Are you sure want to delete these records?</p>",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: "Delete Record",
                    customClass: {
                        confirmButton: 'danger'
                    }
                }).then((result) => {
                    if (result.value) {
                        showWaitDialog()
                        this.$inertia.delete(route('retail.dashboard.product.variant.deleteAll', [this.getSelectedModuleValue(), this.product.uuid, ids]),{
                            preserveScroll: false,
                            onSuccess: () => {
                                hideWaitDialog()
                                this.selectAll = false
                                this.selected = []
                            }
                        })
                    }
                })
            },
            onChildClick () {
                this.selectAll = false
                this.selected = []
            },
        },
        
        mixins: [Helpers]
    }
</script>