<template>
  <Head title="Service Provider" />
  <AuthenticatedLayout>
    <template #breadcrumbs>
      <Breadcrumbs :title="`Service Provider`" :path="`Service Provider`"></Breadcrumbs>
    </template>

    <div class="ms-lg-5">
      <div class="card mb-5 mb-xl-8">
        <div class="card-body">
          <div class="row">
            <div class="col-md-4 bg-white px-0 border border-1 rounded-3">
              <div class="border border-1 rounded-3">
                <div class="d-flex p-5 metronik-table-head-bg-color">
                  <h6 class="fw-bold text-gray fs-4 text-capitalize rounded">
                    {{ title }}
                  </h6>
                </div>

                <div
                  class="menu menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-280px"
                  v-if="business.businessschedules &&
                    business.businessschedules.length > 0
                    ">
                  <div class="menu-item px-5">
                    <a v-for="(Schedule, index) in business.businessschedules" :key="index" :class="[
                      ScheduleName == Schedule.name && !businessHolidays
                        ? 'metronik-table-head-bg-color p-3 fw-bolder text-dark'
                        : 'text-muted',
                      'menu-link d-block px-4  mt-2 fs-6 rounded p-3  fw-bold cursor-pointer text-hover-primary',
                    ]" @click="
  getSchedule(business.id, Schedule.id, Schedule.name)
  ">
                      {{ Schedule.name }}
                    </a>
                  </div>
                  <div class="menu-item px-5">
                    <a :class="[
                      businessHolidays ? 'menu-link metronik-table-head-bg-color p-3 fw-bolder text-dark' : 'text-muted',
                      'd-block px-4 mt-2 fs-6 rounded p-3 fw-bold cursor-pointer text-hover-primary',
                    ]" @click="getHolidays(business.id)">
                      Service Provider Holiday
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-8 ">
              <div :class="[
                (businessHolidays || businessSchedule) ? 'border border border-1 rounded-3' : ''
              ]">
                <div v-if="businessSchedule" class=" row metronik-table-head-bg-color p-3 gx-0">
                  <div class="col-md-4 ">
                    <h2 class="fw-bold text-gray fs-4 text-capitalize border border-1 rounded-3 pt-2">
                      {{ businessSchedule.name }}
                    </h2>
                  </div>
                  <div class="col-md-4 d-flex justify-content-center">
                    <label for="checkbox" class="p-2 mt-1">Enabled</label>
                    <Toggle :status="booleanStatusValue(businessSchedule.status)" class="" @click="
                      updateBusinessSchedule(business.uuid, businessSchedule.id)
                      " />
                  </div>
                  <div class="col-md-4 d-flex justify-content-end">
                    <button @click="
                      openScheduleModal(
                        businessSchedule,
                        businessSchedule.name,
                        'post'
                      )
                      " v-if="checkUserPermissions('add_business_schedule_time')" class="btn btn-primary btn-sm">
                      Add
                    </button>
                  </div>
                </div>
                <div v-if="businessSchedule.scheduletimes" class="table-responsive bg-white ">
                  <table class="table align-middle gs-0 gy-4 table-row-dashed">
                    <thead>
                      <tr class="fw-bolder bg-white text-muted">
                        <th class="ps-4 min-w-400px rounded-start">Lapse</th>
                        <th class="min-w-150px rounded-end text-end px-5">
                          Action
                        </th>
                      </tr>
                    </thead>
                    <tbody v-if="businessSchedule.scheduletimes &&
                      businessSchedule.scheduletimes.length > 0
                      ">
                      <tr v-for="(
                          schedule, index
                        ) in businessSchedule.scheduletimes" :key="index" class="ps-4">
                        <td>
                          <div class="d-flex align-items-center">
                            <div class="d-flex justify-content-start flex-column">
                              <span href="#" class="
                                  text-dark
                                  fw-bolder
                                  text-hover-primary
                                  mb-1
                                  fs-6
                                  ms-4
                                ">
                                {{ schedule.open_at }} - {{ schedule.close_at }}
                              </span>
                            </div>
                          </div>
                        </td>

                        <td class="text-end px-5">
                          <div class="d-flex justify-content-end">
                            <edit-section iconType="modal" permission="edit_business_schedule_time"
                              @click="openScheduleModal(schedule, businessSchedule.name, 'put')" />
                            <a class=" btn btn-icon btn-bg-light btn-active-color-primary btn-sm"
                              @click="onDelete(schedule.id)" v-if="checkUserPermissions('delete_business_schedule_time')"
                              data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="delete">
                              <span class="svg-icon svg-icon-3">
                                <inline-svg :src="'/images/icons/delete.svg'" />
                              </span>
                            </a>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                    <div class="text-secondary ps-4" v-else>
                      No record found
                    </div>
                  </table>
                </div>
                <!-- Holidays Module -->
                <div v-if="businessHolidays" class="row metronik-table-head-bg-color p-3 gx-0">
                  <div class="col-md-6 ">
                    <h2 class="fw-bold text-gray fs-4 text-capitalize rounded pt-2">
                      Service Provider Holidays
                    </h2>
                  </div>
                  <div class="col-md-6 d-flex justify-content-end">
                    <button @click="openHolidayModal(businessHolidays, 'post')"
                      v-if="checkUserPermissions('add_business_schedule_time')" class="btn btn-primary btn-sm">
                      Add
                    </button>
                  </div>
                </div>
                <div v-if="businessHolidays" class="table-responsive bg-white">
                  <table class="table align-middle gs-0 gy-4 table-row-dashed">
                    <thead>
                      <tr class="fw-bolder bg-white text-muted">
                        <th class="ps-4 min-w-200px rounded-start">Title</th>
                        <th class="ps-4 min-w-150px">Date</th>
                        <th class="min-w-150px rounded-end text-end px-5">Action</th>
                      </tr>
                    </thead>
                    <tbody v-if="businessHolidays && businessHolidays.length > 0">
                      <tr v-for="(holiday, index) in businessHolidays" :key="index" class="ps-4">
                        <td>
                          <div class="d-flex align-items-center">
                            <div class="d-flex justify-content-start flex-column">
                              <span href="#" class="
                                  text-dark fw-bolder
                                  text-hover-primary mb-1 fs-6 ms-4
                                ">
                                {{ holiday.title }}
                              </span>
                            </div>
                          </div>
                        </td>
                        <td>
                          <div class="d-flex align-items-center">
                            <div class="d-flex justify-content-start flex-column">
                              <span href="#" class="
                                  text-dark fw-bolder
                                  text-hover-primary mb-1 fs-6 ms-4
                                ">
                                {{ holiday.date }}
                              </span>
                            </div>
                          </div>
                        </td>
                        <td>
                          <div class="text-end px-5">
                            <div @click="openHolidayModal(holiday, 'put')" v-if="checkUserPermissions(
                              'edit_business_schedule_time'
                            )
                              " class="
                                btn btn-icon btn-bg-light
                                btn-active-color-primary btn-sm me-1 ml-3
                              ">
                              <span class="svg-icon svg-icon-3">
                                <inline-svg :src="'/images/icons/edit.svg'" />
                              </span>
                            </div>
                            <a class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" data-bs-toggle="tooltip"
                              data-bs-placement="bottom" data-bs-original-title="delete"
                              @click="onDeleteHoliday(holiday.id)" v-if="checkUserPermissions(
                                'delete_business_schedule_time'
                              )
                                ">
                              <span class="svg-icon svg-icon-3">
                                <inline-svg :src="'/images/icons/delete.svg'" />
                              </span>
                            </a>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                    <div class="text-secondary ps-4" v-else>
                      No record found
                    </div>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
  <BusinessScheduleModal :business="business" v-on:childToParent="onChildClick"></BusinessScheduleModal>
  <BusinessHolidayModal :business="business" v-on:childToParentHolidayModel="onChildHolidayModel"></BusinessHolidayModal>
