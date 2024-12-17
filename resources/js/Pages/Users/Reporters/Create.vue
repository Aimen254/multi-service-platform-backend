<template>
    <Head title="Create Reporter" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Create Reporter`" :path="`Users - Reporters`" />
        </template>
        <div class="sm:px-6 lg:px-8">
            <j-form :type="type" :mediaAvatarSizes="mediaSizes"></j-form>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import { Head } from '@inertiajs/inertia-vue3'
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import JForm from '../Form.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'

export default {
    props: ['mediaSizes'],
    components: {
        Head,
        AuthenticatedLayout,
        JForm,
        Breadcrumbs
    },
    data() {
        return {
            type: 'reporter'
        }
    }
}
</script>