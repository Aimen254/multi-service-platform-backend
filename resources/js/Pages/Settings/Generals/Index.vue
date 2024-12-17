<template>
    <Head title="Settings" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="getGroupName(groupname)" :path="`Settings`" />
        </template>
        <div class="row g-5 g-xl-8">
            <div class="col-sm-12">
                <!--begin::Navbar-->
                <div class="card">
                    <div v-if="groupname == 'email_notification' || groupname == 'push_notification' || groupname == 'social_authentication'"
                        class="card-body pt-9 pb-0">
                        <!--begin::Navs-->
                        <div class="d-flex overflow-auto h-55px">
                            <ul v-if="grouptypes"
                                class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder flex-nowrap">
                                <!--begin::Nav item-->
                                <li v-for="grouptype in grouptypes" :key="grouptype.type" class="nav-item">
                                    <Link
                                        :href="route('dashboard.settings.group.type', [getGroup(groupname), grouptype.id])"
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

                <!--begin::Settings-->
                <div class="card">
                    <!--begin::Content-->
                    <div id="kt_account_email_preferences" class="collapse show">
                        <!--begin::Form-->
                        <form class="form" @submit.prevent="submit" enctype="multipart/form-data">
                            <social-authentication-form v-if="form.group == `social_authentication`" :form="form">
                            </social-authentication-form>
                            <general-form v-if="typename == `General` " :form="form"></general-form>
                            <!--begin::Email and Push Notification-->
                            <div v-if="form.group == 'email_notification' && typename != 'General' || form.group == 'push_notification' && typename != 'General'"
                                class="card-body border-top border-bottom px-9 py-9">
                                <div class="row g-5 g-xl-8">
                                    <div class="col-sm-4" v-for="type in form.settings" :key="type.id">
                                        <label class="form-check form-check-custom form-check-solid align-items-start">
                                            <Checkbox :type="type"></Checkbox>
                                            <span class="form-check-label d-flex flex-column align-items-start">
                                                <span class="fs-5 mb-0">{{type.name}}</span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <!--end::Email and Push Notification-->
                            <stripe-connect-form v-if="form.group == `stripe_connect_settings`" :form="form">
                            </stripe-connect-form>
                            <number-format-form v-if="form.group == `number_format_settings`" :form="form">
                            </number-format-form>
                            <time-format-form v-if="form.group == `time_format_settings`" :form="form">
                            </time-format-form>
                            <tax-model-form v-if="form.group == `tax_model_settings`" :form="form">
                            </tax-model-form>
                            <driver-assignment-form v-if="form.group == `driver_assignment_settings`" :form="form">
                            </driver-assignment-form>
                            <!--begin::Card footer-->
                            <div v-if="form.group != 'checkout_fields_settings'"
                                class="card-footer d-flex justify-content-end py-6 px-9">
                                <Button type="submit" :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing" ref="submitButton">
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
                <!--end::Settings-->
            </div>
        </div>
        <!--begin::Row-->
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
import Header from '@/Components/Header.vue';
import Helpers from '@/Mixins/Helpers';
import Button from '@/Components/Button.vue';
import Input from '@/Components/Input.vue';
import Label from '@/Components/Label.vue'
import Checkbox from '@/Components/Checkbox.vue';
import NumberFormatSettings from '../Partials/NumberFormatSettings.vue';
import TimeFormatSettings from '../Partials/TimeFormatSettings.vue';
import StripeConnectSettings from '../Partials/StripeConnectSettings.vue';
import EmailNotificationSettings from '../Partials/EmailNotificationSettings.vue';
import PushNotification from '../Partials/PushNotification.vue';
import DriverAssignmentSettings from '../Partials/DriverAssignmentSettings.vue';
import GeneralForm from '../Forms/GeneralForm.vue';
import SocialAuthenticationForm from '../Forms/SocialAuthenticationForm.vue';
import NumberFormatForm from '../Forms/NumberFormatForm.vue'
import TimeFormatForm from '../Forms/TimeFormatForm.vue'
import TaxModelForm from '../Forms/TaxModelForm.vue'
import StripeConnectForm from '../Forms/StripeConnectForm.vue'
import DriverAssignmentForm from '../Forms/DriverAssignmentForm.vue'
import { useForm } from '@inertiajs/inertia-vue3';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';


export default {
    components: {
        AuthenticatedLayout,
        Head,
        Header,
        Button,
        Input,
        Label,
        Checkbox,
        Link,
        NumberFormatSettings,
        TimeFormatSettings,
        StripeConnectSettings,
        EmailNotificationSettings,
        PushNotification,
        DriverAssignmentSettings,
        GeneralForm,
        SocialAuthenticationForm,
        NumberFormatForm,
        TimeFormatForm,
        StripeConnectForm,
        DriverAssignmentForm,
        Breadcrumbs,
        TaxModelForm,
    },
    props: ['settingsList', 'typevalues', 'groupname', 'typename', 'grouptypes'],
    watch: {
        typevalues: {
            handler(val) {
                this.form.settings = val
            },
            deep: true
        }
    },
    created () {
        this.groupTitle = 'settings ' + this.groupname;
    },
    mounted() {
        this.form.settings = this.typevalues;
    },
    data() {
        return {
            groupTitle: null,
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
                onSuccess: (data) => {
                    this.$notify({
                        group: "toast",
                        type: data.props.flash.type,
                        text: data.props.flash.message
                    }, 3000) // 3s
                },
            })
        },
        getGroup(groupname) {
            if (groupname == 'email_notification') {
                return 'email-notification';
            } else if (groupname == 'push_notification') {
                return 'push-notification';
            } else {
                return 'social-authentication';
            }
        }
    },
    mixins: [Helpers]
}
</script>
