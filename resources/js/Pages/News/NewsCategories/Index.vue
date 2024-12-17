<template>
  <Head title="News Categories" />
  <AuthenticatedLayout>
      <template #breadcrumbs>
          <Breadcrumbs :title="`News Categories`" :path="`News Categories`"></Breadcrumbs>
          <div class="d-flex align-items-center gap-2 gap-lg-3">
              <div @click="openModal()" v-if="checkUserPermissions('add_news_categories')"
                  class="btn btn-sm btn-primary">
                  <span class="svg-icon svg-icon-2">
                      <inline-svg :src="'/images/icons/arr075.svg'" />
                  </span>
                  Add News Category
              </div>
          </div>
      </template>
    <!--begin::Tables Widget 11-->
    <div :class="widgetClasses" class="card">
      <!--begin::Header-->
      <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
          <span class="card-label fw-bolder fs-3 mb-1">
            <SearchInput :callType="type" :searchedKeyword="searchedKeyword" />
          </span>
        </h3>
      </div>
      <!--end::Header-->
      <!--begin::Body-->
      <div class="card-body py-3">
        <!--begin::Table container-->
        <div class="table-responsive">
          <!--begin::Table-->
          <table class="table align-middle gs-0 gy-4 table-row-dashed">
            <!--begin::Table head-->
            <thead>
              <tr class="fw-bolder text-muted bg-light">
                <th class="ps-4 min-w-150px rounded-start">Title</th>
                <th class="min-w-150px">Created At</th>
                <th class="min-w-150px rounded-end">Action</th>
              </tr>
            </thead>
            <!--end::Table head-->
            <!--begin::Table body-->
            <tbody v-if="newsCategories && newsCategories.data.length > 0">
              <template v-for="newsCategory in newsCategories.data" :key="newsCategory.id">
                <tr>
                  <td class="px-4">
                    <div class="d-flex align-items-center">
                      <div class="d-flex justify-content-start flex-column mx-2">
                        <span
                          class="
                            text-dark
                            fw-bolder
                            text-hover-primary
                            mb-1
                            fs-6
                          "
                        >
                          {{ newsCategory.category_name }}
                        </span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span
                      class="
                        text-muted fw-bold text-muted d-block text-capitalize fs-7
                      "
                    >
                      {{ formatDate(newsCategory.created_at) }}
                    </span>
                  </td>

                  <td>
                    <div class="d-flex">
                      <edit-section iconType="modal"
                          v-if="checkUserPermissions('edit_news_categories')"
                          @click="openModal(newsCategory)" />
                      <delete-section permission="delete_news_categories"
                        :url="route('dashboard.categories.destroy', newsCategory.id)" 
                        :currentPage="newsCategories.current_page" :currentCount="newsCategories.data.length"/>
                    </div>
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
      <!--begin::Body-->
      <pagination :meta="newsCategories" :keyword="searchedKeyword" />
    </div>
    <!--end::Tables Widget 11-->
  </AuthenticatedLayout>
  <news-category-modal></news-category-modal>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import Header from "@/Components/Header.vue";
import Pagination from "@/Components/Pagination.vue";
import Helpers from "@/Mixins/Helpers";
import EditSection from "@/Components/EditSection.vue";
import DeleteSection from "@/Components/DeleteSection.vue";
import SearchInput from "@/Components/SearchInput.vue";
import InlineSvg from "vue-inline-svg";
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import NewsCategoryModal from './NewsCategoryModal.vue'

export default {
    components: {
        AuthenticatedLayout,
        Head,
        Header,
        Link,
        Pagination,
        EditSection,
        DeleteSection,
        SearchInput,
        InlineSvg,
        Breadcrumbs,
        NewsCategoryModal
    },

    props: ["newsCategoriesList", "searchedKeyword"],

    data() {
        return {
          newsCategories: this.newsCategoriesList,
          type: "news_categories",
        };
    },
    watch: {
        newsCategoriesList: {
          handler(newsCategoriesList) {
          this.newsCategories = newsCategoriesList;
        },
        deep: true,
      },
    },

     methods: {
        openModal(newsCategory = null) {
            this.emitter.emit("news-category-modal", {
                newsCategory: newsCategory,
            });
        },
    },
    mixins: [Helpers],
};
</script>