<template>
    <Head title="Social Links" />
    <AuthenticatedLayout>

        <template #breadcrumbs>
            <Breadcrumbs :title="`Social Links`" :path="`Brokers`" :subTitle="`Social Links`"></Breadcrumbs>
        </template>
        <!-- cover, thumbnail, logo section -->
        <!-- <image-section :business="business"></image-section> -->

        <!-- form section -->
        <div class="bg-white p-2 pt-4 rounded">
            <!-- new code -->
            <form v-if="form" @submit.prevent="submit">
                <!--begin::Layout-->
                <div class="">
                    <!--begin::Content-->
                    <div class="ms-lg-5">
                        <div class="card mb-5 mb-xl-8">
                            <div class="card-body pt-10">
                                <div class="row mt-5">
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <Label value="Facebook" />
                                        <Input type="text" :class="[
                                            form.errors.facebook_id ? 'border border-danger' : '',
                                        ]" v-model="form.facebook_id" />
                                        <error :message="form.errors.facebook_id"></error>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <Label value="Instagram" />
                                        <Input type="text" :class="[
                                            form.errors.instagram_id ? 'border border-danger' : '',
                                        ]" v-model="form.instagram_id" />
                                        <error :message="form.errors.instagram_id"></error>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 mt-2">
                                        <Label value="Twitter" />
                                        <Input type="text" :class="[
                                            form.errors.twitter_id ? 'border border-danger' : '',
                                        ]" v-model="form.twitter_id" />
                                        <error :message="form.errors.twitter_id"></error>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 mt-2">
                                        <Label value="Pinterest" />
                                        <Input type="text" :class="[
                                            form.errors.pinterest_id ? 'border border-danger' : '',
                                        ]" v-model="form.pinterest_id" />
                                        <error :message="form.errors.pinterest_id"></error>
                                    </div>
                                </div>
                            </div>
                            <div class="row mx-5">
                                <div class="col-md-12">
                                    <div class="d-flex col-md-12 justify-content-end">
                                        <Button type="submit" :class="{ 'opacity-25': form.processing }"
                                            :disabled="form.processing">
                                            <span class="indicator-label" v-if="!form.processing">{{
                                                'Save'
                                            }}</span>
                                            <span class="indicator-progress" v-if="form.processing">
                                                <span class="spinner-border spinner-border-sm align-middle"></span>
                                            </span>
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Content-->
                </div>
                <!--end::Layout-->
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import { Head } from "@inertiajs/inertia-vue3";
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Input from "@/Components/Input.vue";
import Label from "@/Components/Label.vue";
import Button from "@/Components/Button.vue";
import Error from "@/Components/InputError.vue";
import { useForm } from "@inertiajs/inertia-vue3";
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import Helpers from '@/Mixins/Helpers'

// CSS
import "vue3-timepicker/dist/VueTimepicker.css";

export default {
    props: ["business"],

    components: {
        Head,
        AuthenticatedLayout,
        Input,
        Label,
        Button,
        Error,
        Breadcrumbs,
    },

    data() {
        return {
            form: null,
            businessUuid: null,
            title: ""
        };
    },

    methods: {
        submit() {
            this.form.put(
                route("real-estate.dashboard.broker.social-links.update", [
                    this.getSelectedModuleValue(),
                    this.businessUuid,
                    this.businessUuid,
                ]),
                {
                    preventScroll: true,
                }
            );

        },
    },

    mounted() {
        this.form = useForm({
            facebook_id: this.business.facebook_id ? this.business.facebook_id : null,
            pinterest_id: this.business.pinterest_id ? this.business.pinterest_id : null,
            twitter_id: this.business.twitter_id ? this.business.twitter_id : null,
            instagram_id: this.business.instagram_id ? this.business.instagram_id : null
        });
        this.businessUuid = this.business.uuid;

    },

    mixins: [Helpers],
};
</script>