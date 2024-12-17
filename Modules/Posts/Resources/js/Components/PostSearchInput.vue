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
        "product",
    ],
    components: {
        InlineSvg,
    },
    data() {
        return {
            type: this.callType,
            keyword: this.searchedKeyword,
            timer: null,
            prefix: 'posts.dashboard.',
            filterData: null,
        };
    },
    methods: {
        search() {
            const scrollY = window.scrollY;
            // emitter to get keyword data for filter
            this.emitter.emit('filter-keyword', {
                filterKeyword: this.keyword,
            })
            // emitter ends

            // clearing time out
            clearTimeout(this.timer);

            this.timer = setTimeout(() => {
                this.$inertia.replace(route(this.submissionUrl(this.type), {
                    keyword: this.keyword,
                    moduleId: this.getSelectedModuleValue(),
                    post: this.product
                }),
                                {
                                    onSuccess: () => {
                                        // Restore the scroll position after the search
                                        window.scrollTo(0, scrollY);
                                    }
                                });
            }, 700)
        },
        submissionUrl(type) {
            var url = "";
            switch (type) {
                case "post":
                    url = "posts.index";
                    break;
                case "comments":
                    url = "posts.show";
                    break;
            }
            return this.prefix + url;
        }
    },
    mixins: [Helpers]
}
</script>
