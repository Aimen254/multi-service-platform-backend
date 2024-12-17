<template>
    <Head title="Edit Business Owner" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Edit Business Owner`" :path="`Users - Business Owner`" />
        </template>
        <div class="sm:px-6 lg:px-8">
            <j-form :user="businessOwner" :type="type"  :mediaAvatarSizes="mediaSizes"></j-form>
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
    props: ['businessOwner', 'mediaSizes'],
    data () {
        return {
            type: 'store_owner'
        }
    }
}
</script>