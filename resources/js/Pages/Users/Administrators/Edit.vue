<template>
    <Head title="Edit Administrator" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Edit Administrator`" :path="`Users - Administrators`" />
        </template>
        <div>
            <j-form :user="administrator" :type="type" :mediaAvatarSizes="mediaSizes"></j-form>
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
        Breadcrumbs,
    },
    props: ['administrator', 'mediaSizes'],
    data() {
        return {
            type: 'administrator'
        }
    },

}
</script>