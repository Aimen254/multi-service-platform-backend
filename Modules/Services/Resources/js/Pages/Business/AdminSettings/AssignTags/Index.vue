<template>
    <Head title="Business Tags" />
    <BreezeAuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Business Tags`" :path="`Service Providers - Admin Settings`" />
        </template>
        <div class="d-flex flex-column flex-lg-row">
            <side-menu :deliveryType="this.delivery_type" />
            <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10 bg-white" style="width: 75%!important">
                <div class="mb-5 mb-xl-8">
                    <div class="mt-6 ms-9">
                        <div class="card-title flex-column">
                            <h2 class="d-flex align-items-center text-dark fw-bolder my-1 fs-3">Business tags</h2>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="fv-row mb-10 fs-5 fw-bold ps-4">

                            <div class="col-md-12">
                                <div v-if="industryTagsList.length">
                                    <p class="required px-5">Level Two Tags</p>
                                    <TagComponent placeholder="Level two tags" :uuid="business.uuid"
                                        :assignedTags="industryTags" :autocomplete="industryTagsList"
                                        :onlyFromAutoComplete="true" v-if="industryTags" :tagType="'industry_tags'" />
                                </div>
                            </div>

                            <div class="col-md-12" v-if="industryTags.length > 0">
                                <div>
                                    <p class="required px-5">Level Three tags</p>
                                    <TagComponent placeholder="Level three tags" @fetchSuggestions="fetchSuggestions()"
                                        :uuid="business.uuid" :assignedTags="productTags" type="product"
                                        :autocomplete="this.productTagList" :onlyFromAutoComplete="true" :openOnClick="true"
                                        :level="2" />
                                </div>
                            </div>
                            <div class="d-flex col-md-12 justify-content-end ">
                                <Button type="button" class="btn btn-md btn-primary w-auto me-4 mt-4"
                                    :disabled="processing || industryTags.length == 0 || productTags.length == 0"
                                    :class="{ 'opacity-25': processing }" @click="assignGlobalTags">
                                    <span class="indicator-label" v-if="!processing">
                                        Submit
                                    </span>
                                    <span class="indicator-progress" v-if="processing">
                                        <span class="spinner-border spinner-border-sm align-middle"></span>
                                    </span>
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
import {
    Head
} from "@inertiajs/inertia-vue3";
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import BusinessMenu from "@/Pages/Business/Includes/BusinessMenu.vue";
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import Helpers from '@/Mixins/Helpers'
import InlineSvg from 'vue-inline-svg'
import Input from '@/Components/Input.vue'
import Label from '@/Components/Label.vue'
import Button from '@/Components/Button.vue'
import Error from '@/Components/InputError.vue'
import SideMenu from '../Partials/SideMenu.vue'
import TagComponent from "@/Components/TagComponent.vue";
import { values } from 'lodash';
export default {
    props: ["business", "moduleTagsTagsList", "industryTagsList", "productTagsList", "businessIndustryTags", "levelThreeTags"],

    components: {
        Head,
        BreezeAuthenticatedLayout,
        BusinessMenu,
        SideMenu,
        Breadcrumbs,
        InlineSvg,
        Input,
        Label,
        Button,
        Error,
        TagComponent,
        Button
    },

    data() {
        return {
            businessUuid: null,
            delivery_type: this.business?.delivery_zone?.delivery_type,
            industryTags: this.businessIndustryTags,
            productTags: this.levelThreeTags,
            tags: [],
            disableSubmit: false,
            childTags: [],
            processing: false,
            productTagList: this.productTagsList
        };
    },
    methods: {
        assignGlobalTags(id) {
            this.processing = true
            let tags = this.industryTags
            tags = tags.concat(this.productTags)
            let businessUuid = this.business.uuid;
            this.$inertia.visit(route("services.dashboard.service-provider.assign.standard-tags", [this
                .getSelectedModuleValue(), businessUuid, JSON.stringify(tags)
            ]), {
                preserveScroll: false,
                onSuccess: () => {
                    this.processing = false
                    this.$notify({
                        group: "toast",
                        type: "success",
                        text: 'Store tags synced !',
                    },
                        3000
                    ); // 3s
                },
                onError: (errors) => {
                    this.processing = false
                    this.$notify({
                        group: "toast",
                        type: "error",
                        text: errors.message,
                    },
                        3000
                    ); // 3s
                }
            })
        },
        enableSubmit(tags) {
            setTimeout(() => {
                this.disableSubmit = tags.length > 0 ? true : false
            }, 300)
        },
        removeProductTags(tags) {
            window.axios.post(route('services.dashboard.service-provider.remove-product-tags', [this.getSelectedModuleValue(), this.business.uuid]),
                {
                    filterProductTags: JSON.stringify(tags)
                })
                .then((response) => {
                    const filteredArray = this.productTags.filter(item => !response.data.data.some(obj => obj.id === item.id));
                    this.productTags = filteredArray
                })
                .catch(error => {
                    console.log(error)
                });
        },

        fetchSuggestions() {
            window.axios.get(route('services.dashboard.service-provider.business-level-three-tags', [this.getSelectedModuleValue(), this.business.uuid]),
                {
                    params: {
                        levelTwo: JSON.stringify(this.industryTags)
                    }
                })
                .then((response) => {
                    this.productTagList = response.data.data
                })
                .catch(error => {
                    console.log(error)
                });
        }
    },
    beforeMount() {
    },
    mounted() {
        this.emitter.on('remove_product_tags', (args) => {
            this.removeProductTags(args.industry_tags)
        })
        this.emitter.on('assigned-tags', (args) => {
            this.enableSubmit(args.tags)
        });
    },
    mixins: [Helpers],
}
</script>
