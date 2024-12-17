<template>
    <div>
        <Link :href="url" @click="removeTooltip()" v-if="checkUserPermissions(permission)"
            class="btn btn-active-color-primary btn-sm me-1" :class="{ 'btn-icon btn-bg-light': !text }"
            data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss="click" data-bs-placement="bottom" data-bs-original-title="view">
            <i class="fas fa-eye"></i><span v-if="text">{{ text }}</span>
        </Link>
    </div>
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue3'
    import Helpers from '@/Mixins/Helpers'
    import InlineSvg from 'vue-inline-svg'

    export default {
        props: ['url', 'permission', 'record', '', 'text'],

        components: {
            Link,
            InlineSvg
        },

        methods: {
            removeTooltip() {
                this.hideTooltip()
            }
        },
        mounted(){
            this.showTooltip()
        },
        mixins: [Helpers]
    }
</script>
