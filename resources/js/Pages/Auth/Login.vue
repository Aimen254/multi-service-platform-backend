<template>
    <Head title="Log in" />
    <!--begin::Wrapper-->
    <div class="w-lg-500px bg-white rounded shadow-sm p-10 p-lg-15 mx-auto">
        <form class="form w-100" @submit.prevent="submit">
            <div class="text-center mb-10">
                <h1 class="text-dark mb-3">Sign In to Dashboard</h1>
            </div>
            <div class="fv-row mb-8">
                <Label for="email" value="Email" />
                <Input id="email" type="email" v-model="form.email" autofocus
                    autocomplete="username" placeholder="Enter Email"
                    :class="{'is-invalid border border-danger' : form.errors.email}"/>
                <error :message="form.errors.email"></error>
            </div>
            <div class="fv-row mb-8">
                <div class="d-flex flex-stack mb-2">
                    <Label for="password" value="password" />
                    <Link v-if="canResetPassword" :href="route('password.request')"
                        class="link-primary fs-6 fw-bolder">
                        Forgot Password?
                    </Link>
                </div>
                <Input id="password" type="password" v-model="form.password" autocomplete="off" 
                    placeholder="Enter Password"
                    :class="{'is-invalid border border-danger' : form.errors.password}"/>
                <error :message="form.errors.password"></error>
            </div>
            <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" :btnWidth="'w-100'" ref="submitButton">
                <span class="indicator-label" v-if="!form.processing"> Sign in </span>
                <span class="indicator-progress" v-if="form.processing">
                    Please wait...
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                </span>
            </Button>
        </form>
    </div>
</template>

<script>
import Button from '@/Components/Button.vue'
import BreezeGuestLayout from '@/Layouts/Guest.vue'
import Input from '@/Components/Input.vue'
import Label from '@/Components/Label.vue'
import { Head, Link } from '@inertiajs/inertia-vue3';
import Error from '@/Components/InputError.vue'

export default {
    layout: BreezeGuestLayout,

    components: {
        Button,
        Input,
        Label,
        Head,
        Link,
        Error
    },

    props: {
        canResetPassword: Boolean,
        status: String,
    },

    data() {
        return {
            userAgent: window.navigator.userAgent,
            form: this.$inertia.form({
                email: '',
                password: '',
                remember: false,
                device_type: /Mobile|Tablet/i.test(this.userAgent) ? 'Mobile or Tablet' : 'Desktop',
                device_name: window.navigator.userAgent.match(/\((.*?)\)/)[1],
                device_token: localStorage.getItem('firebase_token') ? localStorage.getItem('firebase_token') : null,
                language: 'en',
                notification: 1
            })
        }
    },

    methods: {
        submit() {
            localStorage.selectedModule = 'settings'
            localStorage.selectedModuleName = 'settings'
            if(localStorage.selectedBusiness != null) {
                localStorage.removeItem('selectedBusiness')
            }
            console.log(localStorage.getItem('firebase_token'));
            console.log(this.form.device_token);
            this.form.post(this.route('login'), {
                onFinish: () => {
                    this.form.reset('password')
                }
            })
        }
    }
}
</script>
