<template>
    <button :type="type" :class="btnClasses">
        <slot />
    </button>
</template>

<script>
export default {
    props: {
        type: {
            type: String,
            default: 'submit',
        },
        classes: {
            type: String,
            default: 'btn btn-md btn-primary'
        },
        btnWidth: {
            type: String,
            default: 'w-auto'
        }
    },
    computed: {
        btnClasses() {
            return this.classes + ' ' + this.btnWidth
        }
    }
}
</script>
