<template>
    <form @submit.prevent="submit" enctype="multipart/form-data">
        <!--begin::Card-->
        <div class="card mb-5 mb-xl-8 h-100">
            <div class="mt-6 ms-9">
                <div class="card-title flex-column">
                    <h2 class="d-flex align-items-center text-dark fw-bolder my-1 fs-3">Profile Picture</h2>
                </div>
            </div>
            <!--begin::Card body-->
            <div class="card-body pt-8">
                <!--begin::Summary-->
                <div class="d-flex flex-center flex-column mb-5">
                    <div class="fv-row mb-4">
                        <div class="image-input image-input-outline image-input-empty" data-kt-image-input="true">
                            <div class="image-input-wrapper w-125px h-125px"
                                :style="{ 'background-image': 'url(' + getImage(url, isSaved) + ')' }"></div>
                            <EditImage :title="'Change Avatar'" @click="openFileDialog">
                            </EditImage>
                            <input id="avatar" type="file" class="d-none" :accept="accept" ref="avatar"
                                @change="onFileChange" />
                            <RemoveImageButton v-if="url" :title="'Remove Avatar'"
                                @click="user ? removeImage(user.id) : removeImage()" />
                        </div>
                    </div>
                    <p class="fs-9 text-muted pt-2">Avatar must be {{mediaAvatarSizes.width}} x {{mediaAvatarSizes.height}}</p>
                    <error v-if="form" :message="form.errors.avatar"></error>
                </div>
                <h3 v-if="user" class="text-gray-800 pb-4 text-center">
                    {{ user.first_name }} {{ user.last_name }}
                </h3>

                <div class="mt-8">
                    <div class="w-100">
                        <ul class="list-group w-100 list-group-flush rounded">
                            <li class="list-group-item cursor-pointer p-4" :class="{
                  active: tabName === 'personal_info',
                  'bg-light': tabName !== 'personal_info',
                }">
                                <a :class="{
                    'link-light fw-bolder fs-7': tabName === 'personal_info',
                    'text-gray-600 fw-bolder fs-7': tabName !== 'personal_info',
                  }" @click="toggleTabsEvent('personal_info')">
                                    Personal Information
                                </a>
                            </li>
                            <li class="list-group-item cursor-pointer p-4" :class="{
                  active: tabName === 'change_password',
                  'bg-light': tabName !== 'change_password',
                }">
                                <a :class="{
                    'link-light fw-bolder fs-7': tabName === 'change_password',
                    'text-gray-600 fw-bolder fs-7':
                      tabName !== 'change_password',
                  }" @click="toggleTabsEvent('change_password')">
                                    Change Password
                                </a>
                            </li>
                            <li class="list-group-item cursor-pointer p-4" :class="{
                  active: tabName === 'update_address',
                  'bg-light': tabName !== 'update_address',
                }">
                                <a :class="{
                    'link-light fw-bolder fs-7': tabName === 'update_address',
                    'text-gray-600 fw-bolder fs-7':
                      tabName !== 'update_address',
                  }" @click="toggleTabsEvent('update_address')">
                                    Address Management
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </form>
</template>

<script>
    import Input from "@/Components/Input.vue";
    import Label from "@/Components/Label.vue";
    import Button from "@/Components/Button.vue";
    import Error from "@/Components/InputError.vue";
    import Helpers from "@/Mixins/Helpers";
    import {
        useForm
    } from "@inertiajs/inertia-vue3"
    import InlineSvg from "vue-inline-svg"
    import RemoveImageButton from '@/Components/RemoveImage.vue'
    import EditImage from '@/Components/EditImage.vue'

    export default {
        props: ["user", "type", "mediaAvatarSizes"],
        components: {
            Input,
            Label,
            Button,
            Error,
            InlineSvg,
            RemoveImageButton,
            EditImage,
        },

        mounted() {
            this.form = useForm({
                avatar: "",
            });
            this.url = this.user ? this.user.avatar : null;
            this.isSaved = this.url ? true : false;
        },
        data() {
            return {
                form: null,
                url: null,
                tabName: "personal_info",
                isOpened: false,
                isSaved: false,
                deleteAvatar: false,
                imageType: 'avatar',
            };
        },

        methods: {
            toggleTabsEvent(tabName) {
                this.tabName = tabName;
                this.$emit("toggleTab", tabName);
            },
            submit() {
                this.form.avatar = this.$refs.avatar.files[0];
                var id = this.$page.props.auth.user.id
                this.form.post(route("dashboard.media.change", [id, this.imageType]), {
                    errorBag: "admin",
                    preserveScroll: true,
                    onSuccess: () => {
                        this.emitter.emit("change-media")
                    }
                });
            },
            openFileDialog() {
                document.getElementById("avatar").click();
            },
            onFileChange(e) {
                const file = e.target.files[0];
                this.deleteAvatar = false;
                this.isSaved = false;
                this.url = URL.createObjectURL(file);
                this.submit();
            },
            removeImage(id = null) {
                this.swal.fire({
                    title: "",
                    html: "<h1 class='text-lg text-gray-800 mb-1'>Remove Avatar</h1><p class='text-base'>Are you sure you want remove avatar?</p>",
                    icon: 'warning',
                    showCancelButton: true,
                    cancelButtonText: 'No',
                    confirmButtonText: "Yes",
                    customClass: {
                        confirmButton: 'danger'
                    }
                }).then((result) => {
                    if (result.value) {
                        if (id) {
                            window.axios.get(route('dashboard.media.remove', [id, this.imageType]))
                                .then((response) => {
                                    this.url = null
                                    if (id == this.$page.props.auth.user.id) {
                                        this.emitter.emit("delete-media")
                                    }
                                    this.$notify({
                                        group: "toast",
                                        type: 'success',
                                        text: response.data.message
                                    }, 3000) // 3s
                                }).catch((error) => {
                                    this.$notify({
                                        group: "toast",
                                        type: 'error',
                                        text: error.response.data.message
                                    }, 3000) // 3s
                                })
                        } else {
                            this.url = null
                            this.$notify({
                                group: "toast",
                                type: 'success',
                                text: "Avatar Removed!"
                            }, 3000) // 3s
                        }
                    }
                })
            },
        },

        mixins: [Helpers],
    };
</script>
