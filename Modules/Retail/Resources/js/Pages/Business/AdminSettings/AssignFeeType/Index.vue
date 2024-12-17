<template>

    <Head title="Settings" />
    <BreezeAuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Settings`" :path="`Businesses - Admin Settings`" />
        </template>
        <div class="d-flex flex-column flex-lg-row">
            <side-menu :deliveryType="this.delivery_type" style="width: 280px;" />
            <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10 bg-white">
                <div class="flex-lg-row-fluid">
                    <div class="card card-flush mb-6 mb-xl-9">
                        <div class="card-header">
                            <div class="card-title flex-column">
                                <h2 class="d-flex align-items-center text-dark fw-bolder my-1 fs-3">
                                    Change Business Fee Type
                                </h2>
                            </div>
                        </div>
                        <div class="card-body p-9 pt-2">
                            <form v-if="form" @submit.prevent="submit">
                                <div class="row">
                                    <div class="col-lg-12 mb-2 fv-row fv-plugins-icon-container">
                                        <Label for="fee_type" value="Delivery fee type" />
                                        <select v-model="form.fee_type" class="form-select text-capitalize form-select-solid"
                                            :class="{'is-invalid border border-danger' : form.errors.fee_type }">
                                            <option  class="text-capitalize" >Delivery fee by mileage</option>
                                            <option class="text-capitalize">
                                                Delivery fee as percentage of sale
                                            </option>
                                        </select>
                                        <error :message="form.errors.fee_type"></error>
                                    </div>
                                    <div class="d-flex col-md-12 justify-content-end pt-3">
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
            this.form.post(route("retail.dashboard.business.feeType.update", [this
                .getSelectedModuleValue(), this.business.uuid, this.business.delivery_zone.id
            ]), {
                preserveScroll: false,
            })
        },
    },

    mounted() {
        this.businessUuid = this.business.uuid;
        this.form = useForm({
            fee_type: this.business.delivery_zone.fee_type,
            delivery_zone_id: this.business.delivery_zone.id,
        });
    },

    mixins: [Helpers],
}
</script>
