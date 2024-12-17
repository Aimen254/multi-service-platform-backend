<template>
    <Head title="Edit Government Staff" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Edit Government Staff`" :path="`Users - Government Staff`" />
        </template>
        <div class="sm:px-6 lg:px-8">
            <j-form :user="staff" :type="type"  :mediaAvatarSizes="mediaSizes"></j-form>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import { Head } from '@inertiajs/inertia-vue3'
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import JForm from './Form.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'

export default {
    components: {
        Head,
        AuthenticatedLayout,
        JForm,
        Breadcrumbs
    },
    props: ['staff', 'mediaSizes'],
    data () {
        return {
            type: 'government_staff'
        }
    }
}
</script>
