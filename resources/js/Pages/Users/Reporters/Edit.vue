<template>
    <Head title="Edit Reporter" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Edit Reporter`" :path="`Users - Reporters`" />
        </template>
        <div>
            <j-form :user="reporter" :type="type" :mediaAvatarSizes="mediaSizes"></j-form>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import { Head } from '@inertiajs/inertia-vue3'
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import JForm from '../Form.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import { onMounted } from 'vue'

export default {
    components: {
        Head,
        AuthenticatedLayout,
        JForm,
        Breadcrumbs
    },
    props: ['reporter', 'mediaSizes'],
    data() {
        return {
            type: 'reporter'
        }
    },

}
</script>