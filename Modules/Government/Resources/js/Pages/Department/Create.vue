<template>
    <Head title="Create department" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Create Department`" :path="`Departments`" :subTitle="`Create Department`"></Breadcrumbs>
        </template>
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
                            <Label for="Address" value="Address" :class="{ 'required': $page.props.auth.user.user_type == 'business_owner' || $page.props.auth.user.user_type == 'admin' }"/>
                            <Input type="text" :class="{ 'is-invalid border border-danger': form.errors.address }"
                                v-model="form.address" autofocus placeholder="Enter Address" />
                            <error :message="form.errors.address"></error>
                        </div>
                        <div class="col-lg-6 fv-row mb-2 fv-plugins-icon-container" v-if="$page.props.auth.user.user_type != 'business_owner' && is_admin">
                            <Label for="User Name" value="User Name" />
                            <Input type="text" :class="{ 'is-invalid border border-danger': form.errors.user_name }"
                                   v-model="form.user_name" autofocus placeholder="Enter User Name" />
                            <error :message="form.errors.user_name"></error>
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
import {
    useForm
} from '@inertiajs/inertia-vue3'
export default {
    props: ['is_admin'],
    components: {
        AuthenticatedLayout,
        Head,
        Link,
        Label,
        Input,
        Select2,
        Error,
        Button,
        Breadcrumbs
    },
    data() {
        return {
            form: null,
            marker: {
                position: {
                    lat: null,
                    lng: null
                },
            },
            isOpened: false,
            isSaved: false,
        }
    },
    methods: {
        submit() {
            this.form.post(route('government.dashboard.departments.store', this.getSelectedModuleValue()), {
                errorBag: 'business',
                preserveScroll: false,
                onSuccess: () => {},
                onError: (error) => {console.log(error)}
            })
        },
    },
    mounted() {
        this.form = useForm({
            name: '',
            user_name: '',
            email: '',
            slug: '',
            address: '',
            city: '',
            phone: '',
            home_delivery: false,
            virtual_appointments: false,
            owner_id: '',
            status: 'active',
        });
    },
    mixins: [Helpers]
}
</script>
