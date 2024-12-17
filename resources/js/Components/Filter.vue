<template>
    <div id="filterMenu" class="menu menu-sub menu-sub-dropdown" :class="[type == 'order' ? 'w-50' : 'w-250px w-md-300px']"
        data-kt-menu="true">
        <div style="height: 530px; overflow: auto;">
            <div class="px-7 py-5">
                <div class="fs-5 text-dark fw-bolder">Filter Options</div>
            </div>
            <div class="separator border-gray-200"></div>
            <!-- <form  @submit.prevent="filter()">  -->
            <div class="px-7 py-5">
                <!--Slot-->
                <slot />
                <div class="d-flex justify-content-end">
                    <button type="reset" class="btn btn-sm btn-white btn-active-light-primary me-2"
                        data-kt-menu-dismiss="true" @click="resetFilter()">
                        Reset
                    </button>

                    <button type="submit" class="btn btn-sm btn-primary" data-kt-menu-dismiss="true" @click="filter()">
                        Apply
                    </button>
                </div>
            </div>
            <!-- </form>  -->
        </div>
    </div>
</template>

<script>
import Helpers from '@/Mixins/Helpers'
export default {
    props: ["business", "callType", "parent", "filterData", "url", "newKeyword", "minPrice", "maxPrice", "orderType", "year"],
    data() {
        return {
            type: this.callType,
            searchedKeyword: this.newKeyword ? this.newKeyword : null,
        }
    },

    methods: {
        filter() {
            this.$inertia.replace(
                route(this.url, {
                    keyword: this.searchedKeyword,
                    form: this.filterData,
                    track_id: (this.type == 'products') ? this.getSelectedModuleValue() : null,
                    moduleId: ['business', 'order', 'products', 'garage', 'dealership', 'vehicles','events', 'boats', 'news', 'obituaries', 'recipes', 'post', 'blogs', 'classifieds', 'services', 'posts', 'taskers', 'notices', 'properties'].includes(this.type) ? this.getSelectedModuleValue() : null,
                    business_uuid: ['services', 'products', 'order', 'posts', 'notices', 'properties'].includes(this.type) ? this.getSelectedBusinessValue() : null,
                    garageId: this.type == 'vehicles' || this.type == 'boats' ? this.getSelectedBusinessValue() : null,
                    type: this.type == 'order' ? this.orderType : null,
                    year: this.type == 'vehicles' || this.type == 'boats' ? this.year : null,
                })
            );
        },
        resetFilter() {
            let resetFilterData = this.filterData;
            resetFilterData.orderBy = 'desc',
                resetFilterData.status = '',
                resetFilterData.barMinValue = this.minPrice !== null ? this.minPrice : null,
                resetFilterData.barMaxValue = this.maxPrice ? this.maxPrice : null,
                resetFilterData.reviewRating = null,
                resetFilterData.sortByOrder = 'desc',
                resetFilterData.order_type = null,
                resetFilterData.order_status_id = null,
                resetFilterData.from = null,
                resetFilterData.to = null,
                resetFilterData.tag = null,
                resetFilterData.type = '',
                resetFilterData.today_created = false,
                resetFilterData.year = null
            this.$inertia.replace(
                route(this.url, {
                    form: resetFilterData,
                    track_id: (this.type == 'products') ? this.getSelectedModuleValue() : null,
                    moduleId: ['business', 'order', 'products', 'garage', 'dealership', 'vehicles', 'boats', 'news', 'obituaries', 'recipes', 'post', 'blogs', 'classifieds', 'services', 'posts', 'taskers', 'notices', 'properties', 'events'].includes(this.type) ? this.getSelectedModuleValue() : null,
                    business_uuid: ['services', 'products', 'order', 'posts', 'notices', 'properties'].includes(this.type) ? this.getSelectedBusinessValue() : null,
                    garageId: this.type == 'vehicles' || this.type == 'boats' ? this.getSelectedBusinessValue() : null,
                    type: this.type == 'order' ? this.orderType : null
                })
            );
            this.$emit("removeSelectedTags");
        }
    },
    mounted() {
        this.emitter.on('filter-keyword', (args) => {
            this.searchedKeyword = args.filterKeyword;
        });
    },

    mixins: [Helpers]
};
</script>
