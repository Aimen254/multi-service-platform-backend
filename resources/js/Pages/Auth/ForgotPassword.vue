<template>
    <Head title="Forgot Password" />
    <div class="w-lg-500px bg-white rounded shadow-sm p-10 p-lg-15 mx-auto">
        <form class="form w-100 fv-plugins-bootstrap5 fv-plugins-framework"
            @submit.prevent="submit">
            <div class="text-center mb-10">
                <h1 class="text-dark mb-3">Forgot Password ?</h1>
                <div class="text-gray-400 fw-bold fs-4">
                    Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
                </div>
            </div>
            <div v-if="status" class="alert alert-success" role="alert">
                {{ status }}
            </div>
            <div class="fv-row mb-10">
                <div class="d-flex flex-stack mb-2">
                    <Label for="email" value="Email" />
                    <Link :href="route('login')"
                        class="link-primary fs-6 fw-bolder">
                        Login?
                    </Link>
                </div>
                <Input id="email" type="email" v-model="form.email" autofocus
                    autocomplete="username" placeholder="Enter Email"
                    :class="{'is-invalid border border-danger' : form.errors.email}"/>
                <error :message="form.errors.email"></error>
            </div>
            <div class="d-flex flex-wrap justify-content-center pb-lg-0">
                <Button type="submit" :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing" :btnWidth="'w-100'" ref="submitButton">
                    <span class="indicator-label" v-if="!form.processing">
                        Email Password Reset Link
                    </span>
                    <span class="indicator-progress" v-if="form.processing">
                        Please wait...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </Button>
            </div>
        </form>
    </div>
</template>

<script>
import Button from '@/Components/Button.vue'
import BreezeGuestLayout from '@/Layouts/Guest.vue'
import Input from '@/Components/Input.vue'
import Label from '@/Components/Label.vue'
import { Head, Link } from '@inertiajs/inertia-vue3'
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
        status: String,
    },

    data() {
        return {
            form: this.$inertia.form({
                email: ''
            })
        }
    },

    methods: {
        submit() {
            this.form.post(this.route('password.email'))
        }
    }
}
</script>
