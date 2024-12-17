<template>
    <div class="d-flex align-items-center position-relative me-4">
        <span class="svg-icon svg-icon-3 position-absolute ms-3">
            <inline-svg :src="'/images/icons/search.svg'" />
        </span>
        <input type="text" class="form-control form-control-solid w-250px ps-9" placeholder="Type into search..."
            autocomplete="off" v-model="keyword" @keyup="search" />
    </div>
</template>

<script>
import InlineSvg from "vue-inline-svg"
import Helpers from '@/Mixins/Helpers'

export default {
    props: ["business", "parent", "slug", "callType", "searchedKeyword", "filterFormData", "orderType", "product", "module"],
    components: {
        InlineSvg,
    },
    data() {
        return {
            type: this.callType,
            keyword: this.searchedKeyword,
            timer: null,
            filterData: null,
            modulName: this.module
        };
    },
    methods: {
        search() {
            // emitter to get keyword data for filter
            this.emitter.emit('filter-keyword',
                {
                    filterKeyword: this.keyword,
                })
            // emitter ends
            clearTimeout(this.timer);
            this.timer = setTimeout(() => {
                if (this.type == 'business') {
                    this.filterData = (this.filterFormData.orderBy != null || this.filterFormData.status != null || this.filterFormData.price != null) ? this.filterFormData : null;
                    this.$inertia.replace(
                        route(this.submissionUrl(this.type), {
                            keyword: this.keyword,
                            form: this.filterData,
                            moduleId: this.getSelectedModuleValue(),
                        })
                    );
                } else if (this.parent) {
                    this.$inertia.replace(
                        route(this.submissionUrl(this.type), {
                            keyword: this.keyword,
                        })
                    );
                } else if (this.type == 'products') {
                    this.filterData = (this.filterFormData.orderBy != null || this.filterFormData.status != null || this.filterFormData.barMinValue != null || this.filterFormData.barMaxValue != null) ? this.filterFormData : null;
                    this.$inertia.replace(
                        route(this.submissionUrl(this.type), {
                            keyword: this.keyword,
                            form: this.filterData,
                            moduleId: this.getSelectedModuleValue(),
                            business_uuid: this.getSelectedBusinessValue(),
                        })
                    );
                } else if (this.type == 'order') {
                    this.filterData = (this.filterFormData.orderBy != null || this.filterFormData.status != null) ? this.filterFormData : null;
                    this.$inertia.replace(
                        route(this.submissionUrl(this.type), {
                            keyword: this.keyword,
                            form: this.type == 'order' ? this.filterData : null,
                            type: this.type == 'order' ? this.orderType : null,
                            business_uuid: this.getSelectedBusinessValue(),
                            moduleId: this.getSelectedModuleValue()
                        })
                    );
                } else if (this.type == 'additionalEmail' || this.type == 'mailing' || this.type == 'extra_tags') {
                    this.$inertia.replace(
                        route(this.submissionUrl(this.type), {
                            keyword: this.keyword,
                            business_uuid: this.getSelectedBusinessValue(),
                        })
                    );
                } else if (this.type == 'color') {
                    this.$inertia.replace(
                        route(this.submissionUrl(this.type), {
                            keyword: this.keyword,
                            business_uuid: this.getSelectedBusinessValue()
                        })
                    );
                } else if (this.type == 'size') {
                    this.$inertia.replace(
                        route(this.submissionUrl(this.type), {
                            keyword: this.keyword,
                            business_uuid: this.getSelectedBusinessValue()
                        })
                    );
                } else if (this.type == 'review') {
                    this.$inertia.replace(
                        route(this.submissionUrl(this.type), {
                            keyword: this.keyword,
                            business_uuid: this.business.uuid
                        })
                    );
                } else if (this.type == 'variants') {
                    this.$inertia.replace(
                        route(this.submissionUrl(this.type), {
                            keyword: this.keyword,
                            uuid: this.product.uuid
                        })
                    );
                } else if (this.type == 'category_mapper' || this.type == 'tags_mapper') {

                    this.$inertia.replace(
                        route(this.submissionUrl(this.type), {
                            keyword: this.keyword,
                        })
                    );
                } else if (this.type == 'industry_tag') {
                    this.$inertia.replace(
                        route(this.submissionUrl(this.type), {
                            keyword: this.keyword,
                        })
                    );
                } else if (this.type == 'attributes') {
                    this.$inertia.replace(
                        route(this.submissionUrl(this.type), {
                            keyword: this.keyword,
                            module_id: this.getSelectedModuleValue(),
                        })
                    );
                } else if (this.type == 'tags_hierarchies') {
                    this.$inertia.replace(
                        route(this.submissionUrl(this.type), {
                            keyword: this.keyword,
                            module_id: this.getSelectedModuleValue(),
                        })
                    );
                } else if (this.type == 'standard_tag_attribute') {
                    this.$inertia.replace(
                        route(this.submissionUrl(this.type), {
                            slug: this.slug,
                            keyword: this.keyword,
                        })
                    );
                } else if (this.type == 'agent') {
                    this.$inertia.replace(
                        route(this.submissionUrl(this.type), {
                            moduleId: this.getSelectedModuleValue(),
                            business_uuid: this.getSelectedBusinessValue(),
                            keyword: this.keyword,
                        })
                    );
                } else if (this.type == 'agentRequest') {
                    this.$inertia.replace(
                        route(this.submissionUrl(this.type), {
                            moduleId: this.getSelectedModuleValue(),
                            business_uuid: this.getSelectedBusinessValue(),
                            keyword: this.keyword,
                        })
                    );
                } else {
                    this.$inertia.replace(
                        route(this.submissionUrl(this.type), {
                            keyword: this.keyword,
                        })
                    );
                }
            }, 700);
        },
        submissionUrl(type) {
            var url = "";
            switch (type) {
                case "administrator":
                    url = "dashboard.administrators.index";
                    break;
                case "store_owner":
                    url = "dashboard.owners.index";
                    break;
                case "customer":
                    url = "dashboard.customers.index";
                    break;
                case "reporter":
                    url = "dashboard.reporters.index";
                    break;
                case "remote_assistant":
                    url = "dashboard.assistants.index";
                    break;
                case "language":
                    url = "dashboard.settings.languages.index";
                    break;
                case "role":
                    url = "dashboard.settings.roles.index";
                    break;
                case "country":
                    url = "dashboard.settings.countries.index";
                    break;
                case "group":
                    url = "dashboard.driver.groups.index";
                    break;
                case "driver":
                    url = "dashboard.drivers.index";
                    break;
                case "driver_manager":
                    url = "dashboard.driver.managers.index";
                    break;
                case "business":
                    // url = "retail.dashboard.businesses.index";
                    url = this.moduleName + "dashboard.businesses.index"
                    break;
                case "products":
                    url = this.modulName + ".dashboard.business.products.index";
                    break;
                case "additionalEmail":
                    url = "dashboard.business.emails.index";
                    break;
                case "review":
                    url = "dashboard.business.reviews.index";
                    break;
                case "size":
                    url = "dashboard.business.sizes.index";
                    break;
                case "color":
                    url = "dashboard.business.colors.index";
                    break;
                case "mailing":
                    url = "dashboard.business.mailings.index";
                    break;
                case "order":
                    url = this.modulName + ".dashboard.business.order.type.orders.index";
                    break;
                case "variants":
                    url = "dashboard.product.variants.index";
                    break;
                case "news":
                    url = "dashboard.news.index"
                    break;
                case "news_categories":
                    url = "dashboard.categories.index"
                    break;
                case "category_mapper":
                    url = "dashboard.module.mappers.index"
                    break;
                case "industry_tag":
                    url = "dashboard.tag.index"
                    break;
                case "standard_tag_product":
                    url = "dashboard.productTag.index"
                    break;
                case "standard_tag_brand":
                    url = "dashboard.brandTag.index"
                    break;
                case "standard_tag_attribute":
                    url = "dashboard.attributeTag.index"
                    break;
                case "attributes":
                    url = "dashboard.attributes.index"
                    break;
                case "tags_mapper":
                    url = "dashboard.tag-mappers.index"
                    break
                case "tags_hierarchies":
                    url = "dashboard.module.tag-hierarchies.index"
                    break
                case "card":
                    url = "dashboard.subscription.payment-method.index"
                    break
                case "extra_tags":
                    url = "dashboard.business.business-tags.index"
                    break
                case 'agent':
                    url = 'real-estate.dashboard.agents.index'
                    break;
                case 'agentRequest':
                    url = 'real-estate.dashboard.agents.external.requests'
                    break;
                case 'government_staff':
                url = 'real-estate.dashboard.staffs.index'
                break;
            }
            return url;
        },
    },

    mixins: [Helpers]
};
</script>
