<template>
    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
        data-kt-image-input-action="remove" data-bs-placement="bottom" data-bs-toggle="tooltip" title=""
        :data-bs-original-title="title">                        
        <i class="bi bi-x fs-2 icon-center"></i>
    </span>
</template>

<script>
import Helpers from '@/Mixins/Helpers'
export default {
    props: ['title'],
    mounted() {
        this.showTooltip()
    },

    mixins: [Helpers]
}
</script>
