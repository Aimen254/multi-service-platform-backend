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
                type: 'number_format_settings',
                title: 'Number Format',
                description: 'Configure the number of decimals and the separators you want to use in your website and apps.'
            }
        }
    }
</script>

<style>

</style>