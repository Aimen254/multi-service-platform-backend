<template>
    <label @click="hideTooltip" class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
        data-kt-image-input-action="change" data-bs-toggle="tooltip" data-bs-trigger="hover" :data-bs-original-title="title">
        <i class="bi bi-pencil-fill fs-7"></i>
    </label>
</template>

<script>
import Helpers from '@/Mixins/Helpers'

export default { 
    props: ['title'],
    methods: {
        hideTooltip() {
            $('[data-bs-toggle="tooltip"]').tooltip("hide");
        }
    },
    mounted() {
        this.showTooltip()
    },

    mixins: [Helpers]

}
</script>
