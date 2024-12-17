<template>
    <div v-if="meta && meta.data.length > 0" class="d-flex px-4 justify-content-between">
        <div>
            <p class="text-muted">
                Showing
                <span class="font-medium">{{ meta.from }}</span>
                to
                <span class="font-medium">{{ meta.to }}</span>
                of
                <span class="font-medium">{{ meta.total }}</span>
                results
            </p>
        </div>
        <div class="dataTables_paginate paging_simple_numbers mt-n2">
            <ul class="pagination">
                <div v-for="(link, index) in meta.links" :key="index">
                    <li class="paginate_button page-item" :class="{ 'active': link.active }">
                        <a v-if="section && link.url" class="page-link" style="cursor: pointer;" v-html="link.label"
                            @click="hierarchyPagination(meta, link.label)"></a>
                        <Link v-else-if="link.url !== null" class="page-link" :href="pageUrl(link.url)" v-html="link.label">
                        </Link>
                        <a v-else class="page-link" v-html="link.label"></a>
                    </li>
                </div>
            </ul>
        </div>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3';
export default {
    name: "Pagination",
    props: ['meta', 'keyword', 'selectedFilters', 'callType', 'orderFrom', 'orderTo', 'section', 'year'],
    components: {
        Link
    },
    data() {
        return {
            selectedFilterData: this.selectedFilters ? this.selectedFilters : null,
        }
    },
    methods: {
        pageUrl(url) {
            let order = this.selectedFilterData && this.selectedFilterData.orderBy ? '&form[orderBy]=' + this.selectedFilters.orderBy : ''
            let status = this.selectedFilterData && this.selectedFilterData.status ? '&form[status]=' + this.selectedFilters.status : ''
            let barMinValue = this.selectedFilterData && this.selectedFilterData.barMinValue !== null ? '&form[barMinValue]=' + this.selectedFilters.barMinValue : ''
            let barMaxValue = this.selectedFilterData && this.selectedFilterData.barMaxValue ? '&form[barMaxValue]=' + this.selectedFilters.barMaxValue : ''
            let reviewRating = this.selectedFilterData && this.selectedFilterData.reviewRating ? '&form[reviewRating]=' + this.selectedFilters.reviewRating : ''
            let sortByOrder = this.selectedFilterData && this.selectedFilterData.sortByOrder ? '&form[sortByOrder]=' + this.selectedFilters.sortByOrder : ''
            let orderType = this.selectedFilterData && this.selectedFilterData.order_type ? '&form[order_type]=' + this.selectedFilters.order_type : ''
            let orderStatus = this.selectedFilterData && this.selectedFilterData.order_status_id ? '&form[order_status_id]=' + this.selectedFilters.order_status_id : ''
            let orderFrom = this.selectedFilterData && this.selectedFilterData.from ? '&form[from]=' + this.orderFrom : ''
            let orderTo = this.selectedFilterData && this.selectedFilterData.to ? '&form[to]=' + this.orderTo : ''
            let searchedKeyword = this.keyword ? '&keyword=' + this.keyword : ''
            let tag = this.selectedFilterData && this.selectedFilterData.tag ? '&form[tag]=' + this.selectedFilters.tag : ''
            let year = this.year ? '&year=' + this.year : null
            let type = this.selectedFilterData && this.selectedFilterData.type ? '&form[type]=' + this.selectedFilterData.type : '&form[type]=' + ''
            switch (this.callType) {
                case 'products':
                    return url + searchedKeyword + order + status + barMinValue + barMaxValue + tag
                case 'business':
                    return url + searchedKeyword + order + status + reviewRating + sortByOrder
                case 'order':
                    return url + searchedKeyword + order + orderType + orderStatus + orderFrom + orderTo
                case 'vehicles':
                    return url + searchedKeyword + order + status + barMinValue + barMaxValue + tag + year + type
                case 'services':
                    return url + searchedKeyword + order + status + barMinValue + barMaxValue + tag + year + type
                case 'boats':
                    return url + searchedKeyword + order + status + barMinValue + barMaxValue + tag + year + type
                case 'classifieds':
                    return url + searchedKeyword + order + status + barMinValue + barMaxValue + tag
                case 'posts':
                    return url + searchedKeyword + order + status + tag
                case 'notices':
                    return url + searchedKeyword + order + status + tag
                case 'headLineFilter':
                    return url + searchedKeyword + type + tag
                default:
                    return url + searchedKeyword
            }
        },

        hierarchyPagination(meta, label) {
            let page = meta.current_page
            if (label.includes('Next')) {
                page = page + 1
            } else if (label.includes('Previous')) {
                page = page - 1
            } else {
                page = label
            }
            this.$emit('pageNo', page);
        }
    },
}

</script>

<style scoped></style>
