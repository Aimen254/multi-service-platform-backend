<template>
    <Head title="Reset Password" />
    <div class="w-lg-500px bg-white rounded shadow-sm p-10 p-lg-15 mx-auto">
        <form class="form w-100" @submit.prevent="submit">
            <div class="text-center mb-10">
                <h1 class="text-dark mb-3">Reset Password</h1>
            </div>
            <div class="fv-row mb-8">
                <Label for="email" value="Email" />
                <Input id="email" type="email" v-model="form.email" autofocus
                    autocomplete="username" placeholder="Enter Email"
                    :class="{'is-invalid border border-danger' : form.errors.email}"/>
                <error :message="form.errors.email"></error>
            </div>
            <div class="fv-row mb-8">
                <Label for="password" value="Password" />
                <Input id="password" type="password" v-model="form.password" autocomplete="off" 
                    placeholder="Enter New Password"
                    :class="{'is-invalid border border-danger' : form.errors.password}"/>
                <error :message="form.errors.password"></error>
            </div>
            <div class="fv-row mb-8">
                <Label for="password" value="Confirm Password" />
                <Input id="password_confirmation" type="password" v-model="form.password_confirmation" autocomplete="off" 
                    placeholder="Confirm New Password"
                    :class="{'is-invalid border border-danger' : form.errors.password_confirmation}"/>
                <error :message="form.errors.password_confirmation"></error>
            </div>
            <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" :btnWidth="'w-100'" ref="submitButton">
                <span class="indicator-label" v-if="!form.processing"> Reset Password </span>
                <span class="indicator-progress" v-if="form.processing">
                    Please wait...
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                </span>
            </Button>
        </form>
        <div class="text-center mt-4">
            <Link :href="route('login')"
                class="link-primary fs-6 fw-bolder">
                Login
            </Link>
        </div>
        
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
        Error,
        Link,
    },

    props: {
        email: String,
        token: String,
    },

    data() {
        return {
            form: this.$inertia.form({
                token: this.token,
                email: this.email,
                password: '',
                password_confirmation: '',
            })
        }
    },

    methods: {
        submit() {
            this.form.post(this.route('password.update'), {
                onSuccess: () => {
                    this.form.reset('password', 'password_confirmation'),
                    this.$notify({
                        group: "toast",
                        type: 'success',
                        text: 'Password Reset!'
                    }, 3000) // 3s
                },
            })
        }
    }
}
</script>
