<template>
    <form v-if="form" @submit.prevent="submit" enctype="multipart/form-data">
        <div class="d-flex flex-column flex-lg-row">
            <div class="flex-column card card-flush flex-lg-row-auto w-lg-250px w-xl-350px mb-10">
                <div class="mb-5 mb-xl-8">
                    <div class="mt-6 ms-9">
                        <div class="card-title flex-column">
                            <h2 class="d-flex align-items-center text-dark fw-bolder my-1 fs-3">Profile Picture</h2>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="fv-row mb-10 text-center">
                            <div class="image-input image-input-outline image-input-empty" data-kt-image-input="true">
                                    <div
                                    class="image-input-wrapper w-125px h-125px"
                                    :style="{ 'background-image': 'url(' + getImage(url, isSaved) + ')' }"
                                    ></div>
                                    <EditImage :title="'Change Avatar'" @click="openFileDialog">
                                    </EditImage>
                                    <input id="avatar" type="file" class="d-none" :accept="accept" ref="avatar"
                                        @change="onFileChange" />
                                    <RemoveImageButton v-if="url" :title="'Remove Avatar'" @click="user ? removeImage(user.id) : removeImage()" />
                            </div>
                            <p class="fs-9 text-muted pt-2 mt-3">Avatar must be {{mediaAvatarSizes.width}} x {{mediaAvatarSizes.height}}</p>
                            <error :message="form.errors.avatar"></error>
                            <h3 v-if="user" class="mt-4 text-capitalize">
                                {{ user.first_name }} {{ user.last_name }}
                            </h3>
                        </div>
                    </div>
                    <hr>
                    <div class="mx-7" v-if="user">
                        <div class="card-title flex-column">
                            <h2 class="d-flex align-items-center text-dark fw-bolder my-1 fs-3">Basic Information</h2>
                        </div>
                        <div class="d-flex">
                            <div>
                                <div class="d-flex">
                                    <span class="svg-icon-4">
                                        <inline-svg :src="'/images/icons/user.svg'" />
                                    </span>
                                    <Label class="px-2" for="Name" value="Name:" />
                                </div>
                            </div>
                            <div>
                                <p class="text-muted text-capitalize">{{ user.first_name }} {{ user.last_name }}</p>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div>
                                <div class="d-flex">
                                    <span class="svg-icon-4">
                                        <inline-svg :src="'/images/icons/email.xml'" />
                                    </span>
                                    <Label class="px-2" for="Name" value="Email:" />
                                </div>
                            </div>
                            <div>
                                <p class="text-muted">{{ user.email}}</p>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div>
                                <div class="d-flex">
                                    <span class="svg-icon-4">
                                        <inline-svg :src="'/images/icons/star.svg'" />
                                    </span>
                                    <Label class="px-2" for="Name" value="Phone:" />
                                </div>
                            </div>
                            <div>
                                <p class="text-muted">{{ user.phone }}</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="flex-lg-row-fluid ms-lg-15">
                <div class="card card-flush mb-6 mb-xl-9">
                    <div class="card-header">
                        <div class="card-title flex-column">
                            <h2 class="d-flex align-items-center text-dark fw-bolder my-1 fs-3">Personal Information
                            </h2>
                        </div>
                    </div>
                    <div class="card-body p-9 pt-2">
                        <div class="row">
                            <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                <Label for="first_name" class="required" value="First Name" />
                                <Input id="first_name" type="text"
                                    :class="{'is-invalid border border-danger' : form.errors.first_name}"
                                    v-model="form.first_name" autofocus autocomplete="first_name"
                                    placeholder="Enter first name" />
                                <error :message="form.errors.first_name"></error>
                            </div>
                            <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                <Label for="last_name" class="required" value="Last Name" />
                                <Input id="last_name" type="text"
                                    :class="{'is-invalid border border-danger' : form.errors.last_name}"
                                    v-model="form.last_name" autofocus autocomplete="first_name"
                                    placeholder="Enter last name" />
                                <error :message="form.errors.last_name"></error>
                            </div>
                            <div class="col-lg-6 fv-row fv-plugins-icon-container pt-2">
                                <Label for="email" class="required" value="Email" />
                                <Input id="email" type="email"
                                    :class="{'is-invalid border border-danger' : form.errors.email}"
                                    v-model="form.email" autofocus autocomplete="email" placeholder="Enter email" />
                                <error :message="form.errors.email"></error>
                            </div>
                            <div class="col-lg-6 fv-row fv-plugins-icon-container pt-2">
                                <Label for="password" class="required" value="Password" />
                                <Input id="password" type="password"
                                    :class="{'is-invalid border border-danger' : form.errors.password}"
                                    v-model="form.password" autofocus autocomplete="off" placeholder="Enter password" />
                                <error :message="form.errors.password"></error>
                            </div>
                            <div class="col-lg-6 fv-row fv-plugins-icon-container pt-2">
                                <Label for="phone" value="Phone" />
                                <Input id="phone" type="text"
                                    :class="{'is-invalid border border-danger' : form.errors.phone}"
                                    v-model="form.phone" autofocus autocomplete="phone"
                                    placeholder="Enter phone number" />
                                <error :message="form.errors.phone"></error>
                            </div>
                            <div class="col-lg-6 fv-row fv-plugins-icon-container pt-2">
                                <Label for="neighborhood_name" value="Neighborhood Name" />
                                <Input id="neighborhood_name" type="text"
                                    :class="{'is-invalid border border-danger' : form.errors.neighborhood_name}"
                                    v-model="form.neighborhood_name" autofocus autocomplete="neighborhood_name"
                                    placeholder="Enter neighborhood name" />
                                <error :message="form.errors.neighborhood_name"></error>
                            </div>
                            <div class="text-end mt-2">
                                <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                                    ref="submitButton">
                                    <span class="indicator-label" v-if="!form.processing">
                                        {{ this.user ? 'Update' : 'Save' }} </span>
                                    <span class="indicator-progress" v-if="form.processing">
                                        <span class="spinner-border spinner-border-sm align-middle"></span>
                                    </span>
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<script>
    import Input from '@/Components/Input.vue'
    import Label from '@/Components/Label.vue'
    import Button from '@/Components/Button.vue'
    import Error from '@/Components/InputError.vue'
    import Helpers from '@/Mixins/Helpers'
    import InlineSvg from 'vue-inline-svg'
    import RemoveImageButton from '@/Components/RemoveImage.vue'
    import EditImage from '@/Components/EditImage.vue'

    export default {
        props: ['user', 'type', 'mediaAvatarSizes'],
        components: {
            Input,
            Label,
            Button,
            Error,
            InlineSvg,
            RemoveImageButton,
            EditImage
        },

        mounted() {
            this.form = this.$inertia.form({
                id: this.user ? this.user.id : '',
                first_name: this.user ? this.user.first_name : '',
                last_name: this.user ? this.user.last_name : '',
                email: this.user ? this.user.email : '',
                password: '',
                avatar: '',
                phone: this.user ? this.user.phone : '',
                neighborhood_name: this.user ? this.user.neighborhood_name : '',
            });
            this.url = this.user ? this.user.avatar : null;
            this.isSaved = this.url ? true : false;
        },

        data() {
            return {
                form: null,
                url: null,
                isOpened: false,
                isSaved: false,
                imageType: 'avatar'
            }
        },
        updated() {
            this.showTooltip()
        },
        methods: {
            submit() {
                this.form.avatar = this.$refs.avatar.files[0];
                const submissionUrl = 'government.dashboard.department.staffs.';
                if (submissionUrl != '') {
                    if (this.user && this.user.id !== '') {
                        this.form.processing = true
                        this.form._method = 'PUT'
                        this.$inertia.post(route(submissionUrl + 'update', [this.getSelectedModuleValue(), this.getSelectedBusinessValue(), this.user.id]), this.form, {
                            errorBag: 'admin',
                            preserveScroll: true,
                            onError: (response) => {
                                this.form.errors = response;
                                this.form.processing = false
                            },
                            onSuccess: (response) => {
                                this.form.processing = false
                            }
                        })
                    } else {
                        this.form.post(route(submissionUrl + 'store', [this.getSelectedModuleValue(), this.getSelectedBusinessValue()]), {
                            errorBag: 'admin',
                            preserveScroll: true
                        })
                    }
                }
            },
            onFileChange(e) {
                const file = e.target.files[0];
                this.isSaved = false;
                this.url = URL.createObjectURL(file);
            },
            openFileDialog() {
                document.getElementById('avatar').click()
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
                    if(id) {
                        window.axios.get(route('dashboard.media.remove', [id, this.imageType]))
                        .then((response) => {
                            this.url = null
                            if(id == this.$page.props.auth.user.id) {
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

        mixins: [Helpers]
    }
</script>
