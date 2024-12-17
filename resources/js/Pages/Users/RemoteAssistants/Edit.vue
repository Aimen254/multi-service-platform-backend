<template>
    <Head title="Edit Remote Assistant" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Edit Remote Assistant`" :path="`Users - Remote Assistants`" />
        </template>
        <div>
            <j-form :user="assistant" :type="type" :mediaAvatarSizes="mediaSizes"></j-form>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import { Head } from '@inertiajs/inertia-vue3'
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import JForm from '../Form.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'

export default {
    components: {
        Head,
        AuthenticatedLayout,
        JForm,
        Breadcrumbs
    },
    props: ['assistant', 'mediaSizes'],
    data() {
        return {
            type: 'remote_assistant'
        }
    }
}
</script>