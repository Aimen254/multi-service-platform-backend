<template>
  <Head title="Profile" />
  <AuthenticatedLayout>
    <div class="d-flex flex-column flex-xl-row">
      <div class="flex-column flex-lg-row-auto w-100 w-xl-350px mb-10">
        <p-form
          :type="type"
          :user="profileinformation"
          :mediaAvatarSizes="mediaAvatarSizes"
          @toggleTab="changeTab($event)"
        ></p-form>
      </div>
      <div class="flex-lg-row-fluid ms-lg-15">
        <div class="card mb-5 mb-xl-8">
          <div class="card-body pt-8">
            <div class="tab-content">
              <div
                :class="{
                  'd-none': tab !== 'personal_info',
                  'd-block': tab === 'personal_info',
                }"
              >
                <i-form :user="profileinformation" :type="type"></i-form>
              </div>
              <div
                :class="{
                  'd-none': tab !== 'change_password',
                  'd-block': tab === 'change_password',
                }"
              >
                <c-form :type="type"></c-form>
              </div>
              <div
                :class="{
                  'd-none': tab !== 'update_address',
                  'd-block': tab === 'update_address',
                }"
              >
                <edit-adress :user="profileinformation"></edit-adress>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>



<script>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import Helpers from "@/Mixins/Helpers";
import PForm from "./ProfileForm.vue";
import IForm from "./PersonalInformation.vue";
import CForm from "./ChangePassword.vue";
import EditAdress from "./EditAddress.vue";

export default {
  components: {
    AuthenticatedLayout,
    Head,
    PForm,
    IForm,
    CForm,
    EditAdress,
  },
  props: ["profileinformation", "mediaAvatarSizes"],
  data() {
    return {
      tab: "personal_info",
      type: "profile",
    };
  },

  methods: {
    changeTab(tab) {
      this.tab = tab;
    },
  },

  mixins: [Helpers],
};
</script>