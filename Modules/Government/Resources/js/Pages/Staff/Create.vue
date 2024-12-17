<template>
    <Head title="Create Government Staff" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Create Government Staff`" :path="`Users - Government Staff`" />
        </template>
        <div>
            <j-form :type="type" :mediaAvatarSizes="mediaSizes"></j-form>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import { Head } from '@inertiajs/inertia-vue3'
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import JForm from './Form.vue'
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
            type: 'government_staff'
        }
    }
}
</script>
