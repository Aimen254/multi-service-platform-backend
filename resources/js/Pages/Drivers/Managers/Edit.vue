<template>
    <Head title="Edit Driver Manager" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
          <Breadcrumbs :title="`Edit Driver Managers`" :path="`Drivers - Driver Managers`" />
        </template>
        <div>
            <driver-form :user="manager" :type="type" :mediaAvatarSizes="mediaSizes"></driver-form>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import { Head } from "@inertiajs/inertia-vue3";
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import DriverForm from "../Form.vue";
import Breadcrumbs from "@/Components/Breadcrumbs.vue";

export default {
  components: {
    Head,
    AuthenticatedLayout,
    DriverForm,
    Breadcrumbs,
  },
  props: ["manager", "mediaSizes"],
  data() {
    return {
      type: "driver_manager",
    };
  },
};
</script>