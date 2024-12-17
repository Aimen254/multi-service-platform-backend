<template>
    <Head title="Edit employer" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Edit Employer`" :path="`Employers`" :subTitle="`Edit Employer`"></Breadcrumbs>
        </template>
        <!-- cover, thumbnail, logo section -->
        <div class="bg-white ps-2 pt-4 rounded">
            <image-section :business="business" :token="token" :mediaLogoSizes="mediaLogoSizes" :mediaThumbnailSizes="mediaThumbnailSizes" :mediaBannerSizes="mediaBannerSizes"></image-section>
        </div>
        <div class="card pt-5">
            <div class="card-body p-9 pt-5">
                <form v-if="form" @submit.prevent="submit" ref="form">
                    <div class="row mb-2">
                        <div class="col-lg-6 fv-row mb-2 fv-plugins-icon-container">
                            <Label for="name" class="required" value="Name" />
                            <Input id="name" type="text" :class="{ 'is-invalid border border-danger': form.errors.name }"
                                v-model="form.name" autofocus autocomplete="name" placeholder="Enter Name" />
                            <error :message="form.errors.name"></error>
                        </div>
                        <div class="col-lg-6 fv-row mb-2 fv-plugins-icon-container">
                            <Label for="email" class="required" value="Email" />
                            <Input id="email" type="email" :class="{ 'is-invalid border border-danger': form.errors.email }"
                                v-model="form.email" autofocus placeholder="Enter Email" />
                            <error :message="form.errors.email"></error>
                        </div>
                        <div class="col-lg-6 fv-row mb-2 fv-plugins-icon-container">
                            <Label for="slug" class="required" value="Slug (https://yourpage.com/slug)" />
                            <Input id="slug" type="text" :class="{ 'is-invalid border border-danger': form.errors.slug }"
                                v-model="form.slug" autofocus placeholder="Enter Slug" />
                            <error :message="form.errors.slug"></error>
                        </div>
                        <div class="col-lg-6 fv-row mb-2 fv-plugins-icon-container">
                            <Label for="phone" :class="{ 'required': $page.props.auth.user.user_type == 'business_owner' || $page.props.auth.user.user_type == 'admin' }" value="Phone Number" />
                            <Input type="text" :class="{ 'is-invalid border border-danger': form.errors.phone }"
                                v-model="form.phone" autofocus placeholder="Enter Phone Number" />
                            <error :message="form.errors.phone"></error>
                        </div>
                        <div class="col-lg-6 fv-row mb-2 fv-plugins-icon-container" v-if="$page.props.auth.user.user_type != 'business_owner' && $page.props.auth.user.user_type != 'admin'">
                            <Label for="City" class="required" value="City" />
                            <Input type="text" :class="{ 'is-invalid border border-danger': form.errors.city }"
                                v-model="form.city" autofocus placeholder="Enter City" />
                            <error :message="form.errors.city"></error>
                        </div>
                        <div class="fv-row mb-2 fv-plugins-icon-container" :class="[$page.props.auth.user.user_type != 'business_owner' && $page.props.auth.user.user_type != 'admin' ?  'col-lg-6' : 'col-lg-12']">
                            <Label for="Address" :class="{ 'required': $page.props.auth.user.user_type == 'business_owner' }" value="Address" />
                            <Input type="text" :class="{ 'is-invalid border border-danger': form.errors.address }"
                                v-model="form.address" autofocus placeholder="Enter Address" />
                            <error :message="form.errors.address"></error>
                        </div>
                        <div class="col-lg-12 fv-row mb-2 fv-plugins-icon-container" v-if="is_admin">
                            <Label for="Dealership Owner" class="required" value="Owner" />
                            <select2 v-model="form.owner_id" :options="Owners"
                                :class="{ 'is-invalid border border-danger': form.errors.owner_id }"
                                placeholder="Select Owner">
                            </select2>
                            <error :message="form.errors.owner_id"></error>
                        </div>
                        <div class="col-lg-4 fv-row mb-2 fv-plugins-icon-container">
                            <div class="form-check pt-5">
                                <input class="form-check-input" type="checkbox" value="" :checked="form.is_featured"
                                    v-model="form.is_featured" />
                                <label class="form-check-label fw-bolder" for="flexCheckDefault">
                                    Featured Employer
                                </label>
                                <error :message="form.errors.is_featured"></error>
                            </div>
                        </div>
                    </div>
                    <div class="text-end mt-4">
                        <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                            ref="submitButton">
                            <span class="indicator-label" v-if="!form.processing"> {{ this.user ? 'Update' : 'Save' }}
                            </span>
                            <span class="indicator-progress" v-if="form.processing">
                                <span class="spinner-border spinner-border-sm align-middle"></span>
                            </span>
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import { Head, Link } from '@inertiajs/inertia-vue3'
import Label from '@/Components/Label.vue'
import Input from '@/Components/Input.vue'
import Select2 from 'vue3-select2-component'
import Error from '@/Components/InputError.vue'
import Button from '@/Components/Button.vue'
import Helpers from '@/Mixins/Helpers'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import ImageSection from "./ImageSection/Form.vue"
import {
    useForm
} from '@inertiajs/inertia-vue3'
export default {
    props: [
        'businessOwners',
        'is_admin',
        'business',
        'mediaLogoSizes',
        'mediaThumbnailSizes',
        'mediaBannerSizes',
        'token',
    ],
    components: {
        AuthenticatedLayout,
        Head,
        Link,
        Label,
        Input,
        Select2,
        Error,
        Button,
        Breadcrumbs,
        ImageSection
    },
    data() {
        return {
            form: null,
            isOpened: false,
            isSaved: false,
            Owners: '',
        }
    },
    methods: {
        submit() {
            this.form.put(route('employment.dashboard.employers.update', [this.getSelectedModuleValue(), this.business.uuid]), {
                errorBag: 'business',
                preserveScroll: false,
            })
        },
    },
    mounted() {
        this.form = useForm({
            id: this.business ? this.business.id : null,
            name: this.business ? this.business.name : null,
            email: this.business ? this.business.email : null,
            slug: this.business ? this.business.slug : null,
            address: this.business ? this.business.address : null,
            city: this.business ? this.business.city : null,
            phone: this.business ? this.business.phone : null,
            owner_id: this.business ? this.business.owner_id : null,
            is_featured: this.business ? this.business.is_featured ? true : false : false
        });
        this.Owners = this.businessOwners
    },
    mixins: [Helpers]
}
</script>
