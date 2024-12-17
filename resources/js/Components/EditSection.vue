<template>
    <div v-if="iconType == 'link'">
        <Link :href="url" @click="removeTooltip()" v-if="checkUserPermissions(permission)"
            class="btn btn-active-color-primary btn-sm me-1" :class="{ 'btn-icon btn-bg-light': !text }" data-bs-toggle="tooltip"
            data-bs-trigger="hover" data-bs-dismiss="click" data-bs-placement="bottom" data-bs-original-title="edit">
        <span class="svg-icon svg-icon-3">
            <inline-svg :src="'/images/icons/edit.svg'" /><span v-if="text">{{ text }}</span>
        </span>
        </Link>
    </div>
    <div v-else>
        <div @click="removeTooltip" v-if="checkUserPermissions('edit_product_variants')"
            class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip"
            data-bs-trigger="hover" data-bs-dismiss="click" data-bs-placement="bottom" data-bs-original-title="edit">
            <span class="svg-icon svg-icon-3">
                <inline-svg :src="'/images/icons/edit.svg'" />
            </span>
        </div>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
import Helpers from '@/Mixins/Helpers'
import InlineSvg from 'vue-inline-svg'

export default {
    props: ['iconType', 'url', 'permission', 'record', '', 'text'],

    components: {
        Link,
        InlineSvg
    },

    methods: {
        removeTooltip() {
            this.hideTooltip()
        }
    },
    mounted() {
        this.showTooltip()
    },

    mixins: [Helpers]
}
</script>
