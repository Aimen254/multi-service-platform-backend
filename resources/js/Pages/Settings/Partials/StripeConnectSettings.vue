<template>
    <Layouts :title="title" :description="description" :type="type"></Layouts>
</template>

<script>
import Layouts from './Layouts.vue';
export default {
    components: {
        Layouts
    },
    data() {
        return {
            type: 'stripe_connect_settings',
            title: 'Stripe Connect',
            description: 'Stripe Connect allows you to save credit cards and to automatically split the payment between the platform (you) and the business.'
        }
    }
}
</script>