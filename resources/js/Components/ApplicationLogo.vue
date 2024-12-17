<template>
    <img :class="classes" :src="logoUrl" alt="Workflow">
</template>

<script>

export default {
    props: {
        classes: {
            type: String,
            default: 'mx-auto h-12 w-auto',
        },
        imgSrc: {
            type: String,
            'default': '/images/logo/localverse.jpeg'
        }
    },
    data() {
        return {
            logoUrl: null
        }
    },
    mounted() {
        const baseUrl = window.location.protocol + "//" + window.location.host
        this.logoUrl = baseUrl + this.imgSrc
    }
}
</script>

