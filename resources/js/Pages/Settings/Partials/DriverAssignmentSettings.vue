<template>
    <Layouts :title="title" :description="description" :type="type"></Layouts>
</template>

<script>
import Layouts from './Layouts.vue';
    export default {
        components: {
            Layouts
        },
        data () {
            return {
                type: 'driver_assignment_settings',
                title: 'Driver Assignment',
                description: 'Configure the maximum orders per driver and auto assign settings'
            }
        }
    }
</script>