<template>

  <Head title="Driver Groups" />
  <AuthenticatedLayout>
    <template #breadcrumbs>
      <Breadcrumbs :title="`Driver Groups`" :path="`Drivers`" />
      <div class="d-flex align-items-center gap-2 gap-lg-3">
        <button class="btn btn-sm btn-light-primary" v-if="checkUserPermissions('add_drivers_group')"
          @click="openModal">
          <span class="svg-icon svg-icon-2">
            <inline-svg :src="'/images/icons/arr075.svg'" />
          </span>
          Add Drivers Group
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
                <th class="min-w-150px">Manager</th>
                <th class="min-w-150px">Drivers Count</th>
                <th class="min-w-150px">Status</th>
                <th class="min-w-150px">Created At</th>
                <th class="min-w-150px rounded-end">Action</th>
              </tr>
            </thead>
            <!--end::Table head-->

            <!--begin::Table body-->
            <tbody v-if="groups && groups.data.length > 0">
              <template v-for="group in groups.data" :key="group.id">
                <tr>
                  <td>
                    <span class="
                        text-dark
                        fw-bolder
                        text-hover-primary
                        d-block
                        mb-1
                        fs-6
                        text-capitalize
                        ms-4
                      ">
                      {{ group.name }}
                    </span>
                  </td>

                  <td>
                    <div class="d-flex align-items-center">
                      <div class="image-input image-input-empty">
                        <div class="image-input-wrapper w-50px h-50px"
                          :style="{ 'background-image': 'url(' + getImage(group.manager.avatar, true, 'avatar') + ')' }">
                        </div>
                      </div>
                      <div class="d-flex justify-content-start flex-column mx-2">
                        <span class="
                            text-muted fw-bold text-muted d-block text-capitalize fs-7
                          ">
                          {{ group.manager.first_name }}
                          {{ group.manager.last_name }}
                        </span>
                      </div>
                    </div>
                  </td>

                  <td>
                    <span class="text-capitalize badge badge-light-warning">
                      {{ group.drivers_count }}
                    </span>
                  </td>

                  <td>
                    <span :class="{
                      'badge badge-light-success': group.status == 'active',
                      'badge badge-light-danger': group.status === 'inactive',
                    }" class="text-capitalize">
                      {{ group.status }}
                    </span>
                  </td>

                  <td>
                    <span class="
                        text-muted fw-bold text-muted d-block text-capitalize fs-7
                      ">
                      {{ formatDate(group.created_at) }}
                    </span>
                  </td>

                  <td>
                    <div class="d-flex">
                      <Toggle v-if="checkUserPermissions('edit_drivers_group')"
                        :status="booleanStatusValue(group.status)" @click.prevent="changeStatus(group.id)" />
                      <edit-section iconType="modal" permission="edit_drivers_group" @click="openModal(group)" />
                      <delete-section permission="delete_drivers_manager"
                        :url="route('dashboard.driver.groups.destroy', group.id)" 
                        :currentPage="groups.current_page" :currentCount="groups.data.length"/>
                    </div>
                  </td>
                </tr>
              </template>
            </tbody>
            <div v-else class="p-4 text-muted">
              Record Not Found
            </div>
            <!--end::Table body-->
          </table>
          <!--end::Table-->
          <pagination :meta="groups" :keyword="searchedKeyword" />
        </div>
        <!--end::Table container-->
      </div>
      <!--begin::Body-->
    </div>
    <!--end::Tables Widget 11-->
  </AuthenticatedLayout>
  <GroupModal></GroupModal>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import Header from "@/Components/Header.vue";
import Pagination from "@/Components/Pagination.vue";
import Helpers from "@/Mixins/Helpers";
import GroupModal from "./GroupModal.vue";
import SearchInput from "@/Components/SearchInput.vue";
import EditSection from "@/Components/EditSection.vue";
import DeleteSection from "@/Components/DeleteSection.vue";
import Toggle from "@/Components/ToggleButton.vue";
import InlineSvg from "vue-inline-svg";
import Breadcrumbs from "@/Components/Breadcrumbs.vue";

export default {
  components: {
    AuthenticatedLayout,
    Head,
    Link,
    Header,
    Pagination,
    GroupModal,
    SearchInput,
    EditSection,
    DeleteSection,
    Toggle,
    InlineSvg,
    Breadcrumbs,
  },

  props: ["managers", "searchedKeyword", "groupsList", "drivers"],

  methods: {
    openModal(groupData = null) {
      this.emitter.emit("group-modal", {
        managers: this.managers,
        drivers: this.drivers,
        group: groupData,
      });
    },

    changeStatus(id) {
      this.$inertia.get(route("dashboard.driver.group.status", id));
    },
  },

  data() {
    return {
      groups: this.groupsList,
      type: "group",
    };
  },

  watch: {
    groupsList: {
      handler(groupsList) {
        this.groups = groupsList;
      },
      deep: true,
    },
  },

  mixins: [Helpers],
};
</script>