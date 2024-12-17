<template>

    <Head title="News Paper Logo" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`News Paper Logo`" :path="`Settings`" />
        </template>
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="container-xxl">
                <!--begin::Row-->
                <div class="row g-5 g-xl-9">
                    <!--begin::Col-->
                    <div class="card card-flush h-md-100">
                        <div class="card-header">
                            <div class="card-title">
                                <h2 class="text-capitalize">News Paper Logo</h2>
                            </div>
                        </div>
                        <div class="card-body pt-1">
                            <div class="fw-bolder text-gray-600 mb-5">
                                <form v-if="form" @submit.prevent="submit" enctype="multipart/form-data">
                                    <div class="text-center fv-row fv-plugins-icon-container">
                                        <div class="fv-row mb-4">
                                            <div class="image-input image-input-outline image-input-empty"
                                                data-kt-image-input="true">
                                                <div class="image-input-wrapper" :style="{
                                                    'background-image':
                                                        'url(' +
                                                        getImage(url, isSaved, 'logo') +
                                                        ')',
                                                        'width': '485px',
                                                        'height': '112px'
                                                }"></div>
                                                <EditImage :title="'Change News Paper Logo'" @click="openFileDialog">
                                                    <input type="hidden" name="avatar_remove" value="1" />
                                                </EditImage>
                                                <input id="icon" type="file" class="d-none" :accept="accept" ref="icon"
                                                    @change="onFileChange" />
                                            </div>
                                            <p class="fs-9 text-muted pt-2">
                                                Logo must be {{ logoSize.width }} x
                                                {{ logoSize.height }}
                                            </p>
                                            <error :message="form.errors.value"></error>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
import EditImage from '@/Components/EditImage.vue'
import Error from "@/Components/InputError.vue";

export default {
    components: {
        AuthenticatedLayout,
        Head,
        Header,
        Button,
        Link,
        Breadcrumbs,
        EditImage,
        Error
    },
    props: ["newsPapreLogo", 'logoSize'],
    data() {
        return {
            form: null,
            url: null,
        };
    },
    methods: {
        openFileDialog() {
            document.getElementById("icon").click();
        },
        onFileChange(e) {
            const file = e.target.files[0];
            this.isSaved = false;
            this.url = URL.createObjectURL(file);
            this.form.value = this.$refs.icon.files[0];
            this.$refs.icon.value = null
            this.submit()
        },

        submit() {
            this.$inertia.post(
                route("dashboard.news-paper.logo.update", [this.form.id]), this.form, {
                        errorBag: "groups",
                        preserveScroll: true,
                        onError: errors => {
                            this.form.errors = errors
                        },
                    }
                );
        }
    },
    mounted() {
        this.form = this.$inertia.form({
            id: this.newsPapreLogo.id,
            value: ""
        });

        this.url = this.newsPapreLogo.value ? this.newsPapreLogo.value : null;
        this.isSaved = this.url ? true : false;
    },
    mixins: [Helpers],
};
</script>
