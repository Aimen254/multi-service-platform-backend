<template>
    <MenuItem>
    <Link :href="route('dashboard.settings.general.group', type)" class="nav-link text-active-primary m-0 mt-3 text-hover-primary"
        :class="[type == group  ? 'active' : '']">
        <i v-if="type == 'push_notification'" class="fas fa-bell me-3 fs-2"></i>
        <i v-if="type == 'email_notification'" class="fas fa-envelope me-3 fs-2"></i> 
        <i v-if="type == 'stripe_connect_settings'" class="fab fa-stripe-s me-3 fs-2"></i>
        <i v-if="type == 'number_format_settings'" class="fas fa-calculator me-3 fs-2"></i>
        <i v-if="type == 'time_format_settings'" class="far fa-clock me-3 fs-2"></i>
        <i v-if="type == 'tax_model_settings'" class="far fa-clock me-3 fs-2"></i>
        <i v-if="type == 'driver_assignment_settings'" class="fab fa-red-river me-3 fs-2"></i>
        <i v-if="type == 'checkout_fields_settings'" class="fas fa-check-circle me-3 fs-2"></i>{{title}}
    </Link>
    </MenuItem>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
    export default {
        components: {
            AuthenticatedLayout,
            Head,
            Link,
        },
        props: ['title', 'description', 'type', 'svgIcon', 'group'],
        data() {
            return {
                type: this.type,
            }
        },
    }
</script>
