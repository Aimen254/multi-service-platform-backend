<template>
    <Head title="Create Administrator" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Create Administrator`" :path="`Users - Administrators`" />
        </template>
        <div>
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
        Breadcrumbs,
    },
    data() {
        return {
            type: 'administrator'
        }
    }
}
</script>