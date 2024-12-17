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
                type: 'tax_model_settings',
                title: 'Tax Model',
                description: 'Select UK or US tax model.'
            }
        }
    }
</script>