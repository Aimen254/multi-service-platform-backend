<template>
  <Head title="Create Driver" />
  <AuthenticatedLayout>
    <template #breadcrumbs>
        <Breadcrumbs :title="`Create Driver`" :path="`Drivers - Drivers`" />
    </template>
    <div>
      <driver-form :type="type" :mediaAvatarSizes="mediaSizes"></driver-form>
    </div>
  </AuthenticatedLayout>
</template>

<script>
import { Head } from "@inertiajs/inertia-vue3";
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import DriverForm from "./Form.vue";
import Breadcrumbs from "@/Components/Breadcrumbs.vue";

export default {
  props:['mediaSizes'],
  components: {
    Head,
    AuthenticatedLayout,
    DriverForm,
    Breadcrumbs
  },
  data() {
    return {
      type: "driver",
    };
  },
};
</script>