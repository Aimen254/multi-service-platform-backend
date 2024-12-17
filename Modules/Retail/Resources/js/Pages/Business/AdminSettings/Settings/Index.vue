<template>

    <Head title="Settings" />
    <BreezeAuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Settings`" :path="`Businesses - Admin Settings`" />
        </template>
        <div class="d-flex flex-column flex-lg-row">
            <side-menu style="width: 280px;" :deliveryType="this.delivery_type"/>
            <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10 bg-white">
                <div class="flex-lg-row-fluid">
                    <div class="card card-flush mb-6 mb-xl-9">
                        <div class="card-header">
                            <div class="card-title flex-column">
                                <h2 class="d-flex align-items-center text-dark fw-bolder my-1 fs-3">
                                    Settings
                                </h2>
                            </div>
                        </div>
                        <div class="card-body p-9 pt-2">
                            <form v-if="form" @submit.prevent="submit">
                                <div class="row">
                                    <div v-for="(setting, index) in form.settings" :key="index">
                                        <div class="col-lg-12 mb-4 fv-row fv-plugins-icon-container"
                                            v-if="setting.key == 'custom_platform_fee_type'">
                                            <Label for="tax_type" class="required" value="Custom Platform Fee Type" />
                                            <select class="form-select text-capitalize form-select-solid"
                                                :class="{'is-invalid border border-danger' : form.errors[`settings.${index}.value`]}"
                                                v-model="setting.value">
                                                <option class="text-capitalize">
                                                    Platform fee in fixed amount
                                                </option>
                                                <option class="text-capitalize">
                                                    Platform fee in percentage
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-lg-12 mb-4 fv-row fv-plugins-icon-container"
                                            v-if="setting.key == 'custom_platform_fee_value'">
                                            <Label for="custom_platform_fee_value" class="required"
                                                value=" Custom Platform Fee Value" />
                                            <Input type="number" :class="[
                                            form.errors[`settings.${index}.value`]
                                                ? 'border border-danger'
                                                : '',]" v-model="setting.value" />
                                            <error :message="form.errors[`settings.${index}.value`]"></error>
                                        </div>
                                    </div>
                                    <div class="d-flex col-md-12 justify-content-end">
                                        <Button type="submit" :class="{ 'opacity-25': form.processing }"
                                            :disabled="form.processing">
                                            <span class="indicator-label" v-if="!form.processing">Update</span>
                                            <span class="indicator-progress" v-if="form.processing">
                                                <span class="spinner-border spinner-border-sm align-middle"></span>
                                            </span>
                                        </Button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
import {
    Head
} from "@inertiajs/inertia-vue3";
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue"
import BusinessMenu from "@/Pages/Business/Includes/BusinessMenu.vue"
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import Helpers from '@/Mixins/Helpers'
import InlineSvg from 'vue-inline-svg'
import Input from '@/Components/Input.vue'
import Label from '@/Components/Label.vue'
import Button from '@/Components/Button.vue'
import Error from '@/Components/InputError.vue'
import SideMenu from '../Partials/SideMenu.vue'
import {
    useForm
} from "@inertiajs/inertia-vue3";

export default {
    props: ["business"],

    components: {
        Head,
        BreezeAuthenticatedLayout,
        BusinessMenu,
        SideMenu,
        Breadcrumbs,
        InlineSvg,
        Input,
        Label,
        Button,
        Error,
    },

    data() {
        return {
            businessUuid: null,
            form: null,
            delivery_type: this.business?.delivery_zone?.delivery_type,
        };
    },
    methods: {
        submit() {
            this.form.post(route("retail.dashboard.business.platform-fee-type.update", [this
                .getSelectedModuleValue(), this.business.id
            ]), {
                preserveScroll: false,
            })
        },
    },

    mounted() {
        this.businessUuid = this.business.uuid;
        this.form = useForm({
            settings: this.business.settings,
        });
    },

    mixins: [Helpers],
}
</script>
