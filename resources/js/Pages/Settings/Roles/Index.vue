<template>

    <Head title="Settings Roles" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Roles and Permissions`" :path="`Settings`" />
        </template>
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-xxl">
                <!--begin::Row-->
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-5 g-xl-9">
                    <!--begin::Col-->
                    <div class="col-md-4" v-for="role in roles" :key="role.id">
                        <!--begin::Card-->
                        <div class="card card-flush h-md-100">
                            <!--begin::Card header-->
                            <div class="card-header">
                                <!--begin::Card title-->
                                <div class="card-title">
                                    <h2 class="text-capitalize">{{ role.title }}</h2>
                                </div>
                                <!--end::Card title-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body pt-1">
                                <!--begin::Users-->
                                <div class="fw-bolder text-gray-600 mb-5">
                                    Total users with this role:  {{role.users.length}}
                                </div>
                                <!--end::Users-->
                            </div>
                            <!--end::Card body-->
                            <!--begin::Card footer-->
                            <div class="card-footer flex-wrap pt-0">
                                <Link v-if="checkUserPermissions('edit_roles')"
                                    :href="route('dashboard.settings.roles.edit', role.id)"
                                    class="btn btn-sm btn-light-primary">
                                Edit Role
                                </Link>
                            </div>
                            <!--end::Card footer-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Row-->
            </div>
            <!--end::Container-->
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import Header from "@/Components/Header.vue";
import Helpers from "@/Mixins/Helpers";
import Button from "@/Components/Button.vue";
import Breadcrumbs from "@/Components/Breadcrumbs.vue";

export default {
    components: {
        AuthenticatedLayout,
        Head,
        Header,
        Button,
        Link,
        Breadcrumbs,
    },
    props: ["rolesList"],
    mounted() {},
    data() {
        return {
            roles: this.rolesList,
        };
    },
    mixins: [Helpers],
};
</script>
