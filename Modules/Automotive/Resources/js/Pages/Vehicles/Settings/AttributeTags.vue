<template>
    <Head title="Attribute tags" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Attribute tags`" :path="`Products - ${product?.name}`" />
        </template>
        <div class="d-flex flex-column flex-lg-row">
            <ProductSidebar :product="product" style="width: 25%!important;"/>
            <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10 bg-white" style="width: 75%!important;">
                <form class="form" v-if="form" @submit.prevent="submit">
                    <div class="card card-flush py-4">
                        <div class="card-body py-3 row">
                            <div v-if="attributes.length > 0">
                                <div class="fv-row mb-4 fv-plugins-icon-container" v-for="attribute in attributes" :key="attribute.id">
                                    <Label :value="attribute.name" class="text-capitalize" />
                                    <TagComponent @fetchSuggestions="fetchSuggestions" class="text-capitalize" :placeholder="`${attribute.name} tags`"
                                        :assignedTags="assignedTags"
                                        type="attribute"
                                        :attribute_id="attribute.id"
                                        :autocomplete="attributesTags"
                                        :onlyFromAutoComplete="true"
                                        :productUuid="product.uuid"
                                        :openOnClick="true"/>
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
                            <div v-else class="p-4 text-muted">
                                Record Not Found
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
    props: ['product', 'assignedTag', 'allAttributeTags', 'attributes'],

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
            attributesTags: [],
            assignedTags: this.assignedTag,
            removedTags: []
        }
    },

    methods: {
        submit () {
            this.form.tags = JSON.stringify(this.assignedTags);
            this.form.removedTags = JSON.stringify(this.removedTags);
            let productUuid = this.product.uuid;
            this.form.get(route("automotive.dashboard.vehicle.attribute-tags.assign", [this
                .getSelectedModuleValue(), productUuid
            ]));
        },

        fetchSuggestions(event) {
            let productUuid = this.product.uuid;
            window.axios
            .get(
                route("automotive.dashboard.vehicle.search.attribute.tags", [
                    this.getSelectedModuleValue(), productUuid
                ]), {
                    params: {
                        'attribute_id': event,
                    }
                }
                )
                .then((response) => {
                    var uniq = {}
                    this.attributesTags = []
                    this.attributesTags = response.data.filter(obj => !uniq[obj.text.toLowerCase()] && (uniq[obj.text.toLowerCase()] = true));
                });
        }
    },
    mounted() {
        this.emitter.on('removedTag', (args) => {
            if(args.tag.pivot) {
                this.removedTags.push(args.tag)
            }
        })
        // var uniq = {}
        // var arrFiltered = this.allAttributeTags.filter(obj => !uniq[obj.text.toLowerCase()] && (uniq[obj.text.toLowerCase()] = true));
        // this.attributesTags = arrFiltered
        // var uniqu = {}
        // var assignedTagFilter = this.assignedTags.filter(obj => !uniqu[obj.slug.toLowerCase()] && (uniqu[obj.slug.toLowerCase()] = true));
        // this.assignedTags = assignedTagFilter
        this.form = this.$inertia.form({
            tags: [],
            removedTags: []
        });
    },

    mixins: [Helpers]
}
</script>
