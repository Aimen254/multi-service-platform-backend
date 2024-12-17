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
            prefix: 'employment.dashboard.',
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
            } else if (this.type === 'boats') {
                this.filterData = (this.filterFormData.orderBy != null || this.filterFormData.status != null || this.filterFormData.price != null) ? this.filterFormData : null;
            }
            else {
                this.filterData = this.filterFormData
            }

            // clearing time out
            clearTimeout(this.timer);

            this.timer = setTimeout(() => {

                if(this.type == 'posts') {
                        this.$inertia.replace(route(this.submissionUrl(this.type), {
                                keyword: this.keyword,
                                moduleId: this.getSelectedModuleValue(),
                                business_uuid: this.getSelectedBusinessValue(),
                                form: this.filterData,
                            })
                        );
                } else {
                            this.$inertia.replace(route(this.submissionUrl(this.type), {
                            keyword: this.keyword,
                            moduleId: this.getSelectedModuleValue(),
                            business_uuid: this.type == 'business' ? null : this.getSelectedBusinessValue(),
                            uuid: this.type == 'reviews' ? this.product : null,
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
                    url = "employers.index";
                    break;
                case 'review':
                    url = "employers.reviews.index";
                    break;
                case "posts":
                    url = "employers.posts.index";
                    break;
                case 'reviews':
                    url = 'post.reviews.index';
                    break;
            }
            return this.prefix + url;
        }
    },
    mixins: [Helpers]
}
</script>
