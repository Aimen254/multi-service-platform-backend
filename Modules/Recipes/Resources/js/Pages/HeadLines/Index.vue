<template>

    <Head title="HeadLines" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Recipes`" :path="`HeadLines`" />
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <Link v-if="checkUserPermissions('add_headlines')" @click="removeHedinLineType()"
                    :href="route('recipes.dashboard.headlines.create', this.getSelectedModuleValue())"
                    class="btn btn-sm btn-primary">
                <span class="svg-icon svg-icon-2">
                    <inline-svg :src="'/images/icons/arr075.svg'" />
                </span>
                Add HeadLine
                </Link>
            </div>
        </template>

        <!--begin::Tables Widget 11-->
        <div :class="widgetClasses" class="card">
            <!--begin::Body-->
            <div class="card-body">
                <!--begin::Table container-->
                <div class="table-responsive">
                    <!--begin::Table-->
                    <table class="table align-middle gs-0 gy-4 table-row-dashed">
                        <!--begin::Table head-->
                        <thead>
                            <tr class="fw-bolder text-muted bg-light">
                                <th class="ps-4 min-w-150px rounded-start">Title</th>
                                <th class="min-w-150px">Category</th>
                                <th class="min-w-150px">Created At</th>
                                <th class="min-w-150px rounded-end">Action</th>
                            </tr>
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody v-if="recipes?.data?.length > 0">
                            <template v-for="item in recipes.data" :key="item.id">
                                <tr>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="image-input image-input-empty">
                                                <div class="image-input-wrapper w-50px h-50px"
                                                    :style="{ 'background-image': 'url(' + getImage(item.main_image ? item.main_image.path : null, true, 'product', item.main_image ? item.main_image.is_external : 0) + ')' }">
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-start flex-column mx-2">
                                                <span class="
                                                    text-dark
                                                    fw-bolder
                                                    text-hover-primary
                                                    mb-1
                                                    fs-6
                                                ">
                                                    {{ limit(item?.name) }}
                                                </span>
                                                <!-- {{ item }} -->
                                                <span class="text-muted fw-bold text-muted d-block fs-7">
                                                    <span v-if="item.headline" style="margin-right:10px">
                                                        <i class="fas fa-check-circle text-success"></i>
                                                        {{ item.headline?.type }}
                                                    </span>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block text-capitalize fs-7">
                                            {{ item.standard_tags[0].name }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block text-capitalize fs-7">
                                            {{ formatDate(item.created_at) }}
                                        </span>
                                    </td>
                                    <td>
                                        <delete-section iconType="link" permission="delete_headlines"
                                            :message="item.headline?.type" v-if="item.headline"
                                            :url="route('recipes.dashboard.headlines.destroy', [getSelectedModuleValue(), item.headline?.id])" 
                                            :currentPage="recipes.current_page" :currentCount="recipes.data.length"/>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <!--end::Table body-->
                        <div v-else class="p-4 text-muted">
                            Record Not Found
                        </div>
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Table container-->
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head, Link } from "@inertiajs/inertia-vue3";
import Header from '@/Components/Header.vue';
import Pagination from '@/Components/Pagination.vue';
import Helpers from '@/Mixins/Helpers';
import EditSection from '@/Components/EditSection.vue';
import DeleteSection from '@/Components/DeleteSection.vue';
import Toggle from '@/Components/ToggleButton.vue';
import InlineSvg from 'vue-inline-svg';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
// import BlogSearchInput from "../../Components/BlogSearchInput.vue";
import DeleteHeadline from "@/Components/DeleteHeadline.vue";

export default {
    components: {
        AuthenticatedLayout,
        Head,
        Header,
        Link,
        Pagination,
        EditSection,
        DeleteSection,
        Toggle,
        InlineSvg,
        Breadcrumbs,
        // BlogSearchInput,
        DeleteHeadline
    },

    props: ["headLines", "tag"],

    data() {
        return {
            recipes: this.headLines,
            type: "recipes",
            module: 'recipes',
        };
    },

    methods: {
        removeHedinLineType() {
            if (localStorage.headLineType != null) {
                localStorage.removeItem('headLineType')
            }
        }
    },

    watch: {
        headLines: {
            handler(headLines) {
                this.recipes = headLines;
            },
            deep: true,
        },
    },


    mixins: [Helpers],
}
</script>

<style></style>