</template>

<script>
import { Head } from "@inertiajs/inertia-vue3"
import AuthenticatedLayout from "@/Layouts/Authenticated.vue"
import Error from "@/Components/InputError.vue"
import Helpers from "@/Mixins/Helpers"
import BusinessScheduleModal from "./BusinessScheduleModal.vue"
import BusinessHolidayModal from "./BusinessHolidayModal.vue"
import EditSection from "@/Components/EditSection.vue"
import DeleteSection from "@/Components/DeleteSection.vue"
import InlineSvg from "vue-inline-svg"
import { Link } from "@inertiajs/inertia-vue3"
import Toggle from "@/Components/ToggleButton.vue"
import Breadcrumbs from '@/Components/Breadcrumbs.vue'

export default {
  props: ["business"],
  components: {
    Head,
    AuthenticatedLayout,
    Error,
    Link,
    Toggle,
    BusinessScheduleModal,
    BusinessHolidayModal,
    EditSection,
    DeleteSection,
    InlineSvg,
    Breadcrumbs
  },
  data() {
    return {
      tab: "Sunday",
      schedule: "",
      scheduleName: "",
      businessSchedule: "",
      businessHolidays: "",
      title: "Business Schedule",
    };
  },
  methods: {
    removeTooltip() {
      this.hideTooltip()
    },
    // Triggered when `childToParent` event is emitted by the child.
    onChildClick(value) {
      this.businessSchedule = value;
    },
    onChildHolidayModel(value) {
      this.businessHolidays = value;
    },
    openScheduleModal(schedule, name, type) {
      this.emitter.emit("business-schedule-model", {
        name: name,
        type: type,
        schedule: schedule ? schedule : null,
      });
    },
    openHolidayModal(holiday, type) {
      this.emitter.emit("business-holiday-modal", {
        type: type,
        holiday: holiday ? holiday : null,
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
            window.axios
              .delete(
                route("services.dashboard.service-provider.businessschedules.destroy", [
                  this.getSelectedModuleValue(),
                  id,
                  this.business.uuid,
                ])
              )
              .then((response) => {
                this.businessSchedule = response.data.schedule;
                this.$notify(
                  {
                    group: "toast",
                    type: "success",
                    text: response.data.message,
                  },
                  3000
                ); // 3s
              })
              .catch((error) => {
                this.$notify(
                  {
                    group: "toast",
                    type: "error",
                    text: error.response.data.message,
                  },
                  3000
                ); // 3s
              });
          }
        });
    },
    onDeleteHoliday(id) {
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
            window.axios
              .delete(
                route("services.dashboard.service-provider.businessholidays.destroy", [
                  this.getSelectedModuleValue(),
                  id,
                  this.business.uuid,
                ])
              )
              .then((response) => {
                this.businessHolidays = response.data.businessHolidays;
                this.$notify(
                  {
                    group: "toast",
                    type: "success",
                    text: response.data.message,
                  },
                  3000
                ); // 3s
              })
              .catch((error) => {
                this.$notify(
                  {
                    group: "toast",
                    type: "error",
                    text: error.response.data.message,
                  },
                  3000
                ); // 3s
              });
          }
        });
    },
    updateBusinessSchedule(businessUuid, scheduleId) {
      window.axios
        .post(
          route("services.dashboard.service-provider.businessschedule.status", [
            this.getSelectedModuleValue(),
            businessUuid,
            scheduleId,
            businessUuid,
          ])
        )
        .then((response) => {
          this.businessSchedule = response.data.schedule;
          this.$notify(
            {
              group: "toast",
              type: "success",
              text: response.data.message,
            },
            3000
          ); // 3s
        })
        .catch((error) => {
          this.$notify(
            {
              group: "toast",
              type: "error",
              text: error.response.data.message,
            },
            3000
          ); // 3s
        });
    },
    getSchedule(businessId, ScheduleId, ScheduleName) {
      this.ScheduleName = ScheduleName;
      window.axios
        .get(
          route("services.dashboard.service-provider.businessschedule.schedule", [
            this.getSelectedModuleValue(),
            businessId,
            ScheduleId,
          ])
        )
        .then((response) => {
          this.businessSchedule = response.data.schedule;
          this.businessHolidays = "";
        });
    },
    getHolidays(businessId) {
      window.axios
        .get(
          route("services.dashboard.service-provider.businessholidays.show", [
            this.getSelectedModuleValue(),
            businessId,
            businessId,
          ])
        )
        .then((response) => {
          this.businessHolidays = response.data.businessHolidays;
          this.businessSchedule = "";
        });

    },
  },
  mounted() {
    this.showTooltip()
  },

  mixins: [Helpers],
};
</script>