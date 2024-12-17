<template>
    <div class="d-flex align-items-center position-relative me-4">
        <span class="svg-icon svg-icon-3 position-absolute ms-3">
            <inline-svg :src="'/images/icons/search.svg'" />
        </span>
        <input type="text" class="form-control form-control-solid w-250px ps-9"
            placeholder="Type into search..." autocomplete="off" v-model="keyword"
            @keyup="search" />
    </div>
</template>

<script>
    import InlineSvg from "vue-inline-svg";
    import Helpers from '@/Mixins/Helpers';

    export default {
        props: [
            "callType",
            "searchedKeyword",
            "product"
        ],
        components: {
            InlineSvg,
        },
        data() {
            return {
                type: this.callType,
                keyword: this.searchedKeyword,
                timer: null,
                prefix: 'retail.dashboard.'
            };
        },
        methods: {
            search() {
                // emitter to get keyword data for filter
                this.emitter.emit('filter-keyword', {
                    filterKeyword: this.keyword,
                })
                // emitter ends

                // clearing time out
                clearTimeout(this.timer);

                this.timer = setTimeout(() => {
                    if (this.type == 'variants') {
                        this.$inertia.replace(route(this.submissionUrl(this.type), {
                                keyword: this.keyword,
                                moduleId: this.getSelectedModuleValue(),
                                uuid: this.product.uuid
                            })
                        );
                    } else {
                        this.$inertia.replace(route(this.submissionUrl(this.type), {
                                keyword: this.keyword,
                                moduleId: this.getSelectedModuleValue(),
                                business_uuid: this.getSelectedBusinessValue()
                            })
                        );
                    }
                }, 700)
            },
            submissionUrl (type) {
                var url = "";
                switch (type) {
                    case "coupon":
                        url = "business.coupons.index";
                        break;
                    case "additionalEmail":
                        url = "business.emails.index";
                        break;
                    case "mailing":
                        url = "business.mailings.index";
                        break;
                    case "extra_tags":
                        url = "business.business-tags.index";
                        break;
                    case "variants":
                        url = "product.variants.index";
                        break;
                    case "size":
                        url = "business.sizes.index";
                        break;
                    case "color":
                        url = "business.colors.index"
                        break;
                    case "review":
                    url = "business.reviews.index"
                    break;
                }
                return this.prefix + url;
            }
        },
        mixins: [Helpers]
    }
</script>