<template>
  <Head title="Edit Role Permissions" />
  <AuthenticatedLayout>
    <template #breadcrumbs>
      <Breadcrumbs :title="`Edit Role Permission`" :path="`Settings - Roles and Permissions`" />
    </template>
    <div>
      <role-form :role="role" :permissionGroups="permissionGroups" :rolePermissions="rolePermissions"></role-form>
    </div>
  </AuthenticatedLayout>
</template>

<script>
import { Head } from "@inertiajs/inertia-vue3";
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import RoleForm from "./Form.vue";
import Breadcrumbs from "@/Components/Breadcrumbs.vue";

export default {
  components: {
    Head,
    AuthenticatedLayout,
    RoleForm,
    Breadcrumbs,
  },
  props: ["role", 'permissionGroups', 'rolePermissions'],
};
</script>