<template>
  <Head title="Edit Driver" />
  <AuthenticatedLayout>
    <template #breadcrumbs>
        <Breadcrumbs :title="`Edit Driver`" :path="`Drivers - Drivers`" />
    </template>
    <div>
      <driver-form :user="driver" :type="type" :mediaAvatarSizes="mediaSizes"></driver-form>
    </div>
  </AuthenticatedLayout>
</template>

<script>
import { Head } from "@inertiajs/inertia-vue3";
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import DriverForm from "./Form.vue";
import Breadcrumbs from "@/Components/Breadcrumbs.vue";

export default {
  components: {
    Head,
    AuthenticatedLayout,
    DriverForm,
    Breadcrumbs
  },
  props: ["driver", "mediaSizes"],
  data() {
    return {
      type: "driver",
    };
  },
};
</script>