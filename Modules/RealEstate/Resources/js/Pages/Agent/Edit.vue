<template>
    <Head title="Edit Real Estate Agent" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Edit Real Estate Agent`" :path="`Users - Real Estate Agent`" />
        </template>
        <div class="sm:px-6 lg:px-8">
            <j-form :user="agent" :type="type"  :mediaAvatarSizes="mediaSizes"></j-form>
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
    props: ['agent', 'mediaSizes'],
    data () {
        return {
            type: 'agent'
        }
    }
}
</script>
