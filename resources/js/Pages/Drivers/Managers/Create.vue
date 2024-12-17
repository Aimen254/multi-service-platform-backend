<template>
  <Head title="Create Driver Manager" />
  <AuthenticatedLayout>
      <template #breadcrumbs>
          <Breadcrumbs :title="`Create Driver Managers`" :path="`Drivers - Driver Managers`" />
      </template>
    <div>
      <driver-form :type="type" :mediaAvatarSizes="mediaSizes"></driver-form>
    </div>
  </AuthenticatedLayout>
</template>

<script>
import { Head } from "@inertiajs/inertia-vue3";
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import DriverForm from "../Form.vue";
import Breadcrumbs from "@/Components/Breadcrumbs.vue";

export default {
  props:['mediaSizes'],
  components: {
    Head,
    AuthenticatedLayout,
    DriverForm,
    Breadcrumbs,
  },
  data() {
    return {
      type: "driver_manager",
    };
  },
};
</script>