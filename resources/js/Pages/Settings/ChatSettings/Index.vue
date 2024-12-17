<template>
    <Head :title="'Chat Settings'" />
    <AuthenticatedLayout>
         <template #breadcrumbs>
            <Breadcrumbs :title="'Chat Settings'" :path="`Settings`" />
        </template>
        <div class="row g-5 g-xl-8">
            <div class="col-sm-12">
                <!--begin::Settings-->
                <div class="card">
                    <div class="card-title">
                        <label class="fs-5 fw-bolder form-label m-4 mb-4 text-capitalize">Chat Settings (on/off)</label>
                    </div>
                    <!--begin::Content-->
                    <div id="kt_account_email_preferences" class="collapse show">
                        <!--begin::Form-->
                        <form class="form" @submit.prevent="submit" enctype="multipart/form-data" v-if="modules">
                            
                            <!--begin::Email and Push Notification-->
                            <div
                                class="card-body border-top border-bottom px-9 py-9">
                                <div class="row g-5 g-xl-8">
                                    <div class="col-sm-4" v-for="module in modules" :key="module">
                                        <label class="form-check form-check-custom form-check-solid align-items-start">
                                            <input :value="module.id" :checked="module.can_chat" class="form-check-input me-3" type="checkbox" @change="setFormValues(module)" />
                                            <span class="form-check-label d-flex flex-column align-items-start">
                                                <span class="fs-5 mb-0">{{module?.name}}</span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <!--begin::Card footer-->
                            <div
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
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import Button from '@/Components/Button.vue';
import { useForm } from '@inertiajs/inertia-vue3';
export default {
     components: {
        AuthenticatedLayout,
        Head,
        Link,
        Breadcrumbs,
        Button,
    },

     props: ["modulesList"],

    data() {
        return {
            modules: null,
            type: "chat_settings",
            disable: false,
            form: useForm({
                modules: this.modulesList
            }),
        };
    },
    
     watch: {
        modulesList: {
            handler(modulesList) {
                this.modules = modulesList;
            },
            deep: true,
        },
    },

    methods: {
        setFormValues(module){
            module.can_chat = !module.can_chat 
        },
        
        submit() {
            this.form.post(route("dashboard.settings.update-chat-setting"), {
                errorBag: "chat_settings",
                preserveScroll: true,
            });
        }
    },

   created() {
    this.modules = this.modulesList;
  },
}
</script>

<style>

</style>