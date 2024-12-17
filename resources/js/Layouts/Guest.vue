<template>
    <div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20">
        <a href="#" class="mb-12">
            <ApplicationLogo :classes="'h-45px'" />
        </a>
        <Toast></Toast>
        <div class="">
            <slot />
        </div>
    </div>
</template>

<script>
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import { Link } from '@inertiajs/inertia-vue3';
import Toast from '@/Components/ToastMessage.vue'

export default {
    components: {
        ApplicationLogo,
        Link,
        Toast
    },
    mounted() {
    }
}
</script>
