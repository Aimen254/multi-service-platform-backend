<template>

    <Head :title="getGroupName(groupname)" />
    <AuthenticatedLayout>

        <!--begin::Navbar-->
        <div class="card mb-5 mb-xl-10">
            <div class="card-body pt-9 pb-0">
                <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
                    <div class="flex-grow-1">
                        <!--begin::Title-->
                        <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                            <div class="d-flex flex-column">
                                <!--begin::Name-->
                                <div class="d-flex align-items-center mb-2">
                                    <h class="text-gray-800 text-hover-primary fs-2 fw-bolder me-1">
                                        {{getGroupName(groupname)}}</h>
                                </div>
                                <!--end::Name-->
                            </div>
                        </div>
                        <!--end::Title-->
                    </div>
                </div>

                <!--begin::Navs-->
                <div class="d-flex overflow-auto h-55px">
                    <ul v-if="grouptypes"
                        class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder flex-nowrap">
                        <!--begin::Nav item-->
                        <li v-for="grouptype in grouptypes" :key="grouptype.type" class="nav-item">
                            <Link :href="route('dashboard.settings.general.group.type', grouptype.id)"
                                class="nav-link text-active-primary me-6"
                                :class="[typename == grouptype.type ? 'active' : '']">
                            {{grouptype.type}}
                            </Link>
                        </li>
                        <!--end::Nav item-->
                    </ul>
                </div>
                <!--begin::Navs-->
            </div>
        </div>
        <!--end::Navbar-->

        <!--begin::Notifications-->
        <div class="card mb-5 mb-xl-10">
            <!--begin::Card header-->
            <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse"
                data-bs-target="#kt_account_email_preferences" aria-expanded="true"
                aria-controls="kt_account_email_preferences">
                <div class="card-title m-0">
                    <h3 class="fw-bolder m-0">{{typename}}</h3>
                </div>
            </div>
            <!--begin::Card header-->

            <!--begin::Content-->
            <div id="kt_account_email_preferences" class="collapse show">
                <!--begin::Form-->
                <form class="form" @submit.prevent="submit" enctype="multipart/form-data">
                    <general-form v-if="typename == 'General'" :form="form"></general-form>
                    <!--begin::Card body-->
                    <div v-else class="card-body border-top px-9 py-9">
                        <div class="row g-5 g-xl-8">
                            <div class="col-sm-4" v-for="type in form.settings" :key="type.id">
                                <!--begin::Option-->
                                <label class="form-check form-check-custom form-check-solid align-items-start">
                                    <!--begin::Input-->
                                    <input :id="index" :value="type.value" v-model="type.value" :checked="type.value"
                                        class="form-check-input me-3" type="checkbox" />
                                    <!--end::Input-->
                                    <!--begin::Label-->
                                    <span class="form-check-label d-flex flex-column align-items-start">
                                        <span class="fs-5 mb-0">{{type.name}}</span>
                                    </span>
                                    <!--end::Label-->
                                </label>
                                <!--end::Option-->
                            </div>
                        </div>
                    </div>
                    <!--end::Card body-->

                    <!--begin::Card footer-->
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                            ref="submitButton">
                            <span class="indicator-label" v-if="!form.processing"> Save Changes </span>
                            <span class="indicator-progress" v-if="form.processing">
                                Please wait.
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </Button>
                    </div>
                    <!--end::Card footer-->
                </form>
                <!--end::Form-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Notifications-->
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
import Helpers from '@/Mixins/Helpers';
import Button from '@/Components/Button.vue';
import Input from '@/Components/Input.vue';
import Label from '@/Components/Label.vue'
import { useForm } from '@inertiajs/inertia-vue3'
import GeneralForm from '../Forms/GeneralForm.vue'
export default {
    components: {
        AuthenticatedLayout,
        Head,
        Link,
        Button,
        Input,
        Label,
        GeneralForm
    },
    props: ['typevalues', 'groupname', 'typename', 'grouptypes'],
    watch: {
        typevalues: {
            handler(val) {
                this.form.settings = val
            },
            deep: true
        }
    },
    mounted() {
        this.form.settings = this.typevalues;
    },
    data() {
        return {
            form: useForm({
                settings: [],
                group: this.groupname,
            }),
        }
    },
    methods: {
        submit() {
            this.form.post(route('dashboard.settings.general.group.type.values'), {
                preserveScroll: true,
            })
        },
    },
    mixins: [Helpers]
}
</script>