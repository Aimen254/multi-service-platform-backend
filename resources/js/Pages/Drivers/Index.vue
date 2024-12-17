<template>
  <Head title="Drivers" />
  <AuthenticatedLayout>
      <template #breadcrumbs>
          <Breadcrumbs :title="`Drivers`" :path="`Drivers`"></Breadcrumbs>
          <div class="d-flex align-items-center gap-2 gap-lg-3">
              <Link v-if="checkUserPermissions('add_drivers')"
                  :href="route('dashboard.drivers.create')"
                  class="btn btn-sm btn-primary">
                  <span class="svg-icon svg-icon-2">
                      <inline-svg :src="'/images/icons/arr075.svg'" />
                  </span>
                  Add Driver
              </Link>
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
                <th class="min-w-150px">Role</th>
                <th class="min-w-150px">Status</th>
                <th class="min-w-150px">Created At</th>
                <th class="min-w-150px rounded-end">Action</th>
              </tr>
            </thead>
            <!--end::Table head-->
            <!--begin::Table body-->
            <tbody v-if="drivers && drivers.data.length > 0">
              <template v-for="driver in drivers.data" :key="driver.id">
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="image-input image-input-empty">
                            <div
                            class="image-input-wrapper w-50px h-50px"
                            :style="{ 'background-image': 'url(' + getImage(driver.avatar, true, 'avatar') + ')' }"
                            ></div>
                      </div>
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
                          {{ driver.first_name }} {{ driver.last_name }}
                        </span>
                        <span
                          class="text-muted fw-bold text-muted d-block fs-7"
                        >
                          {{ driver.email }}
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
                      {{ driver.user_type }}
                    </span>
                  </td>

                  <td>
                    <span
                      :class="{
                        'badge badge-light-success': driver.status == 'active',
                        'badge badge-light-danger':
                          driver.status === 'inactive',
                      }"
                      class="text-capitalize"
                    >
                      {{ driver.status }}
                    </span>
                  </td>

                  <td>
                    <span
                      class="
                        text-muted fw-bold text-muted d-block text-capitalize fs-7
                      "
                    >
                      {{ formatDate(driver.created_at) }}
                    </span>
                  </td>

                  <td>
                    <div class="d-flex">
                      <Toggle
                        v-if="checkUserPermissions('edit_drivers')"
                        :status="booleanStatusValue(driver.status)"
                        @click.prevent="changeStatus(driver.id)"
                      />
                      <edit-section iconType="link" 
                        permission="edit_drivers"
                        :url="route('dashboard.drivers.edit', driver.id)"/>
                      <delete-section permission="delete_drivers"
                        :url="route('dashboard.drivers.destroy', driver.id)" 
                        :currentPage="drivers.current_page" :currentCount="drivers.data.length"/>
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
          <pagination :meta="drivers" :keyword="searchedKeyword" />
        </div>
        <!--end::Table container-->
      </div>
      <!--begin::Body-->
    </div>
    <!--end::Tables Widget 11-->
  </AuthenticatedLayout>
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
import InlineSvg from "vue-inline-svg";
import Breadcrumbs from '@/Components/Breadcrumbs.vue'

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
    InlineSvg,
    Breadcrumbs,
  },

  props: ["driversList", "searchedKeyword"],

  data() {
    return {
      drivers: this.driversList,
      type: "driver",
    };
  },
  watch: {
    driversList: {
      handler(driversList) {
        this.drivers = driversList;
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

            this.$inertia.visit(route("dashboard.driver.status", id), {
              preserveScroll: false,
              onSuccess: () => hideWaitDialog(),
            });
          }
        });
    },
  },

  mixins: [Helpers],
};
</script>