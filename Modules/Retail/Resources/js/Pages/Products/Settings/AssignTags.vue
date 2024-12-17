<template>
    <Head title="Assign Tags" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Assign Tags`" :path="`Products - ${product?.name}`" />
        </template>
        <div class="d-flex flex-column flex-lg-row">
            <ProductSidebar :product="product" style="width: 25%!important;" />
            <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10 bg-white" style="width: 75%!important;">
                <form class="form" v-if="form" @submit.prevent="submit">
                    <div class="card card-flush py-4">
                        <div class="card-body py-3 row">
                            <!-- All tags -->
                            <div class="fv-row mb-4 fv-plugins-icon-container">
                                <Label value="Tags" />
                                <TagComponent placeholder="Tags" :uuid="product.uuid" :assignedTags="productTags" :onlyFromAutoComplete="true" :openOnClick="false" :tooltipContent="'Mapped Tags Information.'"/>
                                <error :message="form.errors.tags" />
                            </div>
                            <!-- Extra -->
                            <div class="fv-row mb-4 fv-plugins-icon-container">
                                <Label value="Extra Tags" />
                                <TagComponent placeholder="Extra Tags" :uuid="product.uuid" :assignedTags="extraTag" :autocomplete="filteredAllTags" :onlyFromAutoComplete="false" :openOnClick="false" :tooltipContent="'Unmapped Tags Information.'"/>
                                <error :message="form.errors.tags" />
                            </div>
                            <!-- Brand tags -->
                            <div class="fv-row mb-4 fv-plugins-icon-container">
                                <Label value="Brand tags" />
                                <TagComponent placeholder="Brand tags" :uuid="product.uuid" :assignedTags="allproductBrandTags" type="brand" :autocomplete="allBrandTags" :onlyFromAutoComplete="true" openOnClick="true" :tooltipContent="'Brand Inappropriate Tags.'"/>
                                <error :message="form.errors.tags" />
                            </div>


                            <div class="fv-row mb-4 fv-plugins-icon-container">
                                <Label value="Ignored Tags" />
                                <TagComponent placeholder="Ignored Tags" :uuid="product.uuid" :assignedTags="ignoredTags" :autocomplete="allIgnoredAutocomplete" :onlyFromAutoComplete="false" :openOnClick="true" :tooltipContent="'Ignored Inappropriate Tags.'"/>
                                <error :message="form.errors.tags" />
                            </div>
                            
                            <div class="col-lg-12 py-2 d-flex justify-content-end">
                                <Button type="submit" :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing">
                                    <span class="indicator-label" v-if="!form.processing">Update</span>
                                    <span class="indicator-progress" v-if="form.processing">
                                        <span class="spinner-border spinner-border-sm align-middle"></span>
                                    </span>
                                </Button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import { Head } from '@inertiajs/inertia-vue3'
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import ProductSidebar from '../Partials/ProductSideMenu.vue'
import Label from '@/Components/Label.vue'
import Button from '@/Components/Button.vue'
import Helpers from '@/Mixins/Helpers'
import Error from '@/Components/InputError.vue';
import TagComponent from '@/Components/TagComponent.vue'
export default {
    props: ['product', 'allTags', 'allBrandTags', 'allproductBrandTags', 'allproductTags', 'extraTags', 'allIgnoredAutocomplete', 'productIgnoredTags'],

    components: {
        Head,
        AuthenticatedLayout,
        Breadcrumbs,
        ProductSidebar,
        TagComponent,
        Label,
        Button,
        Error
    },

    data() {
        return {
            form: null,
            width: 370,
            tags: [],
            levelTwoTags: this.levelTwoTag, 
            removedItem: null,
            productTags: this.allproductTags,
            extraTag: this.extraTags,
            filteredAllTags: [],
            removeOrphanTags: [],
            ignoredTags: this.productIgnoredTags
        }
    },

    methods: {
        submit () {
            // let tags  = this.product.tags.concat(this.levelOneTag)
            this.form.tags = JSON.stringify(this.extraTag)
            this.form.categoryTags = JSON.stringify(this.productTags)
            this.form.brandTags = JSON.stringify(this.allproductBrandTags);
            this.form.removeOrphans = this.removeOrphanTags
            this.form.removeStandardTags
            this.form.ignoredTags = JSON.stringify(this.ignoredTags)
            // this.form.extraTags = JSON.stringify(this.extraTags);
            let productUuid = this.product.uuid;
            this.form.get(route("retail.dashboard.product.product-tags.assign", [this
                .getSelectedModuleValue(), productUuid
            ]));
        },
        allTagsFn() {
            this.allTags.filter((tag, index) => {
                tag.priority == "4" ?  this.filteredAllTags.push(tag): null
            });
        }
    },
    mounted() {
        var uniq = {}
        var arrFiltered = this.productTags.filter(obj => !uniq[obj.text.toLowerCase()] && (uniq[obj.text.toLowerCase()] = true));
        this.productTags = arrFiltered
        this.form = this.$inertia.form({
            tags: [],
            brandTags: [],
            extraTags: [],
            categoryTags: [],
            removeOrphans: [],
            removeStandardTags: [],
            ignoredTags: []
        });
        this.emitter.on('removedTag', (args) => { 
            if(args.tag.pivot) {
                if(args.tag.pivot.tag_id && args.tag.priority != 1) {
                    this.removeOrphanTags.push(args.tag.pivot.tag_id)
                } else if (args.tag.pivot.standard_tag_id && args.tag.priority == 4) {
                    this.form.removeStandardTags.push(args.tag.pivot.standard_tag_id)
                }
            }
        })
        this.allTagsFn()
    },

    mixins: [Helpers]
}
</script>