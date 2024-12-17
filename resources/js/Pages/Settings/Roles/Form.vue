<template>
    <form v-if="form" @submit.prevent="submit" enctype="multipart/form-data">
        <div class="card mb-5 mb-xl-8">
            <div class="card-body pt-10">
                <div class="fv-row">
                    <!--begin::Label-->
                    <label class="fs-5 fw-bolder form-label mb-2 text-capitalize">Role Permissions ({{role.title}})</label>
                    <!--end::Label-->
                    <!--begin::Table wrapper-->
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table align-middle table-row-dashed fs-6 gy-5">
                            <!--begin::Table body-->
                            <tbody class="text-gray-600 fw-bold">
                                <!--begin::Table row-->
                                <tr>
                                    <td>
                                        <!--begin::Checkbox-->
                                        <label class="form-check form-check-sm form-check-custom form-check-solid me-9">
                                            <input class="form-check-input" type="checkbox" value="" @click="selectAll"
                                                v-model="allSelected" id="kt_roles_select_all">
                                            <span class="form-check-label" for="kt_roles_select_all">Select all</span>
                                        </label>
                                        <!--end::Checkbox-->
                                    </td>
                                </tr>
                                <!--end::Table row-->
                                <!--begin::Table row-->
                                <tr v-for="(permission, index) in form.permissions" :key="index">
                                    <!--begin::Label-->
                                    <td class="text-gray-800 text-capitalize">{{ index }}</td>
                                    <!--end::Label-->
                                    <!--begin::Input group-->
                                    <td>
                                        <!--begin::Wrapper-->
                                        <div class="d-flex">
                                            <!--begin::Checkbox-->
                                            <label v-for="(item, i) in permission" :key="i"
                                                class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                <input class="form-check-input" type="checkbox" v-model="item.checked"
                                                    :value="item.id" :checked="item.checked">
                                                <span
                                                    class="form-check-label text-capitalize">{{item.title.split(' ')[0]}}</span>
                                            </label>
                                            <!--end::Checkbox-->
                                        </div>
                                        <!--end::Wrapper-->
                                    </td>
                                    <!--end::Input group-->
                                </tr>
                                <!--end::Table row-->
                            </tbody>
                            <!--end::Table body-->
                        </table>
                        <!--end::Table-->
                    </div>
                    <!--end::Table wrapper-->
                </div>
                <div class="row">
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                            ref="submitButton">
                            <span class="indicator-label" v-if="!form.processing"> Update Changes </span>
                            <span class="indicator-progress" v-if="form.processing">
                                Please wait.
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<script>
import Input from "@/Components/Input.vue";
import Label from "@/Components/Label.vue";
import Button from "@/Components/Button.vue";
import Error from "@/Components/InputError.vue";
import Helpers from "@/Mixins/Helpers";
import InlineSvg from "vue-inline-svg";

export default {
    props: ["role", "rolePermissions", "permissionGroups"],
    components: {
        Input,
        Label,
        Button,
        Error,
        InlineSvg,
    },

    mounted() {
        this.form = this.$inertia.form({
            id: this.role ? this.role.id : "",
            permissions: this.permissionGroups,
            rolePermission: "",
        });
        Object.keys(this.form.permissions).forEach((g) => {
            this.form.permissions[g] = this.form.permissions[g].map((p) => {
                return {
                    ...p,
                    checked: this.rolePermissions.indexOf(p.id) !== -1,
                };
            });
        });
    },

    data() {
        return {
            form: null,
            permissions: this.permissions,
            rolePermissions: this.rolePermissions,
            allSelected: false,
        };
    },

    methods: {
        submit() {
            const rolePermissions = [];
            Object.keys(this.form.permissions).forEach((groupPermission) => {
                this.form.permissions[groupPermission].forEach((permission) => {
                    if (permission.checked) {
                        rolePermissions.push(permission.id);
                    }
                });
            });
            this.form.rolePermission = rolePermissions;
            if (this.role && this.role.id !== "") {
                this.form.put(route("dashboard.settings.roles.update", this.role.id), {
                    errorBag: "role",
                    preserveScroll: true,
                    onSuccess: (data) => {
                    this.$notify({
                        group: "toast",
                        type: data.props.flash.type,
                        text: data.props.flash.message
                    }, 3000) // 3s
                },
                });
            }
        },
        selectAll: function () {

            if (!this.allSelected) {
                Object.keys(this.form.permissions).forEach((groupPermission) => {
                    this.form.permissions[groupPermission] = this.form.permissions[groupPermission].map((permission) => {
                        return {
                            ...permission,
                            checked: true
                        };
                    });
                });
            } else {
                Object.keys(this.form.permissions).forEach((groupPermission) => {
                    this.form.permissions[groupPermission] = this.form.permissions[groupPermission].map((permission) => {
                        return {
                            ...permission,
                            checked: false
                        };
                    });
                });

            }
        },
    },

    mixins: [Helpers],
};
</script>