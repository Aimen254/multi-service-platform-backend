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
                title: 'Push Notification',
                type: 'push_notification',
                description: 'Setup your push notifications settings for your apps'
            }
        }
    }
</script>