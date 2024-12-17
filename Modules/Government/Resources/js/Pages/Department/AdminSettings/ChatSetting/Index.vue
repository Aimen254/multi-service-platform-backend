<template>
    <Head title="Dealership Tags" />
    <BreezeAuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Dealership Tags`" :path="`Dealerships - Admin Settings`" />
        </template>
        <div class="d-flex flex-column flex-lg-row">
            <side-menu :deliveryType="this.delivery_type" />
            <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10 bg-white" style="width: 75%!important">
                <div class="mb-5 mb-xl-8">
                    <div class="mt-6 ms-9">
                        <div class="card-title flex-column">
                            <h2 class="d-flex align-items-center text-dark fw-bolder my-1 fs-3">Chat Settings</h2>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="fv-row mb-10 fs-5 fw-bold ps-4">
                        <form class="form" v-if="form"  @submit.prevent="submit">
                            <div class="card card-flush py-4">
                                <div class="card-body py-3 row">
                                    <div class="col-lg-12 py-2">
                                        <div class="form-check pt-2">
                                            <input class="form-check-input" type="checkbox" value="" :checked="form.can_chat"
                                                v-model="form.can_chat" />
                                            <label class="form-check-label fw-bolder" for="flexCheckDefault">
                                                Enable Chat
                                            </label>
                                            <error :message="form.errors.can_chat"></error>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 py-2 d-flex justify-content-end">
                                        <Button type="submit" :class="{ 'opacity-25': form.processing }"
                                            :disabled="form.processing">
                                            <span class="indicator-label" v-if="!form.processing">Update</span>
                                            <span class="indicator-progress" v-if="form.processing">
                                                <span class="spinner-border spinner-border-sm align-middle"></span>
                                            </span>
                                        </Button>
                                    </div>
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
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import BusinessMenu from "@/Pages/Business/Includes/BusinessMenu.vue";
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import Helpers from '@/Mixins/Helpers'
import InlineSvg from 'vue-inline-svg'
import Input from '@/Components/Input.vue'
import Label from '@/Components/Label.vue'
import Button from '@/Components/Button.vue'
import Error from '@/Components/InputError.vue'
import SideMenu from '../Partials/SideMenu.vue'
import TagComponent from "@/Components/TagComponent.vue";
import { values } from 'lodash';
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
    Button
},

    data() {
        return {
            form: null,
            businessUuid: null,
            delivery_type: this.business?.delivery_zone?.delivery_type,
            disableSubmit: false,
            processing: false,
            tagExist: false,
        };
    },
    watch: {
      
    },
    methods: {
        submit() {
            this.form.preserveScroll = false;
            let businessUuid = this.business.uuid;
            this.form.post(route("government.dashboard.department.enable.business-chat", [this
                .getSelectedModuleValue(), businessUuid
            ]));
        },
    },
    mounted() {

        this.form = this.$inertia.form({
            can_chat: Boolean(this.business.can_chat)
        });
    },
    mixins: [Helpers],
}
</script>
