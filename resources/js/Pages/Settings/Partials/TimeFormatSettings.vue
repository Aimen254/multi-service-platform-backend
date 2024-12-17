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
                type: 'time_format_settings',
                title: 'Time Format',
                description: 'Configure 12 hours or 24 hours time format.'
            }
        }
    }
</script>