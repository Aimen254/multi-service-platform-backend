<template>

    <Head title="Settings Languages" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Languages`" :path="`Settings`" />
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button class="btn btn-sm btn-primary"
                  v-if="checkUserPermissions('add_languages')"
                  @click="openModal"
                >
                  <span class="svg-icon svg-icon-2">
                    <inline-svg :src="'/images/icons/arr075.svg'" />
                  </span>
                  Add Language
                </button>
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
                                <th class="ps-4 min-w-150px rounded-start">Name</th>
                                <th class="min-w-150px">Code</th>
                                <th class="min-w-150px">Status</th>
                                <th class="min-w-150px">Default</th>
                                <th class="min-w-150px rounded-end">Action</th>
                            </tr>
                        </thead>
                        <!--end::Table head-->

                        <!--begin::Table body-->
                        <tbody v-if="languages && languages.data.length > 0">
                            <template v-for="language in languages.data" :key="language.id">
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="d-flex justify-content-start flex-column">
                                                <span href="#"
                                                    class=" text-dark fw-bolder text-hover-primary mb-1 fs-6 ms-4">{{ language.name }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7">
                                            {{ language.code }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            :class="{'badge badge-light-success':language.status == 'active','badge badge-light-danger': language.status === 'inactive',}"
                                            class="text-capitalize">
                                            {{ language.status }}
                                        </span>
                                    </td>
                                    <td class="ps-3">
                                        <div
                                            class="form-check form-check-custom form-check-solid form-check-primary me-6">
                                            <label :class="[language.is_default ? 'opacity-100' : '']">
                                                <input class="form-check-input" type="radio" name="plan" value="Startup"
                                                    :checked="language.is_default ? true : false" :disabled="
                                                    language.is_default || disable ? true : false" :class="[language.is_default ? 'bg-primary' : '']" @click="setDefault(language.id)" />
                                            </label>
                                        </div>
                                        <!-- </label> -->
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <Toggle v-if="checkUserPermissions('edit_languages')"
                                                :status="booleanStatusValue(language.status)"
                                                @click.prevent="changeStatus(language.id)" />
                                            <delete-section permission="delete_languages"
                                                :url="route('dashboard.settings.languages.destroy', language.id)"
                                                v-if="!language.is_default"
                                                :currentPage="languages.current_page" :currentCount="languages.data.length"/>
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
                    <pagination :meta="languages" :keyword="searchedKeyword" />
                </div>
                <!--end::Table container-->
            </div>
            <!--begin::Body-->
        </div>
        <!--end::Tables Widget 11-->
    </AuthenticatedLayout>
    <LanguageModal></LanguageModal>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import Header from "@/Components/Header.vue";
import Pagination from "@/Components/Pagination.vue";
import Helpers from "@/Mixins/Helpers";
import EditSection from "@/Components/EditSection.vue";
import DeleteSection from "@/Components/DeleteSection.vue";
import Toggle from "@/Components/ToggleButton.vue";
import SearchInput from "@/Components/SearchInput.vue";
import LanguageModal from "./LanguageModal.vue";
import InlineSvg from "vue-inline-svg";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import Breadcrumbs from "@/Components/Breadcrumbs.vue";

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
    SearchInput,
    LanguageModal,
    InlineSvg,
    Breadcrumbs,
  },

  props: ["languagesList", "searchedKeyword", "allLanguages"],

  data() {
    return {
      languages: null,
      type: "language",
      disable: false,
    };
  },

  watch: {
    languagesList: {
      handler(languagesList) {
        this.languages = languagesList;
      },
      deep: true,
    },
  },

  methods: {
    changeStatus(id) {
      this.swal
        .fire({
          title: "",
          html: "<h1 class='text-lg text-gray-800 mb-1'>Change Status</h1><p class='text-base'>Are you sure you want to change status?</p>",
          icon: "warning",
          showCancelButton: true,
          cancelButtonText: "No",
          confirmButtonText: "Yes",
          customClass: {
            confirmButton: "danger",
          },
        })
        .then((result) => {
          if (result.value) {
            showWaitDialog();

            this.$inertia.visit(
              route("dashboard.settings.language.change.status", id),
              {
                preserveScroll: false,
                onSuccess: () => hideWaitDialog(),
              }
            );
          }
        });
    },
    openModal() {
      this.emitter.emit("language-modal", {
        languages: this.allLanguages,
      });
    },
    onDelete(id) {
      this.swal
        .fire({
          title: "",
          html: "<h1 class='text-lg text-gray-800 mb-1'>Delete Record</h1><p class='text-base'>Are you sure want to delete this record?</p>",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Delete Record",
          customClass: {
            confirmButton: "danger",
          },
        })
        .then((result) => {
          if (result.value) {
            showWaitDialog();

            this.$inertia.delete(
              route("dashboard.settings.languages.destroy", id),
              {
                preserveScroll: false,
                onSuccess: () => hideWaitDialog(),
              }
            );
          }
        });
    },
    setDefault(id) {
      this.disable = true;
      this.$inertia.put(route("dashboard.settings.languages.update", id), null,
        {
          preserveScroll: false,
          onSuccess: () => {
            this.disable = false;
          },
        }
      );
    },
  },

  created() {
    this.languages = this.languagesList;
  },
  mixins: [Helpers],
};
</script>