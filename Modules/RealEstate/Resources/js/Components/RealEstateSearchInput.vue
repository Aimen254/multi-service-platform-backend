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
import InlineSvg from "vue-inline-svg";
import Helpers from '@/Mixins/Helpers';

export default {
    props: [
        "callType",
        "searchedKeyword",
        "filterFormData",
        "year",
        'product'
    ],
    components: {
        InlineSvg,
    },
    data() {
        return {
            type: this.callType,
            keyword: this.searchedKeyword,
            timer: null,
            prefix: 'real-estate.dashboard.',
            filterData: null,
        };
    },
    methods: {
        search() {
            // emitter to get keyword data for filter
            this.emitter.emit('filter-keyword', {
                filterKeyword: this.keyword,
            })
            // emitter ends
            if (this.type === 'garage') {
                this.filterData = (this.filterFormData.orderBy != null || this.filterFormData.status != null || this.filterFormData.price != null) ? this.filterFormData : null;
            } else {
                this.filterData = this.filterFormData
            }

            // clearing time out
            clearTimeout(this.timer);

            this.timer = setTimeout(() => {
                if (this.type == 'properties') {
                    this.$inertia.replace(route(this.submissionUrl(this.type), {
                        keyword: this.keyword,
                        moduleId: this.getSelectedModuleValue(),
                        business_uuid: this.getSelectedBusinessValue(),
                        form: this.filterData,
                        year: this.year
                    })
                    );
                } else {
                    this.$inertia.replace(route(this.submissionUrl(this.type), {
                        keyword: this.keyword,
                        moduleId: this.getSelectedModuleValue(),
                        business_uuid: this.type == 'comments' ? null : this.getSelectedBusinessValue(),
                        uuid: this.type == 'comments' || this.type == 'reviews' ? this.product : null,
                        form: this.filterData,
                    })
                    );
                }
            }, 700)
        },
        submissionUrl(type) {
            var url = "";
            switch (type) {
                case "business":
                    url = "brokers.index";
                    break;
                case "properties":
                    url = "broker.properties.index";
                    break;
                case "review":
                    url = "broker.reviews.index";
                    break;
                case "notices":
                    url = "organization.notices.index";
                    break;
                case 'reviews':
                    url = 'notices.reviews.index';
                    break;
            }
            return this.prefix + url;
        }
    },
    mixins: [Helpers]
}
</script>
